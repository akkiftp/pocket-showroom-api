<?php

use App\Http\Controllers\WebShowroomController;
use App\Http\Controllers\Api\ShareController;
use App\Http\Controllers\WebAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Pocket Showroom API is running']);
});

// Short tracked links
Route::get('/s/{code}', [ShareController::class, 'resolve']);

// Customer Showroom Web View
Route::get('/showrooms/{slug}', [WebShowroomController::class, 'show']);

// Super Admin Web Dashboard
Route::get('/admin', [WebAdminController::class, 'dashboard']);
Route::post('/admin/shops/{id}/verify', [WebAdminController::class, 'toggleVerify']);
Route::post('/admin/shops/{id}/feature', [WebAdminController::class, 'toggleFeature']);
Route::post('/admin/shops/{id}/toggle-active', [WebAdminController::class, 'toggleActive']);
