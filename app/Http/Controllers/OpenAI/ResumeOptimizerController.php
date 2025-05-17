<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Generators\ResumeOptimizer;

class ResumeOptimizerController extends Controller
{
    public function __construct(protected ResumeOptimizer $optimizer) {}

    public function optimize(Request $request)
    {
        $request->validate([
            'resume' => 'required|string',
            'job' => 'required|string',
        ]);

        return response()->json([
            'optimized' => $this->optimizer->optimize($request->resume, $request->job)
        ]);
    }
}
