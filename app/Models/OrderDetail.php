<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class OrderDetail extends Model
{
    use HasFactory, ActivityLoggable;

    protected $guarded = [];

    // columns
    // id
    // buyer_id
    // buyer_name
    // merchant_id
    // merchant_name
    // style_no
    // total_qty
    // total_bill
    // status
    // company_name
    // order_no
    // shipment_date
    // fabrication
    // remarks
    // created_by
    // edited_by
    // created_at
    // updated_at

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
        return $this->belongsTo(User::class, 'created_by');
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

    public function piItem() {
        return $this->belongsTo(ProformaInvoiceItem::class, 'order_no', 'order_no');
    }

    public function piItems()
    {
        return $this->hasMany(
            ProformaInvoiceItem::class,
            'order_no',   // Foreign key in PI table
            'order_no'    // Local key in OrderDetail
        )->where('style_no', $this->style_no);
    }


    // Optional: fetch items directly (same as items())
    public function getItems()
    {
        return $this->items()->get();
    }

    public function getSewingQty()
    {
        return SewingOutput::where('style_no', $this->style_no)
            // ->where('order_no', $this->order_no ?? null)
            ->sum('production');
    }

public function getCutQty()
{
    $piIds = $this->piItems()
        ->where('style_no', $this->style_no)
        ->pluck('proforma_invoice_id');

    if ($piIds->isEmpty()) {
        return 0;
    }

    return Cutting::whereIn('pi_id', $piIds)
        ->where('style_no', $this->style_no)
        ->sum('cutting_qty');
}
}
