<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Models\Certificat;
use App\Models\CertificatBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CertificatController extends Controller
{
    // ================================================================
    // POST /api/admin/certificats/import
    // Upload Excel → lecture → génération PDF → envoi email
    // ================================================================
    public function import(Request $request)
    {
        $request->validate([
            'fichier'      => 'required|file|mimes:xlsx,xls,csv',
            'nom_batch'    => 'required|string|max:200',
            'organisation' => 'required|string|max:200',
        ]);

        // 1. Lire le fichier Excel
        $path = $request->file('fichier')->store('temp');
        $fullPath = Storage::path($path);

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
        } catch (\Exception $e) {
            Storage::delete($path);
            return response()->json(['success' => false, 'message' => 'Fichier Excel invalide : ' . $e->getMessage()], 422);
        }

        // Ignorer la ligne d'en-tête
        $headers = array_shift($rows);
        $rows = array_filter($rows, fn($r) => !empty($r[0])); // ignorer lignes vides

        if (empty($rows)) {
            Storage::delete($path);
            return response()->json(['success' => false, 'message' => 'Aucune donnée trouvée dans le fichier.'], 422);
        }

        // 2. Créer le batch
        $batch = CertificatBatch::create([
            'nom'    => $request->nom_batch,
            'total'  => count($rows),
            'statut' => 'en_cours',
        ]);

        $envoyes = 0;
        $erreurs = 0;
        $resultats = [];

        // 3. Traiter chaque ligne
        foreach ($rows as $row) {
            // Colonnes attendues : nom_complet | formation | date | duree | mention | organisation | email
            [$nom_complet, $formation, $date_formation, $duree, $mention, $organisation_row, $email] = array_pad($row, 7, null);

            $nom_complet     = trim($nom_complet ?? '');
            $formation       = trim($formation ?? $request->nom_batch);
            $organisation_f  = trim($organisation_row ?? $request->organisation);
            $email           = trim($email ?? '');
            $duree           = trim($duree ?? '');
            $mention         = trim($mention ?? '');

            if (empty($nom_complet)) continue;

            // Parser la date (formats variés)
            try {
                $date = \Carbon\Carbon::parse($date_formation);
            } catch (\Exception $e) {
                $date = now();
            }

            // Générer code unique
            $code = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));

            // Créer le certificat en base
            $certificat = Certificat::create([
                'nom_complet'       => $nom_complet,
                'formation'         => $formation,
                'organisation'      => $organisation_f,
                'date_formation'    => $date,
                'duree'             => $duree,
                'mention'           => $mention,
                'email'             => $email,
                'code_verification' => $code,
                'batch_id'          => $batch->id,
                'statut'            => 'genere',
            ]);

            // Générer le PDF
            $pdf = Pdf::loadView('certificat', [
                'nom_complet'       => $nom_complet,
                'formation'         => $formation,
                'organisation'      => $organisation_f,
                'date_formation'    => $date,
                'duree'             => $duree,
                'mention'           => $mention ?: null,
                'code_verification' => $code,
            ])->setPaper('a4', 'landscape');

            $pdfContent = $pdf->output();
            $pdfFilename = 'certificats/' . $batch->id . '/' . Str::slug($nom_complet) . '-' . $code . '.pdf';
            Storage::put($pdfFilename, $pdfContent);

            // Envoyer par email si adresse disponible
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::send([], [], function ($message) use ($email, $nom_complet, $formation, $pdfContent, $code) {
                        $message
                            ->to($email, $nom_complet)
                            ->subject("Votre certificat de formation — {$formation}")
                            ->html("
                                <div style='font-family:sans-serif;max-width:600px;margin:auto;padding:24px'>
                                    <h2 style='color:#1e40af'>Félicitations, {$nom_complet} !</h2>
                                    <p>Veuillez trouver ci-joint votre certificat de participation à la formation :</p>
                                    <p style='font-weight:bold;color:#1e293b'>{$formation}</p>
                                    <p style='margin-top:16px;color:#64748b;font-size:13px'>
                                        Code de vérification : <strong>{$code}</strong>
                                    </p>
                                    <hr style='margin:24px 0;border-color:#e2e8f0'>
                                    <p style='color:#94a3b8;font-size:12px'>
                                        Ce certificat a été généré automatiquement par Shalom Digital Solutions.
                                    </p>
                                </div>
                            ")
                            ->attachData($pdfContent, Str::slug($nom_complet) . '-certificat.pdf', [
                                'mime' => 'application/pdf',
                            ]);
                    });

                    $certificat->update(['statut' => 'envoye', 'envoye_le' => now()]);
                    $envoyes++;
                    $resultats[] = ['nom' => $nom_complet, 'email' => $email, 'statut' => 'envoye', 'code' => $code];

                } catch (\Exception $e) {
                    $certificat->update(['statut' => 'erreur']);
                    $erreurs++;
                    $resultats[] = ['nom' => $nom_complet, 'email' => $email, 'statut' => 'erreur', 'message' => $e->getMessage()];
                }
            } else {
                // Pas d'email — certificat généré sans envoi
                $resultats[] = ['nom' => $nom_complet, 'email' => null, 'statut' => 'genere', 'code' => $code];
            }
        }

        // 4. Mettre à jour le batch
        $batch->update([
            'envoyes' => $envoyes,
            'erreurs' => $erreurs,
            'statut'  => 'termine',
        ]);

        Storage::delete($path);

        return response()->json([
            'success'   => true,
            'message'   => "{$envoyes} certificat(s) envoyé(s), {$erreurs} erreur(s).",
            'batch_id'  => $batch->id,
            'total'     => count($resultats),
            'envoyes'   => $envoyes,
            'erreurs'   => $erreurs,
            'resultats' => $resultats,
        ]);
    }

    public function manuel(Request $request)
    {
        $request->validate([
            'nom_complet'    => 'required|string',
            'formation'      => 'required|string',
            'date_formation' => 'required|date',
            'organisation'   => 'nullable|string',
            'duree'          => 'nullable|string',
            'mention'        => 'nullable|string',
            'email'          => 'nullable|email',
            'action'         => 'required|in:download,email,both',
        ]);
    
        $code = strtoupper(\Illuminate\Support\Str::random(4) . '-' . \Illuminate\Support\Str::random(4) . '-' . \Illuminate\Support\Str::random(4));
    
        // Batch "manuel" unique par jour
        $batch = \App\Models\CertificatBatch::firstOrCreate(
            ['nom' => 'Manuel — ' . now()->format('d/m/Y')],
            ['total' => 0, 'envoyes' => 0, 'erreurs' => 0, 'statut' => 'en_cours']
        );
        $batch->increment('total');
    
        $certificat = \App\Models\Certificat::create([
            'nom_complet'       => $request->nom_complet,
            'formation'         => $request->formation,
            'organisation'      => $request->organisation ?? 'Shalom Digital Solutions',
            'date_formation'    => $request->date_formation,
            'duree'             => $request->duree,
            'mention'           => $request->mention,
            'email'             => $request->email,
            'code_verification' => $code,
            'batch_id'          => $batch->id,
            'statut'            => 'genere',
        ]);
    
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificat', [
            'nom_complet'       => $request->nom_complet,
            'formation'         => $request->formation,
            'organisation'      => $request->organisation ?? 'Shalom Digital Solutions',
            'date_formation'    => \Carbon\Carbon::parse($request->date_formation),
            'duree'             => $request->duree,
            'mention'           => $request->mention ?: null,
            'code_verification' => $code,
        ])->setPaper('a4', 'landscape');
    
        $pdfContent  = $pdf->output();
        $pdfFilename = 'certificats/manuel/' . \Illuminate\Support\Str::slug($request->nom_complet) . '-' . $code . '.pdf';
        \Illuminate\Support\Facades\Storage::put($pdfFilename, $pdfContent);
        $pdfUrl = \Illuminate\Support\Facades\Storage::url($pdfFilename);
    
        $statut = 'genere';
    
        // Envoi email si demandé
        if (in_array($request->action, ['email', 'both']) && $request->email) {
            try {
                \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($request, $pdfContent, $code) {
                    $message
                        ->to($request->email, $request->nom_complet)
                        ->subject("Votre certificat — {$request->formation}")
                        ->html("
                            <div style='font-family:sans-serif;max-width:600px;margin:auto;padding:24px'>
                                <h2 style='color:#1e40af'>Félicitations, {$request->nom_complet} !</h2>
                                <p>Veuillez trouver ci-joint votre certificat de participation :</p>
                                <p style='font-weight:bold'>{$request->formation}</p>
                                <p style='color:#64748b;font-size:13px'>Code de vérification : <strong>{$code}</strong></p>
                                <hr style='margin:20px 0;border-color:#e2e8f0'>
                                <p style='color:#94a3b8;font-size:12px'>Shalom Digital Solutions</p>
                            </div>
                        ")
                        ->attachData($pdfContent, \Illuminate\Support\Str::slug($request->nom_complet) . '-certificat.pdf', [
                            'mime' => 'application/pdf',
                        ]);
                });
                $statut = 'envoye';
                $certificat->update(['statut' => 'envoye', 'envoye_le' => now()]);
                $batch->increment('envoyes');
            } catch (\Exception $e) {
                $statut = 'erreur_email';
                $batch->increment('erreurs');
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => $statut === 'envoye' ? 'Certificat généré et envoyé !' : 'Certificat généré avec succès.',
            'code'    => $code,
            'pdf_url' => $pdfUrl,
            'statut'  => $statut,
        ]);
    }

    // ================================================================
    // GET /api/admin/certificats
    // Liste des batches avec stats
    // ================================================================
    public function index()
    {
        $batches = CertificatBatch::withCount('certificats')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $batches]);
    }

    // ================================================================
    // GET /api/admin/certificats/batch/{id}
    // Détail d'un batch
    // ================================================================
    public function batch($id)
    {
        $batch = CertificatBatch::findOrFail($id);
        $certificats = Certificat::where('batch_id', $id)->get();

        return response()->json([
            'success'     => true,
            'batch'       => $batch,
            'certificats' => $certificats,
        ]);
    }

    // ================================================================
    // GET /api/verify/{code}
    // Vérification publique par QR code
    // ================================================================
    public function verify($code)
    {
        $certificat = Certificat::where('code_verification', $code)->first();

        if (!$certificat) {
            return response()->json(['success' => false, 'message' => 'Certificat introuvable.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'nom_complet'    => $certificat->nom_complet,
                'formation'      => $certificat->formation,
                'organisation'   => $certificat->organisation,
                'date_formation' => $certificat->date_formation,
                'mention'        => $certificat->mention,
                'statut'         => 'Valide ✓',
            ],
        ]);
    }
}
