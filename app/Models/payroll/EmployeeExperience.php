<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{
    protected $table = 'employee_experience';

    protected $fillable = [
        'user_id', 'company_name', 'designation', 'department',
        'from_date', 'to_date', 'responsibilities'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
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
