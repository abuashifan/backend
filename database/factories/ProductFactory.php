<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => strtoupper($this->faker->unique()->bothify('PRD-#####')),
            'name' => $this->faker->words(3, true),
            'product_category_id' => ProductCategory::factory(),
            'product_unit_id' => ProductUnit::factory(),
            'default_tax_id' => $this->faker->boolean(70) ? Tax::factory() : null,
            'description' => $this->faker->optional()->sentence(),
            'cost_price' => $this->faker->randomFloat(2, 1000, 100000),
            'selling_price' => $this->faker->randomFloat(2, 1000, 100000),
        ];
    }
}
