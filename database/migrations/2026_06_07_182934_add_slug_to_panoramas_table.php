<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('titulo');
        });

        DB::table('panoramas')->get()->each(function ($p) {
            DB::table('panoramas')->where('id', $p->id)->update([
                'slug' => Str::slug($p->titulo) . '-' . $p->id,
            ]);
        });

        Schema::table('panoramas', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
