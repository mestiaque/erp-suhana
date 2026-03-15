<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'year',
        'quarter',
        'rating',
        'attendance_score',
        'task_completion',
        'teamwork',
        'initiative',
        'punctuality',
        'strengths',
        'weaknesses',
        'comments',
        'goals',
        'status',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'attendance_score' => 'decimal:2',
        'task_completion' => 'decimal:2',
        'teamwork' => 'decimal:2',
        'initiative' => 'decimal:2',
        'punctuality' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
