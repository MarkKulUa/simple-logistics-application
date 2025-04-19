<?php

namespace Tests\Unit\Services\Payment;

use App\Services\Payment\PayPalService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Factory as HttpFactory;
use InvalidArgumentException;
use Tests\TestCase;

class PayPalServiceTest extends TestCase
{
    protected PayPalService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'fake-token'
            ], 200),
        ]);

        $this->service = new PayPalService(
            'test-client-id',
            'test-client-secret',
            app(HttpFactory::class),
            sandbox: true
        );
    }

    public function testItGeneratesAccessToken()
    {
        $this->assertEquals('fake-token', $this->getPrivate($this->service, 'accessToken'));
    }

    public function testCreateOrderReturnsData()
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'id' => 'ORDER123',
                'status' => 'CREATED',
            ], 201),
        ]);

        $order = $this->service->createOrder(10.00, 'USD');

        $this->assertEquals('ORDER123', $order['id']);
        $this->assertEquals('CREATED', $order['status']);
    }

    public function testCaptureOrderReturnsData()
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER123/capture' => Http::response([
                'status' => 'COMPLETED'
            ], 200),
        ]);

        $result = $this->service->captureOrder('ORDER123');
        $this->assertEquals('COMPLETED', $result['status']);
    }

    public function testWebhookCreateSuccess()
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v2/notifications/webhooks' => Http::response([
                'id' => 'WH-123',
            ], 201),
        ]);

        $response = $this->service->createWebhook('https://example.com', [
            ['name' => 'PAYMENT.SALE.COMPLETED']
        ]);

        $this->assertEquals('WH-123', $response['id']);
    }

    public function testApiErrorThrowsException()
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response('Unauthorized', 401),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->createOrder(5.0);
    }

    public function testRequestExceptionIsHandled()
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => fn () => throw new \Exception('Connection timeout'),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->createOrder(5.0);
    }
}
