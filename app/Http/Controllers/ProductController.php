<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $limit = intval($request->get('_limit')) ?? 3;

        return ProductResource::collection(Product::take($limit)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return ProductResource
     */
    public function store(StoreProductRequest $request)
    {
//        $this->authorize('product_create');

        $model = Product::create($request->validated());

        WarehouseProduct::insert([
            'warehouse_id' => $request->warehouse_id,
            'product_id' => $request->product_id,
            'quantity_in_stock' => $request->quantity_in_stock,
        ]);

        return new ProductResource($model);
    }
}
