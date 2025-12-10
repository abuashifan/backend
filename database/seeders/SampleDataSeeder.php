<?php

namespace Database\Seeders;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Receipt;
use App\Models\Sale;
use App\Models\StockCard;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::count() ? Warehouse::all() : Warehouse::factory(3)->create();
        $suppliers = Supplier::factory(20)->create();
        $customers = Customer::factory(20)->create();

        if (Product::count() === 0) {
            Product::factory(20)->create();
        }

        $inventoryAccount = ChartOfAccount::where('code', '1140')->first();
        $accountsPayableAccount = ChartOfAccount::where('code', '2110')->first();
        $accountsReceivableAccount = ChartOfAccount::where('code', '1130')->first();
        $salesRevenueAccount = ChartOfAccount::where('code', '4110')->first();

        for ($i = 0; $i < 30; $i++) {
            $purchase = Purchase::factory()->create([
                'supplier_id' => $suppliers->random()->id,
                'warehouse_id' => $warehouses->random()->id,
            ]);

            $originalAmount = $purchase->total_amount ?? 0;
            $remainingAmount = max(0, $originalAmount - rand(0, (int) $originalAmount));

            $accountPayable = AccountPayable::create([
                'supplier_id' => $purchase->supplier_id,
                'purchase_id' => $purchase->id,
                'invoice_number' => $purchase->invoice_number,
                'invoice_date' => $purchase->invoice_date,
                'due_date' => $purchase->due_date,
                'original_amount' => $originalAmount,
                'remaining_amount' => $remainingAmount,
                'status' => $remainingAmount === 0 ? 'closed' : 'open',
            ]);

            $qtyIn = rand(1, 10);

            StockCard::create([
                'product_id' => Product::inRandomOrder()->first()->id,
                'warehouse_id' => $purchase->warehouse_id,
                'trx_date' => $purchase->invoice_date,
                'reference_type' => 'purchase',
                'reference_id' => $purchase->id,
                'qty_in' => $qtyIn,
                'qty_out' => 0,
                'balance_qty' => $qtyIn,
                'notes' => 'Purchase receipt',
            ]);

            if ($inventoryAccount && $accountsPayableAccount) {
                JournalEntry::create([
                    'entry_date' => $purchase->invoice_date,
                    'chart_of_account_id' => $inventoryAccount->id,
                    'description' => 'Purchase inventory',
                    'debit' => $originalAmount,
                    'credit' => 0,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                ]);

                JournalEntry::create([
                    'entry_date' => $purchase->invoice_date,
                    'chart_of_account_id' => $accountsPayableAccount->id,
                    'description' => 'Purchase payable',
                    'debit' => 0,
                    'credit' => $originalAmount,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                ]);
            }

            if (rand(0, 1) && $accountPayable->remaining_amount > 0) {
                $paymentAmount = min($accountPayable->remaining_amount, rand(100, max(100, (int) $accountPayable->remaining_amount)));

                Payment::create([
                    'supplier_id' => $accountPayable->supplier_id,
                    'accounts_payable_id' => $accountPayable->id,
                    'payment_number' => 'PAY-' . strtoupper(Str::random(6)),
                    'payment_date' => $purchase->due_date,
                    'amount' => $paymentAmount,
                    'method' => 'bank_transfer',
                    'notes' => 'Sample payment',
                ]);
            }
        }

        for ($i = 0; $i < 30; $i++) {
            $sale = Sale::factory()->create([
                'customer_id' => $customers->random()->id,
                'warehouse_id' => $warehouses->random()->id,
            ]);

            $originalAmount = $sale->total_amount ?? 0;
            $remainingAmount = max(0, $originalAmount - rand(0, (int) $originalAmount));

            $accountReceivable = AccountReceivable::create([
                'customer_id' => $sale->customer_id,
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'invoice_date' => $sale->invoice_date,
                'due_date' => $sale->due_date,
                'original_amount' => $originalAmount,
                'remaining_amount' => $remainingAmount,
                'status' => $remainingAmount === 0 ? 'closed' : 'open',
            ]);

            $qtyOut = rand(1, 5);

            StockCard::create([
                'product_id' => Product::inRandomOrder()->first()->id,
                'warehouse_id' => $sale->warehouse_id,
                'trx_date' => $sale->invoice_date,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'qty_in' => 0,
                'qty_out' => $qtyOut,
                'balance_qty' => 0,
                'notes' => 'Sale delivery',
            ]);

            if ($accountsReceivableAccount && $salesRevenueAccount) {
                JournalEntry::create([
                    'entry_date' => $sale->invoice_date,
                    'chart_of_account_id' => $accountsReceivableAccount->id,
                    'description' => 'Sale on credit',
                    'debit' => $originalAmount,
                    'credit' => 0,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                ]);

                JournalEntry::create([
                    'entry_date' => $sale->invoice_date,
                    'chart_of_account_id' => $salesRevenueAccount->id,
                    'description' => 'Sales revenue',
                    'debit' => 0,
                    'credit' => $originalAmount,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                ]);
            }

            if (rand(0, 1) && $accountReceivable->remaining_amount > 0) {
                $receiptAmount = min($accountReceivable->remaining_amount, rand(100, max(100, (int) $accountReceivable->remaining_amount)));

                Receipt::create([
                    'customer_id' => $accountReceivable->customer_id,
                    'accounts_receivable_id' => $accountReceivable->id,
                    'receipt_number' => 'RCPT-' . strtoupper(Str::random(6)),
                    'receipt_date' => $sale->due_date,
                    'amount' => $receiptAmount,
                    'method' => 'bank_transfer',
                    'notes' => 'Sample receipt',
                ]);
            }
        }
    }
}
