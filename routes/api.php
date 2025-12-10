<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SearchController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Login Required)
|--------------------------------------------------------------------------
*/
// This is the URL: http://localhost:8000/api/login
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Login Required)
|--------------------------------------------------------------------------
| The 'auth:sanctum' middleware checks for a valid Token.
| If the token is missing or invalid, it blocks the request.
*/
Route::middleware('auth:sanctum')->group(function () {

    // 1. HOME API - Geolocation endpoint
    Route::get('/geo', [SearchController::class, 'getGeoLocation']);

    // 2. Check User Status
    // React calls this. If it gets data back, the user is logged in.
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 3. Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // 4. Search History (We will create this Controller next)
    Route::get('/history', [SearchController::class, 'index']);
    Route::post('/history', [SearchController::class, 'store']);
    Route::post('/history/delete', [SearchController::class, 'destroy']);
});
