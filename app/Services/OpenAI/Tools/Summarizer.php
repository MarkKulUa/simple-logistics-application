<?php

namespace App\Services\OpenAI\Tools;

use App\Services\OpenAI\OpenAiService;

class Summarizer
{
    public function __construct(protected OpenAiService $ai) {}

    public function summarize(string $text): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You summarize long documents into clear and concise overviews.'],
            ['role' => 'user', 'content' => "Summarize the following:\n\n$text"],
        ];

        return $this->ai->chat($messages);
    }
}
