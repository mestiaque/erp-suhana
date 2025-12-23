<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetKnitting extends Model
{
    protected $table = 'budget_knitting';
    protected $guarded = [];


    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }
}
