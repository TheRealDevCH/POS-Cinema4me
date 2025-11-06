<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

Route::post('/employees/login', [EmployeeController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
