<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('WH-###')),
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
