<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Tools\LanguageCoach;

class LanguageCoachController extends Controller
{
    public function __construct(protected LanguageCoach $coach) {}

    public function practice(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'lang' => 'nullable|string',
        ]);

        return response()->json([
            'response' => $this->coach->practice(
                $request->message,
                $request->get('lang', 'English')
            )
        ]);
    }
}
