<?php

namespace App\Models;

use App\Mail\ReservaConfirmada;
use App\Notifications\ReservaPagada;
use App\Notifications\ReservaRechazada;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ReservaRuta extends Model
{
    protected $table = 'ticketera_reservas';

    public const ESTADOS = ['pendiente', 'pagada', 'rechazada', 'anulada', 'expirada'];

    // Estados que reporta Flow en /payment/getStatus: 1 pendiente, 2 pagada, 3 rechazada, 4 anulada.
    public const MAPA_ESTADOS_FLOW = [
        1 => 'pendiente',
        2 => 'pagada',
        3 => 'rechazada',
        4 => 'anulada',
    ];

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
        'checkin_at',
        'checkin_by',
        'expira_en',
        'payload_flow',
        'ip_cliente',
    ];

    protected $casts = [
        'fecha_visita' => 'date',
        'contactado'   => 'boolean',
        'es_prueba'    => 'boolean',
        'pagado_en'    => 'datetime',
        'checkin_at'   => 'datetime',
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

    public function checkinPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'checkin_by');
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
     * Aplica el estado que reporta /payment/getStatus de Flow de forma atómica.
     * Tanto el webhook (urlConfirmation) como el retorno del navegador (urlReturn)
     * llaman a este método casi al mismo tiempo; el lockForUpdate() dentro de la
     * transacción evita que ambos lean "pendiente" a la vez y disparen la
     * notificación de pago/rechazo duplicada.
     */
    public static function aplicarEstadoFlow(int $reservaId, array $estadoFlow): void
    {
        DB::transaction(function () use ($reservaId, $estadoFlow) {
            $reserva = static::where('id', $reservaId)->lockForUpdate()->first();

            if (!$reserva) {
                return;
            }

            $estadoAnterior = $reserva->estado;
            $estado = self::MAPA_ESTADOS_FLOW[$estadoFlow['status'] ?? null] ?? $estadoAnterior;

            $reserva->update([
                'estado'       => $estado,
                'payload_flow' => $estadoFlow,
                'pagado_en'    => $estado === 'pagada' ? ($reserva->pagado_en ?? now()) : $reserva->pagado_en,
            ]);

            if ($estado === $estadoAnterior) {
                return;
            }

            if ($estado === 'pagada') {
                $reserva->notificarPagada();
            } elseif (in_array($estado, ['rechazada', 'anulada'], true)) {
                $reserva->notificarRechazada();
            }
        });
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

    /**
     * Aviso admin de que un pago fue rechazado/anulado por Flow. No hay acción
     * pendiente del cliente (Flow ya le mostró el motivo en su propio flujo),
     * pero el admin necesita saber para hacer seguimiento si el cliente escribe.
     */
    public function notificarRechazada(): void
    {
        try {
            Notification::route('mail', Configuracion::emailsNotificacion())
                ->notify(new ReservaRechazada($this));
        } catch (\Throwable $e) {
            Log::error('ReservaRuta::notificarRechazada — falló el aviso admin', [
                'reserva' => $this->codigo_reserva,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
