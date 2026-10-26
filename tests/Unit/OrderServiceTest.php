<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for OrderService business logic.
 *
 * These tests focus on calculation accuracy, consolidation logic, and
 * the RuntimeException thrown for insufficient stock — without going through
 * the HTTP layer.
 */
class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrderService();
    }

    // ──────────────────────────────────────────────────────────────────────
    // ① Subtotal is price × quantity
    // ──────────────────────────────────────────────────────────────────────

    public function test_subtotal_equals_price_times_quantity(): void
    {
        $product = Product::factory()->inStock(10)->withPrice(150.00)->withTax(0.00)->create();
        $customer = Customer::factory()->create();

        $order = $this->service->createOrder(
            ['name' => $customer->name, 'email' => $customer->email],
            [['product_id' => $product->id, 'quantity' => 4]]
        );

        // 150 × 4 = 600
        $this->assertEquals('600.00', $order->subtotal);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ② Tax amount is subtotal × (tax% / 100)
    // ──────────────────────────────────────────────────────────────────────

    public function test_tax_amount_calculated_correctly(): void
    {
        $product = Product::factory()->inStock(10)->withPrice(200.00)->withTax(15.00)->create();
        $customer = Customer::factory()->create();

        $order = $this->service->createOrder(
            ['name' => $customer->name, 'email' => $customer->email],
            [['product_id' => $product->id, 'quantity' => 1]]
        );

        // subtotal=200, tax=200*15/100=30
        $this->assertEquals('30.00', $order->tax_amount);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ③ Grand total = subtotal + tax
    // ──────────────────────────────────────────────────────────────────────

    public function test_grand_total_equals_subtotal_plus_tax(): void
    {
        $product = Product::factory()->inStock(10)->withPrice(500.00)->withTax(18.00)->create();
        $customer = Customer::factory()->create();

        $order = $this->service->createOrder(
            ['name' => $customer->name, 'email' => $customer->email],
            [['product_id' => $product->id, 'quantity' => 2]]
        );

        // subtotal=1000, tax=180, grand=1180
        $this->assertEquals('1000.00', $order->subtotal);
        $this->assertEquals('180.00', $order->tax_amount);
        $this->assertEquals('1180.00', $order->grand_total);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ④ Insufficient stock throws RuntimeException with code 422
    // ──────────────────────────────────────────────────────────────────────

    public function test_insufficient_stock_throws_runtime_exception(): void
    {
        $product = Product::factory()->inStock(2)->create();
        $customer = Customer::factory()->create();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(422);

        $this->service->createOrder(
            ['name' => $customer->name, 'email' => $customer->email],
            [['product_id' => $product->id, 'quantity' => 5]]
        );
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑤ Duplicate product IDs are consolidated
    // ──────────────────────────────────────────────────────────────────────

    public function test_duplicate_product_ids_consolidated_and_quantities_summed(): void
    {
        $product = Product::factory()->inStock(20)->withPrice(100.00)->withTax(0.00)->create();
        $customer = Customer::factory()->create();

        $order = $this->service->createOrder(
            ['name' => $customer->name, 'email' => $customer->email],
            [
                ['product_id' => $product->id, 'quantity' => 3],
                ['product_id' => $product->id, 'quantity' => 5],
            ]
        );

        // qty = 8, price = 100 → subtotal = 800
        $this->assertEquals('800.00', $order->subtotal);

        $item = $order->items()->first();
        $this->assertSame(8, $item->quantity);

        // Stock deducted by 8
        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 12,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑥ Zero-tax product — tax_amount is 0
    // ──────────────────────────────────────────────────────────────────────

    public function test_zero_tax_product_has_zero_tax_amount(): void
    {
        $product = Product::factory()->inStock(10)->withPrice(300.00)->withTax(0.00)->create();
        $customer = Customer::factory()->create();

        $order = $this->service->createOrder(
            ['name' => $customer->name, 'email' => $customer->email],
            [['product_id' => $product->id, 'quantity' => 2]]
        );

        $this->assertEquals('0.00', $order->tax_amount);
        $this->assertEquals('600.00', $order->grand_total);
    }

    // ──────────────────────────────────────────────────────────────────────
    // ⑦ Transaction rollback — no order or items persisted on failure
    // ──────────────────────────────────────────────────────────────────────

    public function test_transaction_rollback_on_insufficient_stock(): void
    {
        $product = Product::factory()->inStock(1)->create();
        $customer = Customer::factory()->create();

        try {
            $this->service->createOrder(
                ['name' => $customer->name, 'email' => $customer->email],
                [['product_id' => $product->id, 'quantity' => 5]]
            );
        } catch (\RuntimeException) {
            // Expected
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        // Stock unchanged
        $this->assertDatabaseHas('products', [
            'id'             => $product->id,
            'stock_quantity' => 1,
        ]);
    }
}
