<?php

namespace Database\Seeders;

use App\Models\{Purchase, Sale, Supplier, Customer, Product, Warehouse, Tax, AccountPayable, AccountReceivable, Payment, Receipt, StockCard};
use Illuminate\Database\Seeder;

class TransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PurchasesSeeder::class,
            SalesSeeder::class,
            PurchasePaymentsSeeder::class,
            SalesReceiptsSeeder::class,
        ]);
    }
}
