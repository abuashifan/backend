<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Response;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $saleService)
    {
    }

    public function index()
    {
        $sales = Sale::with(['customer', 'warehouse', 'tax', 'accountReceivable'])->paginate();

        return $this->successResponse([
            'items' => SaleResource::collection($sales),
            'meta' => [
                'current_page' => $sales->currentPage(),
                'per_page' => $sales->perPage(),
                'last_page' => $sales->lastPage(),
                'total' => $sales->total(),
            ],
        ], 'Sales retrieved.');
    }

    public function store(StoreSaleRequest $request)
    {
        $sale = $this->saleService->createSale($request->validated());

        return $this->successResponse(new SaleResource($sale), 'Sale created.', Response::HTTP_CREATED);
    }

    public function show(Sale $sale)
    {
        return $this->successResponse(
            new SaleResource($sale->loadMissing(['customer', 'warehouse', 'tax', 'accountReceivable'])),
            'Sale retrieved.'
        );
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        $sale->update($request->validated());

        return $this->successResponse(
            new SaleResource($sale->loadMissing(['customer', 'warehouse', 'tax', 'accountReceivable'])),
            'Sale updated.'
        );
    }
}
