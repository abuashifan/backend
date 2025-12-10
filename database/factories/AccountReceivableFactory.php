<?php

namespace Database\Factories;

use App\Models\AccountReceivable;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountReceivableFactory extends Factory
{
    protected $model = AccountReceivable::class;

    public function definition(): array
    {
        $invoiceDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $dueDate = (clone $invoiceDate)->modify('+' . $this->faker->numberBetween(7, 45) . ' days');
        $originalAmount = $this->faker->randomFloat(2, 1000, 100000);
        $remainingAmount = $this->faker->randomFloat(2, 0, $originalAmount);

        return [
            'customer_id' => Customer::factory(),
            'sale_id' => Sale::factory(),
            'invoice_number' => strtoupper($this->faker->unique()->bothify('AR-####')),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'original_amount' => $originalAmount,
            'remaining_amount' => $remainingAmount,
            'status' => $this->faker->randomElement(['open', 'partial', 'closed']),
        ];
    }
}
