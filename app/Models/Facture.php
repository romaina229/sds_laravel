<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'numero_facture', 'commande_id', 'client_nom', 'client_email',
        'client_telephone', 'client_entreprise',
        'montant_ht', 'tva', 'montant_ttc',
        'fichier_pdf', 'statut', 'date_echeance',
    ];

    protected $casts = [
        'montant_ht' => 'float',
        'tva' => 'float',
        'montant_ttc' => 'float',
        'date_echeance' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public static function genererNumero(): string
    {
        $count = static::whereYear('created_at', date('Y'))->count() + 1;
        return 'FAC-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
