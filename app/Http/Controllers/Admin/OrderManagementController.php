<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderManagementController extends Controller
{
    //
    public function __construct(protected \App\Services\OrderService $orderServices){}

    public function index(Request $request){
        // get all orders with pagination
        $orders = Order::with('user', 'items')->paginate(20);

        return response()->json($orders);
    }

    public function updateStatus(Request $request, Order $order){
        // validate the request
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,completed',
        ]);

        $order = $this->orderServices->updateStatus($order, $validated['status']);

        return response()->json(['message' => 'Order status updated successfully.', 'order' => $order]);
    }

    public function dashboard(){
        return response()->json([
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'orders_by_status' => Order::selectRaw('status, count(*) as count')->groupBy('status')->get(),
            'low_stock_count' => \App\Models\Product::where('stock', '<=', 10)->count(),
        ]);
    }
}
