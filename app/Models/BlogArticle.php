<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogArticle extends Model
{
    protected $table = 'blog_articles';

    protected $fillable = [
        'titre', 'slug', 'contenu', 'extrait', 'image',
        'categorie', 'tags', 'statut', 'auteur_id',
        'date_publication', 'vues',
    ];

    protected $casts = [
        'tags' => 'array',
        'date_publication' => 'datetime',
    ];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function scopePublie($query)
    {
        return $query->where('statut', 'publie');
    }

    public static function genererSlug(string $titre): string
    {
        $slug = Str::slug($titre);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}
