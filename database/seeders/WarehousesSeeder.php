<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehousesSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            ['code' => 'MAIN', 'name' => 'Main Warehouse'],
            ['code' => 'OUTLET', 'name' => 'Outlet Store'],
            ['code' => 'ONLINE', 'name' => 'Online Warehouse'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate(
                ['code' => $warehouse['code']],
                ['name' => $warehouse['name']]
            );
        }
    }
}
