<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->foreignId('punto_interes_id')->nullable()->after('id')
                  ->constrained('puntosinteres')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('punto_interes_id');
        });
    }
};
