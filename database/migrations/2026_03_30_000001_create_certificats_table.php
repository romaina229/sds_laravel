<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificats', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('formation');
            $table->string('organisation')->nullable();
            $table->date('date_formation');
            $table->string('duree')->nullable();
            $table->string('mention')->nullable();       // Excellent, Bien, Passable...
            $table->string('email')->nullable();         // pour envoi auto
            $table->string('code_verification')->unique(); // QR code
            $table->string('statut')->default('genere'); // genere, envoye, erreur
            $table->timestamp('envoye_le')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable(); // groupe d'import
            $table->timestamps();
        });

        Schema::create('certificat_batches', function (Blueprint $table) {
            $table->id();
            $table->string('nom');           // ex: "Formation KoboToolbox Mars 2026"
            $table->integer('total');
            $table->integer('envoyes')->default(0);
            $table->integer('erreurs')->default(0);
            $table->string('statut')->default('en_cours'); // en_cours, termine
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificats');
        Schema::dropIfExists('certificat_batches');
    }
};
