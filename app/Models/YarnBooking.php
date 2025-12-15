<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YarnBooking extends Model
{
    use HasFactory;

    protected $table = 'yarn_bookings';

protected $guarded = [];

    /**
     * Relation: YarnBooking belongs to a Proforma Invoice
     */
    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function items()
    {
        return $this->hasMany(YarnBookingItem::class);
    }

    public function receiveItems()
    {
        return $this->hasMany(YarnItemReceive::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function getBookingNo()
    {
        $length = 8; // ID থেকে কত digit number তৈরি হবে
        return str_pad($this->id, $length, '0', STR_PAD_LEFT);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'editedby_id');
    }

}
