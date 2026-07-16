<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'E-Commerce API',
        'version' => 'v1',
        'status' => 'active',
        'endpoints' => [
            'auth' => ['register' => 'POST /api/v1/register', 'login' => 'POST /api/v1/login'],
            'products' => ['list' => 'GET /api/v1/products', 'show' => 'GET /api/v1/products/{id}'],
            'cart' => ['view' => 'GET /api/v1/cart', 'add' => 'POST /api/v1/cart'],
            'orders' => ['list' => 'GET /api/v1/orders', 'create' => 'POST /api/v1/orders'],
            'admin' => ['dashboard' => 'GET /api/v1/admin/dashboard'],
        ],
    ]);
});
