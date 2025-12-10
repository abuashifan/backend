<?php

namespace Database\Factories;

use App\Models\AccountPayable;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'accounts_payable_id' => AccountPayable::factory(),
            'payment_number' => strtoupper($this->faker->unique()->bothify('PAY-####')),
            'payment_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'method' => $this->faker->randomElement(['cash', 'bank_transfer', 'credit_card']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
