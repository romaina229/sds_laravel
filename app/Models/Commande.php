<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_commande', 'service_id', 'service_nom',
        'montant_fcfa', 'montant_euro', 'tva_fcfa', 'tva_euro',
        'total_ttc_fcfa', 'total_ttc_euro', 'duree_estimee',
        'client_nom', 'client_email', 'client_telephone', 'client_entreprise',
        'message', 'methode_paiement', 'statut',
        'payment_token', 'payment_transaction_id', 'payment_reference',
        'payment_data', 'paiement_at', 'facture_pdf',
    ];

    protected $casts = [
        'montant_fcfa' => 'float',
        'montant_euro' => 'float',
        'tva_fcfa' => 'float',
        'tva_euro' => 'float',
        'total_ttc_fcfa' => 'float',
        'total_ttc_euro' => 'float',
        'payment_data' => 'array',
        'paiement_at' => 'datetime',
    ];

    const STATUTS = [
        'en_attente'        => ['label' => 'En attente', 'color' => 'orange', 'icon' => 'clock'],
        'paiement_en_cours' => ['label' => 'Paiement en cours', 'color' => 'blue', 'icon' => 'spinner'],
        'payee'             => ['label' => 'Payée', 'color' => 'green', 'icon' => 'check-circle'],
        'en_cours'          => ['label' => 'En cours', 'color' => 'purple', 'icon' => 'tools'],
        'livree'            => ['label' => 'Livrée', 'color' => 'teal', 'icon' => 'box-open'],
        'annulee'           => ['label' => 'Annulée', 'color' => 'red', 'icon' => 'times-circle'],
    ];

    const METHODES_PAIEMENT = [
        'mobile_money' => 'Mobile Money (Orange / MTN)',
        'fedapay'      => 'FedaPay (Carte / Virement)',
        'virement'     => 'Virement bancaire',
        'carte'        => 'Carte bancaire (FedaPay)',
    ];

    // Relations
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function facture()
    {
        return $this->hasOne(Facture::class);
    }

    // Scopes
    public function scopePayee($query)
    {
        return $query->where('statut', 'payee');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeByEmail($query, string $email)
    {
        return $query->where('client_email', $email);
    }

    // Accesseurs
    public function getStatutInfoAttribute(): array
    {
        return self::STATUTS[$this->statut] ?? ['label' => $this->statut, 'color' => 'gray', 'icon' => 'question'];
    }

    public function getMethodePaiementLabelAttribute(): string
    {
        return self::METHODES_PAIEMENT[$this->methode_paiement] ?? $this->methode_paiement;
    }

    public function getEstPayeeAttribute(): bool
    {
        return in_array($this->statut, ['payee', 'en_cours', 'livree']);
    }

    public function getMontantFormatAttribute(): string
    {
        return number_format($this->total_ttc_fcfa, 0, ',', ' ') . ' FCFA';
    }

    // Générer numéro de commande unique
    public static function genererNumero(): string
    {
        do {
            $numero = 'SDS-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('numero_commande', $numero)->exists());

        return $numero;
    }
}
