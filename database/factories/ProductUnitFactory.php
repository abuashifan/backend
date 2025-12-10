<?php

namespace Database\Factories;

use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductUnitFactory extends Factory
{
    protected $model = ProductUnit::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('UNIT-##')),
            'name' => $this->faker->word(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
