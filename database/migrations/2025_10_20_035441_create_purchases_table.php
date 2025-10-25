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
       // === Purchase Header ===
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->dateTime('date');
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->foreignId('tax_id')->nullable()->constrained('tax')->restrictOnDelete();
            $table->decimal('tax_nominal', 18, 2)->default(0);
            $table->decimal('other_cost', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->string('status', 20)->default('posted'); // draft/posted/canceled
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('date', 'idx_purchases_date');
            $table->index('supplier_id', 'idx_purchases_supplier');
            $table->index('invoice_number', 'idx_purchase_invoice');
        });

        // === Purchase Detail ===
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->decimal('qty', 18, 4);
            $table->foreignId('unit_id')->nullable()->constrained('product_unit')->restrictOnDelete();
            $table->decimal('price', 18, 2);
            $table->decimal('discount', 18, 2)->default(0);
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->restrictOnDelete();
            $table->decimal('tax_nominal', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->index('product_id', 'idx_purchase_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('purchase_items');
    }
};
