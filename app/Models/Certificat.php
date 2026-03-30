<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificat extends Model
{
    protected $fillable = [
        'nom_complet',
        'formation',
        'organisation',
        'date_formation',
        'duree',
        'mention',
        'email',
        'code_verification',
        'statut',
        'envoye_le',
        'batch_id',
    ];

    protected $casts = [
        'date_formation' => 'date',
        'envoye_le'      => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(CertificatBatch::class, 'batch_id');
    }
}
