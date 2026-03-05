<?php

use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// ===================================================
// PUBLIC
// ===================================================

// Services
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/categorie/{categorie}', [ServiceController::class, 'byCategorie']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

// Commandes (création + callbacks paiement)
Route::post('/commandes', [CommandeController::class, 'store']);
Route::get('/commandes/{numero}/statut', [CommandeController::class, 'statut'])->name('api.commandes.statut');
Route::get('/commandes/{numero}/facture', [CommandeController::class, 'telechargerFacture'])->name('api.commandes.facture');

// Callbacks paiement (pas de auth - appelés par les passerelles)
Route::post('/paiement/callback/fedapay', [CommandeController::class, 'callbackFedaPay'])->name('paiement.callback.fedapay');
Route::post('/paiement/callback/cinetpay', [CommandeController::class, 'callbackCinetPay'])->name('paiement.callback.cinetpay');

// Return URLs (retour après paiement depuis la page du prestataire)
Route::get('/paiement/succes/{commande}', [CommandeController::class, 'succes'])->name('paiement.succes');
Route::get('/paiement/annule/{commande}', function (string $commande) {
    return response()->json(['success' => false, 'commande' => $commande, 'message' => 'Paiement annulé.']);
})->name('paiement.annule');

// Contact
Route::post('/contact', [ContactController::class, 'store']);

// Blog
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/categories', [BlogController::class, 'categories']);
Route::get('/blog/{slug}', [BlogController::class, 'show']);

// Auth admin
Route::post('/auth/login', [AuthController::class, 'login']);

// ===================================================
// ADMIN (protégé par Sanctum)
// ===================================================

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/stats', [DashboardController::class, 'stats']);
    Route::get('/stats/mensuelles', [DashboardController::class, 'statsmensuelles']);
    Route::get('/commandes/recentes', [DashboardController::class, 'commandesRecentes']);

    // Commandes
    Route::get('/commandes', [DashboardController::class, 'commandesList']);
    Route::patch('/commandes/{id}/statut', [DashboardController::class, 'updateStatutCommande']);

    // Services CRUD
    Route::apiResource('services', \App\Http\Controllers\Admin\ServiceAdminController::class);

    // Contacts
    Route::get('/contacts', [\App\Http\Controllers\Admin\ContactAdminController::class, 'index']);
    Route::patch('/contacts/{id}/statut', [\App\Http\Controllers\Admin\ContactAdminController::class, 'updateStatut']);

    // Blog
    Route::apiResource('blog', \App\Http\Controllers\Admin\BlogAdminController::class);

    // Commandes – détail
    Route::get('/commandes/{id}', [DashboardController::class, 'commandeDetail']);

    // Paramètres
    Route::get('/parametres',         [\App\Http\Controllers\Admin\ParametreAdminController::class, 'index']);
    Route::post('/parametres',        [\App\Http\Controllers\Admin\ParametreAdminController::class, 'update']);
    Route::patch('/parametres/{cle}', [\App\Http\Controllers\Admin\ParametreAdminController::class, 'updateOne']);


    // Gestion des administrateurs
    Route::get('/admins',         [\App\Http\Controllers\Admin\AdminUserController::class, 'index']);
    Route::post('/admins',        [\App\Http\Controllers\Admin\AdminUserController::class, 'store']);
    Route::delete('/admins/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy']);

    // Changement de mot de passe
    Route::post('/auth/change-password', [\App\Http\Controllers\Auth\AuthController::class, 'changePassword']);

    // Exports CSV
    Route::get('/exports/commandes', [\App\Http\Controllers\Admin\ExportController::class, 'exportCommandes']);
    Route::get('/exports/contacts',  [\App\Http\Controllers\Admin\ExportController::class, 'exportContacts']);
    Route::get('/exports/revenus',   [\App\Http\Controllers\Admin\ExportController::class, 'exportRevenusMensuels']);
});