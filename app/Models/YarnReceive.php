<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YarnReceive extends Model
{
    use SoftDeletes;

    protected $table = 'yarn_receives';

    protected $fillable = [
        'booking_id',
        'received_qty',
        'receive_date',
        'qc_status',
        'warehouse_location',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = [
        'receive_date',
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
