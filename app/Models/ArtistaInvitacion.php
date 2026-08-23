<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtistaInvitacion extends Model
{
    protected $table = 'artista_invitaciones';

    protected $fillable = [
        'artista_id', 'email', 'token', 'invitado_por_id', 'aceptada_at', 'expires_at',
    ];

    protected $casts = [
        'aceptada_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function artista()
    {
        return $this->belongsTo(Artista::class);
    }

    public function invitadoPor()
    {
        return $this->belongsTo(User::class, 'invitado_por_id');
    }

    public function vigente(): bool
    {
        return is_null($this->aceptada_at) && $this->expires_at->isFuture();
    }
}
