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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->dateTime('date');
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->restrictOnDelete();
            $table->decimal('tax_nominal', 18, 2)->default(0);
            $table->decimal('other_cost', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->string('status', 20)->default('posted'); // draft/posted/canceled
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('date', 'idx_sales_date');
            $table->index('customer_id', 'idx_sales_customer');
            $table->index('invoice_number', 'idx_invoice_number');
        });

        Schema::create('salles_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salles_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('products_id')->constrained('products')->restrictOnDelete();
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

            $table->index('products_id', 'idx_products_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salles');
        Schema::dropIfExists('salles_item');
    }
};
