<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{

    public function index()
    {
        $products  = Product::orderBy('name')->get();
        $threshold = (int) config('app.low_stock_threshold', 10);
        $lowStock  = Product::lowStock($threshold)->orderBy('stock_quantity')->orderBy('name')->get();

        return view('billing.new-order', compact('products', 'lowStock', 'threshold'));
    }


    public function customerLookup(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $customer = Customer::where('email', $request->query('email'))->first();

        if ($customer) {
            return response()->json([
                'found' => true,
                'name'  => $customer->name,
            ]);
        }

        return response()->json(['found' => false]);
    }


    public function lowStock(): JsonResponse
    {
        $threshold = (int) config('app.low_stock_threshold', 10);
        $products  = Product::lowStock($threshold)->orderBy('stock_quantity')->orderBy('name')->get();

        return response()->json([
            'threshold' => $threshold,
            'products'  => $products->map(fn ($p) => [
                'name'           => $p->name,
                'stock_quantity' => $p->stock_quantity,
            ]),
        ]);
    }


    public function productStock(int $id): JsonResponse
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json(['stock_quantity' => $product->stock_quantity]);
    }
}
