<?php

namespace App\Models;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'requested_by',
        'department_id',
        'request_date',
        'expected_delivery_date',
        'note',
        'requisition_no',
        'created_date',
        'note',
        'status',
        'addedby_id',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseRequisitionItem::class, 'requisition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function department()
    {
        return $this->belongsTo(Attribute::class, 'department_id')
                    ->where('type', 3);
    }

    public function designation()
    {
        return $this->belongsTo(Attribute::class, 'designation_id')
                    ->where('type', 2);
    }

}
