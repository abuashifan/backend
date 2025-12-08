<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('accounts_receivable_id')->nullable()->constrained('accounts_receivable')->cascadeOnUpdate()->nullOnDelete();
            $table->string('receipt_number')->unique();
            $table->date('receipt_date');
            $table->decimal('amount', 15, 2);
            $table->string('method');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
