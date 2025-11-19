<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReceive extends Model
{
    use HasFactory;

    protected $table = 'purchase_receives';

    protected $fillable = [
        'purchase_id',
        'purchase_no',
        'challan_no',
        'purchase_receive_no',
        'note',
        'status',
        'addedby_id',
        'created_at',
        'updated_at',
    ];

    // Relation to purchase order
    public function purchase()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_id');
    }

    // Relation to received items
    public function items()
    {
        return $this->hasMany(PurchaseReceiveItem::class, 'purchase_receive_id');
    }

    // Relation to creator user
    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }
}
