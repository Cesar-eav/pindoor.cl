<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads_contacto', function (Blueprint $table) {
            $table->string('estado', 20)->default('pendiente')->after('mensaje');
            $table->text('observaciones')->nullable()->after('estado');
        });

        DB::table('leads_contacto')->where('contactado', true)->update(['estado' => 'contactado']);

        Schema::table('leads_contacto', function (Blueprint $table) {
            $table->dropColumn('contactado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_contacto', function (Blueprint $table) {
            $table->boolean('contactado')->default(false);
        });

        DB::table('leads_contacto')->where('estado', '!=', 'pendiente')->update(['contactado' => true]);

        Schema::table('leads_contacto', function (Blueprint $table) {
            $table->dropColumn(['estado', 'observaciones']);
        });
    }
};
