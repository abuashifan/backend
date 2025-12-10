<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('CAT-###')),
            'name' => $this->faker->word(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
