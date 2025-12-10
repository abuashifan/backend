<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\Response;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchaseService)
    {
    }

    public function index()
    {
        $purchases = Purchase::with(['supplier', 'warehouse', 'tax', 'accountPayable'])->paginate();

        return $this->successResponse([
            'items' => PurchaseResource::collection($purchases),
            'meta' => [
                'current_page' => $purchases->currentPage(),
                'per_page' => $purchases->perPage(),
                'last_page' => $purchases->lastPage(),
                'total' => $purchases->total(),
            ],
        ], 'Purchases retrieved.');
    }

    public function store(StorePurchaseRequest $request)
    {
        $purchase = $this->purchaseService->createPurchase($request->validated());

        return $this->successResponse(new PurchaseResource($purchase), 'Purchase created.', Response::HTTP_CREATED);
    }

    public function show(Purchase $purchase)
    {
        return $this->successResponse(
            new PurchaseResource($purchase->loadMissing(['supplier', 'warehouse', 'tax', 'accountPayable'])),
            'Purchase retrieved.'
        );
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        $purchase->update($request->validated());

        return $this->successResponse(
            new PurchaseResource($purchase->loadMissing(['supplier', 'warehouse', 'tax', 'accountPayable'])),
            'Purchase updated.'
        );
    }
}
