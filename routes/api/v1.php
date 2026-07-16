<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\OrderManagementController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Admin routes (authenticated and admin-only)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/orders', [OrderManagementController::class, 'index']); // view ALL orders, not just user's own
    Route::put('/orders/{order}/status', [OrderManagementController::class, 'updateStatus']);
    Route::get('/dashboard', [OrderManagementController::class, 'dashboard']); // sales reporting
});

// User routes (authenticated)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Product routes
    Route::get('/products', [ProductController::class, 'index']);

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index']);

    // Carts routes
    Route::get('/carts', [CartController::class, 'index']);
    Route::post('/carts', [CartController::class, 'store']);
    Route::put('/carts/{product}', [CartController::class, 'update']);
    Route::delete('/carts/{product}', [CartController::class, 'destroy']);
    Route::delete('/carts', [CartController::class, 'clear']);

    // Order routes
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Payment routes
    Route::post('/orders/{order}/checkout', [PaymentController::class, 'checkout'])->middleware('throttle:5,1'); // Limit to 5 requests per minute per user
});

// Webhook route — NOT behind auth:sanctum, since Stripe calls this directly, not your logged-in user
Route::post('/stripe/webhook', [PaymentController::class, 'webhook']);
