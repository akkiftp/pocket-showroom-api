<?php

use App\Http\Controllers\WebShowroomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Pocket Showroom API is running']);
});

Route::get('/showrooms/{slug}', [WebShowroomController::class, 'show']);
Route::get('/api/public/showrooms/{slug}', [WebShowroomController::class, 'showOrApi']);
