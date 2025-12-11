<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YarnBookingItem extends Model
{
    use HasFactory;

    protected $table = 'yarn_booking_items';

    protected $guarded = [];

    /**
     * Relation: YarnBookingItem belongs to a YarnBooking
     */
    public function yarnBooking()
    {
        return $this->belongsTo(YarnBooking::class, 'yarn_booking_id');
    }

    /**
     * Relation: YarnBookingItem belongs to a Proforma Invoice
     */
    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class, 'proforma_invoice_id');
    }

    /**
     * Relation: YarnBookingItem belongs to a Proforma Invoice Item
     */
    public function piItem()
    {
        return $this->belongsTo(ProformaInvoiceItem::class, 'proforma_invoice_item_id');
    }

    /**
     * Optional: Added/Edited by relations
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'editedby_id');
    }
}
