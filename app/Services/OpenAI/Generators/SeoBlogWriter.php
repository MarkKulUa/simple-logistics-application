<?php

namespace App\Services\OpenAI\Generators;

use App\Services\OpenAI\OpenAiService;

class SeoBlogWriter
{
    public function __construct(protected OpenAiService $ai) {}

    public function generate(string $topic, array $keywords): string
    {
        $kw = implode(', ', $keywords);
        $messages = [
            ['role' => 'system', 'content' => 'You are a professional SEO blog writer.'],
            ['role' => 'user', 'content' => "Write a blog post on '$topic' using these keywords: $kw"],
        ];

        return $this->ai->chat($messages);
    }
}
