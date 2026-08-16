<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerContactController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicShowroomController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn()=>response()->json(['ok'=>true,'service'=>'Showmora API','auth_mode'=>config('pocket_showroom.auth_driver'),'free_mode'=>(bool)config('pocket_showroom.free_mode'),'timestamp'=>now()->toIso8601String()]));
Route::prefix('auth')->group(fn()=>Route::post('/firebase-login',[AuthController::class,'firebaseLogin'])->middleware('throttle:10,1'));

Route::prefix('public/showrooms/{slug}')->group(function(){
    Route::get('/',[PublicShowroomController::class,'show']);
    Route::get('/products',[PublicShowroomController::class,'products']);
    Route::get('/products/{product}',[PublicShowroomController::class,'product']);
    Route::post('/events',[TrackingController::class,'publicEvent'])->middleware('throttle:120,1');
    Route::post('/inquiries',[PublicShowroomController::class,'inquiry'])->middleware('throttle:20,1');
    Route::post('/orders',[OrderController::class,'publicStore'])->middleware('throttle:20,1');
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/me',[AuthController::class,'me']);
    Route::post('/auth/logout',[AuthController::class,'logout']);
    Route::get('/subscription/status',[SubscriptionController::class,'status']);

    // Platform Super Admin: no business ownership; controls all shops/owners.
    Route::prefix('admin')->middleware('admin')->group(function(){
        Route::get('/overview',[AdminController::class,'overview']);
        Route::get('/users',[AdminController::class,'users']);
        Route::get('/businesses/{business}/analytics',[AdminController::class,'businessAnalytics']);
        Route::post('/users/{id}/activate',[AdminController::class,'activate']);
        Route::post('/users/{id}/extend-trial',[AdminController::class,'extendTrial']);
        Route::post('/users/{id}/block',[AdminController::class,'block']);
    });

    // Owner can create/manage shop admins. Super admin may pass ?business_id=ID.
    Route::get('/staff',[StaffController::class,'index']);
    Route::post('/staff',[StaffController::class,'store']);
    Route::match(['put','patch'],'/staff/{staff}',[StaffController::class,'update']);
    Route::delete('/staff/{staff}',[StaffController::class,'destroy']);

    Route::get('/dashboard',[DashboardController::class,'index'])->middleware('permission:dashboard.view');
    Route::get('/analytics',[AnalyticsController::class,'overview'])->middleware('permission:analytics.view');
    Route::get('/analytics/customers',[AnalyticsController::class,'customers'])->middleware('permission:analytics.view');
    Route::get('/analytics/products',[AnalyticsController::class,'products'])->middleware('permission:analytics.view');
    Route::post('/activity',[TrackingController::class,'ownerEvent']);

    Route::get('/business',[BusinessController::class,'show'])->middleware('permission:business.view');
    Route::post('/business',[BusinessController::class,'store']);
    Route::match(['put','patch'],'/business',[BusinessController::class,'update'])->middleware('permission:business.update');
    Route::post('/business/logo',[BusinessController::class,'uploadLogo'])->middleware('permission:business.update');
    Route::post('/business/banner',[BusinessController::class,'uploadBanner'])->middleware('permission:business.update');
    Route::post('/business/theme',[BusinessController::class,'theme'])->middleware('permission:business.update');

    Route::get('/categories',[CategoryController::class,'index'])->middleware('permission:products.view');
    Route::post('/categories',[CategoryController::class,'store'])->middleware('permission:categories.manage');
    Route::match(['put','patch'],'/categories/{category}',[CategoryController::class,'update'])->middleware('permission:categories.manage');
    Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->middleware('permission:categories.manage');

    Route::get('/products',[ProductController::class,'index'])->middleware('permission:products.view');
    Route::post('/products',[ProductController::class,'store'])->middleware('permission:products.create');
    Route::get('/products/{product}',[ProductController::class,'show'])->middleware('permission:products.view');
    Route::match(['put','patch','post'],'/products/{product}',[ProductController::class,'update'])->middleware('permission:products.update');
    Route::delete('/products/{product}',[ProductController::class,'destroy'])->middleware('permission:products.delete');
    Route::post('/products/{product}/toggle-stock',[ProductController::class,'toggleStock'])->middleware('permission:products.update');
    Route::post('/products/{product}/toggle-featured',[ProductController::class,'toggleFeatured'])->middleware('permission:products.update');
    Route::delete('/products/{product}/images/{image}',[ProductController::class,'destroyImage'])->middleware('permission:products.update');

    Route::get('/inquiries',[InquiryController::class,'index'])->middleware('permission:inquiries.view');
    Route::get('/inquiries/{inquiry}',[InquiryController::class,'show'])->middleware('permission:inquiries.view');
    Route::post('/inquiries/{inquiry}/handled',[InquiryController::class,'handled'])->middleware('permission:inquiries.manage');
    Route::post('/inquiries/{inquiry}/pending',[InquiryController::class,'pending'])->middleware('permission:inquiries.manage');
    Route::delete('/inquiries/{inquiry}',[InquiryController::class,'destroy'])->middleware('permission:inquiries.manage');

    Route::get('/customers',[CustomerContactController::class,'index'])->middleware('permission:customers.view');
    Route::post('/customers',[CustomerContactController::class,'store'])->middleware('permission:customers.manage');
    Route::delete('/customers/{customerContact}',[CustomerContactController::class,'destroy'])->middleware('permission:customers.manage');

    Route::get('/orders',[OrderController::class,'index'])->middleware('permission:orders.view');
    Route::post('/orders/{order}/status',[OrderController::class,'status'])->middleware('permission:orders.manage');

    Route::post('/ai/product-draft',[AiController::class,'productDraft'])->middleware('permission:ai.use');
});
