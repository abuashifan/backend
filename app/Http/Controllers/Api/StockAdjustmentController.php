<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockAdjustmentRequest;
use App\Http\Requests\UpdateStockAdjustmentRequest;
use App\Http\Resources\StockAdjustmentResource;
use App\Models\StockAdjustment;
use App\Services\StockAdjustmentService;
use Illuminate\Http\Response;

class StockAdjustmentController extends Controller
{
    public function __construct(private readonly StockAdjustmentService $stockAdjustmentService)
    {
    }

    public function index()
    {
        $adjustments = StockAdjustment::with(['product', 'warehouse'])->paginate();

        return $this->successResponse([
            'items' => StockAdjustmentResource::collection($adjustments),
            'meta' => [
                'current_page' => $adjustments->currentPage(),
                'per_page' => $adjustments->perPage(),
                'last_page' => $adjustments->lastPage(),
                'total' => $adjustments->total(),
            ],
        ], 'Stock adjustments retrieved.');
    }

    public function store(StoreStockAdjustmentRequest $request)
    {
        $adjustment = $this->stockAdjustmentService->createAdjustment($request->validated());

        return $this->successResponse(
            new StockAdjustmentResource($adjustment->loadMissing(['product', 'warehouse'])),
            'Stock adjustment created.',
            Response::HTTP_CREATED
        );
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        return $this->successResponse(
            new StockAdjustmentResource($stockAdjustment->loadMissing(['product', 'warehouse'])),
            'Stock adjustment retrieved.'
        );
    }

    public function update(UpdateStockAdjustmentRequest $request, StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->update($request->validated());

        return $this->successResponse(
            new StockAdjustmentResource($stockAdjustment->loadMissing(['product', 'warehouse'])),
            'Stock adjustment updated.'
        );
    }
}
