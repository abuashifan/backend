<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\StockCard;
use App\Models\Warehouse;
use DomainException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function getCurrentStock(int $productId, int $warehouseId): float
    {
        $balance = StockCard::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->selectRaw('COALESCE(SUM(qty_in - qty_out), 0) as balance')
            ->value('balance');

        return (float) ($balance ?? 0);
    }

    public function increaseStock(
        int $productId,
        int $warehouseId,
        float $quantity,
        string $referenceType,
        int $referenceId,
        ?string $notes = null
    ): StockCard {
        $currentBalance = $this->getCurrentStock($productId, $warehouseId);
        $balanceQty = $currentBalance + $quantity;

        return StockCard::create([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'trx_date' => Carbon::now(),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'qty_in' => $quantity,
            'qty_out' => 0,
            'balance_qty' => $balanceQty,
            'notes' => $notes,
        ]);
    }

    public function decreaseStock(
        int $productId,
        int $warehouseId,
        float $quantity,
        string $referenceType,
        int $referenceId,
        ?string $notes = null,
        bool $allowNegative = false
    ): StockCard {
        $currentBalance = $this->getCurrentStock($productId, $warehouseId);

        if (!$allowNegative && $currentBalance < $quantity) {
            throw new DomainException('Insufficient stock to complete the transaction.');
        }

        $balanceQty = $currentBalance - $quantity;

        return StockCard::create([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'trx_date' => Carbon::now(),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'qty_in' => 0,
            'qty_out' => $quantity,
            'balance_qty' => $balanceQty,
            'notes' => $notes,
        ]);
    }

    public function adjustStock(
        int $productId,
        int $warehouseId,
        float $quantityDifference,
        string $referenceType,
        int $referenceId,
        ?string $notes = null
    ): StockCard {
        if ($quantityDifference === 0.0) {
            throw new DomainException('Quantity difference must not be zero.');
        }

        if ($quantityDifference > 0) {
            return $this->increaseStock(
                $productId,
                $warehouseId,
                $quantityDifference,
                $referenceType,
                $referenceId,
                $notes
            );
        }

        return $this->decreaseStock(
            $productId,
            $warehouseId,
            abs($quantityDifference),
            $referenceType,
            $referenceId,
            $notes
        );
    }

    public function transferStock(
        int $productId,
        int $fromWarehouseId,
        int $toWarehouseId,
        float $quantity,
        string $referenceType,
        int $referenceId,
        ?string $notes = null
    ): array {
        return DB::transaction(function () use (
            $productId,
            $fromWarehouseId,
            $toWarehouseId,
            $quantity,
            $referenceType,
            $referenceId,
            $notes
        ) {
            $outCard = $this->decreaseStock(
                $productId,
                $fromWarehouseId,
                $quantity,
                $referenceType,
                $referenceId,
                $notes
            );

            $inCard = $this->increaseStock(
                $productId,
                $toWarehouseId,
                $quantity,
                $referenceType,
                $referenceId,
                $notes
            );

            return [
                'out' => $outCard,
                'in' => $inCard,
            ];
        });
    }
}
