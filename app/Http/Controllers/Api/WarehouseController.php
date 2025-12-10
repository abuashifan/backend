<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::paginate();

        return $this->successResponse([
            'items' => WarehouseResource::collection($warehouses),
            'meta' => [
                'current_page' => $warehouses->currentPage(),
                'per_page' => $warehouses->perPage(),
                'last_page' => $warehouses->lastPage(),
                'total' => $warehouses->total(),
            ],
        ], 'Warehouses retrieved.');
    }

    public function store(StoreWarehouseRequest $request)
    {
        $warehouse = Warehouse::create($request->validated());

        return $this->successResponse(
            new WarehouseResource($warehouse),
            'Warehouse created.',
            Response::HTTP_CREATED
        );
    }

    public function show(Warehouse $warehouse)
    {
        return $this->successResponse(new WarehouseResource($warehouse), 'Warehouse retrieved.');
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());

        return $this->successResponse(new WarehouseResource($warehouse), 'Warehouse updated.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return $this->successResponse(null, 'Warehouse deleted.');
    }
}
