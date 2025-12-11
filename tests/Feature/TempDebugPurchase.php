<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TempDebugPurchase extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_debug(): void
    {
        $supplier = Supplier::first() ?? Supplier::factory()->create();
        $product = Product::first() ?? Product::factory()->create();
        $warehouse = Warehouse::where('name', 'Main Warehouse')->first() ?? Warehouse::factory()->create();
        $tax = Tax::first() ?? Tax::factory()->create();

        $payload = [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'tax_id' => $tax->id,
            'invoice_number' => 'PUR-DBG-1',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'quantity' => 10,
            'unit_price' => 500,
            'discount_amount' => 0,
        ];

        $response = $this->postJson('/api/purchases', $payload);

        echo "STATUS:" . $response->status() . PHP_EOL;
        echo $response->content() . PHP_EOL;
    }
}
