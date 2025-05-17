<?php

namespace App\Services\OpenAI;

use Illuminate\Support\Facades\Http;

class OpenAiService
{
    public function chat(array $messages, string $model = 'gpt-4'): string
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post(config('services.openai.chat_endpoint'), [
                'model' => $model,
                'messages' => $messages,
            ]);

        return $response['choices'][0]['message']['content'] ?? '';
    }
}
