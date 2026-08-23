<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Link de vista previa compartible con el cliente, sin necesitar sesión de
 * admin. El token vive mientras el registro sigue en borrador — en cuanto
 * pasa a estar en vivo (según previewEstaVivo() de cada modelo) el link deja
 * de servir el borrador y redirige a la URL pública real. También se puede
 * "matar" a mano regenerando el token.
 */
trait HasPreviewToken
{
    protected static function bootHasPreviewToken(): void
    {
        static::creating(function ($model) {
            if (empty($model->preview_token)) {
                $model->preview_token = Str::random(40);
            }
        });
    }

    public function regenerarPreviewToken(): string
    {
        $this->preview_token = Str::random(40);
        $this->save();

        return $this->preview_token;
    }

    public function getPreviewUrlAttribute(): string
    {
        return route('preview.show', ['tipo' => $this->previewTipo(), 'token' => $this->preview_token]);
    }

    /** true mientras el borrador debe seguir siendo visible por el link de preview. */
    abstract public function previewEstaVivo(): bool;

    /** Segmento de URL del tipo de contenido (revival | blog | recomienda). */
    abstract public function previewTipo(): string;
}
