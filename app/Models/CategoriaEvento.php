<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaEvento extends Model
{
    protected $fillable = ['nombre', 'slug', 'emoji', 'orden'];

    protected static ?array $cacheCatalogo = null;

    /**
     * Catálogo completo en la forma ['slug' => ['label' => .., 'emoji' => ..]],
     * la misma que usaban las antiguas Panorama::CATEGORIAS / ModuloItem::catalogoTiposEvento().
     * Memoizado por request para no repetir la query dentro de foreach.
     */
    public static function catalogo(): array
    {
        if (static::$cacheCatalogo === null) {
            static::$cacheCatalogo = static::orderBy('orden')->orderBy('nombre')
                ->get()
                ->mapWithKeys(fn ($c) => [$c->slug => ['label' => $c->nombre, 'emoji' => $c->emoji]])
                ->all();
        }

        return static::$cacheCatalogo;
    }

    public static function slugs(): array
    {
        return array_keys(static::catalogo());
    }

    public function estaEnUso(): bool
    {
        return Panorama::where('categoria', $this->slug)->exists()
            || ModuloItem::where('modulo', 'eventos')->where('datos->tipo', $this->slug)->exists();
    }

    public function contarUsos(): int
    {
        return Panorama::where('categoria', $this->slug)->count()
            + ModuloItem::where('modulo', 'eventos')->where('datos->tipo', $this->slug)->count();
    }
}
