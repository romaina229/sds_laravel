<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Commande exemple
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Nettoyage des commandes abandonnées (> 24h en paiement_en_cours)
Schedule::call(function () {
    \App\Models\Commande::where('statut', 'paiement_en_cours')
        ->where('updated_at', '<', now()->subHours(24))
        ->update(['statut' => 'annulee']);
})->daily()->name('cleanup-abandoned-orders');

// Nettoyage des tokens Sanctum expirés
Schedule::command('sanctum:prune-expired --hours=24')->daily();
