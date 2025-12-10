<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Tax;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $invoiceDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $dueDate = (clone $invoiceDate)->modify('+' . $this->faker->numberBetween(7, 60) . ' days');

        return [
            'customer_id' => Customer::factory(),
            'warehouse_id' => Warehouse::factory(),
            'tax_id' => $this->faker->boolean(70) ? Tax::factory() : null,
            'invoice_number' => strtoupper($this->faker->unique()->bothify('INV-####')),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'subtotal' => $this->faker->randomFloat(2, 1000, 100000),
            'discount_amount' => $this->faker->randomFloat(2, 0, 5000),
            'tax_amount' => $this->faker->randomFloat(2, 0, 5000),
            'total_amount' => $this->faker->randomFloat(2, 1000, 100000),
            'status' => $this->faker->randomElement(['draft', 'sent', 'paid', 'partial']),
        ];
    }
}
