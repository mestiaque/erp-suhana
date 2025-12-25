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

    public function dyeings()
    {
        return $this->hasMany(BudgetDyeing::class);
    }

    public function printEmbroidery()
    {
        return $this->hasMany(BudgetPrintEmbroidery::class);
    }

    public function cms()
    {
        return $this->hasMany(BudgetCm::class);
    }

    public function tests()
    {
        return $this->hasMany(BudgetTest::class);
    }

    public function summary()
    {
        return $this->hasOne(BudgetSummary::class);
    }

    public function productionCosts()
    {
        return $this->hasOne(BudgetProductionCost::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
