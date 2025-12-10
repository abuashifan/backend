<?php

namespace Database\Seeders;

use App\Models\{Purchase, Sale, Supplier, Customer, Product, Warehouse, Tax, AccountPayable, AccountReceivable, Payment, Receipt, StockCard};
use Illuminate\Database\Seeder;

class PurchasesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $suppliers = Supplier::count() ? Supplier::all() : Supplier::factory(10)->create();
        $warehouses = Warehouse::count() ? Warehouse::all() : Warehouse::factory(5)->create();
        $products = Product::count() ? Product::all() : Product::factory(20)->create();

        for ($i = 0; $i < 30; $i++) {
            $supplier = $suppliers->random();
            $warehouse = $warehouses->random();
            $product = $products->random();
            $tax = Tax::inRandomOrder()->first();

            $invoiceDate = $faker->dateTimeBetween('-6 months', 'now');
            $dueDate = (clone $invoiceDate)->modify('+' . $faker->numberBetween(7, 45) . ' days');
            $quantity = $faker->numberBetween(1, 20);
            $unitPrice = $product->cost_price ?? $faker->randomFloat(2, 50, 500);
            $subtotal = round($quantity * $unitPrice, 2);
            $discountAmount = $faker->boolean(40) ? round($faker->randomFloat(2, 0, $subtotal * 0.1), 2) : 0;
            $taxAmount = $tax ? round(($subtotal - $discountAmount) * ((float) $tax->rate / 100), 2) : 0;
            $totalAmount = round($subtotal - $discountAmount + $taxAmount, 2);

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'tax_id' => $tax?->id,
                'invoice_number' => strtoupper($faker->unique()->bothify('PUR-#####')),
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'received',
            ]);

            $accountPayable = AccountPayable::create([
                'supplier_id' => $purchase->supplier_id,
                'purchase_id' => $purchase->id,
                'invoice_number' => $purchase->invoice_number,
                'invoice_date' => $purchase->invoice_date,
                'due_date' => $purchase->due_date,
                'original_amount' => $purchase->total_amount,
                'remaining_amount' => $purchase->total_amount,
                'status' => 'open',
            ]);

            StockCard::create([
                'product_id' => $product->id,
                'warehouse_id' => $purchase->warehouse_id,
                'trx_date' => $purchase->invoice_date,
                'reference_type' => 'purchase',
                'reference_id' => $purchase->id,
                'qty_in' => $quantity,
                'qty_out' => 0,
                'balance_qty' => 0,
                'notes' => 'Sample purchase stock in',
            ]);

            $accountPayable->save();
        }
    }
}
