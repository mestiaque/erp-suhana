<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class OrderDetail extends Model
{
    use HasFactory;
    use ActivityLoggable;

    protected $guarded = [];

    protected $casts = [
        'created_date' => 'date',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    
}
