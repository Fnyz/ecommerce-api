<?php

namespace App\Services;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function addItem(User $user, Product $product, int $quantity): Cart
    {
        $product = Product::findOrFail($product->id);

        // check if the requested quantity exceeds the available stock
        if($product->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => ['Requested quantity exceeds available stock.'],
            ]);
        }

        $cart = $this->getOrCreateCart($user);

        $item = $cart->items()->where('product_id', $product->id)->first();

        // if item is exist update the quantity otherwise create a new item with the requested quantity
        if($item){
            $item->update(['quantity' => $item->quantity + $quantity]);
        }else{
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return $cart->load('items.product');
    }

    public function updateItem(User $user, Product $product, int $quantity): ?Cart
    {
        $cart = $this->getOrCreateCart($user);
        $item = $cart->items()->where('product_id', $product->id)->first();

        if(!$item){
            return null;
        }

        // if the requested quantity is less than or equal to zero, delete the item from the cart, otherwise update the quantity
        if($quantity <= 0){
            $item->delete();
        }else{
            $item->update(['quantity' => $quantity]);
        }
        return $cart->load('items.product');
    }

    public function removeItem(User $user, Product $product): ?Cart
    {
        $cart = $this->getOrCreateCart($user);
        $item = $cart->items()->where('product_id', $product->id)->first();

        if(!$item){
            return null;
        }

        $item->delete();

        return $cart->load('items.product');
    }

    public function clear(User $user): Cart
    {
        $cart = $this->getOrCreateCart($user);
        // delete all items in the cart
        $cart->items()->delete();

        return $cart->load('items.product');
    }

    public function getTotal(Cart $cart): float
    {
        // calculate the total price of the cart by summing the price of each item multiplied by its quantity
        return $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }
}
