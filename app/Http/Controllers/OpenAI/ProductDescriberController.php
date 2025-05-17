<?php

namespace App\Http\Controllers\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenAI\Generators\ProductDescriber;

class ProductDescriberController extends Controller
{
    public function __construct(protected ProductDescriber $writer) {}

    public function generate(Request $request)
    {
        $request->validate([
            'product' => 'required|string',
        ]);

        return response()->json([
            'description' => $this->writer->generate($request->product)
        ]);
    }
}
