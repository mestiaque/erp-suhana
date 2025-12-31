<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YarnBooking extends Model
{
    use SoftDeletes;

    protected $table = 'yarn_bookings';

    protected $fillable = [
        'pi_id',
        'booking_no',
        'order_no',
        'style',
        'fabric_type',
        'yarn_count',
        'yarn_type',
        'required_qty',
        'supplier',
        'expected_delivery',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by',
        'status'

        //received_qty update when receive
        //delivery_qty update when deliver
        //balance ( default 0, increment when received, decremnt when delivery)
    ];

    protected $dates = [
        'expected_delivery',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function deliveries()
    {
        return $this->hasMany(YarnDelivery::class, 'booking_id');
    }

    // public function receives()
    // {
    //     return $this->hasMany(YarnReceive::class, 'booking_id');
    // }

    public function receives()
    {
        // আপনার টেবিল অনুযায়ী কলামের নাম 'booking_no' হলে:
        return $this->hasMany(YarnReceive::class, 'booking_no', 'booking_no');
    }

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

    public function getOrderItem(){
        return OrderDetailItem::where('order_no', $this->order_no)->where('style_no', $this->style)->first();
    }

    public function getYarnDetailsAttribute()
    {
        $details = [];
        $yarnCounts = explode(',', $this->yarn_count);
        $requiredQtys = explode(',', $this->required_qty);

        foreach ($yarnCounts as $index => $count) {
            $details[] = [
                'count' => trim($count),
                'qty' => isset($requiredQtys[$index]) ? (int)trim($requiredQtys[$index]) : 0,
            ];
        }

        return $details;
    }
}
