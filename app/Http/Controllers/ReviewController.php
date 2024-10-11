<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index($prductId)
    {
        $product = Product::findOrFail($prductId);
        $reviews = $product->reviews()->get();
        $formattedreviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'review' => $review->review,
                'rating' => $review->rating,
                'created_at' => $review->created_at,
                'updated_at' => $review->updated_at,
            ];
        });
        return response()->json($formattedreviews);
    }
    public function store(Request $request, $productId)
    {

        $validator = Validator::make($request->all(), [
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $product = Product::findOrfail($productId);
        $review = $product->reviews()->create([
            'review' => $request->review,
            'rating' => $request->rating,
        ]);
        return response()->json($review, 201);
    }
}
