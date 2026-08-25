<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReclamoNegocio extends Model
{
    protected $table = 'reclamos_negocio';

    const ESTADOS = [
        'pending'   => ['label' => 'Pendiente',  'color' => 'amber'],
        'approved'  => ['label' => 'Aprobado',   'color' => 'blue'],
        'rejected'  => ['label' => 'Rechazado',  'color' => 'red'],
        'completed' => ['label' => 'Completado', 'color' => 'green'],
    ];

    protected $fillable = [
        'punto_id', 'name', 'email', 'whatsapp', 'status', 'activation_token', 'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    public function punto(): BelongsTo
    {
        return $this->belongsTo(PuntoInteres::class, 'punto_id');
    }

    public function estadoInfo(): array
    {
        return self::ESTADOS[$this->status] ?? self::ESTADOS['pending'];
    }

    public function vigente(): bool
    {
        return $this->status === 'approved'
            && $this->activation_token
            && $this->token_expires_at?->isFuture();
    }

    public function completar(User $user): void
    {
        $this->punto->update([
            'user_id'             => $user->id,
            'es_cliente'          => true,
            'modulos_habilitados' => PuntoInteres::modulosDefault($this->punto->categoria_id),
        ]);

        $this->update(['status' => 'completed']);
    }
}
