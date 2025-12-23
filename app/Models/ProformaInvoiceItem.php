<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class ProformaInvoiceItem extends Model
{
    use HasFactory;
    use ActivityLoggable;

    protected $guarded = [];

    protected $casts = [
        'created_date' => 'date',
        'shipment_date' => 'date',
        'updated_at' => 'date',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function orderDetails()
    {
        return $this->belongsTo(OrderDetail::class,'style_no','style_no', 'order_no', 'order_no');
    }

    public function getActucalOrder()
    {
        $order = OrderDetail::where('order_no', $this->order_no)->where('style_no', $this->style_no)->first();
        return $order;
    }

}
