<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReceiveItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_receive_items';

    protected $fillable = [
        'purchase_receive_id',
        'purchase_id',
        'purchase_item_id',
        'received_qty',
        'material_id',
        'material_name',
        'note',
        'created_at',
        'updated_at',
    ];

    // Relation to purchase receive
    public function receive()
    {
        return $this->belongsTo(PurchaseReceive::class, 'purchase_receive_id');
    }

    // Relation to purchase order item
    public function orderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_item_id');
    }
    
    public function purchase()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_id');
    }
    
    public function meterial()
    {
        return $this->belongsTo(Post::class, 'material_id');
    }
}
