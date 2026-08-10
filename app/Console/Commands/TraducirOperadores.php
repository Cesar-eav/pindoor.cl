<?php

namespace App\Console\Commands;

use App\Models\OperadorTuristico;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TraducirOperadores extends Command
{
    protected $signature   = 'operadores:traducir {--id= : ID del operador específico} {--force : Sobreescribir traducciones existentes} {--to=en : Idioma destino (en|fr)}';
    protected $description = 'Traduce operadores turísticos de ES a EN/FR usando MyMemory API';

    private const API   = 'https://api.mymemory.translated.net/get';
    private const EMAIL = 'cesar.eav@gmail.com';

    public function handle(): int
    {
        $to = $this->option('to');
        if (!in_array($to, ['en', 'fr'])) {
            $this->error('--to debe ser "en" o "fr".');
            return self::FAILURE;
        }

        $query = OperadorTuristico::query();

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $operadores = $query->get();
        $force      = $this->option('force');

        foreach ($operadores as $operador) {
            $nombreEs      = $operador->getTranslation('nombre', 'es', false);
            $descripcionEs = $operador->getTranslation('descripcion', 'es', false);

            $nombreDestino      = $operador->getTranslation('nombre', $to, false);
            $descripcionDestino = $operador->getTranslation('descripcion', $to, false);

            $this->info("Operador #{$operador->id}: {$nombreEs}");

            if (!$force && $nombreDestino) {
                $this->line("  → Ya tiene traducción {$to}. Usa --force para sobreescribir.");
                continue;
            }

            if ($force || !$nombreDestino) {
                $nombreDestino = $this->traducir($nombreEs, $to);
                $this->line("  nombre: {$nombreDestino}");
            }

            if ($descripcionEs && ($force || !$descripcionDestino)) {
                $descripcionDestino = $this->traducir($descripcionEs, $to);
                $this->line('  descripción: OK');
            }

            $operador->setTranslation('nombre',      $to, $nombreDestino)
                     ->setTranslation('descripcion', $to, $descripcionDestino)
                     ->save();

            $this->info("  ✓ Guardado");
        }

        return self::SUCCESS;
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
