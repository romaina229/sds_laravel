<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Permet à l'équipe SDS de mettre à jour le PDF du guide Finance Pro
 * directement depuis l'Admin, sans déploiement — le fichier est toujours
 * stocké sous le même nom fixe (finance-pro-guide.pdf) sur le disque privé
 * 'guides' : la nouvelle version remplace l'ancienne, et tous les liens de
 * téléchargement déjà envoyés (même anciens, non expirés) pointent
 * automatiquement vers la version à jour.
 */
class GuideFileAdminController extends Controller
{
    private const FILENAME = 'finance-pro-guide.pdf';

    public function show(): JsonResponse
    {
        $exists = Storage::disk('guides')->exists(self::FILENAME);

        return response()->json([
            'success'      => true,
            'present'      => $exists,
            'derniere_maj' => $exists ? Storage::disk('guides')->lastModified(self::FILENAME) : null,
            'taille_ko'    => $exists ? round(Storage::disk('guides')->size(self::FILENAME) / 1024) : null,
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fichier' => 'required|file|mimes:pdf|max:20480', // 20 Mo max
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $request->file('fichier')->storeAs('', self::FILENAME, 'guides');

        return response()->json([
            'success' => true,
            'message' => 'Le guide Finance Pro a été mis à jour. Tous les liens de téléchargement (y compris ceux déjà envoyés par email) pointent désormais vers cette nouvelle version.',
        ]);
    }
}
