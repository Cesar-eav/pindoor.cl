<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convierte columnas de texto plano a JSON por idioma ({"es": "<valor actual>"})
     * antes de que los modelos empiecen a usar HasTranslations sobre ellas — si no,
     * getTranslation()/json_decode() sobre un string no-JSON existente rompe el dato.
     */
    private const TABLAS = [
        'artistas'              => ['nombre', 'descripcion'],
        'artista_eventos'       => ['titulo', 'descripcion'],
        'operadores_turisticos' => ['nombre', 'descripcion'],
    ];

    public function up(): void
    {
        foreach (self::TABLAS as $tabla => $columnas) {
            DB::table($tabla)->orderBy('id')->get()->each(function ($fila) use ($tabla, $columnas) {
                $update = [];
                foreach ($columnas as $columna) {
                    $valor = $fila->$columna;
                    if ($valor === null || $this->esJson($valor)) {
                        continue; // ya migrado o nulo: no tocar
                    }
                    $update[$columna] = json_encode(['es' => $valor], JSON_UNESCAPED_UNICODE);
                }
                if ($update) {
                    DB::table($tabla)->where('id', $fila->id)->update($update);
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLAS as $tabla => $columnas) {
            DB::table($tabla)->orderBy('id')->get()->each(function ($fila) use ($tabla, $columnas) {
                $update = [];
                foreach ($columnas as $columna) {
                    $valor = $fila->$columna;
                    if ($valor === null || !$this->esJson($valor)) {
                        continue;
                    }
                    $decoded = json_decode($valor, true);
                    $update[$columna] = $decoded['es'] ?? '';
                }
                if ($update) {
                    DB::table($tabla)->where('id', $fila->id)->update($update);
                }
            });
        }
    }

    private function esJson(string $valor): bool
    {
        if ($valor === '' || $valor[0] !== '{') {
            return false;
        }
        json_decode($valor);
        return json_last_error() === JSON_ERROR_NONE;
    }
};
