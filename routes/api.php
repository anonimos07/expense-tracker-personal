<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WalletController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $r) => $r->user());
    
    Route::apiResource('wallets', WalletController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('transactions', TransactionController::class);

    Route::get('/summary', [TransactionController::class, 'summary']);
});




