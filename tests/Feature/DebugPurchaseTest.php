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

class DebugPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_purchase_endpoint_debug(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $tax = Tax::factory()->create();

        $payload = [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'tax_id' => $tax->id,
            'invoice_number' => 'PUR-1001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'quantity' => 10,
            'unit_price' => 500,
            'discount_amount' => 0,
        ];

        $response = $this->postJson('/api/purchases', $payload);

        echo "\n\n=== RESPONSE DEBUG ===\n";
        echo "Status: " . $response->status() . "\n";
        echo "Response: " . $response->content() . "\n";
        echo "=== END DEBUG ===\n\n";

        $response->assertCreated();
    }
}
