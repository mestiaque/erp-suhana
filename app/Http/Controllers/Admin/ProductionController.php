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
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\YarnBooking;
use Illuminate\Support\Str;
use App\Models\OrderDetails;
use App\Models\SewingOutput;
use Illuminate\Http\Request;
use App\Models\DyeingBooking;
use App\Models\KnittingBooking;
use App\Models\ProformaInvoice;
use App\Models\YarnBookingItem;
use App\Models\YarnItemReceive;
use App\Models\ProductionSewing;
use App\Models\ProductionPlanning;
use Illuminate\Support\Facades\DB;
use App\Models\ProformaInvoiceItem;
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

        // return $plan->sewingLines;
        $productionStyleNos = ProductionPlanning::pluck('style_no')
            ->filter() // removes null values
            ->map(fn($val) => trim($val)) // removes extra spaces
            ->toArray();

        $styles = OrderDetail::where('status', 'confirmed')
            ->whereNotIn('style_no', $productionStyleNos)
            ->orderBy('id', 'desc')
            ->get();


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

    public function dailyProduction(Request $r)
    {
        $search = $r->search;
        $now = now(); // current timestamp
        $nextHour = $now->copy()->addHour(); // now + 1 hour

        $swings = ProductionSewing::with(['planning', 'planning.style', 'outputs'])
            ->when($search, function ($q) use ($search) {
                $q->where('floor_name', 'LIKE', "%{$search}%")
                ->orWhere('line_name', 'LIKE', "%{$search}%")
                ->orWhereHas('planning', function ($q) use ($search) {
                    $q->where('style_no', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('planning.style', function ($q) use ($search) {
                    $q->where('order_no', 'LIKE', "%{$search}%")
                        ->orWhere('buyer_name', 'LIKE', "%{$search}%");
                });
            })
            ->when($r->startDate || $r->endDate, function ($q) use ($r) {
                $from = $r->startDate ?: now()->format('Y-m-d');
                $to   = $r->endDate ?: now()->format('Y-m-d');

                // Apply date filter on outputs
                $q->whereHas('outputs', function ($q) use ($from, $to) {
                    $q->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
                });
            })
            ->whereHas('planning', function ($q) use ($now, $nextHour) {
                // Filter by exact datetime instead of date only
                $q->where('status', 'confirmed')
                ->where('sewing_start', '<=', $now)
                ->where('sewing_end', '>=', $nextHour);
            })
            ->get();

        return view(adminTheme().'productions.daily.index', compact('swings'));
    }

    public function dailyProductionAction(Request $r, $action)
    {
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

        $swings = ProductionSewing::with('planning')->with('outputs')->get();

        return view(adminTheme().'productions.daily.index', compact('swings'));
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

    public function knittingBooking(Request $r)
    {
        $query = KnittingBooking::query();

        // ----------------------------
        // SEARCH (Buyer Name, Booking No, PI No, Fabrication)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_no', 'like', "%$search%")
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
        return view(adminTheme() . 'productions.knitting-booking.index', compact('bookings'));
    }

    public function knittingBookingAction(Request $r, $action, $id = null)
    {
        // -------------------------------
        // CREATE SAMPLE YARN BOOKING
        // -------------------------------
        if ($action == 'create') {
            $pis = ProformaInvoice::whereNotNull('pi_no')->get();
            return view(adminTheme() . 'productions.knitting-booking.edit', [
                'pis' => $pis,
                'items' => [],
                'booking' => null
            ]);
        }

        // -------------------------------
        // EDIT PAGE
        // -------------------------------
        if ($action == 'edit' && $id) {
            $items = KnittingBooking::where('booking_no', $id)->get();
            $booking = $items->first();
            $pis = ProformaInvoice::whereNotNull('pi_no')->get();
            return view(adminTheme() . 'productions.knitting-booking.edit', compact('pis', 'items', 'booking'));
        }

        // -------------------------------
        // UPDATE / STORE YARN BOOKING
        // -------------------------------
        if ($action == 'update') {
            $r->validate([
                'pi_id'                   => 'required|exists:proforma_invoices,id',
                'items.*.fabrication'     => 'required|string',
                'items.*.style_no'        => 'required|string',
                'items.*.requisition_qty' => 'required|numeric',
                'status'                  => 'required|string'
            ]);

            $pi = ProformaInvoice::findOrFail($r->pi_id);

            // Determine booking_no
            $bookingNo = $r->booking_no ?? (KnittingBooking::max('booking_no') + 1);

            // Delete old rows if editing
            if ($r->booking_no) {
                KnittingBooking::where('booking_no', $r->booking_no)->delete();
            }

            // Save each item
            foreach ($r->items as $item) {

                // Use updateOrCreate to update existing rows if id exists, else create new
                KnittingBooking::updateOrCreate(
                    [
                        'id' => $item['id'] ?? null  // send item id in form if editing
                    ],
                    [
                        'pi_id'         => $pi->id,
                        'booking_no'    => $bookingNo,
                        'buyer_name'    => $pi->buyer_name ?? null,
                        'fabric_type'   => $item['fabrication'],
                        'style'         => $item['style_no'],
                        'required_qty'  => $item['requisition_qty'],
                        'status'        => $r->status,
                        'supplier'      => $r->supplier,
                        'created_by'    => auth()->id(),
                    ]
                );
            }


            session()->flash('success', 'Knitting Booking Saved Successfully');
            return redirect()->route('admin.knittingBooking');
        }

        // -------------------------------
        // DELETE
        // -------------------------------
        if ($action == 'delete') {
            KnittingBooking::where('booking_no', $id)->delete();
            session()->flash('success', 'Knitting Booking Deleted Successfully');
            return redirect()->route('admin.knittingBooking');
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
            $html = view(adminTheme().'productions.knitting-booking.includes.items', [
                'pi' => $pi,
                'items' => $pi->items
            ])->render();

            return response()->json(['success' => true, 'html' => $html, 'order' => $pi]);
        }

        // -------------------------------
        // LIST ALL YARN BOOKINGS
        // -------------------------------
        $bookings = knittingBooking::all();
        return view(adminTheme() . 'productions.knitting-booking.index', compact('bookings'));
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
        return view(adminTheme() . 'productions.yarn-receive.index');

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

        return view(adminTheme() . 'productions.yarn-receive.index', compact('bookings'));
    }

    public function knittingReceive(Request $r)
    {
        return view(adminTheme() . 'productions.knitting-receive.index');

        $query = KnittingBooking::query();

        // ----------------------------
        // SEARCH (Buyer Name, Booking No, PI No, Fabrication)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_no', 'like', "%$search%")
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

        return view(adminTheme() . 'productions.knitting-receive.index', compact('bookings'));
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

}

