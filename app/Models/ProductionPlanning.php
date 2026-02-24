<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class ProductionPlanning extends Model
{
    use HasFactory;
    use ActivityLoggable;

    protected $fillable = [
        'planning_month',
        'addedby_id',
        'editedby_id',
        'pi_id',
        'pi_no',
        'master_plan_id',
        'pi_item_id',
        'order_no',
        'status',
        'remarks',
        'sewing_start',
        'sewing_end',
        'style_qty',
        'style_no',
        'color_name',
        'color_qty',
        'extra_time',
        'working_hours',
        'total_hourly_capacity',
        'total_working_time',
        'created_at',
        'updated_at'

    ];


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
        'planning_month'   => 'array',
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

    // Get Cutting Data
    public function getCuttingData($date = null)
    {
        $query = Cutting::where('pi_no', $this->pi_no)
            ->where('order_no', $this->order_no)
            ->where('style_no', $this->style_no);

        if ($this->color_name) {
            $query->where('color_name', $this->color_name);
        }

        if ($date) {
            $query->whereDate('cutting_date', $date);
            return $query->sum('cutting_qty');
        }
        return $query->sum('cutting_qty');
    }

    public function getTodayCuttingInput()
    {
        return $this->getCuttingData(today());
    }

    public function getTotalCuttingInput()
    {
        return $this->getCuttingData();
    }

    // Get Finishing Data
    public function getFinishingData($date = null)
    {
        $query = Finishing::where('pi_no', $this->pi_no)
            ->where('order_no', $this->order_no)
            ->where('style_no', $this->style_no);

        if ($this->color_name) {
            $query->where('color_name', $this->color_name);
        }

        if ($date) {
            $query->whereDate('finishing_date', $date);
            return $query->sum('finishing_qty');
        }
        return $query->sum('finishing_qty');
    }

    public function getTodayFinishingInput()
    {
        return $this->getFinishingData(today());
    }

    public function getTotalFinishingInput()
    {
        return $this->getFinishingData();
    }

    // Get Iron Data
    public function getIronData($date = null)
    {
        $query = Iron::where('pi_no', $this->pi_no)
            ->where('order_no', $this->order_no)
            ->where('style_no', $this->style_no);

        if ($this->color_name) {
            $query->where('color_name', $this->color_name);
        }

        if ($date) {
            $query->whereDate('iron_date', $date);
            return $query->sum('iron_qty');
        }
        return $query->sum('iron_qty');
    }

    public function getTodayIronInput()
    {
        return $this->getIronData(today());
    }

    public function getTotalIronInput()
    {
        return $this->getIronData();
    }

    // Get Poly Data
    public function getPolyData($date = null)
    {
        $query = Poly::where('pi_no', $this->pi_no)
            ->where('order_no', $this->order_no)
            ->where('style_no', $this->style_no);

        if ($this->color_name) {
            $query->where('color_name', $this->color_name);
        }

        if ($date) {
            $query->whereDate('poly_date', $date);
            return $query->sum('poly_qty');
        }
        return $query->sum('poly_qty');
    }

    public function getTodayPolyInput()
    {
        return $this->getPolyData(today());
    }

    public function getTotalPolyInput()
    {
        return $this->getPolyData();
    }

    // Get Sewing Data
    public function getSewingData($date = null)
    {
        $query = SewingOutput::where('planning_id', $this->id);

        if ($date) {
            $query->whereDate('created_at', $date);
            return $query->sum('production');
        }
        return $query->sum('production');
    }

    public function getTodaySewingInput()
    {
        return $this->getSewingData(today());
    }

    public function getTotalSewingInput()
    {
        return $this->getSewingData();
    }

    // Balance = Total Cutting Input - Total Finishing Input (as final output)
    public function getBalance()
    {
        return $this->getTotalCuttingInput() - $this->getTotalFinishingInput();
    }

    // Master Plan Relationship
    public function masterPlan()
    {
        return $this->belongsTo(MasterPlanning::class, 'master_plan_id');
    }

    // Get available colors from OrderDetailItem for this style/order
    public function getAvailableColors()
    {
        return OrderDetailItem::where('order_no', $this->order_no)
            ->where('style_no', $this->style_no)
            ->where('status', 'confirmed')
            ->get();
    }

    // Get display name with color
    public function getDisplayName()
    {
        $name = $this->style_no;
        if ($this->color_name) {
            $name .= ' - ' . $this->color_name;
        }
        return $name;
    }

    // Get the quantity based on whether color is selected
    public function getPlanningQty()
    {
        return $this->color_qty ?? $this->style_qty;
    }


}
