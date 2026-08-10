<?php

namespace App\Console\Commands;

use App\Models\Artista;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TraducirArtistas extends Command
{
    protected $signature   = 'artistas:traducir {--id= : ID del artista específico} {--force : Sobreescribir traducciones existentes} {--to=en : Idioma destino (en|fr)}';
    protected $description = 'Traduce artistas de La Escena (y sus eventos) de ES a EN/FR usando MyMemory API';

    private const API   = 'https://api.mymemory.translated.net/get';
    private const EMAIL = 'cesar.eav@gmail.com';

    public function handle(): int
    {
        $to = $this->option('to');
        if (!in_array($to, ['en', 'fr'])) {
            $this->error('--to debe ser "en" o "fr".');
            return self::FAILURE;
        }

        $query = Artista::with('eventos');

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $artistas = $query->get();
        $force    = $this->option('force');

        foreach ($artistas as $artista) {
            $nombreEs      = $artista->getTranslation('nombre', 'es', false);
            $descripcionEs = $artista->getTranslation('descripcion', 'es', false);

            $nombreDestino      = $artista->getTranslation('nombre', $to, false);
            $descripcionDestino = $artista->getTranslation('descripcion', $to, false);

            $this->info("Artista #{$artista->id}: {$nombreEs}");

            if ($force || !$nombreDestino) {
                $nombreDestino = $this->traducir($nombreEs, $to);
                $this->line("  nombre: {$nombreDestino}");
            }

            if ($descripcionEs && ($force || !$descripcionDestino)) {
                $descripcionDestino = $this->traducir($descripcionEs, $to);
                $this->line('  descripción: OK');
            }

            $artista->setTranslation('nombre',      $to, $nombreDestino)
                    ->setTranslation('descripcion', $to, $descripcionDestino)
                    ->save();

            $this->info("  ✓ Guardado");

            foreach ($artista->eventos as $evento) {
                $tituloEs        = $evento->getTranslation('titulo', 'es', false);
                $descEventoEs    = $evento->getTranslation('descripcion', 'es', false);
                $tituloDestino   = $evento->getTranslation('titulo', $to, false);
                $descEventoDest  = $evento->getTranslation('descripcion', $to, false);

                if (!$force && $tituloDestino) {
                    continue;
                }

                $this->line("  Evento #{$evento->id}: {$tituloEs}");

                if ($force || !$tituloDestino) {
                    $tituloDestino = $this->traducir($tituloEs, $to);
                    $this->line("    título: {$tituloDestino}");
                }
                if ($descEventoEs && ($force || !$descEventoDest)) {
                    $descEventoDest = $this->traducir($descEventoEs, $to);
                    $this->line('    descripción: OK');
                }

                $evento->setTranslation('titulo',      $to, $tituloDestino)
                       ->setTranslation('descripcion', $to, $descEventoDest)
                       ->save();
            }
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
