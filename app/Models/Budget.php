<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    // use SoftDeletes;

    protected $guarded = [];

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

        // static::deleting(function ($model) {
        //     $model->deleted_by = auth()->id();
        //     $model->save();
        // });
    }

}
