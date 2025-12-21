<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class ProductionPlanning extends Model
{
    use HasFactory;
    use ActivityLoggable;

    protected $guarded = [];

    protected $casts = [
        'created_date'     => 'date',
        'cutting_start'    => 'datetime',
        'cutting_end'      => 'datetime',
        'sewing_start'     => 'datetime',
        'sewing_end'       => 'datetime',
        'packing_start'    => 'datetime',
        'packing_end'      => 'datetime',
        'shippment_start'  => 'datetime',
        'shippment_end'    => 'datetime',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function style()
    {
        return $this->belongsTo(OrderDetail::class,'style_no','style_no');
    }

    public function sewingLines()
    {
        return $this->hasMany(ProductionSewing::class, 'planning_id');
    }

    public function floorLines(){
        return Attribute::whereIn(
            'slug',
            $this->sewingLines()->pluck('line_name')
        )->get();
    }



}
