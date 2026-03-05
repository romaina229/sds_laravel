<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Facture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FactureService
{
    public function genererFacture(Commande $commande): ?Facture
    {
        try {
            // Créer ou récupérer la facture
            $facture = Facture::firstOrCreate(
                ['commande_id' => $commande->id],
                [
                    'numero_facture'   => Facture::genererNumero(),
                    'client_nom'       => $commande->client_nom,
                    'client_email'     => $commande->client_email,
                    'client_telephone' => $commande->client_telephone,
                    'client_entreprise'=> $commande->client_entreprise,
                    'montant_ht'       => $commande->montant_fcfa,
                    'tva'              => $commande->tva_fcfa,
                    'montant_ttc'      => $commande->total_ttc_fcfa,
                    'statut'           => 'payee',
                ]
            );

            // Générer le PDF
            $pdf = Pdf::loadView('pdf.facture', [
                'facture'  => $facture,
                'commande' => $commande,
                'taux_aib' => config('app.taux_aib', 0.05),
            ]);

            $pdf->setPaper('A4');

            $filename = "factures/FAC-{$facture->numero_facture}.pdf";
            Storage::disk('public')->put($filename, $pdf->output());

            $facture->update(['fichier_pdf' => $filename]);
            $commande->update(['facture_pdf' => $filename]);

            return $facture;

        } catch (\Exception $e) {
            Log::error('Erreur génération facture', [
                'commande' => $commande->numero_commande,
                'error'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function telechargerFacture(Commande $commande): ?string
    {
        if (!$commande->est_payee) {
            return null;
        }

        $facture = $commande->facture;
        if (!$facture || !$facture->fichier_pdf) {
            $facture = $this->genererFacture($commande);
        }

        return $facture?->fichier_pdf;
    }
}
