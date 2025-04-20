<?php

namespace App\Services\Communication;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Twilio\TwiML\VoiceResponse;

class TwilioService
{
    private const CACHE_TTL = 3600;
    private const VOICE_LANGUAGE = 'en-US';
    private const VOICE_GENDER = 'woman';
    private const PAUSE_LENGTH = 2;
    protected Client $client;
    private static array $validCallStatuses = ['completed', 'canceled', 'failed'];

    public function __construct(
        protected string $accountSid = '',
        protected string $authToken = '',
        protected string $fromNumber = ''
    ) {
        $this->initializeCredentials();
        $this->validateCredentials();
        $this->initializeClient();
    }

    private function initializeCredentials(): void
    {
        $this->accountSid = $this->accountSid ?: Config::get('services.twilio.account_sid');
        $this->authToken = $this->authToken ?: Config::get('services.twilio.auth_token');
        $this->fromNumber = $this->fromNumber ?: Config::get('services.twilio.from_number');
    }

    private function validateCredentials(): void
    {
        if (!$this->accountSid || !$this->authToken || !$this->fromNumber) {
            throw new InvalidArgumentException('Twilio credentials are not properly configured.');
        }
    }

    private function initializeClient(): void
    {
        $this->client = new Client($this->accountSid, $this->authToken);
    }

    public function createSubAccount(string $friendlyName): string
    {
        try {
            $account = $this->client->accounts->create(['FriendlyName' => $friendlyName]);
            return $account->sid;
        } catch (Throwable $e) {
            Log::error('Twilio Create SubAccount Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAccountDetails(): object
    {
        $cacheKey = "twilio_account_{$this->accountSid}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->client->api->v2010->accounts($this->accountSid)->fetch();
        });
    }

    public function deleteMessage(string $sid): bool
    {
        try {
            $result = $this->client->messages($sid)->delete();
            Cache::forget("twilio_message_{$sid}");
            return $result;
        } catch (Throwable $e) {
            Log::error("Failed to delete message {$sid}: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteCall(string $sid): bool
    {
        try {
            $result = $this->client->calls($sid)->delete();
            Cache::forget("twilio_call_{$sid}");
            return $result;
        } catch (Throwable $e) {
            Log::error("Failed to delete call {$sid}: " . $e->getMessage());
            throw $e;
        }
    }

    public function hangUpCall(string $callSid): bool
    {
        try {
            $call = $this->client->calls($callSid)->update(["status" => "completed"]);
            Cache::forget("twilio_call_{$callSid}");
            return $call->status === 'completed';
        } catch (Throwable $e) {
            Log::error("Failed to hang up call {$callSid}: " . $e->getMessage());
            throw $e;
        }
    }

    public function generateTwiml(string $customerName, string $numberTo, ?int $maxDuration = null): string
    {
        try {
            $twiml = new VoiceResponse();
            $twiml->pause(['length' => self::PAUSE_LENGTH]);

            $twiml->say(
                $this->generateCustomerMessage($customerName),
                $this->getVoiceOptions()
            );

            $twiml->dial($numberTo, $this->getDialOptions($maxDuration));

            return $twiml->__toString();
        } catch (Throwable $e) {
            Log::error('TwiML Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateCustomerMessage(string $customerName): string
    {
        return "You are being connected to customer {$customerName}";
    }

    private function getVoiceOptions(): array
    {
        return [
            'voice' => self::VOICE_GENDER,
            'language' => self::VOICE_LANGUAGE
        ];
    }

    private function getDialOptions(?int $maxDuration): array
    {
        $options = ['record' => 'record-from-ringing-dual'];

        if ($maxDuration !== null) {
            $options['timeLimit'] = $maxDuration * 60;
        }

        return $options;
    }

    public function sendSms(string $to, string $message): string
    {
        $this->validatePhoneNumber($to);

        try {
            $sms = $this->client->messages->create($to, [
                'from' => $this->fromNumber,
                'body' => $message
            ]);
            return $sms->sid;
        } catch (Throwable $e) {
            Log::error('Twilio SMS Error: ' . $e->getMessage(), [
                'to' => $to,
                'message_length' => strlen($message)
            ]);
            throw $e;
        }
    }

    public function makeCall(string $to, string $twimlUrl): string
    {
        $this->validatePhoneNumber($to);
        $this->validateUrl($twimlUrl);

        try {
            $call = $this->client->calls->create($to, $this->fromNumber, [
                'url' => $twimlUrl
            ]);
            return $call->sid;
        } catch (Throwable $e) {
            Log::error('Twilio Call Error: ' . $e->getMessage(), [
                'to' => $to,
                'twiml_url' => $twimlUrl
            ]);
            throw $e;
        }
    }

    public function listMessages(array $filters = []): array
    {
        $cacheKey = 'twilio_messages_' . md5(serialize($filters));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
            return $this->client->messages->read($filters);
        });
    }

    public function getMessage(string $sid): object
    {
        $cacheKey = "twilio_message_{$sid}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($sid) {
            return $this->client->messages($sid)->fetch();
        });
    }

    public function listCalls(array $filters = []): array
    {
        $cacheKey = 'twilio_calls_' . md5(serialize($filters));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
            return $this->client->calls->read($filters);
        });
    }

    public function getCall(string $sid): object
    {
        $cacheKey = "twilio_call_{$sid}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($sid) {
            return $this->client->calls($sid)->fetch();
        });
    }

    public function listPhoneNumbers(): array
    {
        return Cache::remember('twilio_phone_numbers', self::CACHE_TTL, function () {
            return $this->client->incomingPhoneNumbers->read();
        });
    }

    public function downloadRecording(string $callSid): ?string
    {
        try {
            $recordings = $this->client->calls($callSid)->recordings->read();

            if (empty($recordings)) {
                return null;
            }

            $recordingSid = $recordings[0]->sid;
            $url = sprintf(
                'https://api.twilio.com/2010-04-01/Accounts/%s/Recordings/%s.wav',
                $this->client->getAccountSid(),
                $recordingSid
            );

            $response = Http::withBasicAuth(
                $this->client->getAccountSid(),
                $this->client->getAuthToken()
            )->get($url);

            return $response->successful() ? $response->body() : null;
        } catch (\Exception $e) {
            Log::error('Recording download error: ' . $e->getMessage());
            return null;
        }
    }

    private function validatePhoneNumber(string $number): void
    {
        if (!preg_match('/^\+[1-9]\d{1,14}$/', $number)) {
            throw new InvalidArgumentException('Invalid phone number format. Must be E.164 format.');
        }
    }

    private function validateUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }
    }
}
