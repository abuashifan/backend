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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number', 50)->unique();
            $table->dateTime('date')->index();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();

            $table->string('reason', 50)->nullable(); // e.g. stock_opname, broken, shrinkage
            $table->string('status', 20)->default('posted'); // draft/posted/canceled
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['warehouse_id', 'date'], 'idx_adj_wh_date');
        });

        // DETAIL
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete();
            $table->unsignedSmallInteger('line_number')->default(1);

            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('product_unit')->restrictOnDelete();

            $table->decimal('qty_in', 18, 4)->default(0);
            $table->decimal('qty_out', 18, 4)->default(0);

            // snapshot costing (untuk laporan cepat)
            $table->decimal('unit_cost', 18, 4)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);

            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id'], 'idx_adj_item_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjusment');
    }
};
