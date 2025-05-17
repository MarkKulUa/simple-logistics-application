<?php

namespace App\Services\OpenAI\Chatbots;

use App\Services\OpenAI\OpenAiService;

class SupportBot
{
    public function __construct(protected OpenAiService $ai) {}

    public function answer(string $question): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful support agent for an e-commerce platform. Answer questions politely and clearly.'],
            ['role' => 'user', 'content' => $question],
        ];

        return $this->ai->chat($messages);
    }
}
