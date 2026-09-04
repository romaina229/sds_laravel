<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuideDownload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuideDownloadAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = GuideDownload::latest();

        if ($request->filled('pays')) {
            $query->where('pays', $request->pays);
        }
        if ($request->filled('fonction')) {
            $query->where('fonction', $request->fonction);
        }
        if ($request->boolean('consentement_seulement')) {
            $query->where('consentement_marketing', true);
        }
        if ($request->filled('recherche')) {
            $terme = $request->recherche;
            $query->where(fn ($q) => $q
                ->where('nom', 'like', "%{$terme}%")
                ->orWhere('organisation', 'like', "%{$terme}%")
                ->orWhere('email', 'like', "%{$terme}%"));
        }

        $downloads = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $downloads->items(),
            'meta'    => [
                'total'     => $downloads->total(),
                'last_page' => $downloads->lastPage(),
            ],
            'stats' => [
                'total_prospects'  => GuideDownload::count(),
                'total_telecharges' => GuideDownload::whereNotNull('telecharge_at')->count(),
                'total_consentants' => GuideDownload::where('consentement_marketing', true)->count(),
                'cette_semaine'     => GuideDownload::where('created_at', '>=', now()->subDays(7))->count(),
            ],
        ]);
    }
}
