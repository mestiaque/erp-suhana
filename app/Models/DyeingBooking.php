<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DyeingBooking extends Model
{
    use SoftDeletes;

    protected $table = 'dyeing_bookings';

    // Fields that are mass assignable
    protected $fillable = [
        'pi_id',
        'booking_no',
        'style',
        'fabric_type',
        'composition',
        'color',
        'shade',
        'required_qty',
        'buyer_name',
        'expected_delivery',
        'remarks',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // Dates for Carbon instances
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'expected_delivery',
    ];

    public function getBookingNo()
    {
        $length = 8; // ID থেকে কত digit number তৈরি হবে
        return str_pad($this->booking_no, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship with ProformaInvoice
     */
    public function pi()
    {
        return $this->belongsTo(ProformaInvoice::class, 'pi_id');
    }

    /**
     * Created by user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updated by user
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Deleted by user
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
