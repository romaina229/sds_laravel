<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificatBatch extends Model
{
    protected $fillable = [
        'nom',
        'total',
        'envoyes',
        'erreurs',
        'statut',
    ];

    public function certificats()
    {
        return $this->hasMany(Certificat::class, 'batch_id');
    }
}
