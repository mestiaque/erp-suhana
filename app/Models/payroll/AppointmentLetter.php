<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Model;

class AppointmentLetter extends Model
{
    protected $fillable = [
        'user_id', 'letter_date', 'position', 'salary', 'joining_date', 'department', 'terms', 'created_by'
    ];

    protected $casts = [
        'letter_date' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
