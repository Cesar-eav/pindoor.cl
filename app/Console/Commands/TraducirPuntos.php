<?php

namespace App\Console\Commands;

use App\Models\PuntoInteres;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TraducirPuntos extends Command
{
    protected $signature   = 'puntos:traducir {--id= : ID del punto específico} {--force : Sobreescribir traducciones existentes} {--to=en : Idioma destino (en|fr)}';
    protected $description = 'Traduce atractivos (PuntoInteres) de ES a EN/FR usando MyMemory API';

    private const API   = 'https://api.mymemory.translated.net/get';
    private const EMAIL = 'cesar.eav@gmail.com';

    public function handle(): int
    {
        $to = $this->option('to');
        if (!in_array($to, ['en', 'fr'])) {
            $this->error('--to debe ser "en" o "fr".');
            return self::FAILURE;
        }

        $query = PuntoInteres::where('eliminado', false);

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $puntos = $query->get();

        foreach ($puntos as $punto) {
            $titleEs       = $punto->getTranslation('title', 'es', false);
            $descriptionEs = $punto->getTranslation('description', 'es', false);

            $titleDestino       = $punto->getTranslation('title', $to, false);
            $descriptionDestino = $punto->getTranslation('description', $to, false);

            $force = $this->option('force');

            $this->info("Punto #{$punto->id}: {$titleEs}");

            if (!$force && $titleDestino && $descriptionDestino) {
                $this->line("  → Ya tiene traducción {$to}. Usa --force para sobreescribir.");
                continue;
            }

            if ($force || !$titleDestino) {
                $titleDestino = $this->traducir($titleEs, $to);
                $this->line("  título: {$titleDestino}");
            }

            if ($force || !$descriptionDestino) {
                $descriptionDestino = $this->traducirHtml($descriptionEs, $to);
                $this->line('  descripción: OK');
            }

            $punto->setTranslation('title',       $to, $titleDestino)
                  ->setTranslation('description', $to, $descriptionDestino)
                  ->save();

            $this->info("  ✓ Guardado");
        }

        return self::SUCCESS;
    }

    private function traducirHtml(string $html, string $to): string
    {
        $texto = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $texto = preg_replace('/\s+/', ' ', trim($texto));

        if ($texto === '') {
            return '';
        }

        $traducido = $this->traducirTextoLargo($texto, $to);

        $parrafos = array_filter(array_map('trim', explode("\n", $traducido)));
        if (empty($parrafos)) {
            return "<p>{$traducido}</p>";
        }

        return implode('', array_map(fn($p) => "<p>{$p}</p>", $parrafos));
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
