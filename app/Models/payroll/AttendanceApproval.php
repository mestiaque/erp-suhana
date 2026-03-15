<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'in_time',
        'out_time',
        'original_status',
        'requested_status',
        'reason',
        'admin_remark',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'in_time' => 'datetime',
        'out_time' => 'datetime',
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
