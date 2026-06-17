<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepLTranslator
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.deepl.key', '');
    }

    public function translate(string $text, string $from = 'ES', string $to = 'EN'): ?string
    {
        if (empty($this->apiKey) || empty(trim($text))) {
            return null;
        }

        // Las claves del plan gratuito terminan en :fx — usan subdominio distinto
        $url = str_ends_with($this->apiKey, ':fx')
            ? 'https://api-free.deepl.com/v2/translate'
            : 'https://api.deepl.com/v2/translate';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
            ])->post($url, [
                'text'        => [$text],
                'source_lang' => $from,
                'target_lang' => $to,
            ]);

            if ($response->successful()) {
                return $response->json('translations.0.text');
            }

            Log::warning('DeepL error ' . $response->status() . ': ' . $response->body());
        } catch (\Throwable $e) {
            Log::warning('DeepL exception: ' . $e->getMessage());
        }

        return null;
    }
}
