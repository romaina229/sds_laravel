<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    /**
     * Liste paginée des articles publiés.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BlogArticle::publie()->latest('date_publication');

        if ($request->categorie) {
            $query->where('categorie', $request->categorie);
        }

        $articles = $query->paginate(9)->through(fn($a) => [
            'id'               => $a->id,
            'titre'            => $a->titre,
            'slug'             => $a->slug,
            'extrait'          => $a->extrait,
            'image'            => $a->image,
            'categorie'        => $a->categorie,
            'tags'             => $a->tags,
            'vues'             => $a->vues,
            'date_publication' => $a->date_publication?->toISOString(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $articles->items(),
            'meta'    => [
                'current_page' => $articles->currentPage(),
                'last_page'    => $articles->lastPage(),
                'total'        => $articles->total(),
            ],
        ]);
    }

    /**
     * Détail d'un article par son slug.
     */
    public function show(string $slug): JsonResponse
    {
        $article = BlogArticle::publie()->where('slug', $slug)->firstOrFail();

        // Incrémenter les vues
        $article->increment('vues');

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $article->id,
                'titre'            => $article->titre,
                'slug'             => $article->slug,
                'extrait'          => $article->extrait,
                'contenu'          => $article->contenu,
                'image'            => $article->image,
                'categorie'        => $article->categorie,
                'tags'             => $article->tags,
                'vues'             => $article->vues,
                'date_publication' => $article->date_publication?->toISOString(),
            ],
        ]);
    }

    /**
     * Liste des catégories disponibles.
     */
    public function categories(): JsonResponse
    {
        $categories = BlogArticle::publie()
            ->whereNotNull('categorie')
            ->distinct()
            ->pluck('categorie')
            ->sort()
            ->values();

        return response()->json(['success' => true, 'data' => $categories]);
    }
}
