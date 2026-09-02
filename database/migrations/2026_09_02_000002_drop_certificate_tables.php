<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('certificats');
        Schema::dropIfExists('certificat_batches');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Les tables de certificats ont été supprimées volontairement.
    }
};
