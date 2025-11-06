<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'employee_id',
        'location',
        'payment_method',
        'total_amount',
        'cash_received',
        'change_given',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_given' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
