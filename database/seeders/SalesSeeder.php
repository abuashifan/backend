<?php

namespace Database\Seeders;

use App\Models\{Purchase, Sale, Supplier, Customer, Product, Warehouse, Tax, AccountPayable, AccountReceivable, Payment, Receipt, StockCard};
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $customers = Customer::count() ? Customer::all() : Customer::factory(20)->create();
        $warehouses = Warehouse::count() ? Warehouse::all() : Warehouse::factory(5)->create();
        $products = Product::count() ? Product::all() : Product::factory(20)->create();

        for ($i = 0; $i < 30; $i++) {
            $customer = $customers->random();
            $warehouse = $warehouses->random();
            $product = $products->random();
            $tax = Tax::inRandomOrder()->first();

            $invoiceDate = $faker->dateTimeBetween('-6 months', 'now');
            $dueDate = (clone $invoiceDate)->modify('+' . $faker->numberBetween(7, 45) . ' days');
            $quantity = $faker->numberBetween(1, 10);
            $unitPrice = $product->selling_price ?? $faker->randomFloat(2, 100, 800);
            $subtotal = round($quantity * $unitPrice, 2);
            $discountAmount = $faker->boolean(35) ? round($faker->randomFloat(2, 0, $subtotal * 0.1), 2) : 0;
            $taxAmount = $tax ? round(($subtotal - $discountAmount) * ((float) $tax->rate / 100), 2) : 0;
            $totalAmount = round($subtotal - $discountAmount + $taxAmount, 2);

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'warehouse_id' => $warehouse->id,
                'tax_id' => $tax?->id,
                'invoice_number' => strtoupper($faker->unique()->bothify('INV-#####')),
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'sent',
            ]);

            $accountReceivable = AccountReceivable::create([
                'customer_id' => $sale->customer_id,
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'invoice_date' => $sale->invoice_date,
                'due_date' => $sale->due_date,
                'original_amount' => $sale->total_amount,
                'remaining_amount' => $sale->total_amount,
                'status' => 'open',
            ]);

            StockCard::create([
                'product_id' => $product->id,
                'warehouse_id' => $sale->warehouse_id,
                'trx_date' => $sale->invoice_date,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'qty_in' => 0,
                'qty_out' => $quantity,
                'balance_qty' => 0,
                'notes' => 'Sample sale stock out',
            ]);

            $accountReceivable->save();
        }
    }
}
