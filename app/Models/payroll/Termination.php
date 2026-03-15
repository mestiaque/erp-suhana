<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Termination extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'termination_date',
        'termination_type',
        'reason',
        'notice_period',
        'status',
        'approved_by',
        'rejection_reason',
        'exit_interview_notes',
        'documents',
    ];

    protected $casts = [
        'termination_date' => 'date',
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
