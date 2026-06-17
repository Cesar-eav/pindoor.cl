<?php

namespace App\Observers;

use App\Services\DeepLTranslator;
use Illuminate\Database\Eloquent\Model;

class TranslatableObserver
{
    // Campos más largos que esto no se traducen automáticamente
    private const MAX_CHARS = 1500;

    public function saving(Model $model): void
    {
        if (empty($model->translatable)) {
            return;
        }

        $translator = app(DeepLTranslator::class);

        foreach ($model->translatable as $field) {
            $es = $model->getTranslation($field, 'es', false);
            $en = $model->getTranslation($field, 'en', false);

            if ($es && !$en && mb_strlen($es) <= self::MAX_CHARS) {
                $translated = $translator->translate($es);
                if ($translated) {
                    $model->setTranslation($field, 'en', $translated);
                }
            }
        }
    }
}
