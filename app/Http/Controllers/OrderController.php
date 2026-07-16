<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // If you already create the business logic for order, then well go ahead use the services to create the order and order items. But for now, we will just create a simple order and order items.
    public function __construct(protected \App\Services\OrderService $orderService){}

    public function index(Request $request)
    {
        $orders = $this->orderService->getUserOrders($request->user());
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500'
        ]);

        $order = $this->orderService->createFromCart($request->user(), $request->shipping_address);

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order,
        ], 201);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        // Load the order items and return the order details
        return response()->json($order->load('items'));

    }
}
