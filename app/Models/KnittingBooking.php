<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnittingBooking extends Model
{
    use SoftDeletes;

    protected $table = 'knitting_bookings';

    protected $fillable = [
        'pi_id',
        'booking_no',
        'style',
        'fabric_type',
        'required_qty',
        'expected_delivery',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
        'status'
    ];

    protected $dates = [
        'expected_delivery',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships


    public function getBookingNo()
    {
        $length = 8; // ID থেকে কত digit number তৈরি হবে
        return str_pad($this->booking_no, $length, '0', STR_PAD_LEFT);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pi()
    {
        return $this->belongsTo(ProformaInvoice::class,'pi_id');
    }
}

