<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gross_salary',
        'taxable_income',
        'tax_amount',
        'rebate',
        'net_tax',
        'year',
        'month',
        'remarks',
    ];

    protected $casts = [
        'gross_salary' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'rebate' => 'decimal:2',
        'net_tax' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
