<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetSummary extends Model
{
    protected $table = 'budget_summary';
    protected $fillable = [
        'budget_id',
        'total_expenditure',
        'percent_of_total',
        'reservation',
        'btb_percent',
        'btb_value',
        'cash_percent',
        'cash_value',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }
}
