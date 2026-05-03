<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class VerifyEmailQueued extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60; // segundos entre reintentos
}
