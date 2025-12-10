<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WarehouseApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_manage_warehouses(): void
    {
        $createResponse = $this->postJson('/api/warehouses', [
            'code' => 'WH-A',
            'name' => 'Main Warehouse',
            'address' => '123 Test Street',
        ]);

        $createResponse->assertCreated();
        $createResponse->assertJsonPath('data.code', 'WH-A');

        $warehouseId = $createResponse->json('data.id');

        $updateResponse = $this->putJson("/api/warehouses/{$warehouseId}", [
            'name' => 'Updated Warehouse',
        ]);

        $updateResponse->assertOk();
        $updateResponse->assertJsonPath('data.name', 'Updated Warehouse');

        $deleteResponse = $this->deleteJson("/api/warehouses/{$warehouseId}");

        $deleteResponse->assertOk();
        $this->assertDatabaseMissing('warehouses', ['id' => $warehouseId]);
    }

    public function test_warehouse_validation_errors(): void
    {
        Warehouse::factory()->create(['code' => 'WH-EXISTING']);

        $response = $this->postJson('/api/warehouses', [
            'code' => 'WH-EXISTING',
            'name' => null,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code', 'name']);
    }
}
