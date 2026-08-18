<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE recomendaciones ADD COLUMN titulo_new JSON NULL AFTER titulo");
        DB::statement("UPDATE recomendaciones SET titulo_new = JSON_OBJECT('es', titulo)");
        DB::statement("ALTER TABLE recomendaciones DROP COLUMN titulo");
        DB::statement("ALTER TABLE recomendaciones RENAME COLUMN titulo_new TO titulo");

        DB::statement("ALTER TABLE recomendaciones ADD COLUMN resumen_new JSON NULL AFTER resumen");
        DB::statement("UPDATE recomendaciones SET resumen_new = JSON_OBJECT('es', resumen) WHERE resumen IS NOT NULL");
        DB::statement("ALTER TABLE recomendaciones DROP COLUMN resumen");
        DB::statement("ALTER TABLE recomendaciones RENAME COLUMN resumen_new TO resumen");

        DB::statement("ALTER TABLE recomendaciones ADD COLUMN contenido_new JSON NULL AFTER contenido");
        DB::statement("UPDATE recomendaciones SET contenido_new = JSON_OBJECT('es', contenido) WHERE contenido IS NOT NULL");
        DB::statement("ALTER TABLE recomendaciones DROP COLUMN contenido");
        DB::statement("ALTER TABLE recomendaciones RENAME COLUMN contenido_new TO contenido");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE recomendaciones ADD COLUMN titulo_bak VARCHAR(255) NULL");
        DB::statement("UPDATE recomendaciones SET titulo_bak = JSON_UNQUOTE(JSON_EXTRACT(titulo, '$.es'))");
        DB::statement("ALTER TABLE recomendaciones DROP COLUMN titulo");
        DB::statement("ALTER TABLE recomendaciones RENAME COLUMN titulo_bak TO titulo");

        DB::statement("ALTER TABLE recomendaciones ADD COLUMN resumen_bak VARCHAR(600) NULL");
        DB::statement("UPDATE recomendaciones SET resumen_bak = JSON_UNQUOTE(JSON_EXTRACT(resumen, '$.es'))");
        DB::statement("ALTER TABLE recomendaciones DROP COLUMN resumen");
        DB::statement("ALTER TABLE recomendaciones RENAME COLUMN resumen_bak TO resumen");

        DB::statement("ALTER TABLE recomendaciones ADD COLUMN contenido_bak TEXT NULL");
        DB::statement("UPDATE recomendaciones SET contenido_bak = JSON_UNQUOTE(JSON_EXTRACT(contenido, '$.es'))");
        DB::statement("ALTER TABLE recomendaciones DROP COLUMN contenido");
        DB::statement("ALTER TABLE recomendaciones RENAME COLUMN contenido_bak TO contenido");
    }
};
