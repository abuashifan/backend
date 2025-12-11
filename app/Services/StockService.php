<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StockCard;

class StockService
{
    public function __construct(private readonly InventoryService $inventory)
    {
    }

    /**
     * Increase stock via InventoryService.
     */
    public function increaseStock(int $productId, int $warehouseId, float $quantity, string $referenceType, int $referenceId, ?string $notes = null): StockCard
    {
        return $this->inventory->increaseStock($productId, $warehouseId, $quantity, $referenceType, $referenceId, $notes);
    }

    /**
     * Decrease stock via InventoryService.
     */
    public function decreaseStock(int $productId, int $warehouseId, float $quantity, string $referenceType, int $referenceId, ?string $notes = null, bool $allowNegative = false): StockCard
    {
        return $this->inventory->decreaseStock($productId, $warehouseId, $quantity, $referenceType, $referenceId, $notes, $allowNegative);
    }

    /**
     * Transfer stock between warehouses.
     *
     * Returns array with 'out' and 'in' StockCard entries.
     *
     * @return array{out:StockCard,in:StockCard}
     */
    public function transferStock(int $productId, int $fromWarehouseId, int $toWarehouseId, float $quantity, string $referenceType, int $referenceId, ?string $notes = null): array
    {
        return $this->inventory->transferStock($productId, $fromWarehouseId, $toWarehouseId, $quantity, $referenceType, $referenceId, $notes);
    }
}
