<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImagenComprimida
{
    /**
     * Comprime y guarda una imagen subida: redimensiona si supera $maxWidth,
     * reconvierte a WebP en $calidad y la guarda en storage/public/$carpeta
     * con nombre aleatorio. Usado por todos los formularios que suben imágenes.
     */
    public static function guardar(UploadedFile $archivo, string $carpeta, int $maxWidth = 1600, int $calidad = 80): string
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('El servidor no tiene la extensión GD de PHP habilitada, no se pueden procesar imágenes.');
        }

        $img = imagecreatefromstring(file_get_contents($archivo->getPathname()));

        if ($img === false) {
            // GD no pudo decodificar el archivo — típico con fotos HEIC de iPhone,
            // que "pasan" la validación 'image' de Laravel pero GD no las soporta.
            throw new \RuntimeException('No pudimos procesar esa imagen. Prueba con un formato JPG o PNG.');
        }

        $w = imagesx($img);
        $h = imagesy($img);

        if ($w > $maxWidth) {
            $nuevoH = (int) round($h * $maxWidth / $w);
            $redim  = imagecreatetruecolor($maxWidth, $nuevoH);
            imagealphablending($redim, false);
            imagesavealpha($redim, true);
            imagecopyresampled($redim, $img, 0, 0, 0, 0, $maxWidth, $nuevoH, $w, $h);
            imagedestroy($img);
            $img = $redim;
        }

        ob_start();
        imagewebp($img, null, $calidad);
        $webp = ob_get_clean();
        imagedestroy($img);

        $ruta = $carpeta . '/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($ruta, $webp);

        return $ruta;
    }
}
