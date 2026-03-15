<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Probation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'probation_start_date',
        'probation_end_date',
        'months',
        'status',
        'performance_notes',
        'confirmation_status',
        'confirmation_notes',
        'confirmation_date',
        'reviewed_by',
    ];

    protected $casts = [
        'probation_start_date' => 'date',
        'probation_end_date' => 'date',
        'confirmation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
