<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }


    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(
                customerData: $request->input('customer'),
                rawItems:     $request->input('items'),
                amountPaid:   $request->input('amount_paid') !== null
                    ? (float) $request->input('amount_paid')
                    : null,
            );

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully.',
                'data'    => new OrderResource($order),
            ], 201);

        } catch (\RuntimeException $e) {
            
            if ($e->getCode() === 422) {
                $payload = json_decode($e->getMessage(), true);

                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock for one or more items.',
                    'errors'  => $payload,
                ], 422);
            }

            
            Log::error('Unexpected error in OrderController::store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }
}
