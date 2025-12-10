<?php

namespace Database\Seeders;

use App\Models\{Purchase, Sale, Supplier, Customer, Product, Warehouse, Tax, AccountPayable, AccountReceivable, Payment, Receipt, StockCard};
use Illuminate\Database\Seeder;

class PurchasePaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $accountsPayable = AccountPayable::count()
            ? AccountPayable::inRandomOrder()->take((int) ceil(AccountPayable::count() * 0.5))->get()
            : collect();

        foreach ($accountsPayable as $ap) {
            $isFullPayment = $faker->boolean(50);
            $paymentAmount = $isFullPayment
                ? $ap->remaining_amount
                : round($ap->remaining_amount * ($faker->numberBetween(30, 80) / 100), 2);

            $paymentAmount = min($paymentAmount, $ap->remaining_amount);

            Payment::create([
                'supplier_id' => $ap->supplier_id,
                'accounts_payable_id' => $ap->id,
                'payment_number' => 'PAY-' . $faker->unique()->numerify('######'),
                'payment_date' => $faker->dateTimeBetween($ap->invoice_date, '+30 days'),
                'amount' => $paymentAmount,
                'method' => $faker->randomElement(['cash', 'bank_transfer', 'giro']),
                'notes' => 'Sample AP payment',
            ]);

            $remaining = $ap->remaining_amount - $paymentAmount;
            $ap->remaining_amount = max(0, round($remaining, 2));
            $ap->status = $ap->remaining_amount <= 0.01 ? 'paid' : 'partial';
            if ($ap->status === 'paid') {
                $ap->remaining_amount = 0;
            }
            $ap->save();
        }
    }
}
