<?php

namespace Tests\Unit\Services\Payment;

use App\Services\Payment\FioBankService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Factory as HttpFactory;
use InvalidArgumentException;
use Tests\TestCase;

class FioBankServiceTest extends TestCase
{
    private FioBankService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $http = app(HttpFactory::class);
        $this->service = new FioBankService('test-token', 'xml', $http);
    }

    public function testConstructorWithInvalidFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        new FioBankService('token', 'unsupported', app(HttpFactory::class));
    }

    public function testConstructorWithEmptyToken()
    {
        $this->expectException(InvalidArgumentException::class);
        new FioBankService('', 'xml', app(HttpFactory::class));
    }

    public function testFetchTransactionsSuccessful()
    {
        Http::fake([
            '*' => Http::response('<xml>ok</xml>', 200),
        ]);

        $from = Carbon::now()->subDays(1);
        $to = Carbon::now();

        $response = $this->service->fetchTransactions($from, $to);
        $this->assertStringContainsString('ok', $response);
    }

    public function testFetchTransactionsWithInvalidDateRange()
    {
        $from = Carbon::now();
        $to = Carbon::now()->subDay();

        $this->expectException(InvalidArgumentException::class);
        $this->service->fetchTransactions($from, $to);
    }

    public function testFetchTransactionsExceedingMaxRange()
    {
        $from = Carbon::now();
        $to = Carbon::now()->addDays(40);

        $this->expectException(InvalidArgumentException::class);
        $this->service->fetchTransactions($from, $to);
    }

    public function testFetchLatestTransactions()
    {
        Http::fake([
            '*' => Http::response('latest-response', 200),
        ]);

        $response = $this->service->fetchLatestTransactions();
        $this->assertEquals('latest-response', $response);
    }

    public function testSetLastTransactionId()
    {
        Http::fake([
            '*' => Http::response('id-set', 200),
        ]);

        $response = $this->service->setLastTransactionId(123456);
        $this->assertEquals('id-set', $response);
    }

    public function testSetLastTransactionDate()
    {
        Http::fake([
            '*' => Http::response('date-set', 200),
        ]);

        $response = $this->service->setLastTransactionDate('2024-01-01');
        $this->assertEquals('date-set', $response);
    }

    public function testFetchStatementById()
    {
        Http::fake([
            '*' => Http::response('statement-data', 200),
        ]);

        $response = $this->service->fetchStatementById(2024, 5);
        $this->assertEquals('statement-data', $response);
    }

    public function testFetchLastStatementNumber()
    {
        Http::fake([
            '*' => Http::response('42', 200),
        ]);

        $response = $this->service->fetchLastStatementNumber();
        $this->assertEquals('42', $response);
    }

    public function testRequestFailureReturnsNull()
    {
        Http::fake([
            '*' => Http::response('fail', 500),
        ]);

        $this->assertNull($this->service->fetchLatestTransactions());
    }

    public function testRequestThrowsExceptionReturnsNull()
    {
        Http::fake([
            '*' => fn () => throw new \Exception('Network error'),
        ]);

        $this->assertNull($this->service->fetchLatestTransactions());
    }

    public function testSetFormatValidAndInvalid()
    {
        $result = $this->service->setFormat('json');
        $this->assertInstanceOf(FioBankService::class, $result);

        $this->expectException(InvalidArgumentException::class);
        $this->service->setFormat('bad');
    }
}
