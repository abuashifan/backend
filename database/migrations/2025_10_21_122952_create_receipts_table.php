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
        // SALES RECEIPT (Header)
        // =======================
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 50)->unique();
            $table->dateTime('date');

            // Penerimaan dari customer
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();

            // Info metode penerimaan
            $table->string('method', 30)->nullable();        // e.g., cash, bank_transfer, e-wallet
            $table->string('reference_no', 100)->nullable(); // bank ref / slip number

            // Nilai keuangan (disimpan untuk performa & audit)
            $table->decimal('amount', 18, 2)->default(0);     // total diterima (gross)
            $table->decimal('fee', 18, 2)->default(0);        // biaya admin/bank
            $table->decimal('other_cost', 18, 2)->default(0); // penyesuaian lain
            $table->decimal('total', 18, 2)->default(0);      // amount -/+ adj (final)

            $table->string('status', 20)->default('posted'); // draft/posted/canceled
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('date', 'idx_receipts_date');
            $table->index('customer_id', 'idx_receipts_customer');
            $table->index('receipt_number', 'idx_receipts_number');
        });

        // =======================
        // SALES RECEIPT DETAIL (Allocations)
        // =======================
        Schema::create('receipt_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('receipts')->cascadeOnDelete();

            // Alokasikan ke invoice penjualan
            $table->foreignId('sale_id')->constrained('sales')->restrictOnDelete();

            // Opsional: snapshot nomor dokumen sumber
            $table->string('reference_number', 50)->nullable(); // invoice number

            // Alokasi per invoice
            $table->decimal('allocated_amount', 18, 2); // amount applied to that sale
            $table->decimal('discount', 18, 2)->default(0);     // early payment / write-off
            $table->decimal('fee', 18, 2)->default(0);          // per-line fee if any
            $table->decimal('subtotal', 18, 2);                 // allocated_amount - discount + fee

            $table->timestamps();
            $table->softDeletes();

            $table->index('sale_id', 'idx_receipt_detail_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_details');
        Schema::dropIfExists('receipts');
    }
};
