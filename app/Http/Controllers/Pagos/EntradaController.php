<?php

namespace App\Http\Controllers\Pagos;

use App\Exceptions\FlowPaymentException;
use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\EventoEntrada;
use App\Models\ModuloItem;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntradaController extends Controller
{
    private function eventoConVentaActiva(int $itemId): ModuloItem
    {
        abort_unless(Configuracion::ventaEntradasActiva(), 404);

        $item = ModuloItem::where('id', $itemId)
            ->where('modulo', 'eventos')
            ->where('activo', true)
            ->firstOrFail();

        abort_unless($item->datos['entradas_flow_activo'] ?? false, 404);

        return $item;
    }

    /** Página de compra: resumen del evento, cupo restante y formulario de pago. */
    public function show(int $item)
    {
        $item = $this->eventoConVentaActiva($item);
        $item->loadMissing('punto');

        $precio = (float) ($item->datos['precio'] ?? 0);

        abort_if($precio <= 0, 404);

        return view('pagos.entrada-comprar', [
            'item'           => $item,
            'precio'         => $precio,
            'cupoDisponible' => $item->cupoDisponible(),
        ]);
    }

    /**
     * Crea la orden de pago de entradas para un evento de agenda y redirige a Flow.
     * Mismo patrón de doble lock que ReservaController::store(): lockea el ModuloItem,
     * suma entradas competidoras (pagadas o pendientes sin vencer) y recién ahí valida
     * cupo, todo dentro de la misma transacción, para evitar sobreventa.
     */
    public function store(Request $request, int $item, FlowService $flow)
    {
        $item = $this->eventoConVentaActiva($item);

        $data = $request->validate([
            'cantidad_entradas' => 'required|integer|min:1|max:20',
            'nombre_cliente'    => 'required|string|max:255',
            'email_cliente'     => 'required|email|max:255',
            'telefono_cliente'  => 'required|string|max:30',
        ]);

        try {
            $entrada = DB::transaction(function () use ($item, $data, $request) {
                ModuloItem::where('id', $item->id)->lockForUpdate()->first();

                if ($item->cupo_maximo !== null) {
                    $vendidas = EventoEntrada::where('punto_modulo_item_id', $item->id)
                        ->where(function ($q) {
                            $q->where('estado', 'pagada')
                              ->orWhere(function ($q2) {
                                  $q2->where('estado', 'pendiente')->where('expira_en', '>', now());
                              });
                        })
                        ->lockForUpdate()
                        ->sum('cantidad_entradas');

                    if ($vendidas + $data['cantidad_entradas'] > $item->cupo_maximo) {
                        throw new \RuntimeException('sin_cupo');
                    }
                }

                $precioUnitario = (int) ($item->datos['precio'] ?? 0);

                return EventoEntrada::create([
                    'punto_modulo_item_id'     => $item->id,
                    'punto_interes_id'         => $item->punto_interes_id,
                    'evento_titulo_al_pagar'   => $item->datos['titulo'] ?? '',
                    'evento_fecha_al_pagar'    => $item->fecha,
                    'precio_unitario_al_pagar' => $precioUnitario,
                    'cantidad_entradas'        => $data['cantidad_entradas'],
                    'nombre_cliente'           => $data['nombre_cliente'],
                    'email_cliente'            => $data['email_cliente'],
                    'telefono_cliente'         => $data['telefono_cliente'],
                    'estado'                   => 'pendiente',
                    'expira_en'                => now()->addMinutes(30),
                    'ip_cliente'               => $request->ip(),
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'sin_cupo') {
                return back()->withInput()->withErrors(['cantidad_entradas' => 'Ya no quedan entradas disponibles para este evento.']);
            }
            throw $e;
        }

        $entrada->notificarIniciada();

        try {
            $pago = $flow->crearOrdenPagoEntrada($entrada);
        } catch (FlowPaymentException $e) {
            $entrada->update(['estado' => 'anulada']);
            return back()->withInput()->withErrors(['flow' => 'No pudimos iniciar el pago con Flow. Intenta nuevamente en unos minutos.']);
        }

        $entrada->update([
            'flow_token' => $pago['token'],
            'flow_order' => $pago['flowOrder'],
        ]);

        return redirect()->away($flow->urlPago($pago['url'], $pago['token']));
    }
}
