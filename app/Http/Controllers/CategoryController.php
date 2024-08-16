<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all()->map(function ($Category) {
            return [
                'id' => $Category->id,
                'name' => $Category->name,
                'image_url' => $Category->getFirstMedia('category_images') ?? '',
            ];
        });
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category = Category::create([
            'name' => $request->name,
        ]);

        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')->toMediaCollection('category_images');
        }
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
        ]);
        if ($request->hasFile('image')) {
            $category->clearMediaCollection('category_images');
            $category->addMediaFromRequest('imade')->toMediaCollection('category_images');
        }
        return response()->json(['massage' => 'category updated successfully']);
    }
    public function showProducts($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $products = $category->products()->paginate(10);

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'oldPrice' => $product->oldPrice,
                'quantity' => $product->quantity,
                'inStock' => $product->isInStock(),
                'averageRating' => $product->averageRating(),
                'reviews_count' => $product->reviews()->count(),
                'main_image_url' => $product->getFirstMediaUrl('main_images'),
                'images_urls' => $product->getMedia('images')->map->getUrl(),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'products' => $products,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'total_pages' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ],
        ]);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->ClearMediaCollection('category_images');
        $category->delete();
        return response()->json(['massage' => 'category deleted successfully']);
    }
}
