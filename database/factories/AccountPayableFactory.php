<?php

namespace Database\Factories;

use App\Models\AccountPayable;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountPayableFactory extends Factory
{
    protected $model = AccountPayable::class;

    public function definition(): array
    {
        $invoiceDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $dueDate = (clone $invoiceDate)->modify('+' . $this->faker->numberBetween(7, 45) . ' days');
        $originalAmount = $this->faker->randomFloat(2, 1000, 100000);
        $remainingAmount = $this->faker->randomFloat(2, 0, $originalAmount);

        return [
            'supplier_id' => Supplier::factory(),
            'purchase_id' => Purchase::factory(),
            'invoice_number' => strtoupper($this->faker->unique()->bothify('AP-####')),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'original_amount' => $originalAmount,
            'remaining_amount' => $remainingAmount,
            'status' => $this->faker->randomElement(['open', 'partial', 'closed']),
        ];
    }
}
