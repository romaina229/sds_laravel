<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chaque soumission du formulaire /guide-finance-pro devient un prospect
 * qualifié enregistré ici — un mini CRM pour le suivi commercial de
 * Finance Pro, exploitable directement dans l'Admin SDS (liste + export).
 *
 * download_token : jeton opaque et unique généré côté serveur, jamais
 * l'ID de la ligne, pour rendre le lien de téléchargement impossible à
 * deviner ou à énumérer (voir GuideDownloadController::download).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_downloads', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('organisation');
            $table->string('fonction');
            $table->string('pays');
            $table->string('taille_organisation')->nullable();
            $table->unsignedInteger('nombre_projets')->nullable();
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->boolean('consentement_marketing')->default(false);
            $table->string('download_token', 64)->unique();
            $table->timestamp('token_expire_at');
            $table->timestamp('telecharge_at')->nullable();
            $table->unsignedSmallInteger('nombre_telechargements')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_downloads');
    }
};
