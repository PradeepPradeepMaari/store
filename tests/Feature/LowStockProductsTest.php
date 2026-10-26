<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LowStockProductsTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────
    // ① Default threshold returns products below 10
    // ──────────────────────────────────────────────────────────────────────

    public function test_low_stock_uses_default_threshold_of_10(): void
    {
        Product::factory()->create(['stock_quantity' => 5,  'name' => 'LowA', 'code' => 'LOW-001']);
        Product::factory()->create(['stock_quantity' => 0,  'name' => 'LowB', 'code' => 'LOW-002']);
        Product::factory()->create(['stock_quantity' => 10, 'name' => 'OkA',  'code' => 'OK-001']);
        Product::factory()->create(['stock_quantity' => 50, 'name' => 'OkB',  'code' => 'OK-002']);

        $response = $this->getJson('/api/products/low-stock');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('threshold', 10)
            ->assertJsonCount(2, 'data');
    }

    // ──────────────────────────────────────────────────────────────────────
    // ② Custom threshold
    // ──────────────────────────────────────────────────────────────────────

    public function test_custom_threshold_filters_correctly(): void
    {
        Product::factory()->create(['stock_quantity' => 5,  'code' => 'T-001']);
        Product::factory()->create(['stock_quantity' => 15, 'code' => 'T-002']);
        Product::factory()->create(['stock_quantity' => 25, 'code' => 'T-003']);

        $response = $this->getJson('/api/products/low-stock?threshold=20');

        $response->assertStatus(200)
            ->assertJsonPath('threshold', 20)
            ->assertJsonCount(2, 'data'); // stock 5 and 15 are below 20
    }

    // ──────────────────────────────────────────────────────────────────────
    // ③ Empty result when no products match
    // ──────────────────────────────────────────────────────────────────────

    public function test_empty_collection_when_no_low_stock_products(): void
    {
        Product::factory()->create(['stock_quantity' => 100, 'code' => 'HI-001']);
        Product::factory()->create(['stock_quantity' => 200, 'code' => 'HI-002']);

        $response = $this->getJson('/api/products/low-stock?threshold=5');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    // ──────────────────────────────────────────────────────────────────────
    // ④ Response includes correct product fields
    // ──────────────────────────────────────────────────────────────────────

    public function test_response_includes_required_product_fields(): void
    {
        Product::factory()->create([
            'stock_quantity' => 3,
            'code'           => 'FLD-001',
            'price'          => 99.99,
            'tax_percentage' => 18.00,
        ]);

        $response = $this->getJson('/api/products/low-stock?threshold=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'code', 'price', 'tax_percentage', 'stock_quantity'],
                ],
            ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑤ Zero threshold — no products should be returned (none are below 0)
    // ──────────────────────────────────────────────────────────────────────

    public function test_zero_threshold_returns_empty_collection(): void
    {
        Product::factory()->create(['stock_quantity' => 0, 'code' => 'Z-001']);

        $response = $this->getJson('/api/products/low-stock?threshold=0');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑥ Negative threshold is invalid
    // ──────────────────────────────────────────────────────────────────────

    public function test_negative_threshold_returns_validation_error(): void
    {
        $this->getJson('/api/products/low-stock?threshold=-1')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['threshold']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑦ Threshold is strictly less than (not ≤)
    // ──────────────────────────────────────────────────────────────────────

    public function test_threshold_is_strictly_less_than_not_equal(): void
    {
        Product::factory()->create(['stock_quantity' => 10, 'code' => 'EQ-001']);
        Product::factory()->create(['stock_quantity' => 9,  'code' => 'EQ-002']);

        $response = $this->getJson('/api/products/low-stock?threshold=10');

        // Only product with qty=9 should appear; qty=10 is NOT below 10
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->assertSame(9, $response->json('data.0.stock_quantity'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑧ Count field matches returned data
    // ──────────────────────────────────────────────────────────────────────

    public function test_count_field_matches_data_count(): void
    {
        Product::factory()->count(4)->create(['stock_quantity' => 3]);

        $response = $this->getJson('/api/products/low-stock?threshold=10');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertSame($data['count'], count($data['data']));
    }
}
