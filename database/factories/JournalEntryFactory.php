<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        $isDebit = $this->faker->boolean();
        $debit = $isDebit ? $this->faker->randomFloat(2, 1000, 100000) : 0;
        $credit = $isDebit ? 0 : $this->faker->randomFloat(2, 1000, 100000);

        return [
            'entry_date' => $this->faker->date(),
            'chart_of_account_id' => ChartOfAccount::factory(),
            'description' => $this->faker->optional()->sentence(),
            'debit' => $debit,
            'credit' => $credit,
            'reference_type' => $this->faker->word(),
            'reference_id' => $this->faker->randomNumber(),
        ];
    }
}
