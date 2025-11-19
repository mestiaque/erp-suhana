<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'material_id',
        'material_name',
        'qty',
        'unit',
        'price',
        'addedby_id'
    ];

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    public function material()
    {
        return $this->belongsTo(Post::class, 'material_id'); // assuming Post model stores materials
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }
}
