<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    public function __construct(
        private readonly InventoryService $inventory,
    ) {
    }

    public function createAdjustment(array $data): StockAdjustment
    {
        return DB::transaction(function () use ($data) {
            $product = Product::find($data['product_id'] ?? null);
            $warehouse = Warehouse::find($data['warehouse_id'] ?? null);

            if (!$product || !$warehouse) {
                throw new DomainException('Invalid product or warehouse provided.');
            }

            $quantityDifference = (float) ($data['quantity_difference'] ?? 0);

            if ($quantityDifference === 0.0) {
                throw new DomainException('Quantity difference must not be zero.');
            }

            $adjustmentDate = $data['adjustment_date'] ?? null;
            $adjustmentDate = $adjustmentDate instanceof Carbon
                ? $adjustmentDate
                : Carbon::parse($adjustmentDate ?? now());

            $adjustment = StockAdjustment::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'adjustment_date' => $adjustmentDate,
                'quantity_difference' => $quantityDifference,
                'reason' => $data['reason'] ?? '',
                'approved_by' => $data['approved_by'] ?? null,
            ]);

            $this->inventory->adjustStock(
                $product->id,
                $warehouse->id,
                $quantityDifference,
                'stock_adjustment',
                $adjustment->id,
                $data['reason'] ?? null
            );

            return $adjustment;
        });
    }
}
