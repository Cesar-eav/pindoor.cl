<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiencias', function (Blueprint $table) {
            $table->string('estado', 20)->default('pendiente')->after('activo');
            $table->string('email_contacto', 200)->nullable()->after('whatsapp');
        });

        DB::table('experiencias')->update(['estado' => 'aprobada']);
    }

    public function down(): void
    {
        Schema::table('experiencias', function (Blueprint $table) {
            $table->dropColumn(['estado', 'email_contacto']);
        });
    }
};
