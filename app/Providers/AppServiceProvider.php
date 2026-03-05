<?php

namespace App\Providers;

use App\Models\Commande;
use App\Models\Service;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ─── Rate Limiting API ──────────────────────────────────────────────
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Anti-spam commandes : max 5 commandes/min par IP
        RateLimiter::for('commandes', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Auth : max 10 tentatives/min
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // ─── Eloquent Strict Mode en développement ──────────────────────────
        if ($this->app->isLocal()) {
            // Désactiver lazy loading pour détecter les N+1 en dev
            // \Illuminate\Database\Eloquent\Model::preventLazyLoading();
        }
    }
}
