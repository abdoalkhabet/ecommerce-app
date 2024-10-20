<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addItem(Request $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $product = Product::findOrFail($request->product_id);

        if ($product->quantity < $request->quantity) {
            return response()->json(['error' => 'Not enough product quantity available'], 400);
        }
        $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->price = $product->price;
            $cartItem->save();
        } else {

            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]);
        }
        $product->quantity -= $request->quantity;
        $product->save();

        return response()->json($cartItem, 201);
    }

    public function showCart(Request $request)
    {
        $cart = Cart::with('cartItems.product')->where('user_id', $request->user()->id)->first();
        if (!$cart) {
            return response()->json(['message' => 'cart is empty'], 400);
        }

        $cartItem = $cart->cartItems->map(function ($item) {
            return [
                'product_id' => $item->product->id,
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total_price' => $item->quantity * $item->price,
                'image' => $item->product->getFirstMediaUrl('product_main_image')
            ];
        });

        return response()->json([
            'cart_id' => $cart->id,
            'items' => $cartItem
        ], 200);
    }

    public function removeItem(Request $request, $cart_item_id)
    {
        $cartItem = CartItem::findOrFail($cart_item_id);
        $quantityToRemove = $request->input('quantity');

        if (is_null($quantityToRemove)) {
            $product = $cartItem->product;
            $product->quantity += $cartItem->quantity;
            $product->save();
            $cartItem->delete();
            return response()->json(['message' => 'Item removed from cart'], 200);
        } else {

            $cartItem->quantity -= $quantityToRemove;
            $cartItem->save();

            $product = $cartItem->product;
            $product->quantity += $quantityToRemove;
            $product->save();
            return response()->json(['message' => 'Quantity reduced from cart item'], 200);
        }
    }
}
