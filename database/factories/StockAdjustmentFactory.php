<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockAdjustmentFactory extends Factory
{
    protected $model = StockAdjustment::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'adjustment_date' => $this->faker->date(),
            'quantity_difference' => $this->faker->randomFloat(4, -500, 500),
            'reason' => $this->faker->sentence(),
            'approved_by' => $this->faker->optional()->name(),
        ];
    }
}
