<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'order_no',
        'style_no',

        'buyer_id',
        'buyer_name',
        'merchant_id',
        'merchant_name',
        'company_name',

        'total_order_qty',
        'order_unit_price',
        'order_total_value',

        'pre_cost_date',
        'post_cost_date',
        'shipment_date',

        'total_yarn_cost',
        'total_knitting_cost',
        'total_accessories_cost',
        'grand_total_cost',

        'profit_amount',
        'profit_percent',

        'status',
        'remarks',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /* ===============================
     | RELATIONS
     =============================== */

    public function order()
    {
        return $this->belongsTo(OrderDetail::class, 'order_id');
    }

    public function yarns()
    {
        return $this->hasMany(BudgetYarn::class);
    }

    public function knittings()
    {
        return $this->hasMany(BudgetKnitting::class);
    }

    public function accessories()
    {
        return $this->hasMany(BudgetAccessory::class);
    }

    /* ===============================
     | SCOPES (OPTIONAL)
     =============================== */

    public function scopeDraft($q)
    {
        return $q->where('status', 'draft');
    }

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id();
            $model->save();
        });
    }

}
