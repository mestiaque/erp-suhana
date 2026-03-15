<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoiningLetter extends Model
{
    protected $fillable = [
        'user_id', 'letter_date', 'joining_date', 'department', 'designation', 'remarks', 'created_by'
    ];

    protected $casts = [
        'letter_date' => 'date',
        'joining_date' => 'date',
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
