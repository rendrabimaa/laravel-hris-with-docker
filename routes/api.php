<?php

use Illuminate\Support\Facades\Route;

// Anggap saja routes ini sudah dibungkus auth sanctum nantinya
Route::middleware(['auth:sanctum'])->group(function () {

    // Cuma bisa diakses yang punya permission 'manage-users'
    Route::get('/admin/users', function () {
        return response()->json(['message' => 'Masuk ke menu kelola user']);
    })->middleware('check.permission:manage-users');

    // Cuma bisa diakses yang punya permission 'manage-products'
    Route::get('/staff/products', function () {
        return response()->json(['message' => 'Masuk ke menu kelola produk']);
    })->middleware('check.permission:manage-products');

});

// routes/api.php
use App\Http\Controllers\Api\AuthController;
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});