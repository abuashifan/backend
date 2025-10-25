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
         // HEADER
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number', 50)->unique();
            $table->dateTime('date')->index();

            // referensi dokumen sumber (opsional)
            $table->string('reference_type', 30)->nullable()->index(); // sale, purchase, receipt, payment, adj, transfer
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->string('reference_number', 50)->nullable()->index();

            $table->string('status', 20)->default('posted'); // draft/posted/reversed
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // DETAIL
        Schema::create('journal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->unsignedSmallInteger('line_number')->default(1);

            $table->foreignId('account_id')->constrained('chart_of_accounts')->restrictOnDelete();

            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);

            // opsional dimensi tambahan
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index('account_id', 'idx_jdet_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_details');
        Schema::dropIfExists('journal_entries');
    }
};
