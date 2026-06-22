<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;

// =========================
// PUBLIC
// =========================

Route::get('/test', function () {
    return response()->json([
        'message' => 'API berjalan'
    ]);
});

// =========================
// MOBILE FLUTTER
// =========================

Route::post('/register', [MobileAuthController::class, 'register']);
Route::post('/login', [MobileAuthController::class, 'login']);

// =========================
// ADMIN DASHBOARD
// =========================

Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// =========================
// CATEGORY (PUBLIC)
// =========================

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// =========================
// PRODUCT (PUBLIC)
// =========================

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// =========================
// LOGIN REQUIRED
// =========================

Route::middleware('auth:sanctum')->group(function () {


    // =========================
    // MOBILE
    // =========================

    Route::get('/profile', [MobileAuthController::class, 'profile']);
    Route::post('/logout', [MobileAuthController::class, 'logout']);

    // =========================
    // ADMIN
    // =========================

    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

    // =========================
    // CATEGORY CRUD
    // =========================

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // =========================
    // PRODUCT CRUD
    // =========================

    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // =========================
    // ORDER
    // =========================

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{order_code}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{order_code}', [OrderController::class, 'destroy']);

    // =========================
    // CUSTOMER
    // =========================

    Route::get('/customers', [CustomerController::class, 'index']);

    // =========================
    // SHIPPING
    // =========================

    Route::get('/shipping', [OrderController::class, 'shipping']);

    // =========================
    // LAPORAN
    // =========================

    Route::get('/reports', [ReportController::class, 'index']);

    // =========================
    // SETTING
    // =========================

    Route::get('/settings/profile', [SettingController::class, 'profile']);
    Route::put('/settings/profile', [SettingController::class, 'updateProfile']);
    Route::put('/settings/password', [SettingController::class, 'updatePassword']); 
    
});