<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticketera_reservas', function (Blueprint $table) {
            $table->timestamp('checkin_at')->nullable()->after('pagado_en');
            $table->foreignId('checkin_by')->nullable()->after('checkin_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticketera_reservas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checkin_by');
            $table->dropColumn('checkin_at');
        });
    }
};
