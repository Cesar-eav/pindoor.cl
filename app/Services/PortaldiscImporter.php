<?php

namespace App\Services;

use App\Models\Panorama;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PortaldiscImporter
{
    const LISTADO_URL = 'https://www.portaldisc.com/lista_eventos';
    const DETALLE_URL  = 'https://www.portaldisc.com/evento/';
    const COMUNA       = 'Valparaíso';

    const MESES = [
        'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
        'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
        'septiembre' => 9, 'setiembre' => 9, 'octubre' => 10,
        'noviembre' => 11, 'diciembre' => 12,
    ];

    public function importar(bool $dryRun = false): array
    {
        set_time_limit(0);

        $inicio = microtime(true);
        $creados = $actualizados = $omitidos = [];

        $candidatos = $this->listarCandidatos();
        Log::info('[Portaldisc] listado obtenido', [
            'candidatos' => count($candidatos),
            'segundos'   => round(microtime(true) - $inicio, 1),
        ]);

        foreach ($candidatos as $i => $candidato) {
            $tEvento = microtime(true);
            $detalle = $this->obtenerDetalle($candidato['slug']);
            usleep(300000);

            Log::info('[Portaldisc] evento procesado', [
                'progreso' => ($i + 1) . '/' . count($candidatos),
                'slug'     => $candidato['slug'],
                'segundos' => round(microtime(true) - $tEvento, 1),
                'total'    => round(microtime(true) - $inicio, 1),
            ]);

            if (!$detalle) {
                $omitidos[] = ['nombre' => $candidato['titulo'], 'fecha' => null, 'hora' => null, 'lugar' => $candidato['lugar']];
                continue;
            }

            $item = [
                'nombre' => $candidato['titulo'],
                'fecha'  => $detalle['fecha']->locale('es')->isoFormat('D MMM YYYY'),
                'hora'   => $detalle['hora'],
                'lugar'  => $candidato['lugar'],
            ];

            if ($detalle['fecha']->isPast()) {
                $omitidos[] = $item;
                continue;
            }

            if ($dryRun) {
                $creados[] = $item;
                continue;
            }

            $datos = [
                'titulo'      => $candidato['titulo'],
                'fecha'       => $detalle['fecha']->toDateString(),
                'fecha_fin'   => null,
                'hora'        => $detalle['hora'],
                'ubicacion'   => $candidato['lugar'],
                'descripcion' => $this->construirDescripcion($candidato, $detalle),
                'imagen'      => $this->descargarImagen($candidato['imagen_url'], $candidato['id']),
                'enlace'      => self::DETALLE_URL . $candidato['slug'],
                'es_gratuito' => $detalle['precio_min'] === null || $detalle['precio_min'] === 0,
                'categoria'   => 'musica',
                'activo'      => true,
            ];

            $panorama = Panorama::updateOrCreate(
                ['fuente' => 'portaldisc', 'fuente_id' => $candidato['id']],
                $datos
            );

            $item['slug']      = $panorama->slug;
            $item['categoria'] = $panorama->categoria;

            $panorama->wasRecentlyCreated ? ($creados[] = $item) : ($actualizados[] = $item);
        }

        Log::info('[Portaldisc] importación terminada', [
            'creados'      => count($creados),
            'actualizados' => count($actualizados),
            'omitidos'     => count($omitidos),
            'segundos'     => round(microtime(true) - $inicio, 1),
        ]);

        return compact('creados', 'actualizados', 'omitidos');
    }

    private function listarCandidatos(): array
    {
        $response = Http::timeout(20)->get(self::LISTADO_URL);

        if (!$response->successful()) {
            return [];
        }

        $xpath = $this->xpath($response->body());

        $candidatos = [];

        foreach ($xpath->query('//div[@class="info_responsivo"]/a[@href]') as $a) {
            $href = $a->getAttribute('href');
            if (!preg_match('#^/evento/([^/?]+)#', $href, $m)) continue;
            $slug = $m[1];

            $ps = $xpath->query('.//p', $a);
            if ($ps->length < 3) continue;

            $titulo     = trim($ps->item(0)->textContent);
            $lugarTexto = trim($ps->item(2)->textContent);

            $partes = array_map('trim', explode(',', $lugarTexto));
            $comuna = end($partes);
            if (mb_strtolower($comuna) !== mb_strtolower(self::COMUNA)) continue;

            $lugar = $partes[0] !== '' ? $partes[0] : $lugarTexto;

            $album   = $a->parentNode->parentNode;
            $imgNode = $xpath->query('.//div[contains(@class,"cover")]//img', $album)->item(0);
            $imgSrc  = $imgNode ? $imgNode->getAttribute('src') : null;

            if (!$imgSrc || !preg_match('#/eventos/(\d+)\.jpg#', $imgSrc, $im)) continue;

            $candidatos[$im[1]] = [
                'id'         => $im[1],
                'slug'       => $slug,
                'titulo'     => $titulo,
                'lugar'      => $lugar,
                'imagen_url' => $imgSrc,
            ];
        }

        return array_values($candidatos);
    }

    private function obtenerDetalle(string $slug): ?array
    {
        $response = Http::timeout(20)->get(self::DETALLE_URL . $slug);
        if (!$response->successful()) return null;

        $xpath = $this->xpath($response->body());

        $fechaNodo = $xpath->query('//li[h5[contains(text(),"Fecha:")]]/p')->item(0);
        if (!$fechaNodo) return null;

        $fecha = $this->parsearFecha(trim($fechaNodo->textContent));
        if (!$fecha) return null;

        $artistas = [];
        foreach ($xpath->query('//li[h5[contains(text(),"ARTISTAS Y TAGS")]]/p') as $p) {
            $artistas[] = trim($p->textContent);
        }

        $preciosTexto = '';
        $ticketsNodo = $xpath->query('//div[@id="tickets_compra"]')->item(0);
        if ($ticketsNodo) $preciosTexto = $ticketsNodo->textContent;

        preg_match_all('/\$([\d.]+)/', $preciosTexto, $pm);
        $precios   = array_map(fn($v) => (int) str_replace('.', '', $v), $pm[1] ?? []);
        $precioMin = $precios ? min($precios) : null;

        return [
            'fecha'      => $fecha['carbon'],
            'hora'       => $fecha['hora'],
            'artistas'   => implode(', ', $artistas),
            'precio_min' => $precioMin,
        ];
    }

    private function parsearFecha(string $texto): ?array
    {
        if (!preg_match('/(\d{1,2})\s+de\s+(\p{L}+)\s+(?:de\s+)?(\d{4})(?:,\s*(\d{1,2}:\d{2}))?/ui', $texto, $m)) {
            return null;
        }

        $mes = self::MESES[mb_strtolower($m[2])] ?? null;
        if (!$mes) return null;

        $hora   = $m[4] ?? null;
        $carbon = Carbon::createFromDate((int) $m[3], $mes, (int) $m[1]);

        if ($hora) {
            [$h, $min] = explode(':', $hora);
            $carbon->setTime((int) $h, (int) $min);
        }

        return ['carbon' => $carbon, 'hora' => $hora];
    }

    private function construirDescripcion(array $candidato, array $detalle): array
    {
        $fecha  = $detalle['fecha'];
        $hora   = $detalle['hora'];
        $precio = $detalle['precio_min'];

        $fechaEs = $fecha->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
        $fechaEn = $fecha->locale('en')->isoFormat('dddd, MMMM D, YYYY');

        $es = "{$candidato['titulo']} en {$candidato['lugar']}.\n{$fechaEs}";
        if ($hora) $es .= " a las {$hora} hrs.";
        if ($detalle['artistas']) $es .= "\n{$detalle['artistas']}";
        $es .= $precio ? "\nEntrada desde $" . number_format($precio, 0, ',', '.') . " CLP." : "\nEntrada liberada.";

        $en = "{$candidato['titulo']} at {$candidato['lugar']}.\n{$fechaEn}";
        if ($hora) $en .= " at {$hora} hrs.";
        if ($detalle['artistas']) $en .= "\n{$detalle['artistas']}";
        $en .= $precio ? "\nTickets from $" . number_format($precio, 0, ',', '.') . " CLP." : "\nFree entry.";

        return ['es' => $es, 'en' => $en];
    }

    private function descargarImagen(?string $url, string $id): ?string
    {
        if (!$url) return null;

        $ruta    = "panoramas/portaldisc/{$id}.jpg";
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

    private function xpath(string $html): \DOMXPath
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        return new \DOMXPath($dom);
    }
}
