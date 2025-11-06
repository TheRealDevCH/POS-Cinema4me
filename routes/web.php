<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pos');
});

Route::get('/customer-display', function () {
    return view('customer-display');
});
