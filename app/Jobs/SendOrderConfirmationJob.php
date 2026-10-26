<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     *
     * @param  int  $orderId  We pass the order ID rather than the model to
     *                        avoid serialisation issues after a fresh reload.
     */
    public function __construct(public readonly int $orderId)
    {
    }

    /**
     * Execute the job.
     *
     * In production this would send a real email via Mail::to(...)->send(...).
     * For this assignment we simulate the confirmation by writing to the log.
     */
    public function handle(): void
    {
        $order = Order::with(['customer', 'items.product'])->find($this->orderId);

        if ($order === null) {
            Log::warning("[OrderConfirmation] Order #{$this->orderId} not found — skipping confirmation.");
            return;
        }

        Log::info('[OrderConfirmation] Sending order confirmation', [
            'order_id'    => $order->id,
            'customer'    => $order->customer->name,
            'email'       => $order->customer->email,
            'grand_total' => $order->grand_total,
            'items_count' => $order->items->count(),
            'sent_at'     => now()->toDateTimeString(),
        ]);

        // ── Simulate email body ───────────────────────────────────────────────
        Log::channel('stack')->info(
            sprintf(
                "[OrderConfirmation] ✓ Order #%d confirmed for %s <%s> | Grand Total: %.2f | Items: %d",
                $order->id,
                $order->customer->name,
                $order->customer->email,
                $order->grand_total,
                $order->items->count()
            )
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[OrderConfirmation] Job failed', [
            'order_id'  => $this->orderId,
            'error'     => $exception->getMessage(),
            'failed_at' => now()->toDateTimeString(),
        ]);
    }
}
