<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::factory()->create(['name' => 'Main Warehouse']);
        $product = Product::factory()->create();
        $customer = Customer::factory()->create();
        $tax = Tax::factory()->create();

        /** @var InventoryService $inventoryService */
        $inventoryService = app(InventoryService::class);

        $inventoryService->increaseStock(
            $product->id,
            $warehouse->id,
            100,
            'initial-stock',
            0,
            'Seeded starting inventory'
        );

        // Ensure related records are available for tests relying on seeded data
        $customer->tax_id = $tax->id;
        $customer->save();
    }
}
