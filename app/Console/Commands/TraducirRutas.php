<?php

namespace App\Console\Commands;

use App\Models\Ruta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TraducirRutas extends Command
{
    protected $signature   = 'rutas:traducir {--id= : ID de la ruta específica} {--force : Sobreescribir traducciones existentes} {--to=en : Idioma destino (en|fr)}';
    protected $description = 'Traduce rutas Pindoor de ES a EN/FR usando MyMemory API';

    private const API   = 'https://api.mymemory.translated.net/get';
    private const EMAIL = 'cesar.eav@gmail.com';

    public function handle(): int
    {
        $to = $this->option('to');
        if (!in_array($to, ['en', 'fr'])) {
            $this->error('--to debe ser "en" o "fr".');
            return self::FAILURE;
        }

        $query = Ruta::query();

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $rutas = $query->get();

        foreach ($rutas as $ruta) {
            $tituloEs      = $ruta->getTranslation('titulo', 'es', false);
            $descripcionEs = $ruta->getTranslation('descripcion', 'es', false);

            $tituloDestino      = $ruta->getTranslation('titulo', $to, false);
            $descripcionDestino = $ruta->getTranslation('descripcion', $to, false);

            $force = $this->option('force');

            $this->info("Ruta #{$ruta->id}: {$tituloEs}");

            if (!$force && $tituloDestino) {
                $this->line("  → Ya tiene traducción {$to}. Usa --force para sobreescribir.");
                continue;
            }

            // Título
            if ($force || !$tituloDestino) {
                $tituloDestino = $this->traducir($tituloEs, $to);
                $this->line("  título: {$tituloDestino}");
            }

            // Descripción
            if ($force || !$descripcionDestino) {
                $descripcionDestino = $this->traducirTextoLargo($descripcionEs, $to);
                $this->line('  descripción: OK');
            }

            $ruta->setTranslation('titulo',      $to, $tituloDestino)
                 ->setTranslation('descripcion', $to, $descripcionDestino)
                 ->save();

            $this->info("  ✓ Guardado");
        }

        return self::SUCCESS;
    }

    private function traducirTextoLargo(string $texto, string $to): string
    {
        $chunks = $this->dividirEnChunks($texto, 400);
        $partes = [];

        foreach ($chunks as $i => $chunk) {
            if ($i > 0) {
                usleep(600_000); // 600ms entre llamadas
            }
            $partes[] = $this->traducir($chunk, $to);
        }

        return implode(' ', $partes);
    }

    private function dividirEnChunks(string $texto, int $maxLen): array
    {
        $oraciones = preg_split('/(?<=[.!?])\s+/', trim($texto), -1, PREG_SPLIT_NO_EMPTY);
        $chunks    = [];
        $actual    = '';

        foreach ($oraciones as $oracion) {
            if (strlen($actual) + strlen($oracion) + 1 > $maxLen && $actual !== '') {
                $chunks[] = $actual;
                $actual   = $oracion;
            } else {
                $actual = $actual === '' ? $oracion : "{$actual} {$oracion}";
            }
        }

        if ($actual !== '') {
            $chunks[] = $actual;
        }

        return $chunks ?: [$texto];
    }

    private function traducir(string $texto, string $to): string
    {
        if (blank($texto)) {
            return '';
        }

        usleep(300_000); // ritmo base entre llamadas, evita saturar el rate limit

        for ($intento = 1; $intento <= 4; $intento++) {
            $response = Http::timeout(15)->get(self::API, [
                'q'        => $texto,
                'langpair' => "es|{$to}",
                'de'       => self::EMAIL,
            ]);

            if ($response->status() === 429) {
                if ($intento === 4) {
                    $this->warn("  API error: 429 tras 3 reintentos — se deja el texto original");
                    return $texto;
                }
                $espera = 5 * $intento;
                $this->warn("  API error: 429 — reintentando en {$espera}s ({$intento}/3)");
                sleep($espera);
                continue;
            }

            if ($response->failed()) {
                $this->warn("  API error: " . $response->status());
                return $texto;
            }

            $data = $response->json();

            if (($data['responseStatus'] ?? null) !== 200) {
                $this->warn("  MyMemory: " . ($data['responseDetails'] ?? 'error desconocido'));
                return $texto;
            }

            return $data['responseData']['translatedText'] ?? $texto;
        }

        return $texto;
    }
}
