<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $limit = intval($request->get('_limit')) ?? 3;

        return WarehouseResource::collection(Warehouse::take($limit)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return WarehouseResource
     */
    public function store(StoreWarehouseRequest $request)
    {
//        $this->authorize('warehouse_create');

        $model = Warehouse::create($request->validated());

        return new WarehouseResource($model);
    }
}
