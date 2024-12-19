<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\TerminalController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\TransactionController;

// Authentication Routes
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // User Management
    Route::post('/user/register_admin', [UserController::class, 'register_admin']);
    Route::post('/user/register_terminal', [UserController::class, 'register_terminal']);
    
    // Terminal Management
    Route::post('/terminal/create', [TerminalController::class, 'create']);
    Route::get('/terminal/list', [TerminalController::class, 'list']);
    
    // Card Management (Admin Only)
    Route::post('/card/create', [CardController::class, 'create']);
    
    // Schedule Management
    Route::post('/schedule/create', [ScheduleController::class, 'create']);
    Route::get('/schedule/list', [ScheduleController::class, 'list']);
});

// Terminal Routes
Route::middleware(['auth:sanctum', 'terminal'])->group(function () {
    // Card Operations
    Route::get('/card/balance/{id}', [CardController::class, 'getBalance']);
    Route::post('/card/topup', [CardController::class, 'topUp']);
    Route::post('/card/pay', [CardController::class, 'payTicket']);
    Route::get('/card/transactions/{card_id}', [CardController::class, 'transactionHistory']);
    
    // Schedule View
    Route::get('/schedule/list', [ScheduleController::class, 'list']);
});