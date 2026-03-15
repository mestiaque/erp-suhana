<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvidentFund extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_contribution',
        'company_contribution',
        'total_amount',
        'interest_rate',
        'year',
        'month',
        'status',
        'remarks',
    ];

    protected $casts = [
        'employee_contribution' => 'decimal:2',
        'company_contribution' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
