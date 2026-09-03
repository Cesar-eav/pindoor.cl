<?php

namespace App\Services;

use App\Exceptions\FlowPaymentException;
use App\Models\Configuracion;
use App\Models\EventoEntrada;
use App\Models\ReservaRuta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlowService
{
    public function modo(): string
    {
        return Configuracion::get('flow_modo') ?: (config('services.flow.sandbox') ? 'sandbox' : 'produccion');
    }

    private function apiKey(): string
    {
        return (string) (Configuracion::get("flow_{$this->modo()}_api_key") ?: config('services.flow.api_key'));
    }

    private function secretKey(): string
    {
        return (string) (Configuracion::get("flow_{$this->modo()}_secret_key") ?: config('services.flow.secret_key'));
    }

    private function baseUrl(): string
    {
        return $this->modo() === 'sandbox'
            ? 'https://sandbox.flow.cl/api'
            : 'https://www.flow.cl/api';
    }

    private function assertCredenciales(): void
    {
        if (!$this->apiKey() || !$this->secretKey()) {
            throw new FlowPaymentException('Credenciales de Flow no configuradas (FLOW_API_KEY / FLOW_SECRET_KEY).');
        }
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
        return $this->crearOrdenPagoGenerico(
            commerceOrder: $reserva->commerce_order,
            subject: 'Reserva Pindoor ' . $reserva->codigo_reserva,
            monto: $reserva->precio_total,
            email: $reserva->email_cliente,
            urlRetorno: route('flow.retorno', ['codigo' => $reserva->codigo_reserva]),
        );
    }

    public function crearOrdenPagoEntrada(EventoEntrada $entrada): array
    {
        return $this->crearOrdenPagoGenerico(
            commerceOrder: $entrada->commerce_order,
            subject: 'Entrada Pindoor ' . $entrada->codigo_entrada,
            monto: $entrada->monto_total,
            email: $entrada->email_cliente,
            urlRetorno: route('flow.entrada.retorno', ['codigo' => $entrada->codigo_entrada]),
        );
    }

    private function crearOrdenPagoGenerico(string $commerceOrder, string $subject, int $monto, string $email, string $urlRetorno): array
    {
        $this->assertCredenciales();

        $params = [
            'apiKey'          => $this->apiKey(),
            'commerceOrder'   => $commerceOrder,
            'subject'         => $subject,
            'currency'        => 'CLP',
            'amount'          => $monto,
            'email'           => $email,
            'urlConfirmation' => route('flow.confirmacion'),
            'urlReturn'       => $urlRetorno,
        ];

        $params['s'] = $this->firmar($params);

        Log::info('FlowService::crearOrdenPagoGenerico request', ['commerceOrder' => $commerceOrder]);

        $response = Http::asForm()->timeout(15)->post("{$this->baseUrl()}/payment/create", $params);

        if (!$response->successful()) {
            Log::error('FlowService::crearOrdenPagoGenerico fallo', ['body' => $response->body()]);
            throw new FlowPaymentException('Flow rechazó la creación del pago: ' . $response->body());
        }

        $json = $response->json();

        if (empty($json['url']) || empty($json['token'])) {
            Log::error('FlowService::crearOrdenPagoGenerico respuesta inesperada', ['body' => $json]);
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
        $this->assertCredenciales();

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
