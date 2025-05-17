<?php

namespace App\Services\OpenAI\Tools;

use App\Services\OpenAI\OpenAiService;

class CodeReviewer
{
    public function __construct(protected OpenAiService $ai) {}

    public function review(string $code, string $language = 'PHP'): string
    {
        $messages = [
            ['role' => 'system', 'content' => "You are a senior $language developer. Provide code review and improvement suggestions."],
            ['role' => 'user', 'content' => "Review this code:\n\n$code"],
        ];

        return $this->ai->chat($messages);
    }
}
