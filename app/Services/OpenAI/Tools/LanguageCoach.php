<?php

namespace App\Services\OpenAI\Tools;

use App\Services\OpenAI\OpenAiService;

class LanguageCoach
{
    public function __construct(protected OpenAiService $ai) {}

    public function practice(string $message, string $lang = 'English'): string
    {
        $messages = [
            ['role' => 'system', 'content' => "You help users learn and practice $lang. Correct mistakes and explain."],
            ['role' => 'user', 'content' => $message],
        ];

        return $this->ai->chat($messages);
    }
}
