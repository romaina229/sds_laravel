<?php

use Illuminate\Support\Facades\Route;

// ── Prévisualisation (développement uniquement) ──────────────────────────────

if (app()->environment('local')) {

    Route::get('/preview/email-confirmation', function () {
        // Données fictives pour la prévisualisation
        $commande = (object)[
            'numero_commande'   => 'SDS-2026-00123',
            'client_nom'        => 'Jean DUPONT',
            'montant_fcfa'      => 150000,
            'tva_fcfa'          => 7500,
            'total_ttc_fcfa'    => 157500,
            'methode_paiement'  => 'fedapay_mobile',
            'paiement_at'       => now(),
            'service'           => (object)['nom' => 'Site Vitrine Standard', 'duree' => '2-3 semaines'],
        ];
        return view('emails.commande-confirmee', compact('commande'));
    });

    Route::get('/preview/email-contact', function () {
        $contact = (object)[
            'reference'   => 'CTX-2026-00089',
            'nom'         => 'Marie JOHNSON',
            'email'       => 'marie@ong-benin.org',
            'telephone'   => '+229 97 12 34 56',
            'entreprise'  => 'ONG Solidarité Bénin',
            'sujet'       => 'Demande de devis - Système KoboToolbox',
            'message'     => "Bonjour,\n\nNous sommes une ONG basée à Cotonou et nous cherchons à mettre en place un système de collecte de données pour notre enquête terrain 2026.\n\nNous avons environ 50 agents de collecte. Pourriez-vous nous faire une proposition ?\n\nMerci d'avance.",
        ];
        return view('emails.nouveau-contact', compact('contact'));
    });

    Route::get('/preview/facture', function () {
        $facture = (object)[
            'numero_facture' => 'FAC-2026-00123',
            'client_nom'     => 'Jean DUPONT',
            'client_email'   => 'jean.dupont@email.com',
            'client_telephone' => '+229 97 00 11 22',
            'client_entreprise' => 'Solutions SARL',
            'created_at'     => now(),
        ];
        $commande = (object)[
            'numero_commande'        => 'SDS-2026-00123',
            'service_nom'            => 'Site Vitrine Standard',
            'duree_estimee'          => '2-3 semaines',
            'montant_fcfa'           => 150000,
            'montant_euro'           => 230,
            'tva_fcfa'               => 7500,
            'total_ttc_fcfa'         => 157500,
            'methode_paiement_label' => 'FedaPay Mobile Money',
            'paiement_at'            => now(),
        ];
        $taux_aib = 0.05;
        return view('pdf.facture', compact('facture', 'commande', 'taux_aib'));
    });

}

// ── Page principale (API uniquement, pas de React) ───────────────────────────
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');