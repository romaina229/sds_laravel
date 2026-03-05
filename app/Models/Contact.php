<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'reference', 'nom', 'email', 'telephone',
        'entreprise', 'sujet', 'message',
        'statut', 'reponse', 'repondu_at',
    ];

    protected $casts = [
        'repondu_at' => 'datetime',
    ];

    const STATUTS = [
        'nouveau'  => ['label' => 'Nouveau', 'color' => 'blue'],
        'lu'       => ['label' => 'Lu', 'color' => 'orange'],
        'traite'   => ['label' => 'Traité', 'color' => 'green'],
        'archive'  => ['label' => 'Archivé', 'color' => 'gray'],
    ];

    public static function genererReference(): string
    {
        return 'CONTACT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
