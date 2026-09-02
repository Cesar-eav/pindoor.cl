<?php

namespace App\Listeners;

use App\Models\LoginCliente;
use Illuminate\Auth\Events\Login;

class UpdateLastLogin
{
    public function handle(Login $event): void
    {
        $event->user->update(['last_login_at' => now()]);

        if ($event->user->type === 'cliente') {
            LoginCliente::create(['user_id' => $event->user->id]);
        }
    }
}
