<?php

namespace App\Services\OpenAI\Chatbots;

use App\Services\OpenAI\OpenAiService;

class InterviewSimulator
{
    public function __construct(protected OpenAiService $ai) {}

    public function simulate(string $role): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a technical interviewer for the role of ' . $role . '. Ask the candidate challenging questions.'],
            ['role' => 'user', 'content' => 'Let`s begin the interview.'],
        ];

        return $this->ai->chat($messages);
    }
}
