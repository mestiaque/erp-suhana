<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetAccessory extends Model
{
    // use SoftDeletes;

    protected $table = 'budget_accessories';

    protected $guarded = [];


    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         $model->created_by = auth()->id();
    //     });

    //     static::updating(function ($model) {
    //         $model->updated_by = auth()->id();
    //     });

    //     static::deleting(function ($model) {
    //         $model->deleted_by = auth()->id();
    //         $model->save();
    //     });
    // }

}
