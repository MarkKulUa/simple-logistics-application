<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Chatbots\SupportBot;
use Illuminate\Support\Facades\Log;

class SupportBotController extends Controller
{
    public function __construct(protected SupportBot $bot) {}

    public function ask(Request $request)
    {
        $request->validate(['question' => 'required|string']);

        $question = $request->input('question');

        try {
            $answer = $this->bot->answer($question);

            if (!trim($answer)) {
                Log::warning('SupportBot returned empty response.', ['question' => $question]);
            }

            return response()->json(['answer' => $answer]);
        } catch (\Throwable $e) {
            Log::error('SupportBot error', [
                'question' => $question,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
}
