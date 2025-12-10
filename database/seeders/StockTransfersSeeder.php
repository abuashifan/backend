<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockCard;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockTransfersSeeder extends Seeder
{
    public function run(): void
    {
        if (Warehouse::count() < 2 || ! Product::exists()) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $product = Product::inRandomOrder()->first();
            $fromWarehouse = Warehouse::inRandomOrder()->first();
            $toWarehouse = Warehouse::where('id', '!=', $fromWarehouse->id)->inRandomOrder()->first();

            if (! $product || ! $fromWarehouse || ! $toWarehouse) {
                continue;
            }

            $transfer = StockTransfer::factory()->make([
                'product_id' => $product->id,
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $toWarehouse->id,
                'quantity' => fake()->numberBetween(1, 30),
                'transfer_date' => now()->subDays(fake()->numberBetween(0, 60)),
            ]);

            $transfer->save();

            StockCard::create([
                'product_id' => $product->id,
                'warehouse_id' => $fromWarehouse->id,
                'trx_date' => $transfer->transfer_date,
                'reference_type' => 'stock_transfer',
                'reference_id' => $transfer->id,
                'qty_in' => 0,
                'qty_out' => $transfer->quantity,
                'balance_qty' => 0,
                'notes' => 'Transfer out',
            ]);

            StockCard::create([
                'product_id' => $product->id,
                'warehouse_id' => $toWarehouse->id,
                'trx_date' => $transfer->transfer_date,
                'reference_type' => 'stock_transfer',
                'reference_id' => $transfer->id,
                'qty_in' => $transfer->quantity,
                'qty_out' => 0,
                'balance_qty' => $transfer->quantity,
                'notes' => 'Transfer in',
            ]);
        }
    }
}
