<?php

namespace App\Models;

use App\Mail\ReservaConfirmada;
use App\Notifications\ReservaPagada;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ReservaRuta extends Model
{
    protected $table = 'ticketera_reservas';

    public const ESTADOS = ['pendiente', 'pagada', 'rechazada', 'anulada', 'expirada'];

    public const ESTADOS_INFO = [
        'pendiente' => ['label' => 'Pendiente', 'color' => 'amber'],
        'pagada'    => ['label' => 'Pagada',    'color' => 'green'],
        'rechazada' => ['label' => 'Rechazada', 'color' => 'red'],
        'anulada'   => ['label' => 'Anulada',   'color' => 'gray'],
        'expirada'  => ['label' => 'Expirada',  'color' => 'gray'],
    ];

    protected $fillable = [
        'ruta_operador_turistico_id',
        'ruta_operador_horario_id',
        'fecha_visita',
        'nombre_cliente',
        'email_cliente',
        'telefono_cliente',
        'cantidad_adultos',
        'cantidad_ninos',
        'precio_unitario_adulto',
        'precio_unitario_nino',
        'precio_total',
        'codigo_reserva',
        'commerce_order',
        'flow_token',
        'flow_order',
        'estado',
        'es_prueba',
        'contactado',
        'notas_admin',
        'pagado_en',
        'expira_en',
        'payload_flow',
        'ip_cliente',
    ];

    protected $casts = [
        'fecha_visita' => 'date',
        'contactado'   => 'boolean',
        'es_prueba'    => 'boolean',
        'pagado_en'    => 'datetime',
        'expira_en'    => 'datetime',
        'payload_flow' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $reserva) {
            if (!$reserva->codigo_reserva) {
                $reserva->codigo_reserva = static::generarCodigo();
            }
            if (!$reserva->commerce_order) {
                $reserva->commerce_order = static::generarCommerceOrder();
            }
        });
    }

    public static function generarCodigo(): string
    {
        do {
            $codigo = 'PIN-' . strtoupper(Str::random(6));
        } while (static::where('codigo_reserva', $codigo)->exists());

        return $codigo;
    }

    public static function generarCommerceOrder(): string
    {
        return 'PIN-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
    }

    public function rutaOperador()
    {
        return $this->belongsTo(RutaOperador::class, 'ruta_operador_turistico_id');
    }

    public function horario()
    {
        return $this->belongsTo(RutaOperadorHorario::class, 'ruta_operador_horario_id');
    }

    public function estaVigente(): bool
    {
        return $this->estado === 'pagada'
            || ($this->estado === 'pendiente' && $this->expira_en->isFuture());
    }

    public function estadoInfo(): array
    {
        return self::ESTADOS_INFO[$this->estado] ?? self::ESTADOS_INFO['pendiente'];
    }

    /**
     * Aviso al cliente + aviso admin (mail/Telegram) de que el pago quedó confirmado.
     * Llamado tanto desde el webhook de Flow como desde el retorno del navegador,
     * por eso vive acá: evita duplicar el envío y sus try/catch en ambos controladores.
     */
    public function notificarPagada(): void
    {
        try {
            Mail::to($this->email_cliente)->send(new ReservaConfirmada($this));
        } catch (\Throwable $e) {
            Log::error('ReservaRuta::notificarPagada — falló el mail al cliente', [
                'reserva' => $this->codigo_reserva,
                'error'   => $e->getMessage(),
            ]);
        }

        try {
            Notification::route('mail', Configuracion::emailsNotificacion())
                ->notify(new ReservaPagada($this));
        } catch (\Throwable $e) {
            Log::error('ReservaRuta::notificarPagada — falló el aviso admin', [
                'reserva' => $this->codigo_reserva,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
