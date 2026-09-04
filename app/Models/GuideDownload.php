<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GuideDownload extends Model
{
    protected $fillable = [
        'nom', 'organisation', 'fonction', 'pays',
        'taille_organisation', 'nombre_projets',
        'email', 'telephone', 'consentement_marketing',
        'download_token', 'token_expire_at',
        'telecharge_at', 'nombre_telechargements',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'consentement_marketing' => 'boolean',
        'token_expire_at'        => 'datetime',
        'telecharge_at'          => 'datetime',
        'nombre_projets'         => 'integer',
    ];

    public const FONCTIONS = [
        'directeur'      => 'Directeur / Responsable',
        'comptable'      => 'Comptable',
        'raf'            => 'Responsable administratif et financier',
        'gestionnaire'   => 'Gestionnaire de projet',
        'informatique'   => 'Responsable informatique',
        'autre'          => 'Autre',
    ];

    public const TAILLES = [
        '1-5'    => '1 à 5 personnes',
        '6-20'   => '6 à 20 personnes',
        '21-50'  => '21 à 50 personnes',
        '50+'    => 'Plus de 50 personnes',
    ];

    /**
     * Génère un jeton opaque unique et sa date d'expiration (48h — assez
     * long pour qu'un utilisateur légitime qui rouvre son email le
     * lendemain puisse encore télécharger, assez court pour qu'un lien
     * qui fuiterait ne reste pas exploitable indéfiniment).
     */
    public static function genererToken(): array
    {
        return [
            'download_token'  => Str::random(64),
            'token_expire_at' => now()->addHours(48),
        ];
    }

    public function estValide(): bool
    {
        return $this->token_expire_at->isFuture();
    }

    public function fonctionLabel(): string
    {
        return self::FONCTIONS[$this->fonction] ?? $this->fonction;
    }
}
