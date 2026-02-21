<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DyeingReceive extends Model
{
    protected $guarded = [];

    public function booking() {
        return $this->belongsTo(DyeingBooking::class, 'booking_no', 'booking_no');
    }

    public function bookingItem() {
        return $this->belongsTo(DyeingBooking::class, 'booking_item_id', 'id');
    }

    public function pi() {
        return $this->belongsTo(ProformaInvoice::class, 'pi_id');
    }

    public function getReceiveNo()
    {
        $length = 8; // ID থেকে কত digit number তৈরি হবে
        return str_pad($this->receive_no, $length, '0', STR_PAD_LEFT);
    }
    public function getBookingNo()
    {
        $length = 8; // ID থেকে কত digit number তৈরি হবে
        return str_pad($this->booking_no, $length, '0', STR_PAD_LEFT);
    }
}
