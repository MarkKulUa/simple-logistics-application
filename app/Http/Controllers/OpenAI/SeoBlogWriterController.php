<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Generators\SeoBlogWriter;

class SeoBlogWriterController extends Controller
{
    public function __construct(protected SeoBlogWriter $writer) {}

    public function generate(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'keywords' => 'required|array',
        ]);

        return response()->json([
            'blog' => $this->writer->generate($request->topic, $request->keywords)
        ]);
    }
}
