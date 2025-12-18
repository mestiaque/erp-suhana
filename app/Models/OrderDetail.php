<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class OrderDetail extends Model
{
    use HasFactory, ActivityLoggable;

    protected $guarded = [];

    protected $casts = [
        'shipment_date' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    // -----------------------------
    // Relationships
    // -----------------------------
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function editedBy()
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

    public function items()
    {
        return $this->hasMany(OrderDetailItem::class, 'order_detail_id');
    }

 

    // Optional: fetch items directly (same as items())
    public function getItems()
    {
        return $this->items()->get();
    }
}
