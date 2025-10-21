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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name',150);
            $table->text('alamat')->nullable();
            $table->foregnId('categoryID')->nullable()
            ->constrained('product_categories')->restrictOnDelete();
             $table->foregnId('unitID')->nullable()
            ->constrained('product_unit')->restrictOnDelete();
            $table->string('SKU',100)->nullable();
            $table->string('barcode',100)->nullable();
            $t->decimal('harga_beli_default', 18, 2)->nullable();
            $t->decimal('harga_jual_default', 18, 2)->nullable();
            $t->decimal('min_stok', 18, 4)->default(0);
            $t->boolean('aktif')->default(true);
            $table->timestamps();
            $t->softDeletes();

            $t->index('name', 'idx_product_name');
            $t->index('kategori_id', 'idx_barang_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
