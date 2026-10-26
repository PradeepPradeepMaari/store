<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    private function makePayload(array $overrides = []): array
    {
        $product = Product::factory()->inStock(10)->withPrice(100.00)->withTax(10.00)->create();

        return array_merge([
            'customer' => [
                'name'  => 'Test User',
                'email' => 'test@example.com',
            ],
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ], $overrides);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ① Successful order creation
    // ──────────────────────────────────────────────────────────────────────

    public function test_successful_order_creation_returns_201(): void
    {
        $payload = $this->makePayload();

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Order created successfully.')
            ->assertJsonStructure([
                'data' => [
                    'id', 'customer', 'subtotal', 'tax_amount', 'grand_total', 'items',
                ],
            ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ② New customer is created when email is not in DB
    // ──────────────────────────────────────────────────────────────────────

    public function test_new_customer_is_created_when_not_existing(): void
    {
        $payload = $this->makePayload();
        $this->assertDatabaseMissing('customers', ['email' => 'test@example.com']);

        $this->postJson('/api/orders', $payload)->assertStatus(201);

        $this->assertDatabaseHas('customers', [
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ③ Existing customer is reused
    // ──────────────────────────────────────────────────────────────────────

    public function test_existing_customer_is_reused(): void
    {
        $existing = Customer::factory()->create(['email' => 'existing@example.com']);
        $payload  = $this->makePayload([
            'customer' => ['name' => 'Different Name', 'email' => 'existing@example.com'],
        ]);

        $this->postJson('/api/orders', $payload)->assertStatus(201);

        // No duplicate customer created
        $this->assertSame(1, Customer::where('email', 'existing@example.com')->count());

        // The original name is preserved (firstOrCreate)
        $this->assertDatabaseHas('customers', [
            'id'   => $existing->id,
            'name' => $existing->name,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ④ Correct subtotal calculation
    // ──────────────────────────────────────────────────────────────────────

    public function test_order_subtotal_is_correctly_calculated(): void
    {
        // price=100, tax=10%, qty=3  → subtotal = 300.00
        $product = Product::factory()->inStock(10)->withPrice(100.00)->withTax(10.00)->create();
        $payload = [
            'customer' => ['name' => 'Calc User', 'email' => 'calc@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 3]],
        ];

        $response = $this->postJson('/api/orders', $payload)->assertStatus(201);

        $this->assertEquals(300.0, $response->json('data.subtotal'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑤ Correct tax calculation
    // ──────────────────────────────────────────────────────────────────────

    public function test_order_tax_amount_is_correctly_calculated(): void
    {
        // price=100, tax=18%, qty=2  → subtotal=200, tax=36.00
        $product = Product::factory()->inStock(10)->withPrice(100.00)->withTax(18.00)->create();
        $payload = [
            'customer' => ['name' => 'Tax User', 'email' => 'tax@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 2]],
        ];

        $response = $this->postJson('/api/orders', $payload)->assertStatus(201);

        $this->assertEquals(36.0, $response->json('data.tax_amount'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑥ Correct grand total calculation
    // ──────────────────────────────────────────────────────────────────────

    public function test_order_grand_total_is_correctly_calculated(): void
    {
        // price=200, tax=5%, qty=2 → subtotal=400, tax=20, grand=420
        $product = Product::factory()->inStock(10)->withPrice(200.00)->withTax(5.00)->create();
        $payload = [
            'customer' => ['name' => 'Total User', 'email' => 'total@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 2]],
        ];

        $response = $this->postJson('/api/orders', $payload)->assertStatus(201);

        $this->assertEquals(400.0, $response->json('data.subtotal'));
        $this->assertEquals(20.0, $response->json('data.tax_amount'));
        $this->assertEquals(420.0, $response->json('data.grand_total'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑦ Stock is correctly deducted after a successful order
    // ──────────────────────────────────────────────────────────────────────

    public function test_stock_is_deducted_after_successful_order(): void
    {
        $product = Product::factory()->inStock(20)->withPrice(50.00)->withTax(0.00)->create();
        $payload = [
            'customer' => ['name' => 'Stock User', 'email' => 'stock@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 5]],
        ];

        $this->postJson('/api/orders', $payload)->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 15,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑧ Insufficient stock — order is rejected
    // ──────────────────────────────────────────────────────────────────────

    public function test_order_rejected_when_stock_is_insufficient(): void
    {
        $product = Product::factory()->inStock(3)->withPrice(50.00)->withTax(0.00)->create();
        $payload = [
            'customer' => ['name' => 'Over User', 'email' => 'over@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 10]],
        ];

        $this->postJson('/api/orders', $payload)->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑨ Stock unchanged after a failed order
    // ──────────────────────────────────────────────────────────────────────

    public function test_stock_unchanged_after_failed_order(): void
    {
        $product = Product::factory()->inStock(3)->create();
        $payload = [
            'customer' => ['name' => 'Fail User', 'email' => 'fail@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 10]],
        ];

        $this->postJson('/api/orders', $payload)->assertStatus(422);

        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 3,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑩ No partial order created after a failure
    // ──────────────────────────────────────────────────────────────────────

    public function test_no_order_created_when_any_item_has_insufficient_stock(): void
    {
        $productA = Product::factory()->inStock(10)->create();
        $productB = Product::factory()->inStock(2)->create();

        $payload = [
            'customer' => ['name' => 'Partial User', 'email' => 'partial@example.com'],
            'items'    => [
                ['product_id' => $productA->id, 'quantity' => 5],
                ['product_id' => $productB->id, 'quantity' => 5], // exceeds stock
            ],
        ];

        $this->postJson('/api/orders', $payload)->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑪ Invalid customer information
    // ──────────────────────────────────────────────────────────────────────

    public function test_validation_fails_for_missing_customer_name(): void
    {
        $product = Product::factory()->inStock()->create();
        $payload = [
            'customer' => ['email' => 'test@example.com'], // name missing
            'items'    => [['product_id' => $product->id, 'quantity' => 1]],
        ];

        $this->postJson('/api/orders', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['customer.name']);
    }

    public function test_validation_fails_for_invalid_customer_email(): void
    {
        $product = Product::factory()->inStock()->create();
        $payload = [
            'customer' => ['name' => 'Test', 'email' => 'not-an-email'],
            'items'    => [['product_id' => $product->id, 'quantity' => 1]],
        ];

        $this->postJson('/api/orders', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['customer.email']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑫ Invalid product IDs
    // ──────────────────────────────────────────────────────────────────────

    public function test_validation_fails_for_nonexistent_product_id(): void
    {
        $payload = [
            'customer' => ['name' => 'Test', 'email' => 'test@example.com'],
            'items'    => [['product_id' => 99999, 'quantity' => 1]],
        ];

        $this->postJson('/api/orders', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.product_id']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑬ Zero and negative quantities
    // ──────────────────────────────────────────────────────────────────────

    public function test_validation_fails_for_zero_quantity(): void
    {
        $product = Product::factory()->inStock()->create();
        $payload = [
            'customer' => ['name' => 'Test', 'email' => 'test@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 0]],
        ];

        $this->postJson('/api/orders', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.quantity']);
    }

    public function test_validation_fails_for_negative_quantity(): void
    {
        $product = Product::factory()->inStock()->create();
        $payload = [
            'customer' => ['name' => 'Test', 'email' => 'test@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => -5]],
        ];

        $this->postJson('/api/orders', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.quantity']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑭ Duplicate product IDs in items — quantities are summed
    // ──────────────────────────────────────────────────────────────────────

    public function test_duplicate_product_ids_are_consolidated(): void
    {
        $product = Product::factory()->inStock(20)->withPrice(100.00)->withTax(0.00)->create();

        $payload = [
            'customer' => ['name' => 'Dup User', 'email' => 'dup@example.com'],
            'items'    => [
                ['product_id' => $product->id, 'quantity' => 3],
                ['product_id' => $product->id, 'quantity' => 4], // same product
            ],
        ];

        $response = $this->postJson('/api/orders', $payload)->assertStatus(201);

        // Total qty = 7, price=100 → subtotal = 700
        $this->assertEquals(700.0, $response->json('data.subtotal'));

        // Only one order item for this product
        $order = Order::first();
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(7, $order->items()->first()->quantity);

        // Stock deducted by 7
        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 13,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑮ Confirmation job is dispatched on success
    // ──────────────────────────────────────────────────────────────────────

    public function test_confirmation_job_dispatched_on_successful_order(): void
    {
        Queue::fake();
        $payload = $this->makePayload();

        $this->postJson('/api/orders', $payload)->assertStatus(201);

        Queue::assertPushed(SendOrderConfirmationJob::class);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑯ No confirmation job for a failed order
    // ──────────────────────────────────────────────────────────────────────

    public function test_confirmation_job_not_dispatched_on_failed_order(): void
    {
        Queue::fake();

        $product = Product::factory()->outOfStock()->create();
        $payload = [
            'customer' => ['name' => 'Fail User', 'email' => 'fail@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 1]],
        ];

        $this->postJson('/api/orders', $payload)->assertStatus(422);

        Queue::assertNotPushed(SendOrderConfirmationJob::class);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑰ Multi-item order with mixed tax rates
    // ──────────────────────────────────────────────────────────────────────

    public function test_multi_item_order_with_mixed_tax_rates(): void
    {
        $p1 = Product::factory()->inStock(10)->withPrice(100.00)->withTax(10.00)->create();
        $p2 = Product::factory()->inStock(10)->withPrice(200.00)->withTax(20.00)->create();

        $payload = [
            'customer' => ['name' => 'Multi User', 'email' => 'multi@example.com'],
            'items'    => [
                ['product_id' => $p1->id, 'quantity' => 2], // subtotal=200, tax=20
                ['product_id' => $p2->id, 'quantity' => 1], // subtotal=200, tax=40
            ],
        ];

        $response = $this->postJson('/api/orders', $payload)->assertStatus(201);

        // subtotal = 400, tax = 60, grand = 460
        $this->assertEquals(400.0, $response->json('data.subtotal'));
        $this->assertEquals(60.0, $response->json('data.tax_amount'));
        $this->assertEquals(460.0, $response->json('data.grand_total'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑱ Historical price snapshot preserved
    // ──────────────────────────────────────────────────────────────────────

    public function test_order_item_records_historical_price_and_tax(): void
    {
        $product = Product::factory()->inStock(10)->withPrice(500.00)->withTax(12.00)->create();
        $payload = [
            'customer' => ['name' => 'Hist User', 'email' => 'hist@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 1]],
        ];

        $this->postJson('/api/orders', $payload)->assertStatus(201);

        $this->assertDatabaseHas('order_items', [
            'product_id'     => $product->id,
            'unit_price'     => '500.00',
            'tax_percentage' => '12.00',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑲ Empty items array
    // ──────────────────────────────────────────────────────────────────────

    public function test_validation_fails_for_empty_items(): void
    {
        $payload = [
            'customer' => ['name' => 'Test', 'email' => 'test@example.com'],
            'items'    => [],
        ];

        $this->postJson('/api/orders', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑳ Zero stock product rejection
    // ──────────────────────────────────────────────────────────────────────

    public function test_zero_stock_product_rejected(): void
    {
        $product = Product::factory()->outOfStock()->create();
        $payload = [
            'customer' => ['name' => 'Out User', 'email' => 'out@example.com'],
            'items'    => [['product_id' => $product->id, 'quantity' => 1]],
        ];

        $this->postJson('/api/orders', $payload)->assertStatus(422);
    }
}
