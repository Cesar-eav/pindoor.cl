<?php

namespace App\Services;

use App\Exceptions\FlowPaymentException;
use App\Models\ReservaRuta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlowService
{
    private function apiKey(): string
    {
        return (string) config('services.flow.api_key');
    }

    private function secretKey(): string
    {
        return (string) config('services.flow.secret_key');
    }

    private function baseUrl(): string
    {
        return config('services.flow.sandbox')
            ? 'https://sandbox.flow.cl/api'
            : 'https://www.flow.cl/api';
    }

    public function firmar(array $params): string
    {
        ksort($params);

        $concatenado = '';
        foreach ($params as $key => $value) {
            $concatenado .= $key . $value;
        }

        return hash_hmac('sha256', $concatenado, $this->secretKey());
    }

    public function crearOrdenPago(ReservaRuta $reserva): array
    {
        $params = [
            'apiKey'          => $this->apiKey(),
            'commerceOrder'   => $reserva->commerce_order,
            'subject'         => 'Reserva Pindoor ' . $reserva->codigo_reserva,
            'currency'        => 'CLP',
            'amount'          => $reserva->precio_total,
            'email'           => $reserva->email_cliente,
            'urlConfirmation' => route('flow.confirmacion'),
            'urlReturn'       => route('flow.retorno', ['codigo' => $reserva->codigo_reserva]),
        ];

        $params['s'] = $this->firmar($params);

        Log::info('FlowService::crearOrdenPago request', ['commerceOrder' => $reserva->commerce_order]);

        $response = Http::asForm()->timeout(15)->post("{$this->baseUrl()}/payment/create", $params);

        if (!$response->successful()) {
            Log::error('FlowService::crearOrdenPago fallo', ['body' => $response->body()]);
            throw new FlowPaymentException('Flow rechazó la creación del pago: ' . $response->body());
        }

        $json = $response->json();

        if (empty($json['url']) || empty($json['token'])) {
            Log::error('FlowService::crearOrdenPago respuesta inesperada', ['body' => $json]);
            throw new FlowPaymentException('Respuesta inesperada de Flow al crear el pago.');
        }

        return [
            'url'       => $json['url'],
            'token'     => $json['token'],
            'flowOrder' => $json['flowOrder'] ?? null,
        ];
    }

    public function obtenerEstadoPago(string $token): array
    {
        $params = [
            'apiKey' => $this->apiKey(),
            'token'  => $token,
        ];
        $params['s'] = $this->firmar($params);

        $response = Http::timeout(15)->get("{$this->baseUrl()}/payment/getStatus", $params);

        if (!$response->successful()) {
            Log::error('FlowService::obtenerEstadoPago fallo', ['body' => $response->body()]);
            throw new FlowPaymentException('Flow rechazó la consulta de estado: ' . $response->body());
        }

        return $response->json();
    }

    public function urlPago(string $url, string $token): string
    {
        return $url . '?token=' . $token;
    }
}
