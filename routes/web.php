<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WarrantyLookupController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/trang-chu');

Route::get('/trang-chu', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product:slug}', [CartController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/{itemId}', [CartController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/{itemId}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

Route::middleware('auth')->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/thank-you/{order:order_number}', [CheckoutController::class, 'thankYou'])->name('checkout.thank-you');
});

Route::middleware(['auth', 'admin'])->prefix('legacy-admin')->as('legacy-admin.')->group(function () {
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
});

Route::middleware('auth')->group(function () {
    Route::get('/tra-cuu-bao-hanh', [WarrantyLookupController::class, 'index'])->name('warranty-lookup.index');
    Route::post('/tra-cuu-bao-hanh/{warranty}/yeu-cau', [WarrantyLookupController::class, 'store'])
        ->name('warranty-lookup.claims.store');

    Route::get('/don-mua', function () {
        $orders = request()->user()
            ->orders()
            ->with('items')
            ->latest('placed_at')
            ->latest('id')
            ->get();

        return view('orders.purchases', compact('orders'));
    })->name('orders.purchases');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/address', [ProfileController::class, 'updateAddress'])->name('profile.update-address');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/chat', [ChatbotController::class, 'chatbot']);
Route::get('/chat/history', [ChatbotController::class, 'history']);

require __DIR__.'/auth.php';