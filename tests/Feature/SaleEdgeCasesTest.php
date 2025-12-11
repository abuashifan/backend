<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SaleEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());

        ChartOfAccount::firstOrCreate(['code' => '1130'], ['name' => 'Accounts Receivable', 'type' => 'asset', 'is_active' => true]);
        ChartOfAccount::firstOrCreate(['code' => '4110'], ['name' => 'Sales Revenue', 'type' => 'revenue', 'is_active' => true]);
        ChartOfAccount::firstOrCreate(['code' => '5110'], ['name' => 'COGS', 'type' => 'expense', 'is_active' => true]);
        ChartOfAccount::firstOrCreate(['code' => '1141'], ['name' => 'Inventory', 'type' => 'asset', 'is_active' => true]);
        ChartOfAccount::firstOrCreate(['code' => '2110'], ['name' => 'Accounts Payable', 'type' => 'liability', 'is_active' => true]);
    }

    public function test_insufficient_stock_returns_error(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $payload = [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'invoice_number' => 'INV-EDGE-1',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'quantity' => 5,
            'unit_price' => 1000,
            'discount_amount' => 0,
        ];

        $response = $this->postJson('/api/sales', $payload);

        $response->assertStatus(500);
        $response->assertJsonFragment(['message' => 'Insufficient stock to complete the transaction.']);
    }
}
