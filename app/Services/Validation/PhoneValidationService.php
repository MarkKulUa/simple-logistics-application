<?php

namespace App\Services\Validation;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhoneValidationService
{
    protected int $maxRetries = 3;
    protected int $timeoutSeconds = 10;

    /**
     * Normalize phone number to E.164 format: remove non-digits, prepend '+'
     */
    protected function normalizePhoneNumber(string $raw): string
    {
        $digitsOnly = preg_replace('/\D+/', '', $raw);
        return '+' . ltrim($digitsOnly, '0'); // remove leading zeroes
    }

    /**
     * Validate phone number via Real Validito API with retry + timeout
     */
    public function validate(string $rawPhone, int $attempt = 1): ?array
    {
        $phone = $this->normalizePhoneNumber($rawPhone);

        if (!preg_match('/^\+\d{8,15}$/', $phone)) {
            Log::info("Skipped invalid format: {$rawPhone} → {$phone}");
            return [
                'valid' => false,
                'carrier' => null,
                'type' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.validito.token'),
            ])
                ->timeout($this->timeoutSeconds)
                ->get(config('services.validito.endpoint') . '/validate', [
                    'number' => $phone,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'valid' => $data['valid'] ?? false,
                    'carrier' => $data['carrier']['name'] ?? null,
                    'type' => $data['carrier']['type'] ?? null,
                ];
            }

            Log::warning("Validito API error (attempt {$attempt}): " . $response->body());
        } catch (\Throwable $e) {
            Log::error("Validito request failed (attempt {$attempt}): " . $e->getMessage());
        }

        if ($attempt < $this->maxRetries) {
            sleep(pow(2, $attempt - 1)); // exponential backoff: 1s, 2s, 4s
            return $this->validate($rawPhone, $attempt + 1);
        }

        return null;
    }
}
