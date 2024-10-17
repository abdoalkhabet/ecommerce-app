<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with(['category', 'mainImage', 'otherImages', 'reviews'])->get();
        $products = $products->map(function ($product) {
            $discountedPrice = $product->discount ? $product->price * (1 - $product->discount / 100) : null;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'discountedPrice' => $discountedPrice ?? '',
                'oldPrice' => $product->oldPrice,
                'quantity' => $product->quantity,
                'inStock' => $product->isInStock(),
                'category_name' => $product->category->name ?? '',
                'averageRating' => $product->averageRating() ?? null,
                'reviews_count' => $product->reviews()->count() ?? null,
                // 'main_image_url' => $product->getFirstMediaUrl('product_main_image'),
                // 'images_urls' => $product->getMedia('product_other_images')->map->getUrl(),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                'main_image' => $product->mainImage->first()->original_url,
                'other_images' => $product->otherImages->map(fn($i) => $i->original_url)
            ];
        });
        return response()->json($products);
    }
    public function show($id)
    {
        $product = Product::with(['category', 'reviews', 'media'])->findOrFail($id);

        $discountedPrice = $product->discount ? $product->price * (1 - $product->discount / 100) : null;

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'discountedPrice' => $discountedPrice ?? '',
            'oldPrice' => $product->oldPrice ?? '',
            'quantity' => $product->quantity,
            'inStock' => $product->isInStock(),
            'category_name' => $product->category->name ?? '',
            'averageRating' => $product->averageRating() ?? null,
            'reviews_count' => $product->reviews()->count() ?? null,
            'main_image_url' => $product->getFirstMediaUrl('product_main_image'),
            'images_urls' => $product->getMedia('product_other_images')->map->getUrl(),
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'oldPrice' => 'nullable|numeric',
            'quantity' => 'required|integer',
            // 'category_id' => 'required|exists:categories,id',
            'category_name' => 'required|string|exists:categories,name', // important line
            'discount' => 'nullable|numeric',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $category = Category::where('name', $request->input('category_name'))->firstOrFail();

        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'oldPrice' => $request->input('oldPrice'),
            'quantity' => $request->input('quantity'),
            'category_id' => $category->id,
            'discount' => $request->input('discount'),
        ]);
        if ($request->hasFile('main_image')) {
            $product->addMediaFromRequest('main_image')->toMediaCollection('product_main_image');
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('product_other_images');
            }
        }
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'discountedPrice' => $discountedPrice ?? '',
            'oldPrice' => $product->oldPrice ?? '',
            'quantity' => $product->quantity,
            'inStock' => $product->isInStock(),
            'category_name' => $product->category->name ?? '',
            'averageRating' => $product->averageRating() ?? null,
            'reviews_count' => $product->reviews()->count() ?? null,
            'main_image_url' => $product->getFirstMediaUrl('product_main_image'),
            'images_urls' => $product->getMedia('product_other_images')->map(function ($media) {
                return $media->getUrl();
            }),
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'oldPrice' => 'nullable|numeric',
            'quantity' => 'required|integer',
            // 'category_id' => 'required|exists:categories,id',
            'category_name' => 'required|string|exists:categories,name',
            'discount' => 'nullable|numeric',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $category = Category::where('name', $request->input('category_name'))->firstOrFail();


        $product = Product::findOrFail($id);

        $product->update($request->only([
            'name',
            'description',
            'price',
            'oldPrice',
            'quantity',
            'category_id',
            'discount'
        ]));

        if ($request->hasFile('main_image')) {

            $product->clearMediaCollection('product_main_image');
            $product->addMediaFromRequest('main_image')->toMediaCollection('product_main_image');
        }

        if ($request->hasFile('images')) {
            $product->clearMediaCollection('product_other_images');
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('product_other_images');
            }
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'discountedPrice' => $discountedPrice ?? '',
            'oldPrice' => $product->oldPrice ?? '',
            'quantity' => $product->quantity,
            'inStock' => $product->isInStock(),
            'category_name' => $product->category->name ?? '',
            'averageRating' => $product->averageRating() ?? null,
            'reviews_count' => $product->reviews()->count() ?? null,
            'main_image_url' => $product->getFirstMediaUrl('product_main_image'),
            'images_urls' => $product->getMedia('product_other_images')->map->getUrl(),
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->ClearMediaCollection('product_main_image');
        $product->ClearMediaCollection('product_other_images');
        $product->delete();
        return response()->json(['massage' => 'Product deleted successfully']);
    }
    public function getByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->with(['category', 'media', 'reviews'])
            ->paginate(10);

        $products->getCollection()->transform(function ($product) {
            $discountedPrice = $product->discount ? $product->price * (1 - $product->discount / 100) : null;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'discountedPrice' => $discountedPrice ?? '',
                'oldPrice' => $product->oldPrice,
                'quantity' => $product->quantity,
                'inStock' => $product->isInStock(),
                'category_name' => $product->category->name ?? '',
                'averageRating' => $product->averageRating() ?? null,
                'reviews_count' => $product->reviews()->count() ?? null,
                'main_image_url' => $product->getFirstMediaUrl('product_main_image'),
                'images_urls' => $product->getMedia('product_other_images')->map->getUrl(),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'products' => $products->items(),
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
}