<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DuitkuService
{
    public function isConfigured(): bool
    {
        return filled($this->merchantCode()) && filled($this->apiKey());
    }

    public function createInvoice(Payment $payment): array
    {
        $this->ensureConfigured();
        $payment->loadMissing(['organization', 'plan']);

        $timestamp = (string) round(microtime(true) * 1000);
        $amount = (int) round((float) $payment->amount);
        $description = sprintf('Paket %s MWork - %d %s', $payment->plan->name, $payment->duration_value, $payment->duration_unit === 'days' ? 'hari' : 'bulan');

        $payload = [
            'paymentAmount' => $amount,
            'merchantOrderId' => $payment->merchant_order_id,
            'productDetails' => $description,
            'email' => $payment->customer_email,
            'customerVaName' => mb_substr($payment->organization->name, 0, 20),
            'callbackUrl' => route('payments.gateway.callback'),
            'returnUrl' => route('payments.gateway.return'),
            'expiryPeriod' => (int) config('services.duitku.expiry_period', 60),
            'itemDetails' => [[
                'name' => mb_substr($description, 0, 255),
                'price' => $amount,
                'quantity' => 1,
            ]],
        ];
        if (filled($payment->customer_phone)) {
            $payload['phoneNumber'] = $payment->customer_phone;
        }

        $response = $this->client()
            ->withHeaders([
                'x-duitku-timestamp' => $timestamp,
                'x-duitku-merchantcode' => $this->merchantCode(),
                'x-duitku-signature' => hash_hmac('sha256', $this->merchantCode().$timestamp, $this->apiKey()),
            ])
            ->post($this->endpoint('create_invoice_url'), $payload);

        $data = $response->json() ?: [];
        if (! $response->successful() || (string) ($data['statusCode'] ?? '') !== '00' || blank($data['paymentUrl'] ?? null)) {
            throw new RuntimeException($data['statusMessage'] ?? 'Duitku tidak dapat membuat invoice pembayaran.');
        }

        return $data;
    }

    public function transactionStatus(Payment $payment): array
    {
        $this->ensureConfigured();

        $response = $this->client()->post($this->endpoint('transaction_status_url'), [
            'merchantCode' => $this->merchantCode(),
            'merchantOrderId' => $payment->merchant_order_id,
            'signature' => hash_hmac(
                'sha256',
                $this->merchantCode().$payment->merchant_order_id,
                $this->apiKey()
            ),
        ]);

        $data = $response->json() ?: [];
        if (! $response->successful() || ! array_key_exists('statusCode', $data)) {
            throw new RuntimeException($data['statusMessage'] ?? 'Status transaksi belum dapat diperiksa.');
        }

        return $data;
    }

    public function callbackSignature(string $amount, string $merchantOrderId): string
    {
        return hash_hmac('sha256', $this->merchantCode().$amount.$merchantOrderId, $this->apiKey());
    }

    public function merchantCode(): string
    {
        return (string) config('services.duitku.merchant_code');
    }

    protected function apiKey(): string
    {
        return (string) config('services.duitku.api_key');
    }

    protected function endpoint(string $name): string
    {
        $environment = config('services.duitku.environment') === 'production' ? 'production' : 'sandbox';
        return (string) config("services.duitku.{$environment}.{$name}");
    }

    protected function client(): PendingRequest
    {
        return Http::asJson()->acceptJson()->connectTimeout(10)->timeout(25);
    }

    protected function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Merchant Code dan API Key Duitku belum dikonfigurasi.');
        }
    }
}
