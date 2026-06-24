<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->string('created_by')->nullable()->after('fuente_id');
        });
    }

    public function down(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
