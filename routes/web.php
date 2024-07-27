<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-view', [AuthController::class, 'testView']);
Route::get('/login-view', [AuthController::class, 'loginView']);