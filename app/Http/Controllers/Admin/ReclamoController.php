<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReclamoNegocio;
use App\Notifications\ReclamoAprobadoNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ReclamoController extends Controller
{
    public function index()
    {
        $reclamos = ReclamoNegocio::with('punto')->latest()->paginate(15);

        return view('admin.reclamos', compact('reclamos'));
    }

    public function aprobar(ReclamoNegocio $reclamo)
    {
        abort_unless($reclamo->status === 'pending', 409);

        $reclamo->update([
            'status'           => 'approved',
            'activation_token' => Str::random(40),
            'token_expires_at' => now()->addHours(48),
        ]);

        Notification::route('mail', $reclamo->email)
            ->notify(new ReclamoAprobadoNotification($reclamo));

        return back()->with('success', "Reclamo aprobado. Se envió el link de activación a {$reclamo->email}.");
    }

    public function rechazar(ReclamoNegocio $reclamo)
    {
        abort_unless($reclamo->status === 'pending', 409);

        $reclamo->update(['status' => 'rejected']);

        return back()->with('success', 'Reclamo rechazado.');
    }
}
