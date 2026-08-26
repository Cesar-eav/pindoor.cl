<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticketera_reservas', function (Blueprint $table) {
            $table->boolean('es_prueba')->default(false)->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('ticketera_reservas', function (Blueprint $table) {
            $table->dropColumn('es_prueba');
        });
    }
};
