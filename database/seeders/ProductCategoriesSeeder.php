<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'GENERAL', 'name' => 'General'],
            ['code' => 'FOOD', 'name' => 'Food & Beverage'],
            ['code' => 'ELECTRONIC', 'name' => 'Electronics'],
            ['code' => 'SERVICE', 'name' => 'Service'],
        ];

        ProductCategory::upsert($categories, ['code'], ['name']);
    }
}
