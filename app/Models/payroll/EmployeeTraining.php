<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeTraining extends Model
{
    protected $table = 'employee_training';

    protected $fillable = [
        'user_id', 'training_title', 'training_institute', 'duration',
        'start_date', 'end_date', 'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
