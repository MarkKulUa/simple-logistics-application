<?php

namespace App\Services\OpenAI;

use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Throwable;

class OpenAiService
{
    const MAX_HISTORY_MESSAGES = 12; // 6 exchanges (user + assistant)

    /**
     * Compress history if too long and return updated message list.
     *
     * @param array $messages
     * @return array
     */
    protected function trimOrSummarize(array $messages): array
    {
        if (count($messages) <= self::MAX_HISTORY_MESSAGES) {
            return $messages;
        }

        try {
            $summaryResponse = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => array_merge(
                    [
                        ['role' => 'system', 'content' => 'Summarize this conversation in 2-3 sentences.']
                    ],
                    array_slice($messages, 1, self::MAX_HISTORY_MESSAGES) // skip system message
                ),
                'temperature' => 0.3,
            ]);

            $summary = $summaryResponse->choices[0]->message->content ?? null;

            return [
                $messages[0], // original system prompt
                ['role' => 'user', 'content' => 'Summary of previous conversation: ' . $summary],
                end($messages) // last user message
            ];
        } catch (Throwable $e) {
            Log::error('Failed to summarize conversation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // fallback: return last 6 messages
            return array_merge(
                [$messages[0]],
                array_slice($messages, -self::MAX_HISTORY_MESSAGES)
            );
        }
    }

    public function chat(array $messages, string $model = 'gpt-4o-mini'): ?string
    {
        try {
            $trimmedMessages = $this->trimOrSummarize($messages);

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => $trimmedMessages,
                'temperature' => 0.7,
            ]);

            $content = $response->choices[0]->message->content ?? null;

            if (! $content) {
                Log::warning('OpenAI returned empty content', [
                    'model' => $model,
                    'messages' => $trimmedMessages,
                ]);
            }

            return $content;
        } catch (Throwable $e) {
            Log::error('OpenAI request failed', [
                'error' => $e->getMessage(),
                'model' => $model,
                'messages' => $messages,
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}
