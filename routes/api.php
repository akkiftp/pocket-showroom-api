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
use App\Http\Controllers\Api\MarketplaceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicShowroomController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\MarketplaceAdminController;
use App\Http\Controllers\Api\ShareController;
use App\Http\Controllers\Api\SuperAdminDashboardController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ReelController;
use App\Http\Controllers\Api\TravelController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn()=>response()->json(['ok'=>true,'service'=>'Showmora API','auth_mode'=>config('pocket_showroom.auth_driver'),'free_mode'=>(bool)config('pocket_showroom.free_mode'),'timestamp'=>now()->toIso8601String()]));
// SECURITY: migrations must only run from deployment/CLI, never from a public HTTP route.
Route::prefix('auth')->group(fn()=>Route::post('/firebase-login',[AuthController::class,'firebaseLogin'])->middleware('throttle:10,1'));

// Public tracking and share resolution
Route::post('/tracking/event', [TrackingController::class, 'publicEvent'])->middleware('throttle:120,1');
Route::post('/tracking/link-visitor', [TrackingController::class, 'linkVisitor']);
Route::post('/shares/create', [ShareController::class, 'create']);
Route::get('/shares/{code}', [ShareController::class, 'resolve']);

Route::prefix('marketplace')->group(function(){
    Route::get('/home',[MarketplaceController::class,'home']);
    Route::get('/categories',[MarketplaceController::class,'categories']);
    Route::get('/locations',[MarketplaceController::class,'locations']);
    Route::get('/shops',[MarketplaceController::class,'shops']);
    Route::get('/shops/{slug}',[MarketplaceController::class,'shop']);
    Route::get('/search',[MarketplaceController::class,'search']);
});

Route::prefix('public/showrooms/{slug}')->group(function(){
    Route::get('/',[PublicShowroomController::class,'show']);
    Route::get('/products',[PublicShowroomController::class,'products']);
    Route::get('/products/{product}',[PublicShowroomController::class,'product']);
    Route::post('/events',[TrackingController::class,'publicEvent'])->middleware('throttle:120,1');
    Route::post('/inquiries',[PublicShowroomController::class,'inquiry'])->middleware('throttle:20,1');
    Route::post('/orders',[OrderController::class,'publicStore'])->middleware('throttle:20,1');
});

// Local services, reels and travel discovery.
Route::get('/services', [ServiceController::class,'publicIndex']);
Route::post('/services/{service}/book', [ServiceController::class,'book'])->middleware('throttle:20,1');
Route::get('/reels', [ReelController::class,'feed']);
Route::post('/reels/{reel}/view', [ReelController::class,'view'])->middleware('throttle:120,1');
Route::get('/travel/search', [TravelController::class,'publicSearch']);
Route::post('/travel/book', [TravelController::class,'book'])->middleware('throttle:20,1');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/me',[AuthController::class,'me']);
    Route::post('/auth/logout',[AuthController::class,'logout']);
    Route::get('/subscription/status',[SubscriptionController::class,'status']);

    // Super Admin Control Panel API
    Route::prefix('super-admin')->middleware('admin')->group(function(){
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard']);
        Route::get('/owners', [SuperAdminDashboardController::class, 'owners']);
        Route::get('/owners/{id}', [SuperAdminDashboardController::class, 'owner']);
        Route::get('/shops', [SuperAdminDashboardController::class, 'shops']);
        Route::get('/shops/{id}', [SuperAdminDashboardController::class, 'shop']);
        Route::post('/shops/{id}/verify', [SuperAdminDashboardController::class, 'toggleVerify']);
        Route::post('/shops/{id}/feature', [SuperAdminDashboardController::class, 'toggleFeature']);
        Route::post('/shops/{id}/toggle-active', [SuperAdminDashboardController::class, 'toggleActive']);
        Route::get('/products', [SuperAdminDashboardController::class, 'products']);
        Route::get('/customers', [SuperAdminDashboardController::class, 'customers']);
        Route::get('/audit-logs', [SuperAdminDashboardController::class, 'auditLogs']);
    });

    // Legacy Platform Admin routes
    Route::prefix('admin')->middleware('admin')->group(function(){
        Route::get('/overview',[AdminController::class,'overview']);
        Route::get('/marketplace/categories',[MarketplaceAdminController::class,'categories']);
        Route::post('/marketplace/categories',[MarketplaceAdminController::class,'storeCategory']);
        Route::match(['put','patch'],'/marketplace/categories/{category}',[MarketplaceAdminController::class,'updateCategory']);
        Route::delete('/marketplace/categories/{category}',[MarketplaceAdminController::class,'destroyCategory']);
        Route::get('/marketplace/locations',[MarketplaceAdminController::class,'locations']);
        Route::post('/marketplace/locations',[MarketplaceAdminController::class,'storeLocation']);
        Route::match(['put','patch'],'/marketplace/locations/{location}',[MarketplaceAdminController::class,'updateLocation']);
        Route::delete('/marketplace/locations/{location}',[MarketplaceAdminController::class,'destroyLocation']);
        Route::patch('/marketplace/shops/{business}',[MarketplaceAdminController::class,'updateShop']);
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
    Route::get('/owner/analytics',[AnalyticsController::class,'overview'])->middleware('permission:analytics.view');
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

    // Owner: services/home-service bookings
    Route::get('/owner/services',[ServiceController::class,'index']);
    Route::post('/owner/services',[ServiceController::class,'store']);
    Route::match(['put','patch','post'],'/owner/services/{service}',[ServiceController::class,'update']);
    Route::delete('/owner/services/{service}',[ServiceController::class,'destroy']);
    Route::get('/owner/service-bookings',[ServiceController::class,'bookings']);
    Route::patch('/owner/service-bookings/{booking}/status',[ServiceController::class,'bookingStatus']);

    // Owner: reels
    Route::get('/owner/reels',[ReelController::class,'index']);
    Route::post('/owner/reels',[ReelController::class,'store']);
    Route::delete('/owner/reels/{reel}',[ReelController::class,'destroy']);

    // Owner: travel
    Route::get('/owner/travel/vehicles',[TravelController::class,'vehicles']);
    Route::post('/owner/travel/vehicles',[TravelController::class,'storeVehicle']);
    Route::delete('/owner/travel/vehicles/{vehicle}',[TravelController::class,'destroyVehicle']);
    Route::get('/owner/travel/routes',[TravelController::class,'routes']);
    Route::post('/owner/travel/routes',[TravelController::class,'storeRoute']);
    Route::delete('/owner/travel/routes/{route}',[TravelController::class,'destroyRoute']);
    Route::get('/owner/travel/bookings',[TravelController::class,'bookings']);
    Route::patch('/owner/travel/bookings/{booking}/status',[TravelController::class,'status']);
});
