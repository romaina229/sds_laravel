<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_pro_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->json('features')->nullable();
            $table->json('benefits')->nullable();
            $table->json('faq')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('demo_url')->nullable();
            $table->decimal('price_fcfa', 12, 2)->nullable();
            $table->decimal('price_euro', 12, 2)->nullable();
            $table->string('price_period')->nullable();
            $table->boolean('published')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_pro_contents');
    }
};
