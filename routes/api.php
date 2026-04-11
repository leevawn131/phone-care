<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WarrantyController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'store']);
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
Route::get('/warranties/check', [WarrantyController::class, 'check']);
