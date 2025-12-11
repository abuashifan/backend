<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());

        // ensure chart accounts used by journal exist
        ChartOfAccount::firstOrCreate(['code' => '1141'], ['name' => 'Inventory', 'type' => 'asset', 'is_active' => true]);
        ChartOfAccount::firstOrCreate(['code' => '2110'], ['name' => 'Accounts Payable', 'type' => 'liability', 'is_active' => true]);
    }

    public function test_can_create_purchase_without_tax(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $payload = [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'invoice_number' => 'PUR-EDGE-1',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'quantity' => 1,
            'unit_price' => 100,
            'discount_amount' => 0,
        ];

        $response = $this->postJson('/api/purchases', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.tax_amount', 0);
    }
}
