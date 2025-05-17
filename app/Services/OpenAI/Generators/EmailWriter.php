<?php

namespace App\Services\OpenAI\Generators;

use App\Services\OpenAI\OpenAiService;

class EmailWriter
{
    public function __construct(protected OpenAiService $ai) {}

    public function generate(string $leadInfo, string $offer): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a sales expert writing cold outreach emails.'],
            ['role' => 'user', 'content' => "Write a sales email to this lead:\n\n$leadInfo\n\nOffer:\n$offer"],
        ];

        return $this->ai->chat($messages);
    }
}
