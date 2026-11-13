<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AamarpayService
{
    protected string $baseUrl;
    protected string $storeId;
    protected string $signatureKey;

    public function __construct()
    {
        $config = config('services.aamarpay', []);

        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://sandbox.aamarpay.com'), '/');
        $this->storeId = (string) ($config['store_id'] ?? '');
        $this->signatureKey = (string) ($config['signature_key'] ?? '');
    }

    public function configured(): bool
    {
        return $this->storeId !== '' && $this->signatureKey !== '';
    }

    /**
     * Create a payment. Success: {"result":"true","payment_url":"..."}
     * Note: this API has no ipn_url parameter.
     */
    public function initiate(array $payload): array
    {
        $payload = array_merge($payload, [
            'store_id' => $this->storeId,
            'signature_key' => $this->signatureKey,
            'type' => 'json',
        ]);

        $response = Http::asForm()->timeout(30)->post($this->baseUrl . '/index.php', $payload);

        return $response->json() ?? ['raw' => $response->body(), 'http_status' => $response->status()];
    }

    /**
     * Server-side transaction check by our merchant transaction id.
     */
    public function verify(string $tranId): array
    {
        $response = Http::timeout(30)->get($this->baseUrl . '/api/v1/trxcheck/request.php', [
            'request_id' => $tranId,
            'store_id' => $this->storeId,
            'signature_key' => $this->signatureKey,
            'type' => 'json',
        ]);

        return $response->json() ?? ['raw' => $response->body(), 'http_status' => $response->status()];
    }
}
