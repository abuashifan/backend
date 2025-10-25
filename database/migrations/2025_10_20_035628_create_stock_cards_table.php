<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =======================
        // STOCK CARD (Ledger / Movements)
        // =======================
        Schema::create('stock_cards', function (Blueprint $table) {
            $table->id();

            $table->dateTime('date')->index();

            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('product_unit')->restrictOnDelete();

            // Sumber dokumen / referensi
            $table->string('reference_type', 30)->index(); // purchase, purchase_return, sale, sales_return, transfer_in, transfer_out, adjustment_in, adjustment_out, opening_balance, production_in, production_out
            $table->unsignedBigInteger('reference_id')->nullable()->index(); // id dari dokumen sumber (mis. purchase_id/sale_id)
            $table->string('reference_number', 50)->nullable()->index();
            $table->unsignedSmallInteger('line_number')->nullable(); // optional: urutan baris pada dokumen sumber

            // Kuantitas (gunakan salah satu atau keduanya sesuai event)
            $table->decimal('qty_in', 18, 4)->default(0);
            $table->decimal('qty_out', 18, 4)->default(0);

            // Valuasi & costing
            $table->decimal('unit_cost', 18, 4)->default(0);   // biaya per unit untuk movement ini (avg/FIFO snapshot)
            $table->decimal('total_cost', 18, 2)->default(0);  // nilai biaya untuk movement ini (qty * unit_cost, positif)

            // Running balances (disimpan untuk performa laporan)
            $table->decimal('balance_qty', 18, 4)->default(0);   // saldo qty setelah movement ini
            $table->decimal('balance_cost', 18, 2)->default(0);  // saldo nilai persediaan setelah movement
            $table->decimal('avg_cost', 18, 4)->default(0);      // moving average cost setelah movement

            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indeks gabungan yang sering dipakai untuk laporan
            $table->index(['product_id', 'warehouse_id', 'date'], 'idx_stock_cards_prod_wh_date');
            $table->index(['reference_type', 'reference_id'], 'idx_stock_cards_ref');
        });

        // =======================
        // STOCK BALANCES (Snapshot per Product x Warehouse)
        // =======================
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('product_unit')->restrictOnDelete();

            $table->decimal('qty', 18, 4)->default(0);          // saldo kuantitas saat ini
            $table->decimal('avg_cost', 18, 4)->default(0);     // biaya rata-rata bergerak saat ini
            $table->decimal('total_cost', 18, 2)->default(0);   // nilai persediaan saat ini (qty * avg_cost)

            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id'], 'uq_stock_balances_prod_wh');
            $table->index(['warehouse_id', 'product_id'], 'idx_stock_balances_wh_prod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
        Schema::dropIfExists('stock_balances');
        Schema::dropIfExists('stock_cards');
    }
};
