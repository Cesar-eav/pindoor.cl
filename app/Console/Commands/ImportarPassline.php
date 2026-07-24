<?php

namespace App\Console\Commands;

use App\Models\Panorama;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportarPassline extends Command
{
    protected $signature = 'panoramas:importar-passline
                            {--dry-run : Mostrar resultados sin guardar}';

    protected $description = 'Importa eventos de Valparaíso desde la API de Passline';

    const API_URL = 'https://api.passline.com/v1/event/GetBillboardByFilters';

    const CATEGORIA_MAP = [
        'Música'      => 'musica',
        'Music'       => 'musica',
        'Teatro'      => 'teatro',
        'Theatre'     => 'teatro',
        'Arte'        => 'arte',
        'Arts'        => 'arte',
        'Cine'        => 'cine',
        'Danza'       => 'danza',
        'Dance'       => 'danza',
        'Comedia'     => 'standup',
        'Comedy'      => 'standup',
        'Literatura'  => 'literatura',
        'Feria'       => 'feria',
        'Infantil'    => 'infantil',
        'Gastronomía' => 'gastronomia',
        'Conferencia' => 'conferencia',
    ];

    public function handle(): int
    {
        $cfClearance = config('services.passline.cf_clearance');

        if (!$cfClearance) {
            $this->error('Falta PASSLINE_CF_CLEARANCE en .env');
            return Command::FAILURE;
        }

        $dryRun = $this->option('dry-run');

        $this->line('Consultando API de Passline...');

        $response = Http::withHeaders([
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'Origin'       => 'https://home.passline.com',
            'Referer'      => 'https://home.passline.com/',
            'User-Agent'   => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:152.0) Gecko/20100101 Firefox/152.0',
            'Cookie'       => "cf_clearance={$cfClearance}",
        ])->post(self::API_URL, [
            'country'           => 'chile',
            'region'            => null,
            'commune'           => '',
            'communeNum'        => null,
            'type'              => 0,
            'start_date'        => '',
            'end_date'          => '',
            'text'              => 'valparaiso',
            'tag_id'            => null,
            'tag'               => null,
            'upper_category_id' => null,
            'limit'             => '0,300',
            'offset'            => '1',
        ]);

        if (!$response->successful()) {
            $this->error("Error API (HTTP {$response->status()})");
            if ($response->status() === 403) {
                $this->warn('Cookie expirada. Actualiza PASSLINE_CF_CLEARANCE en .env');
            }
            return Command::FAILURE;
        }

        $eventos = $response->json();

        if (!is_array($eventos)) {
            $this->error('Respuesta inesperada de la API.');
            return Command::FAILURE;
        }

        $this->line('Eventos encontrados: ' . count($eventos));

        $creados = $actualizados = $omitidos = 0;

        foreach ($eventos as $ev) {
            $fecha = Carbon::parse($ev['fecha_inicio']);

            if ($fecha->isPast()) {
                $omitidos++;
                continue;
            }

            $hora     = substr($ev['hora_inicio'] ?? '', 0, 5) ?: null;
            $categoria = self::CATEGORIA_MAP[$ev['upper_category_name'] ?? ''] ?? 'otros';

            if ($dryRun) {
                $this->line("  [{$categoria}] {$ev['nombre']} — {$fecha->toDateString()} {$hora} — {$ev['lugar']}");
                $creados++;
                continue;
            }

            $precio  = (float)($ev['precio_min'] ?? 0);
            $fechaEs = $fecha->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
            $fechaEn = $fecha->locale('en')->isoFormat('dddd, MMMM D, YYYY');

            $es = "{$ev['nombre']} en {$ev['lugar']}.\n{$fechaEs}";
            if ($hora) $es .= " a las {$hora} hrs.";
            if (!empty($ev['artistas'])) $es .= "\n{$ev['artistas']}";
            $es .= $precio > 0 ? "\nEntrada desde $" . number_format($precio, 0, ',', '.') . " CLP." : "\nEntrada liberada.";

            $en = "{$ev['nombre']} at {$ev['lugar']}.\n{$fechaEn}";
            if ($hora) $en .= " at {$hora} hrs.";
            if (!empty($ev['artistas'])) $en .= "\n{$ev['artistas']}";
            $en .= $precio > 0 ? "\nTickets from $" . number_format($precio, 0, ',', '.') . " CLP." : "\nFree entry.";

            $desc = ['es' => $es, 'en' => $en];

            $datos = [
                'titulo'      => $ev['nombre'],
                'fecha'       => $fecha->toDateString(),
                // fecha_termino de Passline no indica evento multi-día: es solo la hora de
                // cierre cruzando medianoche, así que no la usamos como fecha_fin.
                'fecha_fin'   => null,
                'hora'        => $hora,
                'ubicacion'   => $ev['lugar'],
                'descripcion' => ['es' => $es, 'en' => $en],
                'imagen'      => $this->descargarImagen($ev['recorte'] ?? null, $ev['id']),
                'enlace'      => $ev['url'],
                'es_gratuito' => (float) ($ev['precio_min'] ?? 1) === 0.0,
                'categoria'   => $categoria,
                'activo'      => true,
            ];

            $panorama = Panorama::updateOrCreate(
                ['fuente' => 'passline', 'fuente_id' => $ev['id']],
                $datos
            );

            $panorama->wasRecentlyCreated ? $creados++ : $actualizados++;
        }

        $this->newLine();
        $this->table(
            ['Creados', 'Actualizados', 'Omitidos'],
            [[$creados, $actualizados, $omitidos]]
        );

        if ($dryRun) {
            $this->warn('Modo dry-run: ningún cambio guardado.');
        }

        return Command::SUCCESS;
    }

    private function descargarImagen(?string $url, string $id): ?string
    {
        if (!$url) return null;

        $ruta = "panoramas/passline/{$id}.jpg";
        $destino = storage_path("app/public/{$ruta}");

        if (file_exists($destino)) return $ruta;

        try {
            @mkdir(dirname($destino), 0755, true);
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                file_put_contents($destino, $response->body());
                return $ruta;
            }
        } catch (\Exception $e) {}

        return null;
    }
}
