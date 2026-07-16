<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService){}

    // Get the current user's cart
    public function index(Request $request){
        $cart = $this->cartService->getOrCreateCart($request->user())->load('items.product');

        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ];
        });

        $total = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'items' => $items,
            'total' => $total ? floatval(number_format($total, 2, '.', '')) : '0.00',
        ]);
    }

    // Add item to cart
    public function store(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = $this->cartService->addItem($request->user(), $product, $request->quantity);

        return response()->json([
            'message' => 'Item added to cart successfully',
            'cart' => $cart,
        ], 201);
    }

    // Update item quantity in cart
    public function update(Request $request, Product $product){
        $validate = $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = $this->cartService->updateItem($request->user(), $product, $validate['quantity']);

        if(!$cart){
            return response()->json([
                'message' => 'Item not found in cart',
            ], 404);
        }

        return response()->json([
            'message' => 'Cart item quantity updated successfully',
            'cart' => $cart,
        ]);
    }

    // Remove item from cart
    public function destroy(Request $request, Product $product){
        $cart = $this->cartService->removeItem($request->user(), $product);

        if(!$cart){
            return response()->json([
                'message' => 'Item not found in cart',
            ], 404);
        }

        return response()->json([
            'message' => 'Item removed from cart successfully',
            'cart' => $cart,
        ]);
    }

    // Clear all items from cart
    public function clear(Request $request){
        $cart = $this->cartService->clear($request->user());

        return response()->json([
            'message' => 'Cart cleared successfully',
            'cart' => $cart,
        ]);
    }
}
