<?php

use App\Http\Controllers\WebShowroomController;
use App\Http\Controllers\Api\ShareController;
use App\Http\Controllers\WebAdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

if (app()->environment('production') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'onrender.com'))) {
    URL::forceScheme('https');
}

Route::get('/', function () {
    return response()->json(['message' => 'Pocket Showroom API is running']);
});

// Short tracked links
Route::get('/s/{code}', [ShareController::class, 'resolve']);

// Customer Showroom Web View
Route::get('/showrooms/{slug}', [WebShowroomController::class, 'show']);

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [WebAdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [WebAdminController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [WebAdminController::class, 'logout'])->name('admin.logout');
    Route::get('/logout', [WebAdminController::class, 'logout'])->name('admin.logout.get');

    // Protected Super Admin Web Dashboard
    Route::middleware('web_admin')->group(function () {
        Route::get('/', [WebAdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Shops Moderation
        Route::post('/shops/{id}/verify', [WebAdminController::class, 'toggleVerify'])->name('admin.shops.verify');
        Route::post('/shops/{id}/feature', [WebAdminController::class, 'toggleFeature'])->name('admin.shops.feature');
        Route::post('/shops/{id}/toggle-active', [WebAdminController::class, 'toggleActive'])->name('admin.shops.toggle-active');
        
        // User / Owner Accounts
        Route::post('/users/{id}/activate', [WebAdminController::class, 'activateUser'])->name('admin.users.activate');
        Route::post('/users/{id}/block', [WebAdminController::class, 'blockUser'])->name('admin.users.block');
        Route::post('/users/{id}/extend-trial', [WebAdminController::class, 'extendTrial'])->name('admin.users.extend-trial');
        
        // Products & Video Reels Catalog
        Route::post('/products/{id}/toggle-stock', [WebAdminController::class, 'toggleStock'])->name('admin.products.toggle-stock');
        Route::post('/products/{id}/toggle-promoted', [WebAdminController::class, 'togglePromoted'])->name('admin.products.toggle-promoted');
        Route::post('/products/{id}/delete', [WebAdminController::class, 'deleteProduct'])->name('admin.products.delete');
        
        // Orders & Inquiries
        Route::post('/orders/{id}/status', [WebAdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
        Route::post('/inquiries/{id}/handled', [WebAdminController::class, 'toggleInquiryStatus'])->name('admin.inquiries.handled');
        
        // Categories & Locations
        Route::post('/categories/store', [WebAdminController::class, 'storeCategory'])->name('admin.categories.store');
        Route::post('/categories/{id}/delete', [WebAdminController::class, 'deleteCategory'])->name('admin.categories.delete');
        Route::post('/locations/store', [WebAdminController::class, 'storeLocation'])->name('admin.locations.store');
        Route::post('/locations/{id}/delete', [WebAdminController::class, 'deleteLocation'])->name('admin.locations.delete');
    });
});
