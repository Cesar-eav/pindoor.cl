<?php

namespace App\Models;

use App\Notifications\VerifyEmailQueued;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PuntoInteres;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'google_id',
        'imagen_logo',
        'email_verified_at',
        'es_sistema',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'es_sistema' => 'boolean',
    ];

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailQueued());
    }

    public function puntoInteres()
    {
        return $this->hasMany(PuntoInteres::class);
    }

    public function artistas()
    {
        return $this->belongsToMany(Artista::class, 'artista_miembros')->withTimestamps();
    }

    public function artistaActivo(): ?Artista
    {
        $id = session('artista_activo_id');
        if ($id && ($activo = $this->artistas->firstWhere('id', $id))) {
            return $activo;
        }
        return $this->artistas->first();
    }

    public function esArtista(): bool
    {
        return $this->type === 'artista';
    }

    public function operador()
    {
        return $this->hasOne(OperadorTuristico::class);
    }

    public function esOperador(): bool
    {
        return $this->type === 'operador';
    }
}
