<?php

namespace App\Http\Controllers;

use App\Mail\NuevoContacto;
use App\Models\LeadContacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function index()
    {
        return view('contacto.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:120',
            'email'    => 'required|email|max:160',
            'telefono' => 'nullable|string|max:25',
            'mensaje'  => 'nullable|string|max:1200',
        ]);

        $lead = LeadContacto::create([
            'nombre'   => $request->nombre,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'mensaje'  => $request->mensaje,
        ]);

        Mail::to(['soporte@pindoor.cl', 'cesar.eav@gmail.com'])->send(new NuevoContacto($lead));

        $this->avisarTelegram($lead);

        return back()->with('success', '¡Mensaje enviado! Te contactaremos pronto.');
    }

    private function avisarTelegram(LeadContacto $lead): void
    {
        $token = config('services.telegram.token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return;
        }

        $texto = "📩 <b>Nuevo mensaje de contacto</b>\n"
            . "Nombre: {$lead->nombre}\n"
            . "Email: {$lead->email}\n"
            . ($lead->telefono ? "Teléfono: {$lead->telefono}\n" : '')
            . ($lead->mensaje ? "Mensaje: {$lead->mensaje}" : '');

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $texto,
            'parse_mode' => 'HTML',
        ]);

        if ($response->failed()) {
            Log::warning('[telegram] envío falló', ['response' => $response->body()]);
        }
    }
}
