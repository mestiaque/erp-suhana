<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Media;
use App\Models\Order;
use App\Models\Sample;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\OrderItem;
use App\Models\SampleItem;
use App\Models\ProductionPlanning;
use App\Models\ProductionSewing;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ProductionController extends Controller
{
    public function productionPlanning(Request $r)
    {

        $orders =ProductionPlanning::latest()
            ->where('status','<>','temp')
            ->where(function($q) use ($r) {

                // SEARCH
                if ($r->search) {
                    $search = $r->search;
                    $q->where(function($qq) use ($search) {
                        $qq->where('style_no', 'LIKE', "%{$search}%")
                        ->orWhereHas('style',function($qqq) use ($search) {
                            $qqq->orWhere('buyer_name', 'LIKE', "%{$search}%")
                            ->orWhere('merchant_name', 'LIKE', "%{$search}%");
                        });
                    });
                }

                // DATE RANGE
                if ($r->startDate || $r->endDate) {
                    $from = $r->startDate ?: now()->format('Y-m-d');
                    $to   = $r->endDate ?: now()->format('Y-m-d');

                    $q->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
                }

                // STATUS
                if ($r->status) {
                    $q->where('pi_status', $r->status);
                }
            })
            ->paginate(25)
            ->appends($r->all());

        $totals = ProductionPlanning::whereNotIn('status', ['trash', 'temp'])
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN status = 'confirmed' THEN 1 END) AS confirmed")
            ->selectRaw("COUNT(CASE WHEN status = 'approved' THEN 1 END) AS approved")
            ->selectRaw("COUNT(CASE WHEN status = 'cancelled' THEN 1 END) AS cancelled")
            ->first();

        return view(adminTheme().'productions.planning.index',compact('orders','totals'));
    }

    public function productionPlanningAction(Request $r, $action, $id = null){

        if($action=='create'){
            $plan =ProductionPlanning::where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$plan){
                $plan =new ProductionPlanning();
                $plan->status='temp';
                $plan->addedby_id=Auth::id();
                
            }
            $plan->created_at=Carbon::now();
            $plan->save();
            return redirect()->route('admin.productionPlanningAction',['edit',$plan->id]);
        }

        $plan = ProductionPlanning::find($id);
        if(!$plan){
            session()->flash('error', 'Plan Not Found');
            return redirect()->route('admin.productionPlanning');
        }

        if($action=='view'){


            return view(adminTheme().'productions.planning.view',compact('plan'));
        }

        if($action=='date-update'){

            $fields = [
                'cutting_start',
                'cutting_end',
                'sewing_start',
                'sewing_end',
                'packing_start',
                'packing_end',
                'shippment_start',
                'shippment_end',
            ];

            if (in_array($r->dataName, $fields)){
                $plan->{$r->dataName} = $r->dataValue; // Dynamic property
                $plan->save();
            }

            return response()->json(['success'=>true,'view'=>'']);
        }

        if($action=='update'){


            $plan->style_no      = $r->style_no;
            $plan->style_qty     = $r->style_qty?:0;
            $plan->extra_time    = $r->extra_time?:0;
            $plan->sewing_end    = $r->sewing_end;
            $plan->working_hours = 10;
            $plan->status = 'pending';
            $plan->save();

            $newFloors = $r->floor ?? [];
            $existingFloors = $plan->sewingLines()->pluck('line_name')
                                ->toArray();

            $toDelete = array_diff($existingFloors, $newFloors);
            if (!empty($toDelete)) {
                    $plan->sewingLines()
                    ->whereIn('line_name', $toDelete)
                    ->delete();
            }

            foreach ($newFloors as $floorSlug) {
                $floorLine = Attribute::where('type', 4)
                                ->where('slug', $floorSlug)
                                ->first();
                if($floorLine){
                    $line = $plan->sewingLines()
                            ->where('line_name', $floorLine->slug)
                            ->first();
                    if (!$line) {
                        $line = new ProductionSewing();
                        $line->planning_id = $plan->id;
                        $line->floor_name  = $floorLine->name;
                        $line->line_name   = $floorLine->slug;
                    }
                    $line->style_no      = $plan->style_no;
                    $line->capacity_hour = $floorLine->capacity;
                    $line->save();
                }
            }

            $plan->total_hourly_capacity = $plan->sewingLines()->sum('capacity_hour');
            
            $totalMinutes = $this->calculateWorkingMinutes($plan->sewing_start, $plan->sewing_end);
            // add lose time
            $totalMinutes += intval($plan->extra_time);
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $plan->total_working_time = "{$hours}h - {$minutes}m";
            $plan->save();

            session()->flash('success','Purchase Receive Updated');
            return redirect()->route('admin.productionPlanningAction',['view',$plan->id]);
        }

        // return $plan->sewingLines;

        return view(adminTheme().'productions.planning.edit', compact('plan'));
    }

    function calculateWorkingMinutes($start, $end){
        $start = Carbon::parse($start);
        $end   = Carbon::parse($end);

        $totalMinutes = 0;
        // working slots
        $slot1Start = 10;
        $slot1End   = 13;
        $slot2Start = 14;
        $slot2End   = 21;

        while ($start < $end) {

            $hour = $start->hour;

            if ($hour >= $slot1Start && $hour < $slot1End) {
                $start->addMinute();
                $totalMinutes++;
            }
            elseif ($hour >= $slot2Start && $hour < $slot2End) {
                $start->addMinute();
                $totalMinutes++;
            }
            else {
                // Jump into next valid working slot
                if ($hour < $slot1Start) {
                    $start->setTime($slot1Start, 0);
                }
                else if ($hour < $slot2Start) {
                    $start->setTime($slot2Start, 0);
                }
                else {
                    $start->addDay()->setTime($slot1Start, 0);
                }
            }
        }
        return $totalMinutes;
    }

    public function production(Request $r)
    {
        $styles =SampleItem::whereHas('sample',function($q){
                        $q->latest()->where('pi_status','pending');            
                    })
                    ->get();
        return view(adminTheme().'productions.productionList',compact('styles'));
    }
    
    public function dailyProduction(Request $r)
    {


        
        return view(adminTheme().'productions.daily.index');
    }

    public function yarnBooking(Request $r)
    {


        
        return view(adminTheme().'productions.yarn-booking.index');
    }

    public function knittingBooking(Request $r)
    {


        
        return view(adminTheme().'productions.knitting-booking.index');
    }

    public function dyingBooking(Request $r)
    {


        return view(adminTheme().'productions.dying-booking.index');
    }

    
    




}
