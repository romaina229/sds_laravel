<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\GuideVisiteurMail;
use App\Mail\NouveauGuideDownload;
use App\Models\GuideDownload;
use App\Models\Parametre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuideDownloadController extends Controller
{
    /**
     * Soumission du formulaire /guide-finance-pro : enregistre le prospect
     * qualifié, génère un jeton de téléchargement à durée limitée, envoie
     * les deux emails (visiteur + notification interne), et renvoie
     * directement le lien de téléchargement pour un accès immédiat côté
     * frontend — sans attendre la réception de l'email.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom'                     => 'required|string|max:255',
            'organisation'            => 'required|string|max:255',
            'fonction'                => ['required', Rule::in(array_keys(GuideDownload::FONCTIONS))],
            'pays'                    => 'required|string|max:100',
            'taille_organisation'     => ['nullable', Rule::in(array_keys(GuideDownload::TAILLES))],
            'nombre_projets'          => 'nullable|integer|min:0|max:10000',
            'email'                   => 'required|email|max:255',
            'telephone'               => 'nullable|string|max:30',
            'consentement_marketing'  => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Le PDF doit être présent avant d'accepter la soumission : on ne
        // qualifie pas un prospect pour un document qu'on ne peut pas lui
        // livrer immédiatement (voir GuideFileAdminController pour l'upload).
        if (! Storage::disk('guides')->exists('finance-pro-guide.pdf')) {
            Log::error('Tentative de téléchargement du guide Finance Pro : fichier absent du disque guides.');
            return response()->json([
                'success' => false,
                'message' => "Le guide n'est pas disponible pour le moment. Merci de réessayer un peu plus tard ou de nous contacter directement.",
            ], 503);
        }

        [$token, $expireAt] = array_values(GuideDownload::genererToken());

        $guideDownload = GuideDownload::create([
            'nom'                    => $request->nom,
            'organisation'           => $request->organisation,
            'fonction'               => $request->fonction,
            'pays'                   => $request->pays,
            'taille_organisation'    => $request->taille_organisation,
            'nombre_projets'         => $request->nombre_projets,
            'email'                  => $request->email,
            'telephone'              => $request->telephone,
            'consentement_marketing' => (bool) $request->consentement_marketing,
            'download_token'         => $token,
            'token_expire_at'        => $expireAt,
            'ip_address'             => $request->ip(),
            'user_agent'             => substr((string) $request->userAgent(), 0, 512),
        ]);

        $downloadUrl = route('api.guide.telecharger', $guideDownload->download_token);

        try {
            Mail::to($guideDownload->email)->send(new GuideVisiteurMail($guideDownload, $downloadUrl));
        } catch (\Exception $e) {
            Log::warning("Echec envoi email guide (visiteur) pour prospect #{$guideDownload->id}: {$e->getMessage()}");
        }

        try {
            $adminEmail = Parametre::get('site_email', 'afrisds@gmail.com');
            Mail::to($adminEmail)->send(new NouveauGuideDownload($guideDownload));
        } catch (\Exception $e) {
            Log::warning("Echec envoi email guide (interne) pour prospect #{$guideDownload->id}: {$e->getMessage()}");
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Merci ! Votre guide est prêt et vous a également été envoyé par email.',
            'download_url' => $downloadUrl,
        ], 201);
    }

    /**
     * Téléchargement effectif, protégé par jeton opaque et non par l'ID
     * de la ligne — impossible à deviner ou à énumérer. Le jeton reste
     * valable 48h et réutilisable pendant cette fenêtre (un visiteur qui
     * revient sur l'email le lendemain doit pouvoir retélécharger), mais
     * jamais au-delà.
     */
    public function download(string $token): StreamedResponse
    {
        $guideDownload = GuideDownload::where('download_token', $token)->firstOrFail();

        abort_unless($guideDownload->estValide(), 410, 'Ce lien de téléchargement a expiré. Merci de redemander le guide depuis notre site.');
        abort_unless(Storage::disk('guides')->exists('finance-pro-guide.pdf'), 404, 'Guide introuvable.');

        $guideDownload->increment('nombre_telechargements');
        if (! $guideDownload->telecharge_at) {
            $guideDownload->update(['telecharge_at' => now()]);
        }

        return Storage::disk('guides')->download('finance-pro-guide.pdf', 'Finance-Pro-Guide-Utilisation.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
