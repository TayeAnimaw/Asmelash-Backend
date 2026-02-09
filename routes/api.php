<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GrnController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\StockAdjustmentController;
use App\Http\Controllers\Api\StockBalanceController;
use App\Http\Controllers\Api\StoreIssueVoucherController;
use App\Http\Controllers\Api\StoreRequisitionController;
use App\Http\Controllers\Api\UserController;
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

// Authentication Routes
Route::post("auth/register", [AuthController::class, "register"]);
Route::post("/auth/login", [AuthController::class, "login"]);
Route::post("/auth/logout", [AuthController::class, "logout"])->middleware("auth:sanctum");

// Profile Routes
Route::put("/profile/update", [ProfileController::class, 'updateProfile'])->middleware(['auth:sanctum']);
Route::post("/profile/upload", [ProfileController::class, 'uploadAvatar'])->middleware(['auth:sanctum']);
Route::get('/profile', [ProfileController::class, 'getProfile'])->middleware(['auth:sanctum']);
Route::post('/profile/update-avatar', [ProfileController::class, 'updateAvatar'])->middleware(['auth:sanctum']);

// Password Reset Routes
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('change-password', [AuthController::class, 'changePassword']);

// Items Routes
Route::prefix('items')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::post('/', [ItemController::class, 'store']);
    Route::get('/{item}', [ItemController::class, 'show']);
    Route::put('/{item}', [ItemController::class, 'update']);
    Route::delete('/{item}', [ItemController::class, 'destroy']);
});

// Projects Routes
Route::prefix('projects')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::get('/{project}', [ProjectController::class, 'show']);
    Route::put('/{project}', [ProjectController::class, 'update']);
    Route::delete('/{project}', [ProjectController::class, 'destroy']);
});

// GRN (Good Receiving Note) Routes
Route::prefix('grns')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [GrnController::class, 'index']);
    Route::post('/', [GrnController::class, 'store']);
    Route::get('/{grn}', [GrnController::class, 'show']);
    Route::put('/{grn}', [GrnController::class, 'update']);
    Route::delete('/{grn}', [GrnController::class, 'destroy']);
});

// Stock Balance Routes
Route::prefix('stock-balances')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [StockBalanceController::class, 'index']);
    Route::post('/', [StockBalanceController::class, 'store']);
    Route::get('/{stockBalance}', [StockBalanceController::class, 'show']);
    Route::put('/{stockBalance}', [StockBalanceController::class, 'update']);
    Route::delete('/{stockBalance}', [StockBalanceController::class, 'destroy']);
});

// Stock Adjustment Routes
Route::prefix('stock-adjustments')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [StockAdjustmentController::class, 'index']);
    Route::post('/', [StockAdjustmentController::class, 'store']);
    Route::get('/{stockAdjustment}', [StockAdjustmentController::class, 'show']);
    Route::put('/{stockAdjustment}', [StockAdjustmentController::class, 'update']);
    Route::delete('/{stockAdjustment}', [StockAdjustmentController::class, 'destroy']);
    Route::post('/{stockAdjustment}/approve', [StockAdjustmentController::class, 'approve']);
});

// Store Issue Voucher Routes
Route::prefix('store-issue-vouchers')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [StoreIssueVoucherController::class, 'index']);
    Route::post('/', [StoreIssueVoucherController::class, 'store']);
    Route::get('/{storeIssueVoucher}', [StoreIssueVoucherController::class, 'show']);
    Route::put('/{storeIssueVoucher}', [StoreIssueVoucherController::class, 'update']);
    Route::delete('/{storeIssueVoucher}', [StoreIssueVoucherController::class, 'destroy']);
});

// Store Requisition Routes
Route::prefix('store-requisitions')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [StoreRequisitionController::class, 'index']);
    Route::post('/', [StoreRequisitionController::class, 'store']);
    Route::get('/{storeRequisition}', [StoreRequisitionController::class, 'show']);
    Route::put('/{storeRequisition}', [StoreRequisitionController::class, 'update']);
    Route::delete('/{storeRequisition}', [StoreRequisitionController::class, 'destroy']);
    Route::post('/{storeRequisition}/approve', [StoreRequisitionController::class, 'approve']);
});

// Users Routes (Admin only)
Route::prefix('users')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::delete('/{user}', [UserController::class, 'destroy']);
});
