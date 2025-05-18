<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Products
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::get('/products', [ProductController::class, 'indexPublic']);
    
    // Carts
    Route::apiResource('carts', CartController::class);
    
    // Transactions
    Route::apiResource('transactions', TransactionController::class);
    Route::post('/transactions/{transaction}/process', [TransactionController::class, 'processPayment']);
    
    // Users
    Route::apiResource('users', UserController::class)->middleware('role:admin');
    
    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->middleware('role:owner');
    Route::get('/reports/financial', [ReportController::class, 'financial'])->middleware('role:owner');
    
    // System Activities
    Route::get('/activities', [UserController::class, 'activities'])->middleware('role:admin');
});