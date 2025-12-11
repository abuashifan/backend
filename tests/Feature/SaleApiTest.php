<?php

namespace Tests\Feature;

use App\Models\AccountReceivable;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\StockCard;
use App\Models\Tax;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SaleApiTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_create_sale(): void
    {
        $customer = Customer::first() ?? Customer::factory()->create();
        $product = Product::first() ?? Product::factory()->create();
        $warehouse = Warehouse::where('name', 'Main Warehouse')->first()
            ?? Warehouse::factory()->create();
        $tax = Tax::first() ?? Tax::factory()->create();
        $supplier = Supplier::first() ?? Supplier::factory()->create();

        $purchasePayload = [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'tax_id' => $tax->id,
            'invoice_number' => 'PUR-STOCK-'.now()->timestamp,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'quantity' => 10,
            'unit_price' => 500,
            'discount_amount' => 0,
        ];

        $this->postJson('/api/purchases', $purchasePayload)->assertCreated();

        $payload = [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'tax_id' => $tax->id,
            'invoice_number' => 'INV-1001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'quantity' => 5,
            'unit_price' => 1000,
            'discount_amount' => 50,
        ];

        $response = $this->postJson('/api/sales', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.invoice_number', 'INV-1001');

        $this->assertDatabaseHas('sales', ['invoice_number' => 'INV-1001']);
        $this->assertSame(1, AccountReceivable::count());
        $this->assertSame(1, Sale::count());
        $this->assertSame(1, StockCard::where('reference_type', 'sale')->count());
    }

    public function test_sale_validation_errors(): void
    {
        $response = $this->postJson('/api/sales', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'customer_id',
            'warehouse_id',
            'product_id',
            'invoice_number',
            'invoice_date',
            'quantity',
            'unit_price',
        ]);
    }
}
