<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSuscriptor;

class NewsletterController extends Controller
{
    public function index()
    {
        $suscriptores = NewsletterSuscriptor::latest()->paginate(50);
        return view('admin.newsletter.index', compact('suscriptores'));
    }

    public function destroy(NewsletterSuscriptor $suscriptor)
    {
        $suscriptor->delete();
        return back()->with('success', 'Suscriptor eliminado.');
    }
}
