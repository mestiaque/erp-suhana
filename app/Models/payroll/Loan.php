<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'principal_amount',
        'interest_rate',
        'total_amount',
        'monthly_installment',
        'total_installments',
        'paid_installments',
        'remaining_installments',
        'disbursement_date',
        'first_installment_date',
        'status',
        'reason',
        'admin_remark',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'total_installments' => 'integer',
        'paid_installments' => 'integer',
        'remaining_installments' => 'integer',
        'disbursement_date' => 'date',
        'first_installment_date' => 'date',
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
