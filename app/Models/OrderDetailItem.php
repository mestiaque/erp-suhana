<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetailItem extends Model
{
    protected $table = 'order_detail_items';

    protected $guarded = [];

    protected $casts = [
        'shipment_date' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * Relationship: belongs to OrderDetail
     */
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetailItem::class, 'order_detail_id');
    }
}
