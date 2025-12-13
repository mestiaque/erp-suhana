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
        $query = YarnBooking::with(['buyer', 'items', 'addedBy']);

        // ----------------------------
        // SEARCH (Buyer Name, Booking No, PI No, Fabrication)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;

            $query->where(function ($q) use ($search) {
                // Booking No or PI No
                $q->where('id', 'LIKE', '%' . ltrim($search, '0') . '%')
                ->orWhere('pi_no', 'LIKE', "%{$search}%")
                // Buyer Name
                ->orWhereHas('buyer', function ($b) use ($search) {
                    $b->where('name', 'LIKE', "%{$search}%");
                })
                // Fabrication in booking items
                ->orWhereHas('items', function ($i) use ($search) {
                    $i->where('fabrication', 'LIKE', "%{$search}%");
                });
            });
        }

        // ----------------------------
        // DATE RANGE FILTER
        // ----------------------------
        if ($r->startDate) {
            $query->whereDate('booking_date', '>=', $r->startDate);
        }

        if ($r->endDate) {
            $query->whereDate('booking_date', '<=', $r->endDate);
        }
        $query->whereNot('status', 'temp');
        // ----------------------------
        // FINAL DATA + PAGINATION
        // ----------------------------
        $list = $query->orderBy('id', 'DESC')->paginate(20);

        // Preserve filters in pagination links
        $list->appends($r->only(['search', 'startDate', 'endDate']));

        return view(adminTheme().'productions.yarn-booking.index', compact('list'));
    }

    public function yarnBookingAction(Request $r, $action, $id = null)
    {
        // -------------------------------
        // CREATE SAMPLE YARN BOOKING
        // -------------------------------
        if ($action == 'create') {

            // Find existing TEMP yarn booking for this user
            $booking = YarnBooking::where('status', 'temp')
                        ->where('addedby_id', Auth::id())
                        ->first();

            // If not exists, create new
            if (!$booking) {
                $booking              = new YarnBooking();
                $booking->status      = 'temp';
                $booking->addedby_id  = Auth::id();
            }

            // Set create time like PI
            $booking->created_at = now();
            $booking->save();

            // Redirect to edit page exactly like PI
            return redirect()->route('admin.yarnBookingAction', ['edit', $booking->id]);
        }

        if ($action == 'delivery-update') {
            $r->validate([
                'id'  => 'required|exists:yarn_item_receives,id',
                'qty' => 'required|numeric|min:0.01',
                'created_at' => 'required|date',
            ]);
            // Existing received record
            $receive = YarnItemReceive::findOrFail($r->id);
            // Corresponding booking item
            $bookingItem = YarnBookingItem::findOrFail($receive->yarn_booking_item_id);
            // Calculate qty difference
            $oldQty = $receive->delivery_qty;
            $newQty = $r->qty;
            $diff   = $newQty - $oldQty;
            // Update received_qty in booking item
            $bookingItem->increment('received_qty', $diff);
            // Update receive record
            $receive->update([
                'delivery_qty' => $newQty,
                'created_at'   => $r->created_at,
            ]);

            session()->flash('success', 'Yarn Received Updated Successfully');

            return redirect()->route('admin.yarnBookingAction', ['delivery', $bookingItem->yarn_booking_id]);
        }



        // -------------------------------
        // FIND YARN BOOKING
        // -------------------------------
        $booking = \App\Models\YarnBooking::find($id);

        if (!$booking && !in_array($action, ['create'])) {
            session()->flash('error', 'Yarn Booking Not Found');
            return redirect()->route('admin.yarnBooking'); // list page route
        }

        // -------------------------------
        // SHOW SINGLE YARN BOOKING
        // -------------------------------
        if ($action == 'show') {
            return view(adminTheme() . 'productions.yarn-booking.show', compact('booking'));
        }

        if ($action == 'delivery') {
            return view(adminTheme() . 'productions.yarn-booking.receive', compact('booking'));
        }

        if ($action == 'delivery-add') {

            $r->validate([
                'items.*.id'           => 'required|exists:yarn_booking_items,id',
                'items.*.delivery_qty'=> 'required|numeric|min:0.01',
            ]);

            foreach ($r->items as $itemData) {

                $bookingItem = YarnBookingItem::find($itemData['id']);

                if (!$bookingItem) {
                    continue;
                }

                YarnItemReceive::create([
                    'yarn_booking_id'       => $booking->id,
                    'yarn_booking_item_id'  => $bookingItem->id,
                    'fabrication'           => $bookingItem->fabrication,
                    'yarn_count'            => $bookingItem->yarn_count,
                    'delivery_qty'          => $itemData['delivery_qty'],
                    'addedby_id'            => Auth::id(),
                ]);

                $bookingItem->increment(
                    'received_qty',
                    $itemData['delivery_qty']
                );
            }
            session()->flash('success', 'Yarn Received Successfully');


            return redirect()->route('admin.yarnBookingAction', ['delivery', $booking->id]);
        }


        // -------------------------------
        // EDIT PAGE
        // -------------------------------
        if ($action == 'edit') {
            $pis = ProformaInvoice::whereNotNull('pi_no')->get();
            $booking = YarnBooking::find($id);
            return view(adminTheme() . 'productions.yarn-booking.edit', compact('booking', 'pis'));
        }

        // -------------------------------
        // UPDATE YARN BOOKING
        // -------------------------------
        if ($action == 'update') {
            $r->validate([
                'pi_no' => 'required',
                'items.*.yarn_count' => 'required|string',
                'items.*.requisition_qty' => 'required|numeric|min:0',
            ]);

            $pi = ProformaInvoice::where('pi_no', $r->pi_no)->first();

            // Update main booking
            $booking->update([
                'proforma_invoice_id' => $pi->id,
                'pi_no'               => $pi->pi_no,
                'buyer_id'            => $pi->buyer_id,
                'buyer_name'          => $pi->buyer_name,
                'status'              => $r->status,
            ]);

            // Handle items
            if (count($booking->items) > 0) {
                foreach ($r->items as $itemData) {
                // Update existing item
                    $item = $booking->items()->find($itemData['id']);
                    if ($item) {
                        $item->update([
                            'yarn_count'      => $itemData['yarn_count'],
                            'requisition_qty' => $itemData['requisition_qty'],
                        ]);
                    }
                }
            } else {
                foreach ($r->items as $itemData) {
                    $piItem  = ProformaInvoiceItem::find($itemData['id']);
                    $newItem = $booking->items()->create([
                        'yarn_booking_id'          => $booking->id,
                        'proforma_invoice_id'      => $booking->proforma_invoice_id,
                        'proforma_invoice_item_id' => $piItem->id,
                        'fabrication' => $piItem->fabrication,
                        'yarn_count'               => $itemData['yarn_count'],
                        'requisition_qty'          => $itemData['requisition_qty'],
                    ]);
                }
                }

            session()->flash('success', 'Yarn Booking Updated Successfully');
            return redirect()->route('admin.yarnBooking');
        }



        // ->makeHidden(['id'])
        if ($action == 'pi-select') {
            $pi = ProformaInvoice::where('pi_no', $r->pi_no)->first();
            $items = $pi->items;
            if (count($items) < 1) {
                return response()->json([
                    'success' => false,
                    'html'    => '<tr><td colspan="4" class="text-center">Order not found</td></tr>'
                ]);
            }

                    // Render items partial
            $html = view(adminTheme().'productions.yarn-booking.includes.items', [
                'pi' => $pi,
                'items' => $items
            ])->render();

            return response()->json(['success' => true, 'html' => $html, 'order' => $pi]);
        }

        // -------------------------------
        // DELETE YARN BOOKING
        // -------------------------------
        if ($action == 'delete') {
            $booking->items()->delete();
            $booking->delete();
            session()->flash('success', 'Yarn Booking Deleted Successfully');
            return redirect()->route('admin.yarnBooking');
        }

        // -------------------------------
        // LIST ALL YARN BOOKINGS
        // -------------------------------
        $bookings = \App\Models\YarnBooking::with(['proformaInvoice', 'item'])->get();
        return view(adminTheme() . 'productions.yarn-booking.index', compact('bookings'));
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

