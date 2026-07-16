<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(protected CartService $cartService){}

    public function createFromCart(User $user, string $shippingAddress): Order
    {
        // get list of items in the cart
        $cart = $this->cartService->getOrCreateCart($user)->load('items.product');

        // check if the cart item is empty , since cart has many items, we can use the isEmpty() method to check if the cart has any items
        if($cart->items->isEmpty()){
            throw ValidationException::withMessages([
                'cart' => ['Cart is empty.'],
            ]);
        }

        // Verify stock is still available for each item in the cart
        foreach($cart->items as $item){
            if($item->quantity > $item->product->stock){
                throw ValidationException::withMessages([
                    'stock' => ["Insufficient stock for product: {$item->product->name}."],
                ]);
            }
        }

        // get the total price of the cart items
        $total = $this->cartService->getTotal($cart);

        // prepare order data
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(10)), // Generate a random order number
            'status' => 'pending',
            'total' => $total,
            'shipping_address' => $shippingAddress,
            'payment_status' => 'unpaid',
        ]);

        // create order items from cart items
        foreach($cart->items as $item){
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
            ]);
        }

        // Clear the cart after creating the order
        $this->cartService->clear($user);

        // load the item that is inserted inside the order and return the order with items
        return $order->load('items');
    }

    public function getUserOrders(User $user){
        return Order::where('user_id', $user->id)
        ->where('payment_status', '!=', 'paid') // Exclude paid orders
        ->with('items')->latest()->paginate(10);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        // Broadcast the order status update event
        broadcast(new \App\Events\OrderStatusUpdated($order));

        return $order;
    }




}
