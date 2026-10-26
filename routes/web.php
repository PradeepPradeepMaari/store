<?php

use App\Http\Controllers\Web\BillingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('billing.index');
});

// Billing routes
Route::prefix('billing')->name('billing.')->group(function () {
    Route::get('/',                         [BillingController::class, 'index'])->name('index');
    Route::get('/customer-lookup',          [BillingController::class, 'customerLookup'])->name('customer-lookup');
    Route::get('/low-stock',                [BillingController::class, 'lowStock'])->name('low-stock');
    Route::get('/product/{id}/stock',       [BillingController::class, 'productStock'])->name('product-stock');
});
