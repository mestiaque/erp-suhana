<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_date' => 'date',
    ];

    // Relationships

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'order_id');
    }

    public function addedby()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }
    public function canPayBy()
    {
        return $this->belongsTo(User::class, 'can_pay_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    public function trashBy()
    {
        return $this->belongsTo(User::class, 'trash_by');
    }
    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }
}
