<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('accounts', AccountController::class);

Route::get('transactions', [TransactionController::class, 'get']);
Route::post('transactions', [TransactionController::class, 'create']);

