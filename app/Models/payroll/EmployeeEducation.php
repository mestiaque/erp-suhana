<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $table = 'employee_education';

    protected $fillable = [
        'user_id', 'degree_name', 'institute_name', 'subject',
        'result', 'passing_year', 'remarks'
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
