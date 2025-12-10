<?php

namespace Database\Factories;

use App\Models\AccountReceivable;
use App\Models\Customer;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceiptFactory extends Factory
{
    protected $model = Receipt::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'accounts_receivable_id' => AccountReceivable::factory(),
            'receipt_number' => strtoupper($this->faker->unique()->bothify('RCPT-####')),
            'receipt_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'method' => $this->faker->randomElement(['cash', 'bank_transfer', 'credit_card']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
