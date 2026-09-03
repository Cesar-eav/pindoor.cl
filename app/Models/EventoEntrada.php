<?php

namespace App\Models;

use App\Mail\EntradaConfirmada;
use App\Notifications\EntradaIniciada;
use App\Notifications\EntradaPagada;
use App\Notifications\EntradaRechazada;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EventoEntrada extends Model
{
    protected $table = 'evento_entradas';

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
        'punto_modulo_item_id',
        'punto_interes_id',
        'evento_titulo_al_pagar',
        'evento_fecha_al_pagar',
        'precio_unitario_al_pagar',
        'cantidad_entradas',
        'monto_total',
        'nombre_cliente',
        'email_cliente',
        'telefono_cliente',
        'codigo_entrada',
        'commerce_order',
        'flow_token',
        'flow_order',
        'estado',
        'es_prueba',
        'pagado_en',
        'expira_en',
        'payload_flow',
        'ip_cliente',
        'payer_email',
        'medio_pago',
        'monto_pagado',
        'fecha_pago_flow',
    ];

    protected $casts = [
        'evento_fecha_al_pagar' => 'date',
        'es_prueba'             => 'boolean',
        'pagado_en'             => 'datetime',
        'expira_en'             => 'datetime',
        'payload_flow'          => 'array',
        'fecha_pago_flow'       => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $entrada) {
            if (!$entrada->codigo_entrada) {
                $entrada->codigo_entrada = static::generarCodigo();
            }
            if (!$entrada->commerce_order) {
                $entrada->commerce_order = static::generarCommerceOrder();
            }
            $entrada->monto_total = $entrada->cantidad_entradas * $entrada->precio_unitario_al_pagar;
        });
    }

    public static function generarCodigo(): string
    {
        do {
            $codigo = 'PIN-EVT-' . strtoupper(Str::random(6));
        } while (static::where('codigo_entrada', $codigo)->exists());

        return $codigo;
    }

    public static function generarCommerceOrder(): string
    {
        return 'PIN-EVT-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
    }

    public function moduloItem()
    {
        return $this->belongsTo(ModuloItem::class, 'punto_modulo_item_id');
    }

    public function punto()
    {
        return $this->belongsTo(PuntoInteres::class, 'punto_interes_id');
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
     * Mismo patrón que ReservaRuta::aplicarEstadoFlow(): webhook y retorno pueden
     * llegar casi al mismo tiempo, el lockForUpdate() evita la doble notificación.
     */
    public static function aplicarEstadoFlow(int $entradaId, array $estadoFlow): void
    {
        DB::transaction(function () use ($entradaId, $estadoFlow) {
            $entrada = static::where('id', $entradaId)->lockForUpdate()->first();

            if (!$entrada) {
                return;
            }

            $estadoAnterior = $entrada->estado;
            $estado = self::MAPA_ESTADOS_FLOW[$estadoFlow['status'] ?? null] ?? $estadoAnterior;

            $entrada->update([
                'estado'          => $estado,
                'payload_flow'    => $estadoFlow,
                'pagado_en'       => $estado === 'pagada' ? ($entrada->pagado_en ?? now()) : $entrada->pagado_en,
                'payer_email'     => $estadoFlow['payer'] ?? $entrada->payer_email,
                'medio_pago'      => $estadoFlow['paymentData']['media'] ?? $entrada->medio_pago,
                'monto_pagado'    => $estadoFlow['paymentData']['amount'] ?? $estadoFlow['amount'] ?? $entrada->monto_pagado,
                'fecha_pago_flow' => $estadoFlow['paymentData']['date'] ?? $entrada->fecha_pago_flow,
            ]);

            if ($estado === $estadoAnterior) {
                return;
            }

            if ($estado === 'pagada') {
                $entrada->notificarPagada();
            } elseif (in_array($estado, ['rechazada', 'anulada'], true)) {
                $entrada->notificarRechazada();
            }
        });
    }

    public function notificarIniciada(): void
    {
        try {
            Notification::route('telegram', Configuracion::telegramChatId())
                ->notify(new EntradaIniciada($this));
        } catch (\Throwable $e) {
            Log::error('EventoEntrada::notificarIniciada — falló el aviso Telegram', [
                'entrada' => $this->codigo_entrada,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function notificarPagada(): void
    {
        try {
            Mail::to($this->email_cliente)->send(new EntradaConfirmada($this));
        } catch (\Throwable $e) {
            Log::error('EventoEntrada::notificarPagada — falló el mail al cliente', [
                'entrada' => $this->codigo_entrada,
                'error'   => $e->getMessage(),
            ]);
        }

        try {
            Notification::route('mail', Configuracion::emailsNotificacion())
                ->notify(new EntradaPagada($this));
        } catch (\Throwable $e) {
            Log::error('EventoEntrada::notificarPagada — falló el aviso admin', [
                'entrada' => $this->codigo_entrada,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function notificarRechazada(): void
    {
        try {
            Notification::route('mail', Configuracion::emailsNotificacion())
                ->notify(new EntradaRechazada($this));
        } catch (\Throwable $e) {
            Log::error('EventoEntrada::notificarRechazada — falló el aviso admin', [
                'entrada' => $this->codigo_entrada,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
