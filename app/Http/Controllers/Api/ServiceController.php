<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Cache::remember('services_all', 1800, function () {
            return Service::actif()
                ->orderByDesc('popular')
                ->orderBy('id')
                ->get()
                ->map(fn($s) => $this->formatService($s));
        });

        return response()->json(['success' => true, 'data' => $services]);
    }

    public function byCategorie(string $categorie): JsonResponse
    {
        $services = Cache::remember("services_{$categorie}", 1800, function () use ($categorie) {
            return Service::actif()
                ->byCategorie($categorie)
                ->orderByDesc('popular')
                ->orderBy('id')
                ->get()
                ->map(fn($s) => $this->formatService($s));
        });

        return response()->json(['success' => true, 'data' => $services]);
    }

    public function show(int $id): JsonResponse
    {
        $service = Service::actif()->findOrFail($id);
        return response()->json(['success' => true, 'data' => $this->formatService($service)]);
    }

    private function formatService(Service $service): array
    {
        $tauxAib = (float) config('app.taux_aib', 0.05);
        $aibFcfa = $service->prix_fcfa * $tauxAib;
        $aibEuro = $service->prix_euro * $tauxAib;

        return [
            'id'            => $service->id,
            'nom'           => $service->nom,
            'description'   => $service->description,
            'icone'         => $service->icone,
            'prix_fcfa'     => $service->prix_fcfa,
            'prix_euro'     => $service->prix_euro,
            'aib_fcfa'      => round($aibFcfa, 2),
            'aib_euro'      => round($aibEuro, 2),
            'ttc_fcfa'      => round($service->prix_fcfa + $aibFcfa, 2),
            'ttc_euro'      => round($service->prix_euro + $aibEuro, 2),
            'duree'         => $service->duree,
            'categorie'     => $service->categorie,
            'popular'       => $service->popular,
            'features'      => $service->features ?? [],
            'taux_aib'      => $tauxAib,
        ];
    }
}
