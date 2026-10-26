<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingPageTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────
    // ① Billing page loads
    // ──────────────────────────────────────────────────────────────────────

    public function test_billing_page_loads_successfully(): void
    {
        Product::factory()->count(3)->inStock()->create();

        $this->get('/billing')->assertStatus(200)
            ->assertSee('Store Billing')
            ->assertSee('New Order');
    }

    // ──────────────────────────────────────────────────────────────────────
    // ② Root redirects to billing
    // ──────────────────────────────────────────────────────────────────────

    public function test_root_redirects_to_billing(): void
    {
        $this->get('/')->assertRedirect('/billing');
    }

    // ──────────────────────────────────────────────────────────────────────
    // ③ Customer lookup — existing customer found
    // ──────────────────────────────────────────────────────────────────────

    public function test_customer_lookup_returns_existing_customer(): void
    {
        Customer::factory()->create(['name' => 'Surya Kumar', 'email' => 'surya@test.com']);

        $this->getJson('/billing/customer-lookup?email=surya@test.com')
            ->assertStatus(200)
            ->assertJson(['found' => true, 'name' => 'Surya Kumar']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ④ Customer lookup — unknown email
    // ──────────────────────────────────────────────────────────────────────

    public function test_customer_lookup_returns_not_found_for_unknown_email(): void
    {
        $this->getJson('/billing/customer-lookup?email=nobody@test.com')
            ->assertStatus(200)
            ->assertJson(['found' => false]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑤ Customer lookup — invalid email fails validation
    // ──────────────────────────────────────────────────────────────────────

    public function test_customer_lookup_rejects_invalid_email(): void
    {
        $this->getJson('/billing/customer-lookup?email=not-an-email')
            ->assertStatus(422);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑥ Low-stock AJAX endpoint
    // ──────────────────────────────────────────────────────────────────────

    public function test_low_stock_ajax_endpoint_returns_correct_products(): void
    {
        Product::factory()->create(['stock_quantity' => 3,  'code' => 'LS-001']);
        Product::factory()->create(['stock_quantity' => 50, 'code' => 'LS-002']);

        $res = $this->getJson('/billing/low-stock')->assertStatus(200);
        $this->assertCount(1, $res->json('products'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑦ Products list API (used by billing JS)
    // ──────────────────────────────────────────────────────────────────────

    public function test_products_api_returns_all_products(): void
    {
        Product::factory()->count(4)->inStock()->create();

        $this->getJson('/api/products')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(4, 'data');
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑧ Full order via API with amount_paid stored
    // ──────────────────────────────────────────────────────────────────────

    public function test_order_stores_amount_paid_and_change(): void
    {
        $product = Product::factory()->inStock(10)->withPrice(100.00)->withTax(0.00)->create();
        $payload = [
            'customer'    => ['name' => 'Test', 'email' => 'test@test.com'],
            'items'       => [['product_id' => $product->id, 'quantity' => 2]],
            'amount_paid' => 250.00,
        ];

        $res = $this->postJson('/api/orders', $payload)->assertStatus(201);

        // grand_total = 200.00, paid = 250 → change = 50
        $this->assertDatabaseHas('orders', [
            'amount_paid'   => '250.00',
            'change_amount' => '50.00',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑨ Insufficient payment validation
    // ──────────────────────────────────────────────────────────────────────

    public function test_negative_amount_paid_is_rejected(): void
    {
        $product = Product::factory()->inStock(10)->withPrice(100.00)->withTax(0.00)->create();
        $payload = [
            'customer'    => ['name' => 'Test', 'email' => 'test@test.com'],
            'items'       => [['product_id' => $product->id, 'quantity' => 1]],
            'amount_paid' => -10,
        ];

        $this->postJson('/api/orders', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['amount_paid']);
    }
}
