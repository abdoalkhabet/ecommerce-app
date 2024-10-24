<?php

namespace App\Http\Controllers;

// namespace App\Models;

use Log;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

use function Pest\Laravel\delete;

class WishlistController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $wishlist = $user->wishlist()->with('product')->get();

        $wishlistWithImages = $wishlist->map(function ($item) {
            return [
                'id' => $item->id,
                'porduct_data' => $item->product->only([
                    'id',
                    'description',
                    'name',
                    'price',
                    'oldPrice',
                    'quantity',
                    'inStock',
                    'discount',
                    'category_id'
                ]),
                'meta' => [
                    'image' => $item->product->getFirstMediaUrl('product_main_image')
                ]
            ];
        });

        return response()->json($wishlistWithImages);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $productId = $request->product_id;
        $productExists = Product::find($productId);

        if (!$productExists) {
            return response()->json(['message' => 'Product not found. Please try again with a valid product ID.'], 404);
        }

        $exists = Wishlist::where('user_id', $user->id)->where('product_id', $productId)->exists();
        if ($exists) {
            return response()->json(['message' => 'Product is already in the wishlist'], 400);
        }
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);
        return response()->json(['message' => 'Product added to wishlist']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        \Log::info('User ID: ' . $user->id);
        \Log::info('Product ID: ' . $id);
        $deletedRows = Wishlist::where('user_id', $user->id)->where('product_id', $id)->delete();

        if ($deletedRows > 0) {
            return response()->json(['message' => 'Product removed from wishlist']);
        } else {
            return response()->json(['message' => 'Product not found in wishlist'], 404);
        }
    }

    public function getSuggestions()
    {
        $user = auth()->user();
        $favoriteProductIds = $user->wishlist()->pluck('product_id');

        if ($favoriteProductIds->isEmpty()) {
            return response()->json(['message' => 'No favorite products found'], 404);
        }

        $suggestedProducts = Product::whereIn('category_id', function ($query) use ($favoriteProductIds) {
            $query->select('category_id')
                ->from('products')
                ->whereId('id', $favoriteProductIds);
        })->whereNotIn('id', $favoriteProductIds)->get();

        if ($suggestedProducts->isEmpty()) {
            return response()->json(['message' => 'No suggested products found'], 404);
        }

        return response()->json($suggestedProducts);
    }
    public function favoriteAnalytics()
    {
        $populerProduct = Product::withCount('wishlists')
            ->orderBy('Whishlists_count', 'desc')
            ->get();

        return response()->json($populerProduct);
    }
}
