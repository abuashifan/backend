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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name',150);
            $table->string('email',50)->nullable();
            $table->string('phone',50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('npwp',50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $t->softDeletes();
            $table->index('name','idx_customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
