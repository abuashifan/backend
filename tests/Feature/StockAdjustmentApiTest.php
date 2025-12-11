<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockCard;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StockAdjustmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_create_stock_adjustment(): void
    {
        $product = Product::first() ?? Product::factory()->create();
        $warehouse = Warehouse::first() ?? Warehouse::factory()->create();

        $payload = [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'adjustment_date' => now()->toDateString(),
            'quantity_difference' => 5,
            'reason' => 'Cycle count',
            'approved_by' => 'Manager',
        ];

        $response = $this->postJson('/api/stock-adjustments', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('stock_adjustments', ['reason' => 'Cycle count']);
        $this->assertGreaterThanOrEqual(1, StockAdjustment::count());
        $this->assertGreaterThanOrEqual(1, StockCard::where('reference_type', 'stock_adjustment')->count());
    }

    public function test_stock_adjustment_validation_error(): void
    {
        $response = $this->postJson('/api/stock-adjustments', [
            'quantity_difference' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_id', 'warehouse_id', 'adjustment_date', 'quantity_difference', 'reason']);
    }
}
