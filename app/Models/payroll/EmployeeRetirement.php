<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRetirement extends Model
{
    use HasFactory;

    protected $table = 'retirements';

    protected $fillable = [
        'user_id',
        'retirement_date',
        'type',
        'settlement_amount',
        'provident_fund',
        'gratuity',
        'notice_period_days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'retirement_date' => 'date',
        'settlement_amount' => 'decimal:2',
        'provident_fund' => 'decimal:2',
        'gratuity' => 'decimal:2',
        'notice_period_days' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
