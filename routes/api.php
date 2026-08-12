<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicShowroomController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/request-otp', [AuthController::class, 'requestOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::prefix('public/showrooms/{slug}')->group(function () {
    Route::get('/', [PublicShowroomController::class, 'show']);
    Route::get('/products', [PublicShowroomController::class, 'products']);
    Route::get('/products/{product}', [PublicShowroomController::class, 'product']);
    Route::post('/inquiries', [PublicShowroomController::class, 'inquiry'])->middleware('throttle:20,1');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/subscription/status', [SubscriptionController::class, 'status']);

    Route::prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users/{id}/activate', [AdminController::class, 'activate']);
        Route::post('/users/{id}/extend-trial', [AdminController::class, 'extendTrial']);
        Route::post('/users/{id}/block', [AdminController::class, 'block']);
    });

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/business', [BusinessController::class, 'show']);
    Route::post('/business', [BusinessController::class, 'store']);
    Route::post('/business/logo', [BusinessController::class, 'uploadLogo']);
    Route::post('/business/banner', [BusinessController::class, 'uploadBanner']);
    Route::post('/business/theme', [BusinessController::class, 'theme']);

    Route::apiResource('categories', CategoryController::class)->except(['show']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::match(['put', 'patch', 'post'], '/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/products/{product}/toggle-stock', [ProductController::class, 'toggleStock']);
    Route::post('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured']);
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'destroyImage']);

    Route::get('/inquiries', [InquiryController::class, 'index']);
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show']);
    Route::post('/inquiries/{inquiry}/handled', [InquiryController::class, 'handled']);
    Route::post('/inquiries/{inquiry}/pending', [InquiryController::class, 'pending']);
    Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy']);
});
