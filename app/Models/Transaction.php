<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'employee_code',
        'location',
        'product_name',
        'product_price',
        'quantity',
        'subtotal',
        'payment_method',
        'total',
        'category',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];
}
