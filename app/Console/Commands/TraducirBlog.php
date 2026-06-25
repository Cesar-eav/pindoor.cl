<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TraducirBlog extends Command
{
    protected $signature   = 'blog:traducir {--id= : ID del post específico} {--force : Sobreescribir traducciones existentes}';
    protected $description = 'Traduce posts del blog de ES a EN usando MyMemory API';

    private const API  = 'https://api.mymemory.translated.net/get';
    private const EMAIL = 'cesar.eav@gmail.com';

    public function handle(): int
    {
        $query = Post::query();

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $posts = $query->get();

        foreach ($posts as $post) {
            $tituloEs   = $post->getTranslation('titulo', 'es', false);
            $resumenEs  = $post->getTranslation('resumen', 'es', false);
            $contenidoEs = $post->getTranslation('contenido', 'es', false);

            $tituloEn   = $post->getTranslation('titulo', 'en', false);
            $resumenEn  = $post->getTranslation('resumen', 'en', false);
            $contenidoEn = $post->getTranslation('contenido', 'en', false);

            $force = $this->option('force');

            $this->info("Post #{$post->id}: {$tituloEs}");

            if (!$force && $tituloEn && $contenidoEn) {
                $this->line('  → Ya tiene traducción EN. Usa --force para sobreescribir.');
                continue;
            }

            // Título
            if ($force || !$tituloEn) {
                $tituloEn = $this->traducir($tituloEs);
                $this->line("  título: {$tituloEn}");
            }

            // Resumen
            if ($force || !$resumenEn) {
                $resumenEn = $this->traducirTextoLargo(strip_tags($resumenEs));
                $this->line('  resumen: OK');
            }

            // Contenido (strip HTML, traducir, reconstruir como párrafos)
            if ($force || !$contenidoEn) {
                $contenidoEn = $this->traducirHtml($contenidoEs);
                $this->line('  contenido: OK');
            }

            $post->setTranslation('titulo',    'en', $tituloEn)
                 ->setTranslation('resumen',   'en', $resumenEn)
                 ->setTranslation('contenido', 'en', $contenidoEn)
                 ->save();

            $this->info("  ✓ Guardado");
        }

        return self::SUCCESS;
    }

    private function traducirHtml(string $html): string
    {
        // Extraer párrafos/bloques de texto sin HTML
        $texto = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $texto = preg_replace('/\s+/', ' ', trim($texto));

        $traducido = $this->traducirTextoLargo($texto);

        // Reconstruir como párrafos HTML
        $parrafos = array_filter(array_map('trim', explode("\n", $traducido)));
        if (empty($parrafos)) {
            return "<p>{$traducido}</p>";
        }

        return implode('', array_map(fn($p) => "<p>{$p}</p>", $parrafos));
    }

    private function traducirTextoLargo(string $texto): string
    {
        $chunks = $this->dividirEnChunks($texto, 400);
        $partes = [];

        foreach ($chunks as $i => $chunk) {
            if ($i > 0) {
                usleep(600_000); // 600ms entre llamadas
            }
            $partes[] = $this->traducir($chunk);
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

    private function traducir(string $texto): string
    {
        if (blank($texto)) {
            return '';
        }

        $response = Http::timeout(15)->get(self::API, [
            'q'        => $texto,
            'langpair' => 'es|en',
            'de'       => self::EMAIL,
        ]);

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
}
