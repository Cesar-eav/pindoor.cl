<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('panoramas')->whereNull('fuente')->update(['fuente' => 'manual']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('panoramas')->where('fuente', 'manual')->update(['fuente' => null]);
    }
};
