<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("auth/register", [AuthController::class, "register"]);
Route::post("/auth/login", [AuthController::class, "login"]);
Route::post("/auth/logout", [AuthController::class, "logout"])->middleware("auth:sanctum");


// update profile
Route::put("/profile/update", [ProfileController::class, 'updateProfile'])->middleware(['auth:sanctum']);
Route::post("/profile/upload", [ProfileController::class, 'uploadAvatar'])->middleware(['auth:sanctum']);
Route::get('/profile', [ProfileController::class, 'getProfile'])->middleware(['auth:sanctum']);
Route::post('/profile/update-avatar', [ProfileController::class, 'updateAvatar'])->middleware(['auth:sanctum']);

// Route::put('/profile/update-avatar', [ProfileController::class, 'updateAvatar'])->middleware(['auth:sanctum']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('change-password', [AuthController::class, 'changePassword']);
