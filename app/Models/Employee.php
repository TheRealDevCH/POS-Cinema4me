<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getDisplayNameAttribute()
    {
        return $this->first_name . ' ' . substr($this->last_name, 0, 1) . '.';
    }
}
