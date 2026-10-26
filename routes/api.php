<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Store Order & Inventory Management
|--------------------------------------------------------------------------
*/

// Order endpoints
Route::post('/orders', [OrderController::class, 'store']);

// Customer endpoints
Route::get('/customers/orders', [CustomerController::class, 'orders']);

// Product endpoints
Route::get('/products',          [ProductController::class, 'index']);
Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
