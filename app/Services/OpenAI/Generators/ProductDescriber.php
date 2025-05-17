<?php

namespace App\Services\OpenAI\Generators;

use App\Services\OpenAI\OpenAiService;

class ProductDescriber
{
    public function __construct(protected OpenAiService $ai) {}

    public function generate(string $productInfo): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You write engaging product descriptions for e-commerce platforms.'],
            ['role' => 'user', 'content' => "Write a product description for:\n$productInfo"],
        ];

        return $this->ai->chat($messages);
    }
}
