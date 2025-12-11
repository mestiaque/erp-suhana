<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetailItem extends Model
{
    protected $table = 'order_detail_items';

    protected $fillable = [
        'order_detail_id',
        'color_name',
        'order_no',
        'style_no',
        'qty',
        'composition',
    ];

    /**
     * Relationship: belongs to OrderDetails
     */
    public function orderDetails()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }

}
