<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\KioskProductController;
use App\Http\Controllers\ReportController;

Route::post('/employees/login', [EmployeeController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);

// Kiosk Product Management
Route::post('/kiosk/verify-password', [KioskProductController::class, 'verifyPassword']);
Route::get('/kiosk/products', [KioskProductController::class, 'index']);
Route::post('/kiosk/products', [KioskProductController::class, 'store']);
Route::put('/kiosk/products/{id}', [KioskProductController::class, 'update']);
Route::delete('/kiosk/products/{id}', [KioskProductController::class, 'destroy']);

// Reporting / Abrechnung
Route::post('/report/verify-code', [ReportController::class, 'verifyCode']);
Route::get('/report', [ReportController::class, 'index']);
Route::post('/report/transactions', [ReportController::class, 'store']);
Route::post('/report/reset', [ReportController::class, 'reset']);
