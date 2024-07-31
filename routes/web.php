<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-view', [AuthController::class, 'testView']);
Route::get('/login-view', [AuthController::class, 'loginView']);
Route::get('/profile-view', [ProfileController::class, 'form']);