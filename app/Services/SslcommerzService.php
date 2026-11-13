<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SslcommerzService
{
    protected string $baseUrl;
    protected string $storeId;
    protected string $storePasswd;

    public function __construct()
    {
        $config = config('services.sslcommerz', []);

        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://sandbox.sslcommerz.com'), '/');
        $this->storeId = (string) ($config['store_id'] ?? '');
        $this->storePasswd = (string) ($config['store_passwd'] ?? '');
    }

    public function configured(): bool
    {
        return $this->storeId !== '' && $this->storePasswd !== '';
    }

    /**
     * Create a gateway session. Success: {"status":"SUCCESS","GatewayPageURL":"..."}
     */
    public function initiate(array $payload): array
    {
        $payload = array_merge($payload, [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePasswd,
        ]);

        $response = Http::asForm()->timeout(30)->post($this->baseUrl . '/gwprocess/v4/api.php', $payload);

        return $response->json() ?? ['raw' => $response->body(), 'http_status' => $response->status()];
    }

    /**
     * Server-side validation of a payment by val_id.
     */
    public function validate(string $valId): array
    {
        $response = Http::timeout(30)->get($this->baseUrl . '/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePasswd,
            'v' => 1,
            'format' => 'json',
        ]);

        return $response->json() ?? ['raw' => $response->body(), 'http_status' => $response->status()];
    }
}
