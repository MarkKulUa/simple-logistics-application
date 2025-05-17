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
        $validated = $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.role' => 'required|string|in:system,user,assistant',
            'messages.*.content' => 'required|string',
        ]);

        try {
            $answer = $this->bot->answer($validated['messages']);

            if (! $answer) {
                Log::warning('SupportBot returned empty response', [
                    'messages' => $validated['messages'],
                ]);
            }

            return response()->json(['answer' => $answer]);
        } catch (\Throwable $e) {
            Log::error('SupportBot error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to get AI response.'], 500);
        }
    }
}
