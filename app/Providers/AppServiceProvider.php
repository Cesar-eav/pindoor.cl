<?php

namespace App\Providers;

use App\Models\Categoria;
use App\Models\Experiencia;
use App\Models\Panorama;
use App\Models\Post;
use App\Models\PuntoInteres;
use App\Observers\TranslatableObserver;
use App\View\Composers\GuestComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer(['layouts.guest', 'auth.*'], GuestComposer::class);

        Panorama::observe(TranslatableObserver::class);
        PuntoInteres::observe(TranslatableObserver::class);
        Post::observe(TranslatableObserver::class);
        Experiencia::observe(TranslatableObserver::class);
        Categoria::observe(TranslatableObserver::class);
    }
}
