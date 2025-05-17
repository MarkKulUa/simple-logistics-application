<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Generators\EmailWriter;

class EmailWriterController extends Controller
{
    public function __construct(protected EmailWriter $writer) {}

    public function generate(Request $request)
    {
        $request->validate([
            'lead' => 'required|string',
            'offer' => 'required|string',
        ]);

        return response()->json([
            'email' => $this->writer->generate($request->lead, $request->offer)
        ]);
    }
}
