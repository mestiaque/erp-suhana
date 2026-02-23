<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetailItem extends Model
{
    protected $table = 'order_detail_items';

    protected $fillable = [
        'order_detail_id',
        'color_name',
        'composition',
        'order_no',
        'style_no',
        'qty',
        'item_name',
        'fabrication',
        'gsm',
        'shipment_date',
        'created_by',
        'edited_by',
        'created_at',
        'updated_at',
    ];

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
