<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySheet extends Model
{
    protected $fillable = [
        'user_id', 'month', 'year', 'basic_salary', 'house_rent', 'medical_allowance',
        'transport_allowance', 'other_allowance', 'gross_salary', 'overtime_amount',
        'special_overtime_amount', 'grass_time_amount', 'other_bonus', 'bonus', 'total_earning',
        'absent_deduction', 'late_deduction', 'tax', 'provident_fund', 'loan_deduction',
        'salary_advance_deduction', 'deduction', 'other_deduction', 'total_deduction',
        'net_salary', 'working_days', 'present_days', 'absent_days', 'leave_days', 'holiday_days',
        'overtime_hours', 'payment_method', 'payment_status', 'payment_date',
        'salary_type', 'remarks'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'basic_salary' => 'decimal:2',
        'house_rent' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'special_overtime_amount' => 'decimal:2',
        'grass_time_amount' => 'decimal:2',
        'other_bonus' => 'decimal:2',
        'bonus' => 'decimal:2',
        'total_earning' => 'decimal:2',
        'absent_deduction' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'tax' => 'decimal:2',
        'provident_fund' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'salary_advance_deduction' => 'decimal:2',
        'deduction' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->hasOneThrough(
            EmployeeInfo::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }

    // Note: Removed hideUser7 global scope - it was causing duplicate entry issues
    // If you need to hide user 7 from listings, filter in controller/view level instead
}
