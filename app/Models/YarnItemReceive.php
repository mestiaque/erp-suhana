<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YarnItemReceive extends Model
{
    use HasFactory;

    protected $table = 'yarn_item_receives';

    protected $guarded = [];

    protected $casts = [
        'created_date' => 'date',
        'created_date' => 'date',
    ];

    /**
     * Relation: YarnBookingItem belongs to a YarnBooking
     */
    public function yarnBooking()
    {
        return $this->belongsTo(YarnBooking::class, 'yarn_booking_id');
    }
    public function yarnBookingItem()
    {
        return $this->belongsTo(YarnBookingItem::class, 'yarn_booking_item_id');
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
