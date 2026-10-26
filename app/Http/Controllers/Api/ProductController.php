<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LowStockRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{

    private const DEFAULT_THRESHOLD = 10;


    public function index(): JsonResponse
    {
        $products = Product::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products),
        ]);
    }


    public function lowStock(LowStockRequest $request): JsonResponse
    {
        $threshold = (int) $request->input(
            'threshold',
            (int) config('app.low_stock_threshold', self::DEFAULT_THRESHOLD)
        );

        $products = Product::lowStock($threshold)
            ->orderBy('stock_quantity')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success'   => true,
            'message'   => "Products with stock below {$threshold} retrieved successfully.",
            'threshold' => $threshold,
            'count'     => $products->count(),
            'data'      => ProductResource::collection($products),
        ]);
    }
}
