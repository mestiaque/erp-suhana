<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAccessories extends Model
{
    protected $table = 'budget_accessories';
    protected $fillable = [
        'budget_id',
        'description',
        'supplier',
        'qty',
        'unit_price',
        'ttl_usd',
        'item_total',
        'percent',
        'company_name',
        'payment_value',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }
}
