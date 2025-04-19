<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use Illuminate\Http\Client\Response;

class FioBankService
{
    protected string $baseUrl = 'https://fioapi.fio.cz/v1/rest';
    private const array ALLOWED_FORMATS = ['xml', 'json'];
    private const int REQUEST_TIMEOUT = 10;


    public function __construct(
        private readonly string $token,
        private string $format = self::ALLOWED_FORMATS[0],
        private readonly Http $http
    ) {
        $this->validateFormat($format);
        $this->validateToken($token);
    }

    public function fetchTransactions(Carbon $from, Carbon $to): ?string
    {
        $this->validateDateRange($from, $to);

        $url = "{$this->baseUrl}/periods/{$this->token}/{$from->format('Y-m-d')}/{$to->format('Y-m-d')}/transactions.{$this->format}";

        return $this->makeRequest($url);
    }

    public function fetchLatestTransactions(): ?string
    {
        $url = "{$this->baseUrl}/last/{$this->token}/transactions.{$this->format}";

        return $this->makeRequest($url);
    }

    public function setLastTransactionId(int $id): ?string
    {
        $url = "{$this->baseUrl}/set-last-id/{$this->token}/{$id}/";

        return $this->makeRequest($url);
    }

    public function setLastTransactionDate(string $date): ?string
    {
        $url = "{$this->baseUrl}/set-last-date/{$this->token}/{$date}/";

        return $this->makeRequest($url);
    }

    public function fetchStatementById(int $year, int $id): ?string
    {
        $url = "{$this->baseUrl}/by-id/{$this->token}/{$year}/{$id}/transactions.{$this->format}";

        return $this->makeRequest($url);
    }

    public function fetchLastStatementNumber(): ?string
    {
        $url = "{$this->baseUrl}/lastStatement/{$this->token}/statement";

        return $this->makeRequest($url);
    }

    protected function makeRequest(string $url): ?string
    {
        try {
            $response = $this->http
                ->timeout(self::REQUEST_TIMEOUT)
                ->get($url);

            return $this->handleResponse($response, $url);
        } catch (Exception $e) {
            $this->logError($e->getMessage(), $url);
            return null;
        }
    }

    private function handleResponse(Response $response, string $url): ?string
    {
        if (!$response->successful()) {
            $this->logError('Request failed', $url, [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->body();
    }

    private function logError(string $message, string $url, array $context = []): void
    {
        Log::error(
            sprintf('FioBankService: %s', $message),
            array_merge(['url' => $url], $context)
        );
    }

    public function setFormat(string $format): self
    {
        $this->validateFormat($format);
        $this->format = $format;
        return $this;
    }

    private function validateFormat(string $format): void
    {
        if (!in_array($format, self::ALLOWED_FORMATS, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid format "%s". Allowed formats are: %s',
                    $format,
                    implode(', ', self::ALLOWED_FORMATS)
                )
            );
        }
    }

    private function validateDateRange(DateTimeInterface $from, DateTimeInterface $to): void
    {
        if ($from > $to) {
            throw new InvalidArgumentException('Start date must be before or equal to end date');
        }

        $maxRange = (clone $from)->modify('+31 days');
        if ($to > $maxRange) {
            throw new InvalidArgumentException('Date range cannot exceed 31 days');
        }
    }

    private function validateToken(string $token): void
    {
        if (empty($token)) {
            throw new InvalidArgumentException('API token cannot be empty');
        }
    }
}
