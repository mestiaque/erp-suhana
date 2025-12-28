<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YarnReceive extends Model
{

    protected $table = 'yarn_receives';

    protected $guarded = [];

    protected $dates = [
        'receive_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationship
    public function booking()
    {
        return $this->belongsTo(YarnBooking::class, 'booking_item_id');
    }

    public function pi()
    {
        return $this->belongsTo(ProformaInvoice::class, 'pi_id');
    }

    public function bookingRow()
    {
        return $this->belongsTo(YarnBooking::class, 'booking_item_id');
    }

    public function getBookingNo()
    {
        $length = 8;
        return str_pad($this->booking_no, $length, '0', STR_PAD_LEFT);
    }
    public function getRecvNo()
    {
        $length = 8;
        return str_pad($this->receive_no, $length, '0', STR_PAD_LEFT);
    }
}
