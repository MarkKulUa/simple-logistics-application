<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class PayPalService
{
    private const int TOKEN_CACHE_DURATION = 3500;
    private const string API_VERSION = 'v2';

    private string $baseUrl;
    private string $accessToken;
    private string $tokenCacheKey;

    public function __construct(
        protected string $clientId,
        protected string $clientSecret,
        private readonly Http $http,
        protected bool $sandbox = true
    ) {
        $this->baseUrl = $sandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $this->tokenCacheKey = 'paypal_access_token_' . md5($clientId);
        $this->accessToken = $this->getAccessToken();
    }

    private function getAccessToken(): string
    {
        return Cache::remember($this->tokenCacheKey, self::TOKEN_CACHE_DURATION, function () {
            return $this->generateAccessToken();
        });
    }

    private function generateAccessToken(): string
    {
        try {
            $response = $this->http->asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials'
                ]);

            if (!$response->successful()) {
                throw new InvalidArgumentException(
                    "PayPal API Error: {$response->status()} - {$response->body()}"
                );
            }

            return $response->json()['access_token'];
        } catch (\Exception $e) {
            Log::error('PayPal token generation failed', ['exception' => $e]);
            throw new InvalidArgumentException('Unable to retrieve PayPal access token.');
        }
    }

    public function createOrder(float $amount, string $currency = 'USD', string $intent = 'CAPTURE'): array
    {
        return $this->makeRequest('post', 'checkout/orders', [
            'intent' => $intent,
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => strtoupper($currency),
                    'value' => number_format($amount, 2, '.', '')
                ]
            ]]
        ]);
    }

    public function captureOrder(string $orderId): array
    {
        return $this->makeRequest('post', "checkout/orders/{$orderId}/capture");
    }

    public function authorizeOrder(string $orderId): array
    {
        return $this->makeRequest('post', "checkout/orders/{$orderId}/authorize");
    }

    public function createSubscription(array $subscriptionData): array
    {
        return $this->makeRequest('post', 'billing/subscriptions', $subscriptionData);
    }

    public function createBillingPlan(array $planData): array
    {
        return $this->makeRequest('post', 'billing/plans', $planData);
    }

    public function updateBillingPlan(string $planId, array $patches): array
    {
        return $this->makeRequest('patch', "billing/plans/{$planId}", $patches);
    }

    public function getBillingPlan(string $planId): array
    {
        return $this->makeRequest('get', "billing/plans/{$planId}");
    }

    public function listBillingPlans(array $params = []): array
    {
        return $this->makeRequest('get', 'billing/plans', $params);
    }

    public function getSubscription(string $subscriptionId): array
    {
        return $this->makeRequest('get', "billing/subscriptions/{$subscriptionId}");
    }

    public function cancelSubscription(string $subscriptionId, string $reason = ''): array
    {
        return $this->makeRequest('post', "billing/subscriptions/{$subscriptionId}/cancel", [
            'reason' => $reason
        ]);
    }

    public function suspendSubscription(string $subscriptionId, string $reason = ''): array
    {
        return $this->makeRequest('post', "billing/subscriptions/{$subscriptionId}/suspend", [
            'reason' => $reason
        ]);
    }

    public function activateSubscription(string $subscriptionId, string $reason = ''): array
    {
        return $this->makeRequest('post', "billing/subscriptions/{$subscriptionId}/activate", [
            'reason' => $reason
        ]);
    }

    public function createPayoutBatch(array $payouts): array
    {
        return $this->makeRequest('post', 'payments/payouts', [
            'items' => $payouts
        ]);
    }

    public function getPayoutBatchStatus(string $batchId): array
    {
        return $this->makeRequest('get', "payments/payouts/{$batchId}");
    }

    public function refundOrder(string $orderId, array $refundData = []): array
    {
        return $this->makeRequest('post', "payments/captures/{$orderId}/refund", $refundData);
    }

    public function getRefundDetails(string $refundId): array
    {
        return $this->makeRequest('get', "payments/refunds/{$refundId}");
    }

    public function getTransactionDetails(string $transactionId): array
    {
        return $this->makeRequest('get', "payments/captures/{$transactionId}");
    }

    public function createWebhook(string $url, array $eventTypes): array
    {
        return $this->makeRequest('post', 'notifications/webhooks', [
            'url' => $url,
            'event_types' => $eventTypes
        ]);
    }

    public function listWebhooks(): array
    {
        return $this->makeRequest('get', 'notifications/webhooks');
    }

    public function deleteWebhook(string $webhookId): array
    {
        return $this->makeRequest('delete', "notifications/webhooks/{$webhookId}");
    }

    public function verifyWebhookSignature(array $webhookData): array
    {
        return $this->makeRequest('post', 'notifications/verify-webhook-signature', $webhookData);
    }

    public function getBalances(string $currency = 'USD'): array
    {
        return $this->makeRequest('get', 'v1/reporting/balances', [
            'currency_code' => $currency
        ]);
    }

    public function getTransactionHistory(array $params = []): array
    {
        return $this->makeRequest('get', 'v1/reporting/transactions', $params);
    }

    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = "{$this->baseUrl}/" . (str_starts_with($endpoint, 'v1/') ? '' : self::API_VERSION . '/') . $endpoint;

        try {
            $response = match ($method) {
                'get' => $this->http->withToken($this->accessToken)->get($url, $data),
                'post' => $this->http->withToken($this->accessToken)->post($url, $data),
                'patch' => $this->http->withToken($this->accessToken)->patch($url, $data),
                'delete' => $this->http->withToken($this->accessToken)->delete($url),
                default => throw new InvalidArgumentException("Unsupported HTTP method: $method")
            };

            if (!$response->successful()) {
                Log::error('PayPal API error', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new InvalidArgumentException("PayPal API Error: {$response->status()}");
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('PayPal API request failed', [
                'url' => $url,
                'exception' => $e->getMessage()
            ]);
            throw new InvalidArgumentException("PayPal API request failed: {$e->getMessage()}");
        }
    }
}
