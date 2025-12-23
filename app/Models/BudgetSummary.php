<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetSummary extends Model
{
    protected $table = 'budget_summary';
    protected $guarded = [];


    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }
}
