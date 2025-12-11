<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_create_product(): void
    {
        $category = ProductCategory::first() ?? ProductCategory::factory()->create();
        $unit = ProductUnit::first() ?? ProductUnit::factory()->create();
        $tax = Tax::first() ?? Tax::factory()->create();

        $payload = [
            'sku' => 'PRD-1001',
            'name' => 'Sample Product',
            'product_category_id' => $category->id,
            'product_unit_id' => $unit->id,
            'default_tax_id' => $tax->id,
            'description' => 'A test product',
            'cost_price' => 1000,
            'selling_price' => 1500,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertCreated();
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('products', [
            'sku' => 'PRD-1001',
            'name' => 'Sample Product',
        ]);
    }

    public function test_product_validation_errors(): void
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sku', 'name', 'product_category_id', 'product_unit_id', 'cost_price', 'selling_price']);
    }

    public function test_can_update_and_delete_product(): void
    {
        $category = ProductCategory::first() ?? ProductCategory::factory()->create();
        $unit = ProductUnit::first() ?? ProductUnit::factory()->create();
        $tax = Tax::first() ?? Tax::factory()->create();

        $product = Product::factory()->create([
            'product_category_id' => $category->id,
            'product_unit_id' => $unit->id,
            'default_tax_id' => $tax->id,
        ]);

        $updateResponse = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Name',
            'sku' => 'PRD-2000',
            'cost_price' => 500,
            'selling_price' => 800,
        ]);

        $updateResponse->assertOk();
        $updateResponse->assertJsonPath('data.name', 'Updated Name');

        $deleteResponse = $this->deleteJson("/api/products/{$product->id}");

        $deleteResponse->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
