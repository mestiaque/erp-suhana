<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requested_amount',
        'approved_amount',
        'installment_months',
        'monthly_deduction',
        'reason',
        'admin_remark',
        'status',
        'approved_by',
        'approved_at',
        'disbursement_date',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'installment_months' => 'integer',
        'approved_at' => 'datetime',
        'disbursement_date' => 'date',
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
