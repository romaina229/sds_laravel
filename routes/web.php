<?php

use Illuminate\Support\Facades\Route;

// ── Page principale (API uniquement, pas de React) ───────────────────────────
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');