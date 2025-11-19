<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'company_id',
        'addedby_id',
        'status',
        'note',
        'created_date',
        'expected_date'
    ];

    // Relationships

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id')
                    ->where('supplier', 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }
}
