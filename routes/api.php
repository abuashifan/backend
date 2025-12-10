<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\StockAdjustmentController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('sales', SaleController::class)->only(['index', 'store', 'show', 'update']);
    Route::apiResource('purchases', PurchaseController::class)->only(['index', 'store', 'show', 'update']);
    Route::apiResource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'store', 'show', 'update']);
    Route::apiResource('stock-transfers', StockTransferController::class)->only(['index', 'store', 'show', 'update']);
});
