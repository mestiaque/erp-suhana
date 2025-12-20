<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YarnDelivery extends Model
{
    use SoftDeletes;

    protected $table = 'yarn_deliveries';

    protected $fillable = [
        'booking_id',
        'delivered_qty',
        'delivery_date',
        'production_line',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = [
        'delivery_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationship
    public function booking()
    {
        return $this->belongsTo(YarnBooking::class, 'booking_id');
    }
}
