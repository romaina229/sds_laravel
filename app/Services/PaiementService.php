<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Parametre;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaiementService
{
    // ====================================================
    // FEDAPAY (Carte bancaire + virement)
    // ====================================================

    public function initierFedaPay(Commande $commande): array
    {
        $secretKey = Parametre::get('fedapay_secret_key');
        $env       = Parametre::get('fedapay_environment', 'sandbox');

        $baseUrl = $env === 'live'
            ? 'https://api.fedapay.com/v1'
            : 'https://sandbox-api.fedapay.com/';

        $callbackUrl  = route('paiement.callback.fedapay');
        $successUrl   = route('paiement.succes', ['commande' => $commande->numero_commande]);
        $cancelUrl    = route('paiement.annule', ['commande' => $commande->numero_commande]);

        try {
            $response = Http::withToken($secretKey)
                ->post("{$baseUrl}/transactions", [
                    'description'  => "Commande {$commande->numero_commande} - {$commande->service_nom}",
                    'amount'       => (int) round($commande->total_ttc_fcfa),
                    'currency'     => ['iso' => 'XOF'],
                    'callback_url' => $callbackUrl,
                    'customer'     => [
                        'firstname' => explode(' ', $commande->client_nom)[0],
                        'lastname'  => implode(' ', array_slice(explode(' ', $commande->client_nom), 1)) ?: '-',
                        'email'     => $commande->client_email,
                        'phone_number' => [
                            'number'  => $commande->client_telephone,
                            'country' => 'BJ',
                        ],
                    ],
                    'meta'         => [
                        'commande_id'     => $commande->id,
                        'numero_commande' => $commande->numero_commande,
                    ],
                ]);

            if ($response->successful()) {
                $data          = $response->json();
                $transactionId = $data['v1/transaction']['id'];

                // Générer le token de paiement
                $tokenResponse = Http::withToken($secretKey)
                    ->post("{$baseUrl}/transactions/{$transactionId}/token");

                if ($tokenResponse->successful()) {
                    $tokenData = $tokenResponse->json();
                    $token     = $tokenData['token'];
                    $payUrl    = $tokenData['url'] ?? "https://checkout.fedapay.com/{$token}";

                    // Sauvegarder le token
                    $commande->update([
                        'payment_token'          => $token,
                        'payment_transaction_id' => $transactionId,
                        'statut'                 => 'paiement_en_cours',
                    ]);

                    return [
                        'success'        => true,
                        'payment_url'    => $payUrl,
                        'token'          => $token,
                        'transaction_id' => $transactionId,
                    ];
                }
            }

            Log::error('FedaPay init error', ['response' => $response->json()]);
            return ['success' => false, 'message' => 'Erreur lors de l\'initialisation du paiement FedaPay.'];

        } catch (\Exception $e) {
            Log::error('FedaPay exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Erreur de connexion au service de paiement.'];
        }
    }

    /**
     * Vérifier une transaction FedaPay (webhook ou retour)
     */
    public function verifierFedaPay(string $transactionId): array
    {
        $secretKey = Parametre::get('fedapay_secret_key');
        $env       = Parametre::get('fedapay_environment', 'sandbox');

        $baseUrl = $env === 'live'
            ? 'https://api.fedapay.com/v1'
            : 'https://sandbox-api.fedapay.com/v1';

        try {
            $response = Http::withToken($secretKey)
                ->get("{$baseUrl}/transactions/{$transactionId}");

            if ($response->successful()) {
                $data   = $response->json();
                $status = $data['v1/transaction']['status'] ?? 'unknown';

                return [
                    'success'        => true,
                    'transaction_id' => $transactionId,
                    'status'         => $status,
                    'approved'       => in_array($status, ['approved', 'transferred']),
                    'data'           => $data,
                ];
            }

            return ['success' => false, 'message' => 'Transaction introuvable.'];

        } catch (\Exception $e) {
            Log::error('FedaPay verify exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Erreur de vérification.'];
        }
    }

    // ====================================================
    // CINETPAY (Mobile Money - Orange, MTN, Moov)
    // ====================================================

    public function initierCinetPay(Commande $commande): array
    {
        $apiKey   = Parametre::get('cinetpay_api_key');
        $siteId   = Parametre::get('cinetpay_site_id');
        $baseUrl  = 'https://api-checkout.cinetpay.com/v2/payment';

        $transactionId = 'SDS-' . time() . '-' . $commande->id;

        $notifyUrl  = route('paiement.callback.cinetpay');
        $returnUrl  = route('paiement.succes', ['commande' => $commande->numero_commande]);
        $cancelUrl  = route('paiement.annule', ['commande' => $commande->numero_commande]);

        try {
            $response = Http::post($baseUrl, [
                'apikey'                => $apiKey,
                'site_id'               => $siteId,
                'transaction_id'        => $transactionId,
                'amount'                => (int) round($commande->total_ttc_fcfa),
                'currency'              => 'XOF',
                'description'           => "Commande {$commande->numero_commande} - {$commande->service_nom}",
                'customer_name'         => $commande->client_nom,
                'customer_surname'      => '',
                'customer_email'        => $commande->client_email,
                'customer_phone_number' => $commande->client_telephone,
                'customer_address'      => 'Bénin',
                'customer_city'         => 'Abomey-Calavi',
                'customer_country'      => 'BJ',
                'customer_state'        => 'BJ',
                'customer_zip_code'     => '00229',
                'notify_url'            => $notifyUrl,
                'return_url'            => $returnUrl,
                'cancel_url'            => $cancelUrl,
                'channels'              => 'MOBILE_MONEY', // Orange, MTN, Moov
                'metadata'              => json_encode([
                    'commande_id'     => $commande->id,
                    'numero_commande' => $commande->numero_commande,
                ]),
                'lang'                  => 'fr',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['code'] === '201') {
                    $paymentUrl = $data['data']['payment_url'];

                    $commande->update([
                        'payment_token'          => $transactionId,
                        'payment_transaction_id' => $transactionId,
                        'statut'                 => 'paiement_en_cours',
                        'payment_data'           => $data,
                    ]);

                    return [
                        'success'        => true,
                        'payment_url'    => $paymentUrl,
                        'transaction_id' => $transactionId,
                    ];
                }
            }

            Log::error('CinetPay init error', ['response' => $response->json()]);
            return ['success' => false, 'message' => 'Erreur lors de l\'initialisation du paiement Mobile Money.'];

        } catch (\Exception $e) {
            Log::error('CinetPay exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Erreur de connexion au service de paiement.'];
        }
    }

    /**
     * Vérifier une transaction CinetPay
     */
    public function verifierCinetPay(string $transactionId, string $token): array
    {
        $apiKey  = Parametre::get('cinetpay_api_key');
        $siteId  = Parametre::get('cinetpay_site_id');
        $baseUrl = 'https://api-checkout.cinetpay.com/v2/payment/check';

        try {
            $response = Http::post($baseUrl, [
                'apikey'         => $apiKey,
                'site_id'        => $siteId,
                'transaction_id' => $transactionId,
                'token'          => $token,
            ]);

            if ($response->successful()) {
                $data   = $response->json();
                $status = $data['data']['status'] ?? 'UNKNOWN';

                return [
                    'success'        => true,
                    'transaction_id' => $transactionId,
                    'status'         => $status,
                    'approved'       => $status === 'ACCEPTED',
                    'data'           => $data,
                ];
            }

            return ['success' => false, 'message' => 'Vérification impossible.'];

        } catch (\Exception $e) {
            Log::error('CinetPay verify exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Erreur de vérification.'];
        }
    }

    // ====================================================
    // TRAITEMENT COMMUN après paiement réussi
    // ====================================================

    public function confirmerPaiement(Commande $commande, array $paymentData = []): void
    {
        $commande->update([
            'statut'       => 'payee',
            'payment_data' => array_merge($commande->payment_data ?? [], $paymentData),
            'paiement_at'  => now(),
        ]);

        // Générer la facture PDF
        app(FactureService::class)->genererFacture($commande);

        // Envoyer email de confirmation au client
        try {
            \Illuminate\Support\Facades\Mail::to($commande->client_email)
                ->send(new \App\Mail\CommandeConfirmee($commande->load('service')));
        } catch (\Exception $e) {
            Log::warning("Echec envoi email confirmation commande {$commande->numero_commande}: {$e->getMessage()}");
        }

        Log::info("Paiement confirmé - Commande {$commande->numero_commande}");
    }
}
