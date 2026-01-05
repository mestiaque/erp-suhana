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

    public function pi()
    {
        return $this->belongsTo(ProformaInvoice::class, 'proforma_invoice_id');
    }
    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class, 'proforma_invoice_id');
    }

    public function order()
    {
        return $this->belongsTo(OrderDetail::class, 'order_no', 'order_no');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderDetailItem::class, 'style_no', 'style_no')
                    ->where('order_no', $this->order_no);
    }

    public function orderDetails()
    {
        return $this->belongsTo(OrderDetail::class,'style_no','style_no', 'order_no', 'order_no');
    }

    public function detailItems()
    {
        return OrderDetailItem::where('order_no', $this->order_no)
            ->where('style_no', $this->style_no)
            ->get();
    }

    public function getActucalOrder()
    {
        $order = OrderDetail::where('order_no', $this->order_no)->where('style_no', $this->style_no)->first();
        return $order;
    }

}
