<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController; // Tetap mengarah ke namespace Api Anda
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =========================
// PUBLIC (Tanpa Login)
// =========================

Route::get('/test', function () {
    return response()->json(['message' => 'API berjalan']);
});

// Mobile & Admin Auth
Route::post('/register', [MobileAuthController::class, 'register']);
Route::post('/login', [MobileAuthController::class, 'login']);
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Categories & Products
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// ORDER (Checkout dari Frontend / Customer)
Route::post('/orders', [OrderController::class, 'store']);


// =========================
// LOGIN REQUIRED (Perlu Token)
// =========================

Route::middleware('auth:sanctum')->group(function () {

    // Mobile Profile & Logout
    Route::get('/profile', [MobileAuthController::class, 'profile']);
    Route::post('/logout', [MobileAuthController::class, 'logout']);

    // Admin Profile & Logout
    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

    // Category CRUD
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Product CRUD
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // ==========================================
    // REFACTOR ORDER (Menggunakan order_code)
    // ==========================================
    Route::get('/orders', [OrderController::class, 'index']);
    Route::put('/orders/{order_code}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{order_code}', [OrderController::class, 'destroy']);

    // Customer
    Route::get('/customers', [CustomerController::class, 'index']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);

    // Setting
    Route::get('/settings/profile', [SettingController::class, 'profile']);
    Route::put('/settings/profile', [SettingController::class, 'updateProfile']);
    Route::put('/settings/password', [SettingController::class, 'updatePassword']); 
});