<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSuscriptor;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email'  => 'required|email|max:190',
            'origen' => 'nullable|string|max:40',
        ]);

        NewsletterSuscriptor::firstOrCreate(
            ['email' => $data['email']],
            ['origen' => $data['origen'] ?? 'panoramas']
        );

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', '¡Listo! Te avisaremos por correo cuando haya novedades.');
    }
}
