<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{

    public function createOrder(array $customerData, array $rawItems, ?float $amountPaid = null): Order
    {
        // ── 1. Consolidate duplicate product IDs by summing their quantities ──
        $consolidated = $this->consolidateItems($rawItems);


        $productIds = $consolidated->keys()->sort()->values()->all();

        $order = DB::transaction(function () use ($customerData, $consolidated, $productIds, $amountPaid) {

            // ── 2. Lock product rows ──────────────────────────────────────────
            /** @var Collection<int, Product> $products */
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->orderBy('id')          // consistent row-lock order
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // ── 3. Stock validation inside the lock ───────────────────────────
            $insufficientItems = [];

            foreach ($consolidated as $productId => $requestedQty) {
                $product = $products->get($productId);

                if ($product === null) {
                    // Should not happen — FormRequest already validates exists,
                    // but guard defensively.
                    $insufficientItems[] = [
                        'product_id' => $productId,
                        'message'    => "Product #{$productId} not found.",
                    ];
                    continue;
                }

                if ($product->stock_quantity < $requestedQty) {
                    $insufficientItems[] = [
                        'product_id'   => $productId,
                        'product_name' => $product->name,
                        'requested'    => $requestedQty,
                        'available'    => $product->stock_quantity,
                        'message'      => "Insufficient stock for '{$product->name}'. "
                            . "Requested: {$requestedQty}, Available: {$product->stock_quantity}.",
                    ];
                }
            }

            if (!empty($insufficientItems)) {
                throw new \RuntimeException(
                    json_encode(['items' => $insufficientItems]),
                    422
                );
            }

            // ── 4. Find or create customer ────────────────────────────────────
            $customer = Customer::firstOrCreate(
                ['email' => $customerData['email']],
                ['name'  => $customerData['name']]
            );

            // ── 5. Calculate per-item figures ─────────────────────────────────
            $orderSubtotal  = '0.00';
            $orderTaxAmount = '0.00';
            $itemsData      = [];

            foreach ($consolidated as $productId => $requestedQty) {
                $product = $products->get($productId);

                $itemSubtotal  = bcmul((string) $product->price, (string) $requestedQty, 4);
                $itemTaxAmount = bcdiv(
                    bcmul($itemSubtotal, (string) $product->tax_percentage, 4),
                    '100',
                    4
                );
                $itemTotal = bcadd($itemSubtotal, $itemTaxAmount, 4);

                $orderSubtotal  = bcadd($orderSubtotal,  $itemSubtotal,  4);
                $orderTaxAmount = bcadd($orderTaxAmount, $itemTaxAmount, 4);

                $itemsData[] = [
                    'product_id'     => $productId,
                    'quantity'       => $requestedQty,
                    'unit_price'     => $this->round4($product->price),
                    'tax_percentage' => $this->round4($product->tax_percentage),
                    'subtotal'       => $this->round4($itemSubtotal),
                    'tax_amount'     => $this->round4($itemTaxAmount),
                    'total'          => $this->round4($itemTotal),
                ];
            }

            $orderGrandTotal = bcadd($orderSubtotal, $orderTaxAmount, 4);

            // ── 6. Persist order + items ──────────────────────────────────────
            $changeAmount = $amountPaid !== null
                ? max(0, bcsub((string) $amountPaid, $orderGrandTotal, 2))
                : null;

            /** @var Order $order */
            $order = Order::create([
                'customer_id'  => $customer->id,
                'subtotal'     => $this->round2($orderSubtotal),
                'tax_amount'   => $this->round2($orderTaxAmount),
                'grand_total'  => $this->round2($orderGrandTotal),
                'amount_paid'  => $amountPaid,
                'change_amount'=> $changeAmount,
            ]);

            $order->items()->createMany($itemsData);

            // ── 7. Atomic stock deduction ─────────────────────────────────────
            foreach ($consolidated as $productId => $requestedQty) {
                Product::where('id', $productId)
                    ->decrement('stock_quantity', $requestedQty);
            }

            return $order;
        });

        // ── 8. Dispatch confirmation job after commit ─────────────────────────
        // Laravel 10 afterCommit() on the job ensures it only runs if the
        // transaction commits successfully.
        SendOrderConfirmationJob::dispatch($order->id)->afterCommit();

        // Reload with all relationships for the API response
        return $order->load(['customer', 'items.product']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Merge duplicate product IDs by summing quantities.
     *
     * @param  array  $items  [['product_id' => int, 'quantity' => int], ...]
     * @return Collection<int, int>  keyed by product_id → total quantity
     */
    private function consolidateItems(array $items): Collection
    {
        return collect($items)->groupBy('product_id')->map(
            fn (Collection $group) => $group->sum('quantity')
        );
    }

    /** Round a bcmath string to 4 decimal places (internal precision). */
    private function round4(mixed $value): string
    {
        return bcadd((string) $value, '0', 4);
    }

    /** Round a bcmath string to 2 decimal places (storage precision). */
    private function round2(mixed $value): string
    {
        return bcadd((string) $value, '0', 2);
    }
}
