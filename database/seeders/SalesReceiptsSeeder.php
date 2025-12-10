<?php

namespace Database\Seeders;

use App\Models\{Purchase, Sale, Supplier, Customer, Product, Warehouse, Tax, AccountPayable, AccountReceivable, Payment, Receipt, StockCard};
use Illuminate\Database\Seeder;

class SalesReceiptsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $accountsReceivable = AccountReceivable::count()
            ? AccountReceivable::inRandomOrder()->take((int) ceil(AccountReceivable::count() * 0.5))->get()
            : collect();

        foreach ($accountsReceivable as $ar) {
            $isFullPayment = $faker->boolean(50);
            $receiptAmount = $isFullPayment
                ? $ar->remaining_amount
                : round($ar->remaining_amount * ($faker->numberBetween(30, 80) / 100), 2);

            $receiptAmount = min($receiptAmount, $ar->remaining_amount);

            Receipt::create([
                'customer_id' => $ar->customer_id,
                'accounts_receivable_id' => $ar->id,
                'receipt_number' => 'RCV-' . $faker->unique()->numerify('######'),
                'receipt_date' => $faker->dateTimeBetween($ar->invoice_date, '+30 days'),
                'amount' => $receiptAmount,
                'method' => $faker->randomElement(['cash', 'bank_transfer', 'credit_card']),
                'notes' => 'Sample AR receipt',
            ]);

            $remaining = $ar->remaining_amount - $receiptAmount;
            $ar->remaining_amount = max(0, round($remaining, 2));
            $ar->status = $ar->remaining_amount <= 0.01 ? 'paid' : 'partial';
            if ($ar->status === 'paid') {
                $ar->remaining_amount = 0;
            }
            $ar->save();
        }
    }
}
