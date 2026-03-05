<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ServiceAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::orderByDesc('popular')->orderBy('id')->get();
        return response()->json(['success' => true, 'data' => $services]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom'        => 'required|string|max:255',
            'description'=> 'required|string',
            'prix_fcfa'  => 'required|numeric|min:0',
            'categorie'  => 'required|in:web,excel,survey,formation',
        ]);

        $service = Service::create([
            'nom'         => $request->nom,
            'description' => $request->description,
            'icone'       => $request->icone ?? 'fas fa-code',
            'prix_fcfa'   => $request->prix_fcfa,
            'prix_euro'   => $request->prix_euro ?? 0,
            'duree'       => $request->duree,
            'categorie'   => $request->categorie,
            'popular'     => $request->popular ?? false,
            'actif'       => $request->actif ?? true,
            'features'    => $request->features ?? [],
        ]);

        Cache::flush(); // Invalider le cache services
        return response()->json(['success' => true, 'data' => $service], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $service = Service::findOrFail($id);

        $service->update([
            'nom'         => $request->nom ?? $service->nom,
            'description' => $request->description ?? $service->description,
            'icone'       => $request->icone ?? $service->icone,
            'prix_fcfa'   => $request->prix_fcfa ?? $service->prix_fcfa,
            'prix_euro'   => $request->prix_euro ?? $service->prix_euro,
            'duree'       => $request->duree ?? $service->duree,
            'categorie'   => $request->categorie ?? $service->categorie,
            'popular'     => $request->has('popular') ? (bool)$request->popular : $service->popular,
            'actif'       => $request->has('actif') ? (bool)$request->actif : $service->actif,
            'features'    => $request->features ?? $service->features,
        ]);

        Cache::flush();
        return response()->json(['success' => true, 'data' => $service]);
    }

    public function destroy(int $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $service->update(['actif' => false]); // Soft disable plutôt que supprimer
        Cache::flush();
        return response()->json(['success' => true, 'message' => 'Service désactivé.']);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Service::findOrFail($id)]);
    }
}
