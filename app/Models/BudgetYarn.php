<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetYarn extends Model
{
    use SoftDeletes;

    protected $table = 'budget_yarns';

    protected $fillable = [
        'budget_id',
        'fab_desc',
        'supplier_name',
        'yarn_count',

        'unit_price',
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
