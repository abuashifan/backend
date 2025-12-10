<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\Tax;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        ProductCategory::factory(5)->create();
        ProductUnit::factory(5)->create();
        Tax::factory(3)->create();

        Product::factory(50)->make()->each(function (Product $product) {
            $product->product_category_id = ProductCategory::inRandomOrder()->first()->id;
            $product->product_unit_id = ProductUnit::inRandomOrder()->first()->id;
            $product->default_tax_id = Tax::inRandomOrder()->first()->id ?? null;
            $product->save();
        });
    }
}
