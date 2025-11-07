<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pos');
});

Route::get('/customer-display', function () {
    return view('customer-display');
});

Route::get('/receipt/{transactionId}', function ($transactionId) {
    return view('receipt', ['transactionId' => $transactionId]);
});
