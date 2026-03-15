<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'planned_hours',
        'actual_hours',
        'overtime_hours',
        'late_hours',
        'grass_hours',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'planned_hours' => 'datetime',
        'actual_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'late_hours' => 'decimal:2',
        'grass_hours' => 'decimal:2',
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
