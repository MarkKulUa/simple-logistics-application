<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return SupplierResource::collection(Supplier::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return SupplierResource
     */
    public function store(StoreSupplierRequest $request)
    {
        $this->authorize('warehouse_create');

        $model = Supplier::create($request->validated());

        return new SupplierResource($model);
    }
}
