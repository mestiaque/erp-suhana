<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionItem extends Model
{
    protected $fillable = [
        'requisition_id',
        'material_id',
        'material_name',
        'unit',
        'addedby_id',
        'qty',
    ];

    public function requision()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'requisition_id');
    }
    public function material()
    {
        return $this->belongsTo(Post::class, 'material_id'); // assuming Post model stores materials
    }
    
}
