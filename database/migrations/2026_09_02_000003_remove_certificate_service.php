<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('services')
            ->where('nom', 'Attestations & Certificats Automatisés')
            ->delete();
    }

    public function down(): void
    {
        // Le service a été retiré volontairement du catalogue public.
    }
};
