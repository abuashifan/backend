<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    public function __construct(private readonly InventoryService $inventory)
    {
    }

    public function createTransfer(array $data): StockTransfer
    {
        return DB::transaction(function () use ($data) {
            $product = Product::find($data['product_id'] ?? null);
            $fromWarehouse = Warehouse::find($data['from_warehouse_id'] ?? null);
            $toWarehouse = Warehouse::find($data['to_warehouse_id'] ?? null);

            if (!$product || !$fromWarehouse || !$toWarehouse) {
                throw new DomainException('Invalid product or warehouse provided.');
            }

            if ($fromWarehouse->id === $toWarehouse->id) {
                throw new DomainException('Source and destination warehouses must be different.');
            }

            $transferDate = match (true) {
                ($data['transfer_date'] ?? null) instanceof Carbon => $data['transfer_date'],
                isset($data['transfer_date']) && $data['transfer_date'] !== null => Carbon::parse($data['transfer_date']),
                default => Carbon::now(),
            };

            $transfer = StockTransfer::create([
                'product_id' => $product->id,
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $toWarehouse->id,
                'transfer_date' => $transferDate,
                'quantity' => (float) ($data['quantity'] ?? 0),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->inventory->transferStock(
                $product->id,
                $fromWarehouse->id,
                $toWarehouse->id,
                (float) ($data['quantity'] ?? 0),
                'stock_transfer',
                $transfer->id,
                $data['notes'] ?? null
            );

            return $transfer->loadMissing(['product', 'fromWarehouse', 'toWarehouse']);
        });
    }
}
