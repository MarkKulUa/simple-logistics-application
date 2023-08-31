<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $limit = intval($request->get('_limit')) ?? 3;

        return SupplierResource::collection(Supplier::take($limit)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return SupplierResource
     */
    public function store(StoreSupplierRequest $request)
    {
//        $this->authorize('supplier_create');

        $model = Supplier::create($request->validated());

        return new SupplierResource($model);
    }
}
