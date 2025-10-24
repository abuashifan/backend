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
        Schema::create('accounts_receivable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('sale_id')->unique()->constrained('sales')->restrictOnDelete(); // satu AR per sale

            $table->string('invoice_number', 50)->index();
            $table->date('invoice_date')->index();
            $table->date('due_date')->nullable()->index();

            $table->decimal('original_amount', 18, 2)->default(0); // dari sales.total
            $table->decimal('paid_amount', 18, 2)->default(0);     // akumulasi penerimaan
            $table->decimal('balance_amount', 18, 2)->default(0);  // sisa
            $table->string('status', 20)->default('open'); // open/partial/closed/overdue

            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status'], 'idx_ar_customer_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_receivable');
    }
};
