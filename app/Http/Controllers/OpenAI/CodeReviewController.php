<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Tools\CodeReviewer;

class CodeReviewController extends Controller
{
    public function __construct(protected CodeReviewer $reviewer) {}

    public function review(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'language' => 'nullable|string',
        ]);

        return response()->json([
            'review' => $this->reviewer->review(
                $request->code,
                $request->get('language', 'PHP')
            )
        ]);
    }
}
