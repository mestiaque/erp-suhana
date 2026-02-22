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

    // columns
    // id
    // addedby_id
    // editedby_id
    // pi_id
    // pi_no
    // master_plan_id
    // pi_item_id
    // order_no
    // status
    // remarks
    // sewing_start
    // sewing_end
    // style_qty
    // style_no
    // extra_time
    // working_hours
    // total_hourly_capacity
    // total_working_time
    // created_at
    // updated_at

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

    public function orderDetailItems()
    {
        return $this->belongsTo(OrderDetail::class,'order_no','order_no');
    }

    public function sewingLines()
    {
        return $this->hasMany(ProductionSewing::class, 'planning_id');
    }

    public function sewingOutputs()
    {
        return $this->hasMany(SewingOutput::class, 'planning_id');
    }

    public function floorLines(){
        return Attribute::whereIn(
            'slug',
            $this->sewingLines()->pluck('line_name')
        )->get();
    }



}
