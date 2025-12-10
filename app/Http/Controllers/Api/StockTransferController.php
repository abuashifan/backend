<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Requests\UpdateStockTransferRequest;
use App\Http\Resources\StockTransferResource;
use App\Models\StockTransfer;
use App\Services\StockTransferService;
use Illuminate\Http\Response;

class StockTransferController extends Controller
{
    public function __construct(private readonly StockTransferService $stockTransferService)
    {
    }

    public function index()
    {
        $transfers = StockTransfer::with(['product', 'fromWarehouse', 'toWarehouse'])->paginate();

        return $this->successResponse([
            'items' => StockTransferResource::collection($transfers),
            'meta' => [
                'current_page' => $transfers->currentPage(),
                'per_page' => $transfers->perPage(),
                'last_page' => $transfers->lastPage(),
                'total' => $transfers->total(),
            ],
        ], 'Stock transfers retrieved.');
    }

    public function store(StoreStockTransferRequest $request)
    {
        $transfer = $this->stockTransferService->createTransfer($request->validated());

        return $this->successResponse(
            new StockTransferResource($transfer),
            'Stock transfer created.',
            Response::HTTP_CREATED
        );
    }

    public function show(StockTransfer $stockTransfer)
    {
        return $this->successResponse(
            new StockTransferResource($stockTransfer->loadMissing(['product', 'fromWarehouse', 'toWarehouse'])),
            'Stock transfer retrieved.'
        );
    }

    public function update(UpdateStockTransferRequest $request, StockTransfer $stockTransfer)
    {
        $stockTransfer->update($request->validated());

        return $this->successResponse(
            new StockTransferResource($stockTransfer->loadMissing(['product', 'fromWarehouse', 'toWarehouse'])),
            'Stock transfer updated.'
        );
    }
}
