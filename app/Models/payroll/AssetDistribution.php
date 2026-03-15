<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'assignment_date',
        'return_date',
        'condition_on_assign',
        'condition_on_return',
        'status',
        'remarks',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'return_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
