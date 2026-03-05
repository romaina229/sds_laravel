<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'description', 'icone', 'prix_fcfa', 'prix_euro',
        'duree', 'categorie', 'popular', 'actif', 'features',
    ];

    protected $casts = [
        'popular' => 'boolean',
        'actif' => 'boolean',
        'features' => 'array',
        'prix_fcfa' => 'float',
        'prix_euro' => 'float',
    ];

    // Relations
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeByCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    public function scopePopulaires($query)
    {
        return $query->where('popular', true);
    }

    // Accesseurs calculés
    public function getTauxAibAttribute(): float
    {
        return (float) config('app.taux_aib', 0.05);
    }

    public function getAibFcfaAttribute(): float
    {
        return $this->prix_fcfa * $this->taux_aib;
    }

    public function getAibEuroAttribute(): float
    {
        return $this->prix_euro * $this->taux_aib;
    }

    public function getTotalTtcFcfaAttribute(): float
    {
        return $this->prix_fcfa + $this->aib_fcfa;
    }

    public function getTotalTtcEuroAttribute(): float
    {
        return $this->prix_euro + $this->aib_euro;
    }

    // Prix formaté lisible
    public function getPrixFormatAttribute(): string
    {
        return number_format($this->prix_fcfa, 0, ',', ' ') . ' FCFA';
    }

    public function getTtcFormatAttribute(): string
    {
        return number_format($this->total_ttc_fcfa, 0, ',', ' ') . ' FCFA';
    }
}
