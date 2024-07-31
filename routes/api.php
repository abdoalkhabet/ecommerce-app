<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

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
    Route::post('/update', [ProfileController::class, 'update'])->name('api.profile.update');
    Route::post('/logout', [AuthController::class, 'logout']);
});
