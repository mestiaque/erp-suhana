<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeBank extends Model
{
    protected $table = 'employee_bank';

    protected $fillable = [
        'user_id', 'bank_name', 'branch_name', 'account_number',
        'account_holder_name', 'routing_number', 'payment_method',
        'mobile_banking_number', 'is_primary'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'user_id', 'user_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('hideUser7', function (Builder $builder) {
            $builder->where('user_id', '!=', 7);
        });
    }
}
