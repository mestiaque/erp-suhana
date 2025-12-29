<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Cutting;
use App\Models\Attribute;
use App\Models\OrderDetail;
use App\Models\YarnBooking;
use App\Models\YarnReceive;
use App\Models\SewingOutput;
use Illuminate\Http\Request;
use App\Models\DyeingBooking;
use App\Models\KnittingBooking;
use App\Models\KnittingReceive;
use App\Models\ProformaInvoice;
use App\Models\ProductionSewing;
use App\Models\ProductionPlanning;
use Illuminate\Support\Facades\DB;
use App\Models\ProformaInvoiceItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
                    $q->where('status', $r->status);
                }
            })
            ->paginate(25)
            ->appends($r->all());

        if($r->has('print')){
            $rows = [];
            $totalOrders = 0;
            $totalColors = 0;

            foreach ($orders as $order) {

                $items = $order->style?->items ?? collect();

                $itemNames  = $items->pluck('item_name')->unique()->values()->implode(', ');
                $colors     = $items->pluck('color_name')->unique()->values()->implode(', ');
                $colorQty   = $items->pluck('qty')->sum();

                foreach ($order->sewingLines as $line) {

                    $rows[] = [
                        'buyer'        => $order->style?->buyer_name,
                        'customer'     => $order->style?->buyer_name,
                        'style_no'     => $order->style_no,
                        'description'  => $itemNames,
                        'order_qty'    => number_format($order->style_qty),
                        'colors'       => $colors,
                        'color_qty'    => number_format($colorQty),
                        'line'         => $line->line_name,
                        'status'       => ucfirst($order->status),
                        'fabrication'  => $order->fabrication,
                        'start_time'   => $order->sewing_start
                                            ? Carbon::parse($order->sewing_start)->format('d M, h:i A')
                                            : null,
                        'end_time'     => $order->sewing_end
                                            ? Carbon::parse($order->sewing_end)->format('d M, h:i A')
                                            : null,
                    ];
                }

                $totalOrders += $order->style_qty;
                $totalColors += $colorQty;
            }

            $rows = collect($rows)->sortBy('line')->values();


            return view(adminTheme().'productions.planning.printList',compact('totalOrders','totalColors','rows'));
        }

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

        if ($action == 'update') {

            $plan->style_no      = $r->style_no;
            $plan->style_qty     = $r->style_qty ?: 0;
            $plan->extra_time    = $r->extra_time ?: 0;
            $plan->sewing_end    = $r->sewing_end;
            $plan->status        = 'confirmed';
            $plan->save();

            /* ---------------------------
            FLOOR DATA COLLECTION
            ----------------------------*/
            $selectedFloorData = collect($r->floor)->map(function($floorSlug) use ($r) {
                return [
                    'line'     => $floorSlug,
                    'capacity' => $r->capacity[$floorSlug] ?? 0,
                    'whours'   => $r->hours[$floorSlug] ?? 0,
                ];
            });

            /* ---------------------------
            DELETE REMOVED FLOORS
            ----------------------------*/
            $existingFloors = $plan->sewingLines()->pluck('line_name')->toArray();
            $newFloors      = $selectedFloorData->pluck('line')->toArray();

            $toDelete = array_diff($existingFloors, $newFloors);

            if (!empty($toDelete)) {
                $plan->sewingLines()->whereIn('line_name', $toDelete)->delete();
            }

            /* ---------------------------
            INSERT / UPDATE FLOORS
            ----------------------------*/
            foreach ($selectedFloorData as $floor) {

                $floorLine = Attribute::where('type', 4)
                            ->where('slug', $floor['line'])
                            ->first();

                if (!$floorLine) continue;

                $line = $plan->sewingLines()
                        ->where('line_name', $floorLine->slug)
                        ->first();

                if (!$line) {
                    $line = new ProductionSewing();
                    $line->planning_id = $plan->id;
                    $line->floor_name  = $floorLine->name;
                    $line->line_name   = $floorLine->slug;
                }

                // UPDATE LINE-WISE capacity + working hour
                $line->style_no      = $plan->style_no;
                $line->capacity_hour = intval($floor['capacity']);
                $line->working_hours = intval($floor['whours']);
                $line->save();
            }

            /* ---------------------------
                TOTAL HOURLY CAPACITY
            ----------------------------*/
            $plan->total_hourly_capacity = $plan->sewingLines()->sum('capacity_hour');


            /* ---------------------------
                TOTAL WORKING TIME CALC
            ----------------------------*/
            $totalMinutes = 0;

            foreach ($selectedFloorData as $floor) {
                $totalMinutes += intval($floor['whours']) * 60;
            }

            // add extra time
            $totalMinutes += intval($plan->extra_time);

            $hours   = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            $plan->total_working_time = "{$hours}h - {$minutes}m";
            $plan->save();

            session()->flash('success', 'Production Planning Updated');
            return redirect()->route('admin.productionPlanningAction', ['view', $plan->id]);
        }

        if($action=='delete'){
            $plan->sewingLines()->delete();
            $plan->delete();
            session()->flash('success', 'Production Plan Deleted');
            return redirect()->route('admin.productionPlanning');
        }
        // return $plan->sewingLines;
        $productionStyleNos = ProductionPlanning::pluck('style_no')
            ->filter() // removes null values
            ->map(fn($val) => trim($val)) // removes extra spaces
            ->toArray();

        $styles = OrderDetail::where('status', 'confirmed')
            ->whereNotIn('style_no', $productionStyleNos)
            ->orderBy('id', 'desc')
            ->get();


        if($action == 'print'){
            $plan = ProductionPlanning::with(['style', 'sewingLines'])->find($id);
            if (!$plan) {
                session()->flash('error', 'Plan Not Found');
                return redirect()->route('admin.productionPlanning');
            }
            // Prepare summary data if needed
            $totalCapacity = $plan->sewingLines->sum('capacity_hour');
            $totalWorkingHours = $plan->sewingLines->sum('working_hours');

            return view(adminTheme().'productions.planning.print', compact('plan', 'totalCapacity', 'totalWorkingHours'));
        }


        return view(adminTheme().'productions.planning.edit', compact('plan', 'styles'));
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
        $orders =ProductionPlanning::latest()
                    ->whereNotIN('status',['temp', 'pending'])
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

        // return view(adminTheme().'productions.planning.index',compact('orders','totals'));
        return view(adminTheme().'productions.productionList',compact('orders','totals'));
    }

    public function productionAction(Request $r, $action, $id = null){

        $plan = ProductionPlanning::find($id);
        if(!$plan){
            session()->flash('error', 'Plan Not Found');
            return redirect()->route('admin.production');
        }

        if($action=='view'){

            return view(adminTheme().'productions.productionView',compact('plan'));
        }

        return back();
    }

    public function dailyProduction(Request $r)
    {
        $search = $r->search;
        $now = now(); // current timestamp
        $nextHour = $now->copy()->addHour(); // now + 1 hour

        $startDate = $r->startDate? Carbon::parse($r->startDate) : now();

        $floorLines = Attribute::where('type', 4)
                    ->select('id', 'name', 'slug')
                    ->orderBy('slug')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id'    => $item->id,
                            'floor' => $item->name,
                            'line'  => $item->slug,
                            'key'   => $item->name . ' - ' . $item->slug,
                        ];
                    });

        $swings = ProductionSewing::with(['planning', 'planning.style', 'outputs'])
                        ->where('status', 1)
                        ->get()
                        ->groupBy(function ($item) {
                            return $item->floor_name . ' - ' . $item->line_name;
                        });

        return view(adminTheme().'productions.daily.index', compact('swings','startDate', 'floorLines'));
    }

    public function dailyProductionAction(Request $r, $action)
    {
        $startDate = $r->startDate? Carbon::parse($r->startDate) : now();
        if ($action == 'get-style') {
            $floorLine = Attribute::where('type', 4)->find($r->line_id);
            $swings = ProductionSewing::where('floor_name', $floorLine->name)
                    ->where('line_name', $floorLine->slug)
                    ->where(function($query) {
                        $query->where('status', 0)
                            ->orWhere('status', 3);
                    })
                    ->select('id', 'style_no')
                    ->get();

            if (!$swings || $swings->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Sewing Line Not Found']);
            }

            return response()->json([
                'success'    => true,
                'swings'     => $swings,
            ]);
        }

        if ($action == "update") {
            $swing = ProductionSewing::findOrFail($r->plan_id);
            SewingOutput::updateOrCreate(
                [
                    "planning_id"   => $swing->planning->id,
                    "sewing_id"     => $swing->id,
                    'floor_name'    => $swing->floor_name,
                    'line_name'     => $swing->line_name,
                    'style_no'      => $swing->style_no,
                    'capacity_hour' => $swing->capacity_hour,
                    'addedby_id'    => auth()->id(),
                    'sewing_id'     => $r->plan_id,
                    'date'          => $r->date,
                    'hour'          => $r->hour,
                ],
                [
                    'production' => $r->value,
                    'editedby_id' => auth()->id(),
                ]
            );

            return response()->json(['success' => true]);
        }

        if($action == 'status-update'){
            $swing = ProductionSewing::find($r->s_id);
            if ($swing) {
                $swing->update(['status' => 3]);
            }
            return redirect()->route('admin.dailyProduction', ['startDate' => $startDate->format('Y-m-d')]);
            return redirect()->route('admin.dailyProduction', compact('startDate'));
        }

        if($action == 'assing-style'){

            $swing = ProductionSewing::find($r->style_select);
            if ($swing) {
                $swing->update(['status' => 1]);
            }

            return redirect()->route('admin.dailyProduction', ['startDate' => $startDate->format('Y-m-d')]);
            return redirect()->route('admin.dailyProduction', compact('startDate'));
        }

        $floorLines = Attribute::where('type', 4)
                    ->select('id', 'name', 'slug')
                    ->orderBy('slug')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id'    => $item->id,
                            'floor' => $item->name,
                            'line'  => $item->slug,
                            'key'   => $item->name . ' - ' . $item->slug,
                        ];
                    });

        $swings = ProductionSewing::with(['planning.style', 'outputs'])
                        ->where('status', 1)
                        ->get()
                        ->groupBy(function ($item) {
                            return $item->floor_name . ' - ' . $item->line_name;
                        });

        return view(adminTheme().'productions.daily.index', compact('swings, floorLines', 'startDate'));
    }

    public function cutting(Request $r)
    {
        $query = Cutting::query();

        // ডেট ফিল্টার
        if ($r->startDate && $r->endDate) {
            $query->whereBetween('cutting_date', [$r->startDate, $r->endDate]);
        }

        // সার্চ লজিক
        if ($r->search) {
            $query->where(function($q) use ($r) {
                $q->where('pi_no', 'LIKE', "%{$r->search}%")
                ->orWhere('style_no', 'LIKE', "%{$r->search}%");
            });
        }

        $cuttings = $query->latest('cutting_date')->paginate(20);
        $pis = ProformaInvoice::whereNotNull('pi_no')->get();

        return view('admin.productions.cutting.index', compact('cuttings', 'pis'));
    }

    public function cuttingAction(Request $r, $action)
    {

        if ($action == 'get-styles') {
            // একই style_no থাকলে সেটিকে গ্রুপ করে style_qty যোগ করা হচ্ছে
            $styles = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id) // অথবা 'pi_id'
                    ->select('style_no', \DB::raw('SUM(order_qty) as total_style_qty'))
                    ->groupBy('style_no')
                    ->get();

            return response()->json($styles);
        }

        if($action == 'create'){
            $pi = ProformaInvoice::find($r->pi_no);
            Cutting::create([
                'pi_id'         => $pi->id,
                'pi_no'         => $pi->pi_no,
                'style_no'      => $r->style_no,
                'cutting_qty'  => $r->cutting_qty,
                'cutting_date' => $r->cutting_date,
                'remarks'       => $r->remarks,
                'created_by'   => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'Cutting Record Added Successfully');
        }

        if ($action == "update") {
            $cut = Cutting::findorFail($r->id);
            $cut->update([
                'cutting_qty'  => $r->cutting_qty,
                'cutting_date' => $r->cutting_date,
                'remarks'       => $r->remarks,
            ]);
            return redirect()->back()->with('success', 'Cutting Record Updated Successfully');
        }

        if($action == "delete"){
            $cut = Cutting::findorFail($r->id);
            $cut->delete();
            return redirect()->back()->with('success', 'Cutting Record Updated Successfully');
        }

        return redirect()->route('admin.cutting');
    }




    public function yarnBooking(Request $r)
    {
        $query = YarnBooking::query();

        // ----------------------------
        // SEARCH (Buyer Name, Booking No, PI No, Fabrication)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('buyer_name', 'like', "%$search%")
                ->orWhere('booking_no', 'like', "%$search%")
                ->orWhere('pi_id', 'like', "%$search%")
                ->orWhere('fabric_type', 'like', "%$search%");
            });
        }

        // ----------------------------
        // DATE RANGE FILTER
        // ----------------------------
        if ($r->startDate) {
            $query->whereDate('created_at', '>=', $r->startDate);
        }

        if ($r->endDate) {
            $query->whereDate('created_at', '<=', $r->endDate);
        }

        // ----------------------------
        // GROUP BY booking_no / pi_id with required columns
        // ----------------------------
        $bookings = $query->select(
                            'pi_id',
                            'booking_no',
                            DB::raw('MAX(supplier) as supplier'),
                            DB::raw('MAX(expected_delivery) as expected_delivery'),
                            DB::raw('MAX(remarks) as remarks'),
                            DB::raw('SUM(required_qty) as total_req_qty'),
                            DB::raw('COUNT(id) as total_items'),
                            DB::raw('MAX(created_by) as created_by'),
                            DB::raw('MAX(updated_by) as updated_by'),
                            DB::raw('MAX(deleted_by) as deleted_by'),
                            DB::raw('MAX(status) as status')
                        )
                        ->groupBy('pi_id', 'booking_no')
                        ->orderBy('booking_no', 'DESC')
                        ->paginate(10); // pagination

        return view(adminTheme() . 'productions.yarn-booking.index', compact('bookings'));
    }

    public function yarnBookingAction(Request $r, $action, $id = null)
    {
        // -------------------------------
        // CREATE SAMPLE YARN BOOKING
        // -------------------------------
        if ($action == 'create') {
            $pis = ProformaInvoice::whereNotNull('pi_no')->get();
            return view(adminTheme() . 'productions.yarn-booking.edit', [
                'pis' => $pis,
                'items' => [],
                'booking' => null
            ]);
        }

        // -------------------------------
        // EDIT PAGE
        // -------------------------------
        if ($action == 'edit' && $id) {
            $items = YarnBooking::where('booking_no', $id)->get();
            $booking = $items->first();
            $pis = ProformaInvoice::whereNotNull('pi_no')->get();
            return view(adminTheme() . 'productions.yarn-booking.edit', compact('pis', 'items', 'booking'));
        }

        // -------------------------------
        // UPDATE / STORE YARN BOOKING
        // -------------------------------
        if ($action == 'update') {

            $r->validate([
                'pi_id' => 'required|exists:proforma_invoices,id',
                'items.*.fabrication' => 'required|string',
                'items.*.style_no' => 'required|string',
                'items.*.yarn_count' => 'required|array',
                'items.*.yarn_qty' => 'required|array',
                'items.*.requisition_qty' => 'required|numeric|min:0',
                'status' => 'required|string'
            ]);

            $pi = ProformaInvoice::findOrFail($r->pi_id);

            // Determine booking_no
            $bookingNo = $r->booking_no ?? (YarnBooking::max('booking_no') + 1);

            // Delete old rows if editing
            if ($r->booking_no) {
                YarnBooking::where('booking_no', $r->booking_no)->delete();
            }

            // Save each item
            foreach ($r->items as $item) {

                $yarnData = [];
                if (isset($item['yarn_count'], $item['yarn_qty'])) {
                    foreach ($item['yarn_count'] as $index => $count) {
                        $qty = $item['yarn_qty'][$index] ?? 0;
                        if ($count) {
                            $yarnData[] = [
                                'count' => $count,
                                'qty'   => floatval($qty)
                            ];
                        }
                    }
                }

                // Use updateOrCreate to update existing rows if id exists, else create new
                YarnBooking::updateOrCreate(
                    [
                        'id' => $item['id'] ?? null  // send item id in form if editing
                    ],
                    [
                        'pi_id'         => $pi->id,
                        'booking_no'    => $bookingNo,
                        'buyer_name'    => $pi->buyer_name ?? null,
                        'fabric_type'   => $item['fabrication'],
                        'style'         => $item['style_no'],
                        'yarn_count'    => json_encode($yarnData),
                        'required_qty'  => $item['requisition_qty'],
                        'status'        => $r->status,
                        'supplier'      => $r->supplier,
                        'created_by'    => auth()->id(),
                    ]
                );
            }


            session()->flash('success', 'Yarn Booking Saved Successfully');
            return redirect()->route('admin.yarnBooking');
        }

        // -------------------------------
        // DELETE
        // -------------------------------
        if ($action == 'delete') {
            YarnBooking::where('booking_no', $id)->delete();
            session()->flash('success', 'Yarn Booking Deleted Successfully');
            return redirect()->route('admin.yarnBooking');
        }

        // -------------------------------
        // AJAX: PI SELECT
        // -------------------------------
        if ($action == 'pi-select') {
            $pi = ProformaInvoice::find($r->pi_no);
            if (!$pi || $pi->items->count() < 1) {
                return response()->json([
                    'success' => false,
                    'html'    => '<tr><td colspan="5" class="text-center">No items found</td></tr>'
                ]);
            }
            $html = view(adminTheme().'productions.yarn-booking.includes.items', [
                'pi' => $pi,
                'items' => $pi->items
            ])->render();

            return response()->json(['success' => true, 'html' => $html, 'order' => $pi]);
        }

        // -------------------------------
        // LIST ALL YARN BOOKINGS
        // -------------------------------
        $bookings = YarnBooking::all();
        return view(adminTheme() . 'productions.yarn-booking.index', compact('bookings'));
    }

    public function dyeingBooking(Request $r)
    {
        $query = DyeingBooking::query();

        // ----------------------------
        // SEARCH (Booking No, PI No, Style, Fabric Type, Color)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_no', 'like', "%$search%")
                  ->orWhere('pi_id', 'like', "%$search%")
                  ->orWhere('style', 'like', "%$search%")
                  ->orWhere('fabric_type', 'like', "%$search%")
                  ->orWhere('color', 'like', "%$search%");
            });
        }

        // ----------------------------
        // DATE RANGE FILTER
        // ----------------------------
        if ($r->startDate) {
            $query->whereDate('created_at', '>=', $r->startDate);
        }

        if ($r->endDate) {
            $query->whereDate('created_at', '<=', $r->endDate);
        }

        // ----------------------------
        // GROUP BY booking_no / pi_id with required columns
        // ----------------------------
        $bookings = $query->select(
                            'pi_id',
                            'booking_no',
                            DB::raw('MAX(style) as style'),
                            DB::raw('MAX(fabric_type) as fabric_type'),
                            DB::raw('MAX(color) as color'),
                            DB::raw('MAX(shade) as shade'),
                            DB::raw('MAX(expected_delivery) as expected_delivery'),
                            DB::raw('MAX(buyer_name) as buyer_name'),
                            DB::raw('MAX(remarks) as remarks'),
                            DB::raw('SUM(required_qty) as total_req_qty'),
                            DB::raw('COUNT(id) as total_items'),
                            DB::raw('MAX(created_by) as created_by'),
                            DB::raw('MAX(updated_by) as updated_by'),
                            DB::raw('MAX(deleted_by) as deleted_by'),
                            DB::raw('MAX(status) as status')
                        )
                        ->groupBy('pi_id', 'booking_no')
                        ->orderBy('booking_no', 'DESC')
                        ->paginate(10); // pagination

        return view(adminTheme() . 'productions.dyeing-booking.index', compact('bookings'));
    }

    public function dyeingBookingAction(Request $r, $action, $id = null)
    {
        // -------------------------------
        // CREATE NEW DYEING BOOKING
        // -------------------------------
        if ($action == 'create') {
            $pis = ProformaInvoice::whereNotNull('pi_no')->get();
            return view(adminTheme() . 'productions.dyeing-booking.edit', [
                'pis' => $pis,
                'items' => [],
                'booking' => null,
                'action' => 'create'
            ]);
        }

        // -------------------------------
        // EDIT PAGE
        // -------------------------------
        if ($action == 'edit' && $id) { // id = booking no

            $items = DyeingBooking::where('booking_no', $id)->get();
            $booking = $items->first();
            $pis = ProformaInvoice::whereNotNull('pi_no')->get();
            $action = 'update';
            return view(adminTheme() . 'productions.dyeing-booking.edit', compact('pis', 'items', 'booking','action'));
        }

        // -------------------------------
        // UPDATE / STORE DYEING BOOKING
        // -------------------------------

        if ($action === 'update'){
            // Validate request
            $r->validate([
                'pi_id' => 'required|exists:proforma_invoices,id',
                'status' => 'required|string',
                // 'booking_no' => 'required|string', // hidden input
                'items' => 'required|array',
                'items.*.style_no' => 'required|string',
                'items.*.fabrication' => 'required|string',
                'items.*.composition' => 'required|string',
                'items.*.color' => 'required|string',
                'items.*.requisition_qty' => 'required|numeric',
            ]);

            $pi = ProformaInvoice::findOrFail($r->pi_id);

            $newBookingNo = DyeingBooking::max('booking_no') + 1;

            foreach ($r->items as $item) {
                if ($r->action === 'create') {
                    // Create new DyeingBooking
                    DyeingBooking::create([
                        'booking_no'        => $newBookingNo,
                        'pi_id'             => $pi->id,
                        'style'             => $item['style_no'],
                        'fabric_type'       => $item['fabrication'],
                        'composition'       => $item['composition'],
                        'color'             => $item['color'],
                        'required_qty'      => $item['requisition_qty'],
                        'buyer_name'        => $r->buyer_name ?? null,
                        'expected_delivery' => $r->expected_delivery ?? null,
                        'remarks'           => $r->remarks ?? null,
                        'status'            => $r->status,
                        'created_by'        => auth()->id(),
                        'updated_by'        => auth()->id(),
                    ]);
                }

                if ($r->action === 'update') {
                    // Only update existing records (no create)
                    $booking = DyeingBooking::where('booking_no', $item['booking_no'])
                        ->where('style', $item['style_no'])
                        ->where('color', $item['color'])
                        ->first();

                    if ($booking) {
                        $booking->update([
                            'pi_id'             => $pi->id,
                            'required_qty'      => $item['requisition_qty'],
                            'expected_delivery' => $r->expected_delivery ?? null,
                            'buyer_name'        => $r->buyer_name ?? null,
                            'remarks'           => $r->remarks ?? null,
                            'status'            => $r->status,
                            'updated_by'        => auth()->id(),
                        ]);
                    }
                }
            }

            session()->flash(
                'success',
                $r->action === 'create'
                    ? 'Dyeing Booking Created Successfully'
                    : 'Dyeing Booking Updated Successfully'
            );

            return redirect()->route('admin.dyeingBooking');
        }

        // -------------------------------
        // DELETE
        // -------------------------------
        if ($action == 'delete') {
            DyeingBooking::where('booking_no', $id)->delete();
            session()->flash('success', 'Dyeing Booking Deleted Successfully');
            return redirect()->route('admin.dyeingBooking');
        }

        // -------------------------------
        // AJAX: PI SELECT
        // -------------------------------
        if ($action == 'pi-select') {
            $pi = ProformaInvoice::find($r->pi_no);
            if (!$pi || $pi->items->count() < 1) {
                return response()->json([
                    'success' => false,
                    'html'    => '<tr><td colspan="6" class="text-center">No items found</td></tr>'
                ]);
            }

                $items = $pi->items
                    ->flatMap(function ($item) {
                        return optional($item->orderDetails)->items ?? collect();
                    });

                if ($items->count() < 1) {
                    return response()->json([
                        'success' => false,
                        'html' => '<tr><td colspan="6" class="text-center">No items found</td></tr>'
                    ]);
                }

            $html = view(adminTheme().'productions.dyeing-booking.includes.items', [
                'pi'     => $pi,
                'items'  => $items,
                'action' => 'create'
            ])->render();

            return response()->json(['success' => true, 'html' => $html, 'order' => $pi]);
        }


        // -------------------------------
        // LIST ALL DYEING BOOKINGS
        // -------------------------------
        $bookings = DyeingBooking::all();
        return view(adminTheme() . 'productions.dyeing-booking.index', compact('bookings'));
    }


    public function yarnReceive(Request $r)
    {
        // ১. কুয়েরি শুরু (Eager Loading সহ যাতে ডাটা দ্রুত আসে)
        $query = YarnReceive::with(['bookingRow', 'bookingRow.pi']);

        // ২. ফিল্টারিং (Receive No অথবা Chalan No দিয়ে সার্চ)
        if ($r->search) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('receive_no', 'like', "%$search%")
                ->orWhere('chalan_no', 'like', "%$search%")
                ->orWhere('booking_no', 'like', "%$search%");
            });
        }

        // ৩. ডেট রেঞ্জ ফিল্টার
        if ($r->startDate) {
            $query->whereDate('receive_date', '>=', $r->startDate);
        }
        if ($r->endDate) {
            $query->whereDate('receive_date', '<=', $r->endDate);
        }

        // ৪. গ্রুপ বাই লজিক (Receive No অনুযায়ী সামারি দেখানো)
        $receives = $query->select(
                            'receive_no',
                            'pi_id',
                            'booking_no',
                            'receive_date',
                            'chalan_no',
                            'supplier',
                            DB::raw('SUM(receive_qty) as total_receive_qty'),
                            DB::raw('COUNT(id) as total_items_count'),
                            DB::raw('MAX(created_at) as created_at')
                        )
                        ->groupBy('receive_no', 'pi_id', 'booking_no', 'receive_date', 'chalan_no', 'supplier')
                        ->orderBy('created_at', 'DESC')
                        ->paginate(10);
            // dd($receives);
        return view(adminTheme() . 'productions.yarn-receive.index', compact('receives'));
    }

    public function yarnReceiveAction(Request $r, $action, $id = null)
    {
        // -------------------------------------------------------
        // CREATE PAGE
        // -------------------------------------------------------
        if ($action == 'create') {
            $piIds = YarnBooking::pluck('pi_id')->unique();
            $pis = ProformaInvoice::whereIn('id', $piIds)->get();

            return view(adminTheme() . 'productions.yarn-receive.edit', [
                'pis'     => $pis,
                'items'   => [],
                'receive' => null
            ]);
        }

        // -------------------------------------------------------
        // UPDATE / STORE
        // -------------------------------------------------------
        if ($action == 'update') {
            // validation...

            $receiveNo = $r->receive_no ?: 'RECV-' . date('Ymd') . '-' . rand(100, 999);

            // এডিট মোড হলে আগের রিসিভ রেকর্ড মুছে ফেলা (যাতে ডুপ্লিকেট না হয়)
            if ($r->receive_no) {
                $oldReceives = YarnReceive::where('receive_no', $r->receive_no)->get();
                // আগের রিসিভ করা ডাটা বুকিং টেবিল থেকে মাইনাস করে দেওয়া (ব্যালেন্স ঠিক রাখতে)
                foreach($oldReceives as $old) {
                    YarnBooking::where('id', $old->booking_item_id)->decrement('received_qty', $old->receive_qty);
                }
                YarnReceive::where('receive_no', $r->receive_no)->forceDelete();
            }
            $receiveNo = $r->receive_no ?? (YarnReceive::max('receive_no') + 1);
            foreach ($r->items as $item) {
                if (isset($item['yarn_count']) && is_array($item['yarn_count'])) {
                    $rowTotal = 0;
                    foreach ($item['yarn_count'] as $index => $countName) {
                        $qty = isset($item['yarn_receive_qty'][$index]) ? floatval($item['yarn_receive_qty'][$index]) : 0;

                        if ($qty > 0) {
                            YarnReceive::create([
                                'pi_id'           => $r->pi_id,
                                'receive_no'      => $receiveNo,
                                'booking_no'      => $r->booking_no,
                                'booking_item_id' => $item['id'],
                                'receive_date'    => $r->receive_date,
                                'supplier'        => $r->supplier,
                                'chalan_no'       => $r->chalan_no,
                                'yarn_count'      => $countName,
                                'receive_qty'     => $qty,
                                'created_by'      => auth()->id(),
                            ]);
                            $rowTotal += $qty;
                        }
                    }

                    // বুকিং টেবিলের ব্যালেন্স আপডেট
                    if ($rowTotal > 0) {
                        YarnBooking::where('id', $item['id'])->increment('received_qty', $rowTotal);
                    }
                }
            }

            session()->flash('success', 'Yarn Received Successfully');
            return redirect()->route('admin.yarnReceive');
        }



        // -------------------------------------------------------
        // EDIT PAGE
        // -------------------------------------------------------
        if ($action == 'edit' && $id) {
            // ১. ওই নির্দিষ্ট receive_no এর সব ডাটা আনা
            $receives = YarnReceive::where('receive_no', $id)->get();
            $receive = $receives->first();

            if (!$receive) {
                return redirect()->back()->with('error', 'Receive record not found.');
            }

            // ২. এই রিসিভের সাথে সংশ্লিষ্ট বুকিং আইটেমগুলো লোড করা
            // আমরা YarnBooking এর ডাটা পাঠাবো যাতে ব্লেড ফাইলে লিস্ট দেখা যায়
            $items = YarnBooking::where('pi_id', $receive->pi_id)->get();

            // ৩. প্রতিটি বুকিং আইটেমের বিপরীতে বর্তমানে কতটুকু রিসিভ হয়েছে তা ম্যাপ করা
            foreach ($items as $item) {
                // এই নির্দিষ্ট এডিটে এই বুকিং রো এর জন্য কোন কোন কাউন্ট কতটুকু রিসিভ হয়েছে
                $item->current_receive_data = YarnReceive::where('receive_no', $id)
                                                ->where('booking_item_id', $item->id)
                                                ->pluck('receive_qty', 'yarn_count')
                                                ->toArray();
            }

            $piIds = YarnBooking::pluck('pi_id')->unique();
            $pis = ProformaInvoice::whereIn('id', $piIds)->get();

            return view(adminTheme() . 'productions.yarn-receive.edit', compact('pis', 'items', 'receive'));
        }

        // ... আপনার বিদ্যমান update কোড ...

        // -------------------------------------------------------
        // DELETE ACTION
        // -------------------------------------------------------
        if ($action == 'delete' && $id) {
            $receives = YarnReceive::where('receive_no', $id)->get();

            if ($receives->isEmpty()) {
                return redirect()->back()->with('error', 'Record not found.');
            }

            // ডিলিট করার আগে YarnBooking টেবিলের ব্যালেন্স কমিয়ে দেওয়া
            foreach ($receives as $row) {
                YarnBooking::where('id', $row->booking_item_id)->decrement('received_qty', $row->receive_qty);
            }

            // সব রেকর্ড ডিলিট করা
            YarnReceive::where('receive_no', $id)->forceDelete();

            session()->flash('success', 'Entire Receive Record Deleted Successfully');
            return redirect()->route('admin.yarnReceive');
        }


        // -------------------------------------------------------
        // AJAX: PI SELECT
        // -------------------------------------------------------
        if ($action == 'pi-select') {
            $bookingItems = YarnBooking::where('pi_id', $r->pi_id)->get();

            if ($bookingItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No Booking found!']);
            }

            // আপনার মডেলের getYarnDetailsAttribute ব্যবহার করে ডাটা প্রসেস করা
            $html = view(adminTheme().'productions.yarn-receive.includes.items', [
                'items' => $bookingItems
            ])->render();

            $first = $bookingItems->first();
            return response()->json([
                'success'    => true,
                'html'       => $html,
                'supplier'   => $first->supplier,
                'booking_no' => $first->booking_no,
            ]);
        }

        // Default Action: Delete or Index logic...
    }




    public function dyeingReceive(Request $r)
    {
        return view(adminTheme() . 'productions.dyeing-receive.index');

        $query = DyeingBooking::query();

        // ----------------------------
        // SEARCH (Booking No, PI No, Style, Fabric Type, Color)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_no', 'like', "%$search%")
                  ->orWhere('pi_id', 'like', "%$search%")
                  ->orWhere('style', 'like', "%$search%")
                  ->orWhere('fabric_type', 'like', "%$search%")
                  ->orWhere('color', 'like', "%$search%");
            });
        }

        // ----------------------------
        // DATE RANGE FILTER
        // ----------------------------
        if ($r->startDate) {
            $query->whereDate('created_at', '>=', $r->startDate);
        }

        if ($r->endDate) {
            $query->whereDate('created_at', '<=', $r->endDate);
        }

        // ----------------------------
        // GROUP BY booking_no / pi_id with required columns
        // ----------------------------
        $bookings = $query->select(
                            'pi_id',
                            'booking_no',
                            DB::raw('MAX(style) as style'),
                            DB::raw('MAX(fabric_type) as fabric_type'),
                            DB::raw('MAX(color) as color'),
                            DB::raw('MAX(shade) as shade'),
                            DB::raw('MAX(expected_delivery) as expected_delivery'),
                            DB::raw('MAX(buyer_name) as buyer_name'),
                            DB::raw('MAX(remarks) as remarks'),
                            DB::raw('SUM(required_qty) as total_req_qty'),
                            DB::raw('COUNT(id) as total_items'),
                            DB::raw('MAX(created_by) as created_by'),
                            DB::raw('MAX(updated_by) as updated_by'),
                            DB::raw('MAX(deleted_by) as deleted_by'),
                            DB::raw('MAX(status) as status')
                        )
                        ->groupBy('pi_id', 'booking_no')
                        ->orderBy('booking_no', 'DESC')
                        ->paginate(10); // pagination

        return view(adminTheme() . 'productions.dyeing-receive.index', compact('bookings'));
    }

    public function knittingBooking(Request $r)
    {
        // ১. কুয়েরি শুরু (PI রিলেশনসহ Eager Load করা হয়েছে)
        $query = KnittingBooking::with(['pi']);

        // ২. সার্চ ফিল্টার (Knit Booking No, Yarn Booking No, PI No, Fabric Type দিয়ে সার্চ)
        if ($r->search) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_no', 'like', "%$search%")
                ->orWhere('yarn_booking_no', 'like', "%$search%")
                ->orWhere('fabric_type', 'like', "%$search%")
                // PI No দিয়ে সার্চ করতে চাইলে জয়েন করতে হবে বা রিলেশন ব্যবহার করতে হবে
                ->orWhereHas('pi', function ($q_pi) use ($search) {
                    $q_pi->where('pi_no', 'like', "%$search%");
                });
            });
        }

        // ৩. ডেট রেঞ্জ ফিল্টার (created_at)
        if ($r->startDate) {
            $query->whereDate('created_at', '>=', $r->startDate);
        }
        if ($r->endDate) {
            $query->whereDate('created_at', '<=', $r->endDate);
        }

        // ৪. গ্রুপ বাই লজিক (Knit Booking No অনুযায়ী সামারি দেখানো)
        $bookings = $query->select(
                                'booking_no',
                                'pi_id',
                                DB::raw('MAX(knitting_unit) as knitting_unit'),
                                DB::raw('MAX(status) as status'),
                                DB::raw('SUM(booking_qty) as total_booking_qty'), // মোট টার্গেট Qty
                                DB::raw('SUM(produced_qty) as total_produced_qty'), // মোট প্রোডাকশন Qty
                                DB::raw('COUNT(id) as total_items_count'), // কতগুলো ফ্যাব্রিক আইটেম আছে
                                DB::raw('MAX(created_at) as created_at'),
                                DB::raw('MAX(created_by) as created_by')
                            )
                            ->groupBy('booking_no', 'pi_id')
                            ->orderBy('created_at', 'DESC')
                            ->paginate(10); // pagination

        return view(adminTheme() . 'productions.knitting-booking.index', compact('bookings'));
    }

    public function knittingBookingAction(Request $r, $action, $id = null)
    {
            // --- CREATE PAGE ---
        if ($action == 'create') {
            $pis = ProformaInvoice::whereHas('yarnBookings')->get();
            return view(adminTheme() . 'productions.knitting-booking.edit', [
                'pis' => $pis,
                'booking' => null,
                'items' => []
            ]);
        }

        // --- UPDATE / STORE ---
        if ($action == 'updatex') {
            $r->validate([
                'pi_id' => 'required|exists:proforma_invoices,id',
                'items.*.booking_qty' => 'required|numeric|min:0.1',
            ]);

            // নির্ধারণ করা হচ্ছে booking_no (নতুন নাকি এডিট)
            if ($r->booking_no) {
                $knitBookingNo = $r->booking_no;
                // এডিট মোড হলে আগের রেকর্ডগুলো মুছে নতুন করে এন্ট্রি দেওয়া হবে
                \App\Models\KnittingBooking::where('booking_no', $knitBookingNo)->forceDelete();
            } else {
                // নতুন বুকিং হলে অটো-ইনক্রিমেন্ট নম্বর (যেমন: ১০০১ থেকে শুরু)
                $knitBookingNo = \App\Models\KnittingBooking::max('booking_no') + 1;
            }

            foreach ($r->items as $item) {
                if (isset($item['booking_qty']) && $item['booking_qty'] > 0) {

                    // YarnBooking থেকে মূল ম্যানুয়াল নম্বরটি সংগ্রহের জন্য (যদি রিকোয়েস্টে না থাকে)
                    $yarnBooking = \App\Models\YarnBooking::find($item['yarn_booking_id']);

                    \App\Models\KnittingBooking::create([
                        'booking_no'   => $knitBookingNo,
                        'pi_id'             => $r->pi_id,
                        'yarn_booking_no'   => $yarnBooking->booking_no ?? null, // yarn_ prefix
                        'yarn_booking_id'   => $item['yarn_booking_id'],         // yarn_ prefix
                        'style'             => $item['style'] ?? null,
                        'fabric_type'       => $item['fabrication'] ?? null,
                        'gsm'               => $item['gsm'] ?? null,
                        'dia'               => $item['dia'] ?? null,
                        'stitch_length'     => $item['stitch_length'] ?? null,
                        'booking_qty'       => $item['booking_qty'],
                        'knitting_unit'     => $r->knitting_unit,
                        'status'            => $r->status ?? 'pending',
                        'created_by'        => auth()->id(),
                    ]);
                }
            }

            session()->flash('success', 'Knitting Booking Saved Successfully. No: ' . $knitBookingNo);
            return redirect()->route('admin.knittingBooking');
        }

        if ($action == 'update') {
            $r->validate([
                'pi_id' => 'required|exists:proforma_invoices,id',
                'items.*.booking_qty' => 'required|numeric|min:0.1',
            ]);

            // ১. বুকিং নম্বর নির্ধারণ (এডিট হলে আগেরটা থাকবে, নতুন হলে নতুন তৈরি হবে)
            if ($r->booking_no) {
                // যদি রিকোয়েস্টে booking_no থাকে, তার মানে এটি এডিট মোড
                $knitBookingNo = $r->booking_no;

                // এডিট মোডে পুরাতন সব আইটেম মুছে ফেলতে হবে (যাতে ডুপ্লিকেট না হয়)
                KnittingBooking::where('booking_no', $knitBookingNo)->forceDelete();
            } else {
                // নতুন ক্রিয়েট হলে সর্বোচ্চ নম্বরের সাথে ১ যোগ হবে
                $knitBookingNo = KnittingBooking::max('booking_no') + 1;
            }

            // ২. ডাটা সেভ করা
            foreach ($r->items as $item) {
                if (isset($item['booking_qty']) && $item['booking_qty'] > 0) {

                    $yarnBooking = YarnBooking::find($item['yarn_booking_id']);

                    KnittingBooking::create([
                        'booking_no'        => $knitBookingNo, // সবার জন্য একই নম্বর
                        'pi_id'             => $r->pi_id,
                        'yarn_booking_no'   => $yarnBooking->booking_no ?? null,
                        'yarn_booking_id'   => $item['yarn_booking_id'],
                        'style'             => $item['style'] ?? null,
                        'fabric_type'       => $item['fabrication'] ?? null,
                        'gsm'               => $item['gsm'] ?? null,
                        'dia'               => $item['dia'] ?? null,
                        'stitch_length'     => $item['stitch_length'] ?? null,
                        'booking_qty'       => $item['booking_qty'],
                        'knitting_unit'     => $r->knitting_unit,
                        'status'            => $r->status ?? 'pending',
                        'created_by'        => auth()->id(),
                    ]);
                }
            }

            session()->flash('success', 'Knitting Booking Updated Successfully. No: ' . $knitBookingNo);
            return redirect()->route('admin.knittingBooking');
        }

        // --- AJAX: PI SELECT ---
        if ($action == 'pi-select') {
            // অর্ডারের GSM এবং Fabrication পাওয়ার জন্য রিলেশনসহ লোড করা হয়েছে
            $yarnBookings = \App\Models\YarnBooking::where('pi_id', $r->pi_id)
                            ->with(['pi.order.items'])
                            ->get();

            if ($yarnBookings->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No Yarn Booking found!']);
            }

            $html = view(adminTheme().'productions.knitting-booking.includes.items', [
                'items' => $yarnBookings
            ])->render();

            return response()->json([
                'success'    => true,
                'html'       => $html,
                'buyer_name' => $yarnBookings->first()->pi->buyer_name ?? ''
            ]);
        }

        // -------------------------------------------------------
        // EDIT PAGE
        // -------------------------------------------------------
        if ($action == 'edit' && $id) {
            $knitItems = KnittingBooking::where('booking_no', $id)->get();
            $booking = $knitItems->first();

            if (!$booking) return redirect()->back()->with('error', 'Not found.');

            // PI এর আন্ডারে YarnBooking এবং সংশ্লিষ্ট Order Items লোড করা
            $yarnBookings = YarnBooking::where('pi_id', $booking->pi_id)
                            ->with('pi.order.items')
                            ->get();

            foreach ($yarnBookings as $yarnItem) {
                $savedKnitRow = $knitItems->where('yarn_booking_id', $yarnItem->id)->first();

                if ($savedKnitRow) {
                    // এই ভ্যালুগুলো ব্লেড ফাইলে ব্যবহার করা হবে
                    $yarnItem->dia = $savedKnitRow->dia;
                    $yarnItem->gsm = $savedKnitRow->gsm;
                    $yarnItem->stitch_length = $savedKnitRow->stitch_length;
                    $yarnItem->knit_booking_qty = $savedKnitRow->booking_qty;
                }
            }

            $pis = ProformaInvoice::whereHas('yarnBookings')->get();
            return view(adminTheme() . 'productions.knitting-booking.edit', [
                'pis'     => $pis,
                'items'   => $yarnBookings,
                'booking' => $booking
            ]);
        }

        // --- DELETE ---
        if ($action == 'delete' && $id) {
            KnittingBooking::where('booking_no', $id)->delete();
            return redirect()->back()->with('success', 'Booking Deleted');
        }
    }

    public function knittingReceive(Request $r)
    {
        // ১. কুয়েরি শুরু (PI রিলেশনসহ)
        $query = KnittingReceive::with(['pi']);

        // ২. সার্চ ফিল্টার
        if ($r->search) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('receive_no', 'like', "%$search%")
                ->orWhere('chalan_no', 'like', "%$search%")
                ->orWhere('knit_booking_no', 'like', "%$search%");
            });
        }

        // ৩. ডেট রেঞ্জ ফিল্টার
        if ($r->startDate) {
            $query->whereDate('receive_date', '>=', $r->startDate);
        }
        if ($r->endDate) {
            $query->whereDate('receive_date', '<=', $r->endDate);
        }

        // ৪. গ্রুপ বাই লজিক (নিখুঁত করার জন্য শুধুমাত্র receive_no দিয়ে গ্রুপ করা হলো)
        $receives = $query->select(
                                'receive_no',
                                \DB::raw('MAX(pi_id) as pi_id'),
                                \DB::raw('MAX(knit_booking_no) as knit_booking_no'),
                                \DB::raw('MAX(receive_date) as receive_date'),
                                \DB::raw('MAX(chalan_no) as chalan_no'),
                                \DB::raw('SUM(weight) as total_weight'),
                                \DB::raw('SUM(roll_qty) as total_rolls'),
                                \DB::raw('COUNT(id) as total_items_count'),
                                \DB::raw('MAX(created_at) as created_at')
                            )
                            ->groupBy('receive_no')
                            ->orderBy('created_at', 'DESC')
                            ->paginate(10);

        return view(adminTheme() . 'productions.knitting-receive.index', compact('receives'));
    }

    public function knittingReceiveAction(Request $r, $action, $id = null)
    {
        if ($action == 'create') {
            $pis = ProformaInvoice::whereHas('yarnBookings')->get();
            return view(adminTheme() . 'productions.knitting-receive.edit', ['pis' => $pis, 'receive' => null, 'items' => []]);
        }

        if ($action == 'update') {
            $receiveNo = KnittingReceive::max('receive_no') + 1;

            if ($r->receive_no) {
                // Delete old balance from booking before re-inserting
                $oldItems = KnittingReceive::where('receive_no', $r->receive_no)->get();
                foreach($oldItems as $old) {
                    KnittingBooking::where('id', $old->knit_id)->decrement('produced_qty', $old->weight);
                }
                KnittingReceive::where('receive_no', $r->receive_no)->delete();
            }

            foreach ($r->items as $item) {
                if ($item['weight'] > 0) {
                    KnittingReceive::create([
                        'receive_no' => $receiveNo,
                        'receive_date' => $r->receive_date,
                        'pi_id' => $r->pi_id,
                        'knit_booking_no' => $r->knit_booking_no,
                        'knit_id' => $item['knit_id'],
                        'roll_qty' => $item['roll_qty'] ?? 0,
                        'weight' => $item['weight'],
                        'chalan_no' => $r->chalan_no,
                        'created_by' => auth()->id(),
                    ]);

                    // Update knitting_bookings table balance
                    KnittingBooking::where('id', $item['knit_id'])->increment('produced_qty', $item['weight']);
                }
            }
            return redirect()->route('admin.knittingReceive')->with('success', 'Knitting Received Successfully');
        }

        if ($action == 'knit-booking-select') {
            $items = KnittingBooking::where('pi_id', $r->pi_id)->get();
            $html = view(adminTheme().'productions.knitting-receive.includes.items', compact('items'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'knit_booking_no' => $items->first()->booking_no ?? '',
                'knit_booking_no_show' => $items->first()->getBookingNo() ?? '',
            ]);
        }


        if ($action == 'edit' && $id) {
            // ১. রিসিভ ডাটা আনা
            $recvItems = KnittingReceive::where('receive_no', $id)->get();
            $receive = $recvItems->first();

            if (!$receive) return redirect()->back()->with('error', 'Receive record not found.');

            // ২. ওই PI এর সব নিটিং বুকিং আইটেম আনা
            $knitItems = KnittingBooking::where('pi_id', $receive->pi_id)->get();

            // ৩. ম্যাপ করা (কোন আইটেমে কতটুকু রিসিভ হয়েছে)
            foreach ($knitItems as $item) {
                $savedRow = $recvItems->where('knit_id', $item->id)->first();
                if ($savedRow) {
                    $item->current_roll_qty = $savedRow->roll_qty;
                    $item->current_weight = $savedRow->weight;
                }
            }

            $pis = ProformaInvoice::whereHas('yarnBookings')->get();
            return view(adminTheme() . 'productions.knitting-receive.edit', [
                'pis'     => $pis,
                'items'   => $knitItems,
                'receive' => $receive
            ]);
        }

        if ($action == 'delete' && $id) {
            // ১. ওই নির্দিষ্ট receive_no এর সব আইটেম খুঁজে বের করা
            $receives = KnittingReceive::where('receive_no', $id)->get();

            if ($receives->isEmpty()) {
                return redirect()->back()->with('error', 'Receive record not found.');
            }

            // ২. লুপ চালিয়ে প্রতিটি আইটেমের ওজন KnittingBooking টেবিল থেকে কমিয়ে দেওয়া
            foreach ($receives as $row) {
                // Produced Qty অ্যাডজাস্টমেন্ট
                KnittingBooking::where('id', $row->knit_id)->decrement('produced_qty', $row->weight);
            }

            // ৩. রিসিভ টেবিল থেকে এই চালানের সব ডাটা ডিলিট করা
            KnittingReceive::where('receive_no', $id)->delete();

            session()->flash('success', 'Knitting Receive Record Deleted Successfully and Booking Balance Updated.');
            return redirect()->route('admin.knittingReceive');
        }
    }

}

