<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Tools\Summarizer;

class SummarizerController extends Controller
{
    public function __construct(protected Summarizer $summarizer) {}

    public function summarize(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        return response()->json([
            'summary' => $this->summarizer->summarize($request->text)
        ]);
    }
}
