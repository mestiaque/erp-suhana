<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmationLetter extends Model
{
    protected $fillable = [
        'user_id', 'letter_date', 'confirmation_date', 'performance_remarks', 'status', 'remarks', 'created_by'
    ];

    protected $casts = [
        'letter_date' => 'date',
        'confirmation_date' => 'date',
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
