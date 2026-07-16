<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name'    => 'E-Commerce API',
        'version' => 'v1',
        'status'  => 'read-only demo',
        'note'    => 'This API is in read-only mode. Add, update, and delete operations are disabled. Use the test credentials below to explore the API.',
        'test_credentials' => [
            'email'    => 'admin@gmail.com',
            'password' => 'admin123',
            'note'     => 'Login via POST /api/v1/login to get a Bearer token, then use it to access protected endpoints.',
        ],
        'endpoints' => [
            'auth' => [
                'login'  => 'POST /api/v1/login',
                'logout' => 'POST /api/v1/logout',
            ],
            'products' => [
                'list' => 'GET /api/v1/products',
                'show' => 'GET /api/v1/products/{id}',
            ],
            'categories' => [
                'list' => 'GET /api/v1/categories',
            ],
            'cart' => [
                'view' => 'GET /api/v1/carts',
            ],
            'orders' => [
                'list' => 'GET /api/v1/orders',
                'show' => 'GET /api/v1/orders/{id}',
            ],
            'admin' => [
                'dashboard' => 'GET /api/v1/admin/dashboard',
                'orders'    => 'GET /api/v1/admin/orders',
            ],
        ],
    ]);
});
