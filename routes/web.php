<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'E-Commerce API',
        'version' => 'v1',
        'status' => 'active',
        'endpoints' => [
            'auth' => [
                'register' => 'POST /api/v1/register',
                'login'    => 'POST /api/v1/login',
                'logout'   => 'POST /api/v1/logout',
            ],
            'products' => [
                'list'   => 'GET /api/v1/products',
                'show'   => 'GET /api/v1/products/{id}',
                'create' => 'POST /api/v1/products',
                'update' => 'PUT /api/v1/products/{id}',
                'delete' => 'DELETE /api/v1/products/{id}',
            ],
            'categories' => [
                'list'   => 'GET /api/v1/categories',
                'create' => 'POST /api/v1/categories',
                'update' => 'PUT /api/v1/categories/{id}',
                'delete' => 'DELETE /api/v1/categories/{id}',
            ],
            'cart' => [
                'view'   => 'GET /api/v1/carts',
                'add'    => 'POST /api/v1/carts',
                'update' => 'PUT /api/v1/carts/{product}',
                'remove' => 'DELETE /api/v1/carts/{product}',
                'clear'  => 'DELETE /api/v1/carts',
            ],
            'orders' => [
                'list'     => 'GET /api/v1/orders',
                'show'     => 'GET /api/v1/orders/{id}',
                'create'   => 'POST /api/v1/orders',
                'checkout' => 'POST /api/v1/orders/{id}/checkout',
            ],
            'admin' => [
                'dashboard'     => 'GET /api/v1/admin/dashboard',
                'orders'        => 'GET /api/v1/admin/orders',
                'update_status' => 'PUT /api/v1/admin/orders/{id}/status',
            ],
        ],
    ]);
});
