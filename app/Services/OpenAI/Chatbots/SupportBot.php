<?php

namespace App\Services\OpenAI\Chatbots;

use App\Services\OpenAI\OpenAiService;

class SupportBot
{
    public function __construct(protected OpenAiService $ai) {}

    /**
     * @param array $messages - Chat history including system/user/assistant roles
     * @return string|null
     */
    public function answer(array $messages): ?string
    {
        return $this->ai->chat($messages);
    }
}
