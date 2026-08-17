<?php

use App\Http\Controllers\WebShowroomController;
use App\Http\Controllers\Api\ShareController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Pocket Showroom API is running']);
});

Route::get('/s/{code}', [ShareController::class, 'resolve']);
Route::get('/showrooms/{slug}', [WebShowroomController::class, 'show']);
