<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockCard;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StockTransferApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_create_stock_transfer(): void
    {
        $product = Product::factory()->create();
        $fromWarehouse = Warehouse::factory()->create();
        $toWarehouse = Warehouse::factory()->create();

        /** @var InventoryService $inventory */
        $inventory = $this->app->make(InventoryService::class);
        $inventory->increaseStock($product->id, $fromWarehouse->id, 20, 'initial', 1);

        $payload = [
            'product_id' => $product->id,
            'from_warehouse_id' => $fromWarehouse->id,
            'to_warehouse_id' => $toWarehouse->id,
            'transfer_date' => now()->toDateString(),
            'quantity' => 5,
            'notes' => 'Restock',
        ];

        $response = $this->postJson('/api/stock-transfers', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('stock_transfers', ['notes' => 'Restock']);
        $this->assertSame(1, StockTransfer::count());
        $this->assertSame(2, StockCard::where('reference_type', 'stock_transfer')->count());
    }

    public function test_stock_transfer_validation_error(): void
    {
        $response = $this->postJson('/api/stock-transfers', [
            'quantity' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'product_id',
            'from_warehouse_id',
            'to_warehouse_id',
            'transfer_date',
            'quantity',
        ]);
    }
}
