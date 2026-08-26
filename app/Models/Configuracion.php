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

    public static function emailsNotificacion(): array
    {
        $default = 'cesar.eav@gmail.com,danielapazcabrera89@gmail.com,soporte@pindoor.cl,cesarandrade@pindoor.cl';

        return collect(explode(',', static::get('notificaciones_emails', $default)))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->values()
            ->all();
    }

    public static function telegramChatId(): ?string
    {
        return static::get('notificaciones_telegram_chat_id') ?: config('services.telegram.chat_id');
    }
}
