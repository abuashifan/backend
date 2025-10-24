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
        Schema::create('accounts_payable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('purchase_id')->unique()->constrained('purchases')->restrictOnDelete(); // satu AP per purchase

            $table->string('bill_number', 50)->index();
            $table->date('bill_date')->index();
            $table->date('due_date')->nullable()->index();

            $table->decimal('original_amount', 18, 2)->default(0); // dari purchases.total
            $table->decimal('paid_amount', 18, 2)->default(0);     // akumulasi pembayaran
            $table->decimal('balance_amount', 18, 2)->default(0);  // sisa
            $table->string('status', 20)->default('open'); // open/partial/closed/overdue

            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['supplier_id', 'status'], 'idx_ap_supplier_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_payable');
    }
};
