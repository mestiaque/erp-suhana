<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetDyeing extends Model
{
    protected $table = 'budget_dyeing';
    protected $guarded = [];


    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }
}
