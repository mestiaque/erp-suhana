<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'serial_number',
        'model',
        'purchase_date',
        'value',
        'status',
        'description',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'value' => 'decimal:2',
    ];

    public function distributions()
    {
        return $this->hasMany(AssetDistribution::class);
    }

    public function currentHolder()
    {
        return $this->hasOne(AssetDistribution::class)->where('status', 'active');
    }
}
