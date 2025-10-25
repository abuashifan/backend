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
       Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);

            // asset, liability, equity, revenue, expense, other
            $table->string('type', 20)->index();
            // normal balance side: debit/credit
            $table->string('normal_balance', 10)->default('debit');

            // hierarchical
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->unsignedSmallInteger('level')->default(1);
            $table->boolean('is_active')->default(true);

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['type', 'code'], 'idx_coa_type_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
