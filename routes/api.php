<?php

use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\FinanceProController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\FinanceProContentAdminController;
use Illuminate\Support\Facades\Route;

// ===================================================
// PUBLIC
// ===================================================
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/categorie/{categorie}', [ServiceController::class, 'byCategorie']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/finance-pro', [FinanceProController::class, 'show']);
Route::post('/commandes', [CommandeController::class, 'store']);
Route::get('/commandes/{numero}/statut', [CommandeController::class, 'statut'])->name('api.commandes.statut');
Route::get('/commandes/{numero}/facture', [CommandeController::class, 'telechargerFacture'])->name('api.commandes.facture');
Route::post('/paiement/callback/fedapay', [CommandeController::class, 'callbackFedaPay'])->name('paiement.callback.fedapay');
Route::post('/paiement/callback/cinetpay', [CommandeController::class, 'callbackCinetPay'])->name('paiement.callback.cinetpay');
Route::get('/paiement/succes/{commande}', [CommandeController::class, 'succes'])->name('paiement.succes');
Route::get('/paiement/annule/{commande}', function (string $commande) {
    return response()->json(['success' => false, 'commande' => $commande, 'message' => 'Paiement annulé.']);
})->name('paiement.annule');
Route::post('/contact', [ContactController::class, 'store']);
Route::get('health', fn() => response()->json(['status' => 'ok']));
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/categories', [BlogController::class, 'categories']);
Route::get('/blog/{slug}', [BlogController::class, 'show']);
Route::post('/auth/login', [AuthController::class, 'login']);

// ===================================================
// ADMIN (protégé par Sanctum + admin)
// ===================================================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/stats', [DashboardController::class, 'stats']);
    Route::get('/stats/mensuelles', [DashboardController::class, 'statsmensuelles']);
    Route::get('/commandes/recentes', [DashboardController::class, 'commandesRecentes']);
    Route::get('/commandes', [DashboardController::class, 'commandesList']);
    Route::patch('/commandes/{id}/statut', [DashboardController::class, 'updateStatutCommande']);
    Route::apiResource('services', \App\Http\Controllers\Admin\ServiceAdminController::class);
    Route::get('/contacts', [\App\Http\Controllers\Admin\ContactAdminController::class, 'index']);
    Route::patch('/contacts/{id}/statut', [\App\Http\Controllers\Admin\ContactAdminController::class, 'updateStatut']);
    Route::apiResource('blog', \App\Http\Controllers\Admin\BlogAdminController::class);

    // Finance Pro — gestion du contenu commercial
    Route::get('/finance-pro', [FinanceProContentAdminController::class, 'index']);
    Route::post('/finance-pro', [FinanceProContentAdminController::class, 'store']);
    Route::patch('/finance-pro/{id}', [FinanceProContentAdminController::class, 'update']);
    Route::post('/finance-pro/{id}/publish', [FinanceProContentAdminController::class, 'publish']);
    Route::post('/finance-pro/{id}/unpublish', [FinanceProContentAdminController::class, 'unpublish']);
    Route::delete('/finance-pro/{id}', [FinanceProContentAdminController::class, 'destroy']);

    Route::get('/commandes/{id}', [DashboardController::class, 'commandeDetail']);
    Route::get('/parametres', [\App\Http\Controllers\Admin\ParametreAdminController::class, 'index']);
    Route::post('/parametres', [\App\Http\Controllers\Admin\ParametreAdminController::class, 'update']);
    Route::patch('/parametres/{cle}', [\App\Http\Controllers\Admin\ParametreAdminController::class, 'updateOne']);
    Route::get('/admins', [\App\Http\Controllers\Admin\AdminUserController::class, 'index']);
    Route::post('/admins', [\App\Http\Controllers\Admin\AdminUserController::class, 'store']);
    Route::delete('/admins/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy']);
    Route::post('/auth/change-password', [\App\Http\Controllers\Auth\AuthController::class, 'changePassword']);
    Route::get('/exports/commandes', [\App\Http\Controllers\Admin\ExportController::class, 'exportCommandes']);
    Route::get('/exports/contacts', [\App\Http\Controllers\Admin\ExportController::class, 'exportContacts']);
    Route::get('/exports/revenus', [\App\Http\Controllers\Admin\ExportController::class, 'exportRevenusMensuels']);
});
