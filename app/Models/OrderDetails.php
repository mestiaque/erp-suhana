<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $guarded = [];

    protected $casts = [
        'created_at'        => 'date',
        'updated_at'        => 'date',
        'created_date'      => 'date',
        'shipment_date'      => 'date',
    ];

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'editedby_id');
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
