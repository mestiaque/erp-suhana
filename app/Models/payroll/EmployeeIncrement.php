<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeIncrement extends Model
{
    protected $fillable = [
        'user_id', 'increment_date', 'previous_salary', 'increment_amount',
        'increment_percentage', 'new_salary', 'remarks', 'approved_by'
    ];

    protected $casts = [
        'increment_date' => 'date',
        'previous_salary' => 'decimal:2',
        'increment_amount' => 'decimal:2',
        'increment_percentage' => 'decimal:2',
        'new_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

        protected static function booted()
        {
            static::addGlobalScope('hideUser7', function (Builder $builder) {
                $builder->where('user_id', '!=', 7);
            });
        }
}
