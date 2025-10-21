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
        // PURCHASE PAYMENT (Header)
        // =======================
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 50)->unique();
            $table->dateTime('date');

            // Pembayaran ke supplier
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();

            // Info metode pembayaran
            $table->string('method', 30)->nullable();   // e.g., cash, bank_transfer
            $table->string('reference_no', 100)->nullable(); // nomor bukti transfer / giro

            // Nilai keuangan
            $table->decimal('amount', 18, 2)->default(0);     // total dibayar
            $table->decimal('fee', 18, 2)->default(0);        // biaya admin/bank
            $table->decimal('other_cost', 18, 2)->default(0); // biaya lain-lain
            $table->decimal('total', 18, 2)->default(0);      // jumlah akhir dibayar (disimpan agar cepat query)

            $table->string('status', 20)->default('posted'); // draft/posted/canceled
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index untuk optimasi pencarian
            $table->index('date', 'idx_payment_date');
            $table->index('supplier_id', 'idx_payment_supplier');
            $table->index('payment_number', 'idx_payment_number');
        });

        // =======================
        // PURCHASE PAYMENT DETAIL
        // =======================
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('purchase_id')->constrained('purchases')->restrictOnDelete();

            // Informasi tambahan
            $table->string('reference_number', 50)->nullable(); // nomor invoice pembelian
            $table->decimal('allocated_amount', 18, 2);         // jumlah dibayar untuk invoice tersebut
            $table->decimal('discount', 18, 2)->default(0);     // diskon pembayaran
            $table->decimal('fee', 18, 2)->default(0);          // biaya tambahan (per invoice)
            $table->decimal('subtotal', 18, 2);                 // total akhir per detail

            $table->timestamps();
            $table->softDeletes();

            $table->index('purchase_id', 'idx_payment_detail_purchase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
        Schema::dropIfExists('payments');
    }
};
