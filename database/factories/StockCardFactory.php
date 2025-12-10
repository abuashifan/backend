<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockCard;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockCardFactory extends Factory
{
    protected $model = StockCard::class;

    public function definition(): array
    {
        $qtyIn = $this->faker->randomFloat(4, 1, 500);
        $qtyOut = $this->faker->randomFloat(4, 1, 500);

        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'trx_date' => $this->faker->date(),
            'reference_type' => $this->faker->randomElement(['sale', 'purchase', 'stock_adjustment', 'stock_transfer']),
            'reference_id' => $this->faker->randomNumber(),
            'qty_in' => $qtyIn,
            'qty_out' => $qtyOut,
            'balance_qty' => $this->faker->randomFloat(4, 1, 500),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
