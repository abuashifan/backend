<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockAdjustmentsSeeder extends Seeder
{
    public function run(): void
    {
        $productsExist = Product::exists();
        $warehousesExist = Warehouse::exists();

        if (! $productsExist || ! $warehousesExist) {
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            $product = Product::inRandomOrder()->first();
            $warehouse = Warehouse::inRandomOrder()->first();

            if (! $product || ! $warehouse) {
                continue;
            }

            $quantityDifference = fake()->randomFloat(2, -20, 20);

            while (abs($quantityDifference) < 0.01) {
                $quantityDifference = fake()->randomFloat(2, -20, 20);
            }

            $adjustment = StockAdjustment::factory()->make([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'quantity_difference' => $quantityDifference,
                'adjustment_date' => now()->subDays(fake()->numberBetween(0, 30)),
                'reason' => 'Stock opname adjustment',
            ]);

            $adjustment->save();
        }
    }
}
