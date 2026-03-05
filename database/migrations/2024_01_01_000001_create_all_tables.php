<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tables natives Laravel (obligatoires en premier) ─────────────

        // Table users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user'); // admin ou user
            $table->rememberToken();
            $table->timestamps();
        });

        // Table personal_access_tokens (Sanctum)
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Table password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Table sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Table cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // Table jobs
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        // ── Tables metier SDS ────────────────────────────────────────────────

        // Table services
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description');
            $table->string('icone')->default('fas fa-code');
            $table->decimal('prix_fcfa', 10, 2);
            $table->decimal('prix_euro', 10, 2);
            $table->string('duree')->nullable();
            $table->enum('categorie', ['web', 'excel', 'survey', 'formation']);
            $table->boolean('popular')->default(false);
            $table->boolean('actif')->default(true);
            $table->json('features')->nullable(); // liste des fonctionnalités
            $table->timestamps();
        });

        // Table commandes
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_commande')->unique();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_nom');
            $table->decimal('montant_fcfa', 10, 2);
            $table->decimal('montant_euro', 10, 2);
            $table->decimal('tva_fcfa', 10, 2)->default(0);
            $table->decimal('tva_euro', 10, 2)->default(0);
            $table->decimal('total_ttc_fcfa', 10, 2);
            $table->decimal('total_ttc_euro', 10, 2);
            $table->string('duree_estimee')->nullable();
            $table->string('client_nom');
            $table->string('client_email');
            $table->string('client_telephone');
            $table->string('client_entreprise')->nullable();
            $table->text('message')->nullable();
            $table->enum('methode_paiement', ['mobile_money', 'fedapay', 'virement', 'carte']);
            $table->enum('statut', ['en_attente', 'paiement_en_cours', 'payee', 'en_cours', 'livree', 'annulee'])->default('en_attente');
            $table->string('payment_token')->nullable(); // token retour paiement
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_reference')->nullable();
            $table->json('payment_data')->nullable(); // données brutes du paiement
            $table->timestamp('paiement_at')->nullable();
            $table->string('facture_pdf')->nullable();
            $table->timestamps();

            $table->index('client_email');
            $table->index('statut');
        });

        // Table contacts
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('nom');
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->string('entreprise')->nullable();
            $table->string('sujet');
            $table->text('message');
            $table->enum('statut', ['nouveau', 'lu', 'traite', 'archive'])->default('nouveau');
            $table->text('reponse')->nullable();
            $table->timestamp('repondu_at')->nullable();
            $table->timestamps();
        });

        // Table blog
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('slug')->unique();
            $table->text('contenu');
            $table->text('extrait')->nullable();
            $table->string('image')->nullable();
            $table->string('categorie')->nullable();
            $table->json('tags')->nullable();
            $table->enum('statut', ['brouillon', 'publie'])->default('brouillon');
            $table->foreignId('auteur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_publication')->nullable();
            $table->integer('vues')->default(0);
            $table->timestamps();
        });

        // Table factures
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();
            $table->string('client_nom');
            $table->string('client_email');
            $table->string('client_telephone')->nullable();
            $table->string('client_entreprise')->nullable();
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('tva', 10, 2);
            $table->decimal('montant_ttc', 10, 2);
            $table->string('fichier_pdf')->nullable();
            $table->enum('statut', ['brouillon', 'envoyee', 'payee'])->default('brouillon');
            $table->timestamp('date_echeance')->nullable();
            $table->timestamps();
        });

        // Table system_logs
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // info, warning, error, success
            $table->text('message');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('created_at');
        });

        // Table parametres
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('cle')->unique();
            $table->text('valeur')->nullable();
            $table->string('groupe')->default('general');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres');
        Schema::dropIfExists('system_logs');
        Schema::dropIfExists('factures');
        Schema::dropIfExists('blog_articles');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('commandes');
        Schema::dropIfExists('services');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('users');
    }
};
