<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Contact;
use App\Models\Service;
use App\Models\BlogArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = [
            'total_commandes'      => Commande::count(),
            'commandes_payees'     => Commande::payee()->count(),
            'commandes_en_attente' => Commande::enAttente()->count(),
            'revenus_total'        => Commande::payee()->sum('total_ttc_fcfa'),
            'revenus_mois'         => Commande::payee()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_ttc_fcfa'),
            'commandes_aujourd_hui'=> Commande::whereDate('created_at', today())->count(),
            'nouveaux_contacts'    => Contact::where('statut', 'nouveau')->count(),
            'total_services'       => Service::actif()->count(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    public function commandesRecentes(): JsonResponse
    {
        $commandes = Commande::with('service')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id'               => $c->id,
                'numero_commande'  => $c->numero_commande,
                'client_nom'       => $c->client_nom,
                'client_email'     => $c->client_email,
                'service_nom'      => $c->service_nom,
                'montant_format'   => $c->montant_format,
                'total_ttc_fcfa'   => $c->total_ttc_fcfa,
                'methode_paiement' => $c->methode_paiement_label,
                'statut'           => $c->statut,
                'statut_info'      => $c->statut_info,
                'created_at'       => $c->created_at->format('d/m/Y H:i'),
            ]);

        return response()->json(['success' => true, 'data' => $commandes]);
    }

    public function statsmensuelles(): JsonResponse
    {
        $data = Commande::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mois"),
            DB::raw('COUNT(*) as nb_commandes'),
            DB::raw('SUM(total_ttc_fcfa) as total')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('mois')
        ->orderBy('mois')
        ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function commandesList(Request $request): JsonResponse
    {
        $query = Commande::with('service')->latest();

        if ($request->statut) $query->where('statut', $request->statut);
        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('numero_commande', 'like', "%{$s}%")
                  ->orWhere('client_nom', 'like', "%{$s}%")
                  ->orWhere('client_email', 'like', "%{$s}%");
            });
        }

        $commandes = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $commandes->items(),
            'meta'    => ['total' => $commandes->total(), 'last_page' => $commandes->lastPage()],
        ]);
    }

    public function updateStatutCommande(Request $request, int $id): JsonResponse
    {
        $commande = Commande::findOrFail($id);
        $request->validate([
            'statut' => 'required|in:en_attente,paiement_en_cours,payee,en_cours,livree,annulee',
        ]);

        $commande->update(['statut' => $request->statut]);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour.', 'commande' => $commande]);
    }

    public function commandeDetail(int $id): JsonResponse
    {
        $commande = Commande::with('service')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $commande->id,
                'numero_commande'     => $commande->numero_commande,
                'client_nom'          => $commande->client_nom,
                'client_email'        => $commande->client_email,
                'client_telephone'    => $commande->client_telephone,
                'client_entreprise'   => $commande->client_entreprise,
                'message'             => $commande->message,
                'service_nom'         => $commande->service?->nom,
                'duree_estimee'       => $commande->service?->duree,
                'methode_paiement'    => $commande->methode_paiement,
                'montant_fcfa'        => $commande->montant_fcfa,
                'tva_fcfa'            => $commande->tva_fcfa,
                'total_ttc_fcfa'      => $commande->total_ttc_fcfa,
                'statut'              => $commande->statut,
                'paiement_at'         => $commande->paiement_at?->toISOString(),
                'facture_pdf'         => $commande->facture_pdf,
                'created_at'          => $commande->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }
}
