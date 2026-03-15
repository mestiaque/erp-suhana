<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_name',
        'email',
        'phone',
        'position',
        'department_id',
        'interview_date',
        'interview_time',
        'venue',
        'notes',
        'status',
        'interview_type',
        'interviewer_id',
        'written_marks',
        'oral_marks',
        'practical_marks',
        'total_marks',
        'feedback',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'interview_time' => 'datetime:H:i',
        'written_marks' => 'decimal:2',
        'oral_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Attribute::class, 'department_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
