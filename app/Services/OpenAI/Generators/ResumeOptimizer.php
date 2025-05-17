<?php

namespace App\Services\OpenAI\Generators;

use App\Services\OpenAI\OpenAiService;

class ResumeOptimizer
{
    public function __construct(protected OpenAiService $ai) {}

    public function optimize(string $resumeText, string $jobDescription): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are an expert in writing and optimizing resumes.'],
            ['role' => 'user', 'content' => "Optimize this resume:\n\n$resumeText\n\nfor the job:\n\n$jobDescription"],
        ];

        return $this->ai->chat($messages);
    }
}
