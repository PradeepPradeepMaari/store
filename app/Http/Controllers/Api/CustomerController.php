<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerOrderHistoryRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{

    public function orders(CustomerOrderHistoryRequest $request): JsonResponse
    {
        $customer = Customer::with([
            'orders' => fn ($q) => $q->latest('created_at'),
            'orders.items.product',
        ])->where('email', $request->input('email'))->first();

        if ($customer === null) {
            return response()->json([
                'success' => false,
                'message' => 'No customer found with the provided email address.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer order history retrieved successfully.',
            'data'    => new CustomerResource($customer),
        ]);
    }
}
