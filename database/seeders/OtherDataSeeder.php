<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OtherDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WarehousesSeeder::class,
            ProductUnitsSeeder::class,
            ProductCategoriesSeeder::class,
            TaxesSeeder::class,
            StockAdjustmentsSeeder::class,
            StockTransfersSeeder::class,
            JournalEntriesSeeder::class,
        ]);
    }
}
