<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_payable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->cascadeOnUpdate()->nullOnDelete();
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('original_amount', 15, 2);
            $table->decimal('remaining_amount', 15, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_payable');
    }
};
