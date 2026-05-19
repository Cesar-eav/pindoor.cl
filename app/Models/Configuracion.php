<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table      = 'configuraciones';
    protected $primaryKey = 'clave';
    public    $incrementing = false;
    protected $keyType    = 'string';
    protected $fillable   = ['clave', 'valor'];

    public static function get(string $clave, $default = null): mixed
    {
        return static::find($clave)?->valor ?? $default;
    }

    public static function set(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
}
