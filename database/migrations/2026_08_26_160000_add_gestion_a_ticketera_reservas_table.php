<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticketera_reservas', function (Blueprint $table) {
            $table->boolean('contactado')->default(false)->after('estado');
            $table->text('notas_admin')->nullable()->after('contactado');
        });
    }

    public function down(): void
    {
        Schema::table('ticketera_reservas', function (Blueprint $table) {
            $table->dropColumn(['contactado', 'notas_admin']);
        });
    }
};
