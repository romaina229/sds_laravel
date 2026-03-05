<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Service;
use App\Services\PaiementService;
use App\Services\FactureService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommandeController extends Controller
{
    public function __construct(
        private PaiementService $paiementService,
        private FactureService $factureService,
    ) {}

    /**
     * Créer une commande et initier le paiement
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id'        => 'required|exists:services,id',
            'client_nom'        => 'required|string|max:255',
            'client_email'      => 'required|email|max:255',
            'client_telephone'  => 'required|string|min:8|max:20',
            'client_entreprise' => 'nullable|string|max:255',
            'message'           => 'nullable|string|max:2000',
            'methode_paiement'  => 'required|in:mobile_money,fedapay,virement,carte',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $service   = Service::findOrFail($request->service_id);
        $tauxAib   = (float) config('app.taux_aib', 0.05);
        $aibFcfa   = $service->prix_fcfa * $tauxAib;
        $aibEuro   = $service->prix_euro * $tauxAib;
        $ttcFcfa   = $service->prix_fcfa + $aibFcfa;
        $ttcEuro   = $service->prix_euro + $aibEuro;

        $commande = Commande::create([
            'numero_commande'  => Commande::genererNumero(),
            'service_id'       => $service->id,
            'service_nom'      => $service->nom,
            'montant_fcfa'     => $service->prix_fcfa,
            'montant_euro'     => $service->prix_euro,
            'tva_fcfa'         => $aibFcfa,
            'tva_euro'         => $aibEuro,
            'total_ttc_fcfa'   => $ttcFcfa,
            'total_ttc_euro'   => $ttcEuro,
            'duree_estimee'    => $service->duree,
            'client_nom'       => $request->client_nom,
            'client_email'     => $request->client_email,
            'client_telephone' => $request->client_telephone,
            'client_entreprise'=> $request->client_entreprise,
            'message'          => $request->message,
            'methode_paiement' => $request->methode_paiement,
            'statut'           => 'en_attente',
        ]);

        // Initier le paiement selon la méthode
        if ($request->methode_paiement === 'mobile_money') {
            $result = $this->paiementService->initierCinetPay($commande);
        } elseif (in_array($request->methode_paiement, ['fedapay', 'carte'])) {
            $result = $this->paiementService->initierFedaPay($commande);
        } else {
            // Virement bancaire - pas de redirection paiement
            return response()->json([
                'success'         => true,
                'commande'        => $commande,
                'methode'         => 'virement',
                'instructions'    => $this->getVirementInstructions($commande),
                'redirect'        => false,
            ]);
        }

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);
        }

        return response()->json([
            'success'     => true,
            'commande'    => $commande,
            'payment_url' => $result['payment_url'],
            'redirect'    => true,
        ]);
    }

    /**
     * Callback FedaPay (webhook)
     */
    public function callbackFedaPay(Request $request): JsonResponse
    {
        Log::info('FedaPay callback', $request->all());

        $transactionId = $request->input('id') ?? $request->input('transaction_id');

        if (!$transactionId) {
            return response()->json(['message' => 'Transaction ID manquant'], 400);
        }

        $verification = $this->paiementService->verifierFedaPay($transactionId);

        if (!$verification['success']) {
            return response()->json(['message' => 'Vérification échouée'], 400);
        }

        $commande = Commande::where('payment_transaction_id', $transactionId)->first();

        if (!$commande) {
            return response()->json(['message' => 'Commande introuvable'], 404);
        }

        if ($verification['approved']) {
            $this->paiementService->confirmerPaiement($commande, [
                'fedapay_status' => $verification['status'],
                'fedapay_data'   => $verification['data'],
            ]);
        } elseif (in_array($verification['status'], ['declined', 'cancelled'])) {
            $commande->update(['statut' => 'annulee']);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Callback CinetPay (Mobile Money)
     */
    public function callbackCinetPay(Request $request): JsonResponse
    {
        Log::info('CinetPay callback', $request->all());

        $transactionId = $request->input('cpm_trans_id');
        $token         = $request->input('token') ?? '';

        if (!$transactionId) {
            return response()->json(['cpm_result' => '99', 'cpm_trans_id' => ''], 400);
        }

        $verification = $this->paiementService->verifierCinetPay($transactionId, $token);

        $commande = Commande::where('payment_token', $transactionId)
            ->orWhere('payment_transaction_id', $transactionId)
            ->first();

        if (!$commande) {
            return response()->json(['cpm_result' => '99', 'cpm_trans_id' => $transactionId], 404);
        }

        if ($verification['approved']) {
            $this->paiementService->confirmerPaiement($commande, [
                'cinetpay_status' => $verification['status'],
                'cinetpay_data'   => $verification['data'],
            ]);
        } elseif ($verification['success'] && !$verification['approved']) {
            $commande->update(['statut' => 'annulee']);
        }

        // CinetPay attend cette réponse précise
        return response()->json([
            'cpm_result'   => $verification['approved'] ? '00' : '01',
            'cpm_trans_id' => $transactionId,
        ], 200);
    }

    /**
     * Page de succès après paiement (return URL)
     */
    public function succes(Request $request, string $numeroCommande): JsonResponse
    {
        $commande = Commande::where('numero_commande', $numeroCommande)->firstOrFail();

        // Double vérification selon la méthode
        if ($commande->statut === 'paiement_en_cours') {
            if ($commande->methode_paiement === 'mobile_money') {
                // Re-vérifier CinetPay au retour
                $token = $request->input('token') ?? $commande->payment_token;
                $verification = $this->paiementService->verifierCinetPay(
                    $commande->payment_transaction_id,
                    $token
                );
                if ($verification['approved']) {
                    $this->paiementService->confirmerPaiement($commande, $verification['data']);
                    $commande->refresh();
                }
            } elseif (in_array($commande->methode_paiement, ['fedapay', 'carte'])) {
                $verification = $this->paiementService->verifierFedaPay(
                    $commande->payment_transaction_id
                );
                if ($verification['approved']) {
                    $this->paiementService->confirmerPaiement($commande, $verification['data']);
                    $commande->refresh();
                }
            }
        }

        return response()->json([
            'success'  => $commande->est_payee,
            'commande' => $this->formatCommande($commande),
        ]);
    }

    /**
     * Téléchargement de la facture PDF
     */
    public function telechargerFacture(string $numeroCommande): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $commande = Commande::where('numero_commande', $numeroCommande)->firstOrFail();

        abort_unless($commande->est_payee, 403, 'Le paiement n\'est pas encore confirmé.');

        $path = $this->factureService->telechargerFacture($commande);

        if (!$path) {
            abort(404, 'Facture introuvable.');
        }

        $fullPath = storage_path("app/public/{$path}");

        return response()->download(
            $fullPath,
            "Facture-{$commande->numero_commande}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Statut d'une commande (polling)
     */
    public function statut(string $numeroCommande): JsonResponse
    {
        $commande = Commande::where('numero_commande', $numeroCommande)->firstOrFail();

        return response()->json([
            'statut'          => $commande->statut,
            'est_payee'       => $commande->est_payee,
            'statut_info'     => $commande->statut_info,
            'facture_url'     => $commande->est_payee
                ? route('api.commandes.facture', $commande->numero_commande)
                : null,
        ]);
    }

    // Helpers
    private function getVirementInstructions(Commande $commande): array
    {
        return [
            'titulaire'        => 'Shalom Digital Solutions',
            'banque'           => 'Bank Of Africa Bénin',
            'rib'              => 'BJ06 0115 0301 0012 3456 7890 123',
            'swift'            => 'AFRIBJBJ',
            'montant'          => $commande->total_ttc_fcfa,
            'montant_format'   => $commande->montant_format,
            'reference'        => $commande->numero_commande,
            'email_notification'=> 'liferopro@gmail.com',
        ];
    }

    private function formatCommande(Commande $commande): array
    {
        return [
            'id'                       => $commande->id,
            'numero_commande'          => $commande->numero_commande,
            'service_nom'              => $commande->service_nom,
            'duree_estimee'            => $commande->duree_estimee,
            'client_nom'               => $commande->client_nom,
            'client_email'             => $commande->client_email,
            'montant_fcfa'             => $commande->montant_fcfa,
            'tva_fcfa'                 => $commande->tva_fcfa,
            'total_ttc_fcfa'           => $commande->total_ttc_fcfa,
            'methode_paiement'         => $commande->methode_paiement,
            'methode_paiement_label'   => $commande->methode_paiement_label,
            'statut'                   => $commande->statut,
            'statut_info'              => $commande->statut_info,
            'est_payee'                => $commande->est_payee,
            'paiement_at'              => $commande->paiement_at?->format('d/m/Y H:i'),
            'created_at'               => $commande->created_at->format('d/m/Y H:i'),
            'facture_url'              => $commande->est_payee
                ? route('api.commandes.facture', $commande->numero_commande)
                : null,
        ];
    }
}
