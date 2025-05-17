<?php

namespace App\Services\OpenAI;

use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Throwable;

class OpenAiService
{
    /**
     * Send a message array to OpenAI's Chat API and return the response.
     *
     *  gpt-4o-mini-audio-preview,
     *  gpt-4o-mini-realtime-preview,
     *  gpt-4o-mini-realtime-preview-2024-12-17,
     *  gpt-4o-mini-search-preview,
     *  o3-mini-2025-01-31,
     *  gpt-4o-mini-search-preview-2025-03-11,
     *  o1-mini,
     *  gpt-4o-mini-tts,
     *  gpt-4o-mini-2024-07-18,
     *  gpt-4.1-mini,
     *  gpt-4o-mini,
     *  gpt-4o-mini-audio-preview-2024-12-17,
     *  gpt-4o-mini-transcribe,
     *  gpt-4.1-mini-2025-04-14,
     *  o3-mini,
     *  o4-mini-2025-04-16,
     *  o4-mini,
     *  codex-mini-latest
     *
     * @param array $messages Chat messages array
     * @param string $model OpenAI model ID
     * @param int $timeout Timeout in seconds
     * @return string|null
     */
    public function chat(array $messages, string $model = 'gpt-4o-mini', int $timeout = 30): ?string
    {
        try {
            /** @var CreateResponse $response */
            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'timeout' => $timeout,
            ]);

            $content = $response->choices[0]->message->content ?? null;

            if (! $content) {
                Log::warning('OpenAI returned empty content', [
                    'model' => $model,
                    'messages' => $messages,
                ]);
            }

            return $content;
        } catch (Throwable $e) {
            Log::error('OpenAI request failed', [
                'error' => $e->getMessage(),
                'model' => $model,
                'messages' => $messages
            ]);

            return null;
        }
    }
}
