<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Chatbots\SupportBot;

class SupportBotController extends Controller
{
    public function __construct(protected SupportBot $bot) {}

    public function ask(Request $request)
    {
        $request->validate(['question' => 'required|string']);
        return response()->json([
            'answer' => $this->bot->answer($request->question)
        ]);
    }
}
