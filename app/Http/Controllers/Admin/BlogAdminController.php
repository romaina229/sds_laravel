<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $articles = BlogArticle::with('auteur')
            ->latest()
            ->get()
            ->map(fn($a) => [
                'id'               => $a->id,
                'titre'            => $a->titre,
                'slug'             => $a->slug,
                'extrait'          => $a->extrait,
                'image'            => $a->image,
                'categorie'        => $a->categorie,
                'tags'             => $a->tags,
                'statut'           => $a->statut,
                'vues'             => $a->vues,
                'date_publication' => $a->date_publication?->toISOString(),
                'created_at'       => $a->created_at->toISOString(),
            ]);

        return response()->json(['success' => true, 'data' => $articles]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'titre'   => 'required|string|max:255',
            'contenu' => 'required|string',
            'statut'  => 'in:brouillon,publie',
        ]);

        $article = BlogArticle::create([
            'titre'            => $request->titre,
            'slug'             => BlogArticle::genererSlug($request->titre),
            'extrait'          => $request->extrait,
            'contenu'          => $request->contenu,
            'image'            => $request->image,
            'categorie'        => $request->categorie,
            'tags'             => $request->tags ?? [],
            'statut'           => $request->statut ?? 'brouillon',
            'auteur_id'        => $request->user()->id,
            'date_publication' => $request->statut === 'publie' ? now() : null,
        ]);

        return response()->json(['success' => true, 'data' => $article], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => BlogArticle::findOrFail($id)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $article = BlogArticle::findOrFail($id);

        $wasPublished = $article->statut === 'publie';
        $nowPublished = ($request->statut ?? $article->statut) === 'publie';

        $article->update([
            'titre'            => $request->titre ?? $article->titre,
            'slug'             => $request->titre ? BlogArticle::genererSlug($request->titre) : $article->slug,
            'extrait'          => $request->extrait ?? $article->extrait,
            'contenu'          => $request->contenu ?? $article->contenu,
            'image'            => $request->image ?? $article->image,
            'categorie'        => $request->categorie ?? $article->categorie,
            'tags'             => $request->tags ?? $article->tags,
            'statut'           => $request->statut ?? $article->statut,
            'date_publication' => (!$wasPublished && $nowPublished) ? now() : $article->date_publication,
        ]);

        return response()->json(['success' => true, 'data' => $article]);
    }

    public function destroy(int $id): JsonResponse
    {
        BlogArticle::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Article supprimé.']);
    }
}
