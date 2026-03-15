<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceMachineLog extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'attendance_machine_logs';

    // Primary key
    protected $primaryKey = 'id';

    // If your primary key is auto-incrementing
    public $incrementing = true;

    // Timestamp columns (created_at already exists)
    public $timestamps = false; // because we only have created_at, not updated_at

    // Fillable fields (for mass assignment)
    protected $fillable = [
        'device_sn',
        'user_id',
        'log_time',
        'type_code',
        'type_name',
        'created_at',
    ];

    // If you want, you can cast log_time to Carbon instance
    protected $casts = [
        'log_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'employee_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('hideUser7', function (Builder $builder) {
            $builder->where('user_id', '!=', 7);
        });
    }
}
