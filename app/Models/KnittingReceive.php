<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnittingReceive extends Model
{
    protected $guarded = [];

    public function knittingBooking()
    {
        return $this->belongsTo(KnittingBooking::class, 'knit_id');
    }

    public function pi()
    {
        return $this->belongsTo(ProformaInvoice::class, 'pi_id');
    }

    public function getRecvNo()
    {
        $length = 8; // ID থেকে কত digit number তৈরি হবে
        return str_pad($this->receive_no, $length, '0', STR_PAD_LEFT);
    }

    public function getKBookingNo()
    {
        $length = 8; // ID থেকে কত digit number তৈরি হবে
        return str_pad($this->knit_booking_no, $length, '0', STR_PAD_LEFT);
    }
}
