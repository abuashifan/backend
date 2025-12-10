<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockTransferFactory extends Factory
{
    protected $model = StockTransfer::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'from_warehouse_id' => Warehouse::factory(),
            'to_warehouse_id' => Warehouse::factory(),
            'transfer_date' => $this->faker->date(),
            'quantity' => $this->faker->randomFloat(4, 1, 500),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
