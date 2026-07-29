<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DiagnosticarPublicacion extends Command
{
    protected $signature   = 'negocio:diagnosticar';
    protected $description = 'Chequea el entorno (GD, storage, límites de PHP) y muestra errores recientes de publicación/imágenes';

    public function handle(): int
    {
        $this->info('=== Diagnóstico: publicación de negocio / imágenes ===');
        $this->newLine();

        $ok = true;

        // ── GD ──────────────────────────────────────────────────────────────
        if (extension_loaded('gd')) {
            $this->info('✓ Extensión GD: habilitada');
            $info = gd_info();
            $webp = $info['WebP Support'] ?? false;
            $this->line('  Soporte WebP: ' . ($webp ? '✓ sí' : '✗ NO — imagewebp() fallará al guardar fotos'));
            if (!$webp) {
                $ok = false;
            }
        } else {
            $this->error('✗ Extensión GD: NO habilitada — ninguna subida de imagen va a funcionar');
            $this->line('  Fix: instalar/activar php-gd y reiniciar el servidor web.');
            $ok = false;
        }

        $this->newLine();

        // ── Storage ─────────────────────────────────────────────────────────
        $rutaPublica = storage_path('app/public');
        if (is_dir($rutaPublica) && is_writable($rutaPublica)) {
            $this->info('✓ storage/app/public: existe y tiene permisos de escritura');
        } else {
            $this->error('✗ storage/app/public: no existe o no se puede escribir ahí');
            $ok = false;
        }

        $symlink = public_path('storage');
        if (is_link($symlink) || is_dir($symlink)) {
            $this->info('✓ Symlink public/storage: existe');
        } else {
            $this->error('✗ Symlink public/storage: falta — correr "php artisan storage:link"');
            $ok = false;
        }

        try {
            $prueba = 'diagnostico_' . time() . '.txt';
            Storage::disk('public')->put($prueba, 'ok');
            Storage::disk('public')->delete($prueba);
            $this->info('✓ Escritura de prueba en disco "public": OK');
        } catch (\Throwable $e) {
            $this->error('✗ No se pudo escribir en el disco "public": ' . $e->getMessage());
            $ok = false;
        }

        $this->newLine();

        // ── Límites de PHP ──────────────────────────────────────────────────
        $this->info('Límites de PHP actuales:');
        $this->line('  upload_max_filesize: ' . ini_get('upload_max_filesize') . ' (validación pide hasta 10MB por foto)');
        $this->line('  post_max_size:       ' . ini_get('post_max_size'));
        $this->line('  memory_limit:        ' . ini_get('memory_limit') . ' (fotos grandes pueden necesitar bastante memoria al procesarse)');

        $this->newLine();

        // ── Errores recientes relacionados ──────────────────────────────────
        $log = storage_path('logs/laravel.log');
        if (is_file($log)) {
            $lineas = collect(file($log))
                ->filter(fn ($l) => preg_match('/\[(onboarding|galeria|logo)\].*(ERROR|WARNING)/i', $l)
                    || str_contains($l, '[onboarding]') || str_contains($l, '[galeria]') || str_contains($l, '[logo]'))
                ->slice(-15);

            if ($lineas->isEmpty()) {
                $this->info('Sin entradas recientes de publicación/imágenes en el log.');
            } else {
                $this->info('Últimas entradas relacionadas en laravel.log:');
                foreach ($lineas as $linea) {
                    $this->line('  ' . trim($linea));
                }
            }
        } else {
            $this->warn('No se encontró storage/logs/laravel.log.');
        }

        $this->newLine();
        $this->info($ok ? '=== Todo en orden ===' : '=== Hay problemas que revisar arriba ===');

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
