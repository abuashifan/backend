<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChartOfAccountFactory extends Factory
{
    protected $model = ChartOfAccount::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('ACCT-###')),
            'name' => $this->faker->words(2, true),
            'type' => $this->faker->randomElement(['asset', 'liability', 'equity', 'revenue', 'expense']),
            'parent_id' => null,
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
