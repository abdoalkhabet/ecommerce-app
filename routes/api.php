<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth::routes();
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api_login');
// Route::middleware('auth')->group(function () {
//     // Add other routes that require authentication here
// });
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/updateProfile', [ProfileController::class, 'update'])->name('api.profile.update');
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/singleCategory/{id}', [CategoryController::class, 'showProducts']);
    Route::post('/addCategory', [CategoryController::class, 'store']);
    Route::put('/updateCategory/{id}', [CategoryController::class, 'update']);
    Route::delete('/deletCategory/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/single/{id}', [ProductController::class, 'show']);
    Route::post('/addProduct', [ProductController::class, 'store']);
    Route::put('/update/{id}', [ProductController::class, 'update']);
    Route::delete('/deleteProduct/{id}', [ProductController::class, 'destroy']);

    // عرض المنتجات حسب الفئة مع التدوير
    Route::get('/category/{categoryId}', [ProductController::class, 'getByCategory']);
});
Route::get('/products/{productId}/allreviews', [ReviewController::class, 'index']);

// إضافة مراجعة لمنتج معين
Route::post('/products/{productId}/reviews', [ReviewController::class, 'store']);

// حذف مراجعة
// Route::delete('/products/{productId}/reviews/{reviewId}', [ReviewController::class, 'destroy'])->middleware('auth:sanctum');