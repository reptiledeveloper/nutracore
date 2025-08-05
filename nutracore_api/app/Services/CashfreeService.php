<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CashfreeService
{
    private $base_url;
    private $app_id;
    private $secret_key;

    public function __construct()
    {
        $this->base_url = config('cashfree.env') === 'sandbox'
            ? 'https://sandbox.cashfree.com/pg'
            : 'https://api.cashfree.com/pg';
        $this->app_id = config('cashfree.app_id');
        $this->secret_key = config('cashfree.secret_key');
    }

    public function createOrder($orderData)
    {

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-client-id' => $this->app_id,
            'x-client-secret' => $this->secret_key,
        ])->post($this->base_url . '/orders', $orderData);

        return $response->json();
    }

    public function getOrder($orderId)
    {
        $response = Http::withHeaders([
            'x-client-id' => $this->app_id,
            'x-client-secret' => $this->secret_key,
        ])->get($this->base_url . "/orders/{$orderId}");

        return $response->json();
    }
}
