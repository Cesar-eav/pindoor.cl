<?php

namespace App\Http\Controllers;

use App\Models\EventoFicha;
use App\Models\PuntoInteres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class EventoFichaController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|max:255',
            'tipo' => 'required|string|in:visita,como_llegar,whatsapp',
        ]);

        // Bots de previsualización (WhatsApp, Facebook, Telegram...) no ejecutan este JS,
        // pero igual filtramos acá crawlers que sí llegan a pegarle al endpoint (ej. Googlebot
        // renderizando JS) para no inflar las estadísticas que ve el cliente.
        if ((new CrawlerDetect)->isCrawler($request->userAgent())) {
            return response()->json(['ok' => true]);
        }

        // visibleFicha() deja pasar los puntos de ejemplo/demo mientras dure la sesión de
        // /registro — así se puede probar el tracking completo en clientes de prueba antes
        // de habilitarlo para clientes reales. Fuera de esa sesión se comporta igual que publico().
        $punto = PuntoInteres::visibleFicha()->where('slug', $data['slug'])->first();

        if (!$punto) {
            return response()->json(['ok' => true]);
        }

        if ($data['tipo'] === 'visita') {
            if (Auth::id() === $punto->user_id) {
                return response()->json(['ok' => true]);
            }

            $clave = "visita_ficha_{$punto->id}_" . now()->format('Y-m-d');
            if ($request->session()->has($clave)) {
                return response()->json(['ok' => true]);
            }
            $request->session()->put($clave, true);
        }

        EventoFicha::create([
            'punto_interes_id' => $punto->id,
            'tipo'             => $data['tipo'],
        ]);

        return response()->json(['ok' => true]);
    }
}
