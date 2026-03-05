<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    /**
     * Export commandes en CSV.
     */
    public function exportCommandes(Request $request): Response
    {
        $query = Commande::with('service')->latest();

        if ($request->statut) {
            $query->where('statut', $request->statut);
        }
        if ($request->date_debut) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->date_fin) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $commandes = $query->get();

        $csv = $this->buildCsv(
            ['Numéro', 'Date', 'Client', 'Email', 'Téléphone', 'Entreprise',
             'Service', 'Catégorie', 'Montant HT (FCFA)', 'AIB (FCFA)',
             'Total TTC (FCFA)', 'Méthode paiement', 'Statut', 'Date paiement'],
            $commandes->map(fn($c) => [
                $c->numero_commande,
                $c->created_at->format('d/m/Y H:i'),
                $c->client_nom,
                $c->client_email,
                $c->client_telephone,
                $c->client_entreprise ?? '',
                $c->service?->nom ?? '',
                $c->service?->categorie ?? '',
                $c->montant_fcfa,
                $c->tva_fcfa,
                $c->total_ttc_fcfa,
                $c->methode_paiement,
                $c->statut,
                $c->paiement_at ? $c->paiement_at->format('d/m/Y H:i') : '',
            ])->toArray()
        );

        $filename = 'commandes_' . now()->format('Ymd_His') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export contacts en CSV.
     */
    public function exportContacts(Request $request): Response
    {
        $query = Contact::latest();

        if ($request->statut) {
            $query->where('statut', $request->statut);
        }

        $contacts = $query->get();

        $csv = $this->buildCsv(
            ['Référence', 'Date', 'Nom', 'Email', 'Téléphone', 'Entreprise', 'Sujet', 'Statut'],
            $contacts->map(fn($c) => [
                $c->reference,
                $c->created_at->format('d/m/Y H:i'),
                $c->nom,
                $c->email,
                $c->telephone ?? '',
                $c->entreprise ?? '',
                $c->sujet,
                $c->statut,
            ])->toArray()
        );

        $filename = 'contacts_' . now()->format('Ymd_His') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Résumé des revenus par mois en CSV.
     */
    public function exportRevenusMensuels(): Response
    {
        $revenus = Commande::where('statut', 'payee')
            ->selectRaw("DATE_FORMAT(paiement_at, '%Y-%m') as mois,
                         COUNT(*) as nb_commandes,
                         SUM(montant_fcfa) as total_ht,
                         SUM(tva_fcfa) as total_aib,
                         SUM(total_ttc_fcfa) as total_ttc")
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        $csv = $this->buildCsv(
            ['Mois', 'Nb Commandes', 'Total HT (FCFA)', 'AIB (FCFA)', 'Total TTC (FCFA)'],
            $revenus->map(fn($r) => [
                $r->mois,
                $r->nb_commandes,
                $r->total_ht,
                $r->total_aib,
                $r->total_ttc,
            ])->toArray()
        );

        $filename = 'revenus_mensuels_' . now()->format('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─── Helper ────────────────────────────────────────────────────────────────

    private function buildCsv(array $headers, array $rows): string
    {
        $output = fopen('php://temp', 'r+');

        // BOM UTF-8 pour Excel
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, $headers, ';');
        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
