<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetAccessory extends Model
{
    use SoftDeletes;

    protected $table = 'budget_accessories';

    protected $fillable = [
        'budget_id',
        'accessories_desc',
        'supplier_name',

        'unit_price',
        'unit_number',
        'consumption',
        'wastage_percent',

        'total_qty',
        'total_cost',
        'pre_cost_percent',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
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
