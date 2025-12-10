<?php

namespace Tests\Feature;

use App\Models\AccountPayable;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockCard;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_create_purchase(): void
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

        $response->assertCreated();
        $response->assertJsonPath('data.invoice_number', 'PUR-1001');

        $this->assertDatabaseHas('purchases', ['invoice_number' => 'PUR-1001']);
        $this->assertSame(1, AccountPayable::count());
        $this->assertSame(1, Purchase::count());
        $this->assertSame(1, StockCard::where('reference_type', 'purchase')->count());
    }

    public function test_purchase_validation_errors(): void
    {
        $response = $this->postJson('/api/purchases', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'supplier_id',
            'warehouse_id',
            'product_id',
            'invoice_number',
            'invoice_date',
            'quantity',
            'unit_price',
        ]);
    }
}
