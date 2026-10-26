<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────
    // ① Valid customer with orders
    // ──────────────────────────────────────────────────────────────────────

    public function test_customer_order_history_returned_successfully(): void
    {
        $customer = Customer::factory()->create(['email' => 'history@example.com']);
        $product  = Product::factory()->inStock(20)->withPrice(100.00)->withTax(10.00)->create();

        // Place an order via the API to ensure full pipeline runs
        $this->postJson('/api/orders', [
            'customer' => ['name' => $customer->name, 'email' => $customer->email],
            'items'    => [['product_id' => $product->id, 'quantity' => 2]],
        ])->assertStatus(201);

        $response = $this->getJson('/api/customers/orders?email=history@example.com');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'history@example.com')
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'email',
                    'orders' => [
                        '*' => [
                            'id', 'subtotal', 'tax_amount', 'grand_total', 'items',
                        ],
                    ],
                ],
            ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ② Unknown customer → 404
    // ──────────────────────────────────────────────────────────────────────

    public function test_unknown_customer_returns_404(): void
    {
        $this->getJson('/api/customers/orders?email=nobody@example.com')
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ③ Customer with no orders
    // ──────────────────────────────────────────────────────────────────────

    public function test_customer_with_no_orders_returns_empty_orders(): void
    {
        Customer::factory()->create(['email' => 'noorders@example.com']);

        $response = $this->getJson('/api/customers/orders?email=noorders@example.com');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'noorders@example.com')
            ->assertJsonCount(0, 'data.orders');
    }

    // ──────────────────────────────────────────────────────────────────────
    // ④ Missing email parameter → 422
    // ──────────────────────────────────────────────────────────────────────

    public function test_missing_email_returns_validation_error(): void
    {
        $this->getJson('/api/customers/orders')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑤ Invalid email format → 422
    // ──────────────────────────────────────────────────────────────────────

    public function test_invalid_email_format_returns_validation_error(): void
    {
        $this->getJson('/api/customers/orders?email=not-an-email')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑥ Order history includes product details in items
    // ──────────────────────────────────────────────────────────────────────

    public function test_order_history_includes_product_details(): void
    {
        $customer = Customer::factory()->create(['email' => 'detail@example.com']);
        $product  = Product::factory()->inStock(10)->withPrice(250.00)->withTax(18.00)->create();

        $this->postJson('/api/orders', [
            'customer' => ['name' => $customer->name, 'email' => $customer->email],
            'items'    => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertStatus(201);

        $response = $this->getJson('/api/customers/orders?email=detail@example.com');

        $response->assertStatus(200);

        $item = $response->json('data.orders.0.items.0');
        $this->assertArrayHasKey('product_name', $item);
        $this->assertArrayHasKey('unit_price', $item);
        $this->assertArrayHasKey('tax_percentage', $item);
        $this->assertArrayHasKey('subtotal', $item);
        $this->assertArrayHasKey('tax_amount', $item);
        $this->assertArrayHasKey('total', $item);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑦ Multiple orders in history
    // ──────────────────────────────────────────────────────────────────────

    public function test_customer_multiple_orders_are_all_returned(): void
    {
        $customer = Customer::factory()->create(['email' => 'multi@example.com']);
        $product  = Product::factory()->inStock(50)->withPrice(100.00)->withTax(0.00)->create();

        // Place two orders
        $this->postJson('/api/orders', [
            'customer' => ['name' => $customer->name, 'email' => $customer->email],
            'items'    => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertStatus(201);

        $this->postJson('/api/orders', [
            'customer' => ['name' => $customer->name, 'email' => $customer->email],
            'items'    => [['product_id' => $product->id, 'quantity' => 2]],
        ])->assertStatus(201);

        $response = $this->getJson('/api/customers/orders?email=multi@example.com');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.orders');
    }
}
