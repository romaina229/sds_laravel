<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ParametreAdminController extends Controller
{
    /**
     * Retourne tous les paramètres groupés.
     */
    public function index(): JsonResponse
    {
        $parametres = Parametre::orderBy('groupe')->orderBy('cle')->get();

        $grouped = $parametres->groupBy('groupe')->map(function ($items) {
            return $items->pluck('valeur', 'cle');
        });

        return response()->json([
            'success' => true,
            'data'    => $grouped,
            'flat'    => $parametres->pluck('valeur', 'cle'),
        ]);
    }

    /**
     * Met à jour un ensemble de paramètres.
     * Body: { "parametres": { "site_nom": "SDS", "taux_aib": "0.05", ... } }
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'parametres' => 'required|array',
        ]);

        $updated = 0;
        foreach ($request->parametres as $cle => $valeur) {
            // Sécurité : ne pas accepter des clés arbitraires
            $allowed = [
                'site_nom', 'site_email', 'site_telephone', 'site_whatsapp',
                'site_adresse', 'site_description', 'taux_aib',
                'fedapay_public_key', 'fedapay_secret_key', 'fedapay_environment',
                'cinetpay_api_key', 'cinetpay_site_id',
                'maintenance_mode', 'smtp_host', 'smtp_port', 'smtp_user',
                'smtp_password', 'smtp_from_name', 'smtp_from_email',
                'facebook_url', 'linkedin_url', 'whatsapp_business',
            ];

            if (in_array($cle, $allowed)) {
                Parametre::set($cle, $valeur);
                $updated++;
            }
        }

        // Invalider le cache
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => "{$updated} paramètre(s) mis à jour.",
        ]);
    }

    /**
     * Met à jour un seul paramètre.
     */
    public function updateOne(Request $request, string $cle): JsonResponse
    {
        $request->validate(['valeur' => 'required']);

        Parametre::set($cle, $request->valeur);
        Cache::forget("parametre_{$cle}");

        return response()->json([
            'success' => true,
            'message' => "Paramètre '{$cle}' mis à jour.",
        ]);
    }
}
