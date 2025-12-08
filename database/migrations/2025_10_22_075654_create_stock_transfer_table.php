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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number', 50)->unique();
            $table->dateTime('date')->index();

            $table->foreignId('warehouse_from_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('warehouse_to_id')->constrained('warehouses')->restrictOnDelete();

            $table->string('status', 20)->default('posted'); // draft/posted/canceled
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['warehouse_from_id', 'warehouse_to_id', 'date'], 'idx_transfer_wh_date');
        });

        // DETAIL
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->cascadeOnDelete();
            $table->unsignedSmallInteger('line_number')->default(1);

            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('product_units')->restrictOnDelete();
            $table->decimal('qty', 18, 4);

            // snapshot biaya; pada banyak sistem transfer tidak mengubah nilai, ini hanya untuk jejak
            $table->decimal('unit_cost', 18, 4)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);

            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('product_id', 'idx_transfer_item_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
    }
};
