<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Cutting;
use App\Models\Finishing;
use App\Models\Iron;
use App\Models\Poly;
use App\Models\Attribute;
use App\Models\OrderDetail;
use App\Models\YarnBooking;
use App\Models\YarnReceive;
use App\Models\SewingOutput;
use Illuminate\Http\Request;
use App\Models\DyeingBooking;
use App\Models\DyeingReceive;
use App\Models\MasterPlanning;
use App\Models\KnittingBooking;
use App\Models\KnittingReceive;
use App\Models\ProformaInvoice;
use App\Models\ProductionSewing;
use App\Models\ProductionPlanning;
use Illuminate\Support\Facades\DB;
use App\Models\ProformaInvoiceItem;
use App\Http\Controllers\Controller;
use App\Models\OrderDetailItem;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function productionPlanning(Request $r) // master planning
    {
        // ================= INDEX =================
        // Show ProductionPlanning records with production metrics
        $month = $r->month ?? now()->format('Y-m');

        $productions = ProductionPlanning::with(['masterPlan', 'style'])
            ->where('planning_month', 'LIKE', "%{$month}%")
            ->when($r->pi_no, fn($q) => $q->where('pi_no', $r->pi_no))
            ->when($r->order_no, fn($q) => $q->where('order_no', $r->order_no))
            ->when($r->style_no, fn($q) => $q->where('style_no', 'LIKE', "%{$r->style_no}%"))
            ->when($r->search, function($q) use($r) {
                $search = $r->search;
                $q->where(function($qq) use($search) {
                    $qq->where('pi_no', 'LIKE', "%{$search}%")
                    ->orWhere('order_no', 'LIKE', "%{$search}%")
                    ->orWhere('style_no', 'LIKE', "%{$search}%")
                    ->orWhereHas('style', function($qqq) use($search) {
                        $qqq->where('buyer_name', 'LIKE', "%{$search}%")
                            ->orWhere('merchant_name', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->when($r->buyer, function($q) use($r) {
                $q->whereHas('style', function($qq) use($r) {
                    $qq->where('buyer_name', 'LIKE', "%{$r->buyer}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->appends($r->all());

        // ================= FILTER OPTIONS =================
        $piNos = ProductionPlanning::distinct()->pluck('pi_no')->filter()->sort()->values();
        $orderNos = ProductionPlanning::distinct()->pluck('order_no')->filter()->sort()->values();
        $styleNos = ProductionPlanning::distinct()->pluck('style_no')->filter()->sort()->values();
        $buyers = OrderDetail::whereNotNull('buyer_name')->distinct()->pluck('buyer_name')->filter()->sort()->values();

        // ================= TOTALS =================
        $totalsQuery = ProductionPlanning::where('planning_month', 'LIKE', '%'.$r->month.'%');

        if ($r->status) {
            $totalsQuery->where('status', $r->status);
        }

        $totals = $totalsQuery
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("COUNT(CASE WHEN status='pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN status='confirmed' THEN 1 END) AS confirmed")
            ->selectRaw("COUNT(CASE WHEN status='approved' THEN 1 END) AS approved")
            ->selectRaw("COUNT(CASE WHEN status='cancelled' THEN 1 END) AS cancelled")
            ->first();

        // Check if print mode
        if ($r->print == 1) {
            return view(adminTheme().'productions.planning.print-index', compact('productions', 'totals', 'piNos', 'orderNos', 'styleNos', 'buyers', 'month'));
        }

        return view(adminTheme().'productions.planning.index', compact('productions', 'totals', 'piNos', 'orderNos', 'styleNos', 'buyers', 'month'));
    }

    public function productionPlanningAction(Request $r, $action, $id = null) // master planning action
    {
        try{

            // ১️⃣ Step: Already planned colors
            $plannedColors = ProductionPlanning::query()
                ->select('pi_item_id','style_no','order_no','color_name')
                ->get()
                ->map(function($p){
                    return $p->pi_item_id.'__'.$p->style_no.'__'.$p->order_no.'__'.($p->color_name ?? '');
                })
                ->toArray();

            // ২️⃣ Step: All order detail items
            $allColors = OrderDetailItem::orderBy('id','desc')->get();

            // ৩️⃣ Step: Filter available colors
            $colors = $allColors->filter(function($c) use ($plannedColors){
                // pi_item_id relation দিয়ে বের করা
                $pi_item_id = $c?->orderDetail?->piItem?->id ?? null;

                // Key বানানো plannedColors-এর সাথে মিলানোর জন্য
                $key = $pi_item_id.'__'.$c->style_no.'__'.$c->order_no.'__'.$c->color_name;

                // যদি plannedColors এ না থাকে, keep
                return !in_array($key, $plannedColors);
            });

            // ================= CREATE FORM =================
            if($action == 'create') {

                return view(adminTheme().'productions.planning.edit', [
                    'method' => 'store',
                    'colors' => $colors,
                    'planning_month' => $r->planning_month ?? now()->format('Y-m')
                ]);
            }

            // ================= STORE =================
            if($action == 'store') {
                $r->validate([
                    'styles' => 'required|array|min:1',
                    'planning_month' => 'required',
                ]);
                $months = $r->planning_month;
                if (is_string($months)) {
                    $months = json_decode($months, true);
                }

                $masterPlan = MasterPlanning::create([
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                    'planning_month' => $months,
                    'planning_no' => $pn = Carbon::now()->format('ym') .
                                    str_pad(((int)substr(MasterPlanning::whereYear('created_at', now()->year)
                                    ->whereMonth('created_at', now()->month)->max('planning_no') ?? 0, -4) + 1), 4, '0', STR_PAD_LEFT),
                ]);

                foreach($r->styles as $styleNo) {
                    $piItem = ProformaInvoiceItem::find($styleNo['pi_item_id']);
                    // If colors are provided, create separate planning for each color
                    if(isset($styleNo['colors']) && is_array($styleNo['colors'])) {
                        foreach($styleNo['colors'] as $color) {
                            // Create planning for each selected month
                            ProductionPlanning::create([
                                'master_plan_id' => $masterPlan->id,
                                'style_no'       => $styleNo['style_no'],
                                'pi_id'          => $piItem?->proforma_invoice_id ?? null,
                                'pi_item_id'     => $piItem?->id,
                                'pi_no'          => $piItem?->pi?->pi_no,
                                'order_no'       => $styleNo['order_no'],
                                'style_no'       => $styleNo['style_no'],
                                'style_qty'      => $piItem->order_qty ?? 0,
                                'color_name'     => $color['color_name'] ?? null,
                                'color_qty'      => $color['color_qty'] ?? 0,
                                'status'         => 'pending',
                                'planning_month' => $months,
                            ]);
                        }
                    } else {
                       throw new Exception('No colors provided for style: '.$styleNo['style_no']);
                    }
                }
                session()->flash('success','Master Planning Created');
                return redirect()->route('admin.productionPlanning');
            }

            // ================= FETCH MASTER PLAN =================
            $masterPlan = MasterPlanning::with('productions.style')->find($id);

            if(!$masterPlan){
                session()->flash('error','Master Plan Not Found');
                return redirect()->route('admin.productionPlanning');
            }

            // ================= VIEW =================
            if($action=='view'){
                return view(adminTheme().'productions.planning.view', compact('masterPlan'));
            }

            // ================= UPDATE =================
            if($action=='update') {
                $r->validate([
                    'styles' => 'required|array|min:1',
                    'planning_month' => 'required',
                ]);

                $months = $r->planning_month;
                if (is_string($months)) {
                    $months = json_decode($months, true);
                }

                // Update Master Plan
                $masterPlan = MasterPlanning::findOrFail($id);
                $masterPlan->update([
                    'planning_month' => $months,
                    'updated_by'     => auth()->id(),
                ]);

                // ১️⃣ Collect all current pi_item_id + color_name combination submitted by user
                $submittedKeys = [];
                foreach ($r->styles as $styleNo) {
                    $piItemId = $styleNo['pi_item_id'];
                    if(isset($styleNo['colors']) && is_array($styleNo['colors'])) {
                        foreach($styleNo['colors'] as $color) {
                            $submittedKeys[] = $piItemId.'__'.($color['color_name'] ?? '');
                        }
                    }
                }

                // ২️⃣ Delete removed entries
                ProductionPlanning::where('master_plan_id', $masterPlan->id)
                    ->whereNotIn(\DB::raw("CONCAT(pi_item_id,'__',color_name)"), $submittedKeys)
                    ->delete();

                // ৩️⃣ Add or update entries
                foreach ($r->styles as $styleNo) {
                    $piItem = ProformaInvoiceItem::find($styleNo['pi_item_id']);
                    if(isset($styleNo['colors']) && is_array($styleNo['colors'])) {
                        foreach($styleNo['colors'] as $color) {
                            $existing = ProductionPlanning::where('master_plan_id', $masterPlan->id)
                                ->where('pi_item_id', $piItem?->id)
                                ->where('color_name', $color['color_name'] ?? null)
                                ->first();

                            if($existing) {
                                // Update existing entry
                                $existing->update([
                                    'style_qty'      => $piItem->order_qty ?? 0,
                                    'color_qty'      => $color['color_qty'] ?? 0,
                                    'planning_month' => $months,
                                    'status'         => 'pending',
                                ]);
                            } else {
                                // Create new entry
                                ProductionPlanning::create([
                                    'master_plan_id' => $masterPlan->id,
                                    'style_no'       => $styleNo['style_no'],
                                    'pi_id'          => $piItem?->proforma_invoice_id ?? null,
                                    'pi_item_id'     => $piItem?->id,
                                    'pi_no'          => $piItem?->pi?->pi_no,
                                    'order_no'       => $styleNo['order_no'],
                                    'style_qty'      => $piItem->order_qty ?? 0,
                                    'color_name'     => $color['color_name'] ?? null,
                                    'color_qty'      => $color['color_qty'] ?? 0,
                                    'status'         => 'pending',
                                    'planning_month' => $months,
                                ]);
                            }
                        }
                    } else {
                        throw new \Exception('No colors provided for style: '.$styleNo['style_no']);
                    }
                }

                session()->flash('success','Master Planning Updated');
                return redirect()->route('admin.productionPlanning');
            }

            // ================= DELETE =================
            if ($action == 'delete') {
                DB::transaction(function () use ($masterPlan) {
                    $productionPlannings = ProductionPlanning::where('master_plan_id', $masterPlan->id)->get();
                    foreach ($productionPlannings as $pp) {
                        SewingOutput::where('planning_id', $pp->id)->delete();
                        ProductionSewing::where('planning_id', $pp->id)->delete();
                        $pp->delete();
                    }
                    $masterPlan->delete();
                });

                session()->flash('success', 'Master Plan and all related data deleted successfully');
                return redirect()->route('admin.productionPlanning');
            }


            // ================= PRINT =================
            if($action=='approve'){
                $masterPlan->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => Auth::id()
                ]);
                foreach($masterPlan->productions as $production){
                    $production->update(['status' => 'approved']);
                }
                session()->flash('success','Master Planning Approved');
                return redirect()->route('admin.productionPlanning');
            }

            // ================= EDIT =================

            return view(adminTheme().'productions.planning.edit', compact('masterPlan','colors'));
            } catch(Exception $e){
                dd($e);
                session()->flash('error', $e->getMessage());
                return redirect()->route('admin.productionPlanning');
            }

    }

    public function floorPlanning(Request $r) // floor planning
    {
        // মাস ফিল্টার
        $month = $r->month ?? now()->format('Y-m');
        // dd($month);

        // Get all active floor lines
        $floorLines = Attribute::where('type', 4)->where('status', 'active')->orderBy('slug')->get();

        // মাস অনুযায়ী প্ল্যান ফিল্টার
        $plans = ProductionPlanning::with(['style', 'sewingLines'])
            ->whereHas('masterPlan', function($q) {
                $q->where('status', 'approved');
            })
            ->when($r->search, function($q) use($r) {
                $search = $r->search;
                $q->where(function($qq) use($search) {
                    $qq->where('pi_no', 'LIKE', "%{$search}%")
                    ->orWhere('order_no', 'LIKE', "%{$search}%")
                    ->orWhere('style_no', 'LIKE', "%{$search}%")
                    ->orWhereHas('style', function($qqq) use($search) {
                        $qqq->where('buyer_name', 'LIKE', "%{$search}%")
                            ->orWhere('merchant_name', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->when($r->line, function($q) use($r) {
                $line = $r->line;
                $q->whereHas('sewingLines', function($qq) use($line) {
                    $qq->where('line_name', $line);
                });
            })
            ->when($r->buyer, function($q) use($r) {
                $q->whereHas('style', function($qq) use($r) {
                    $qq->where('buyer_name', $r->buyer);
                });
            })
            ->when($r->style_no, function($q) use($r) {
                $q->where('style_no', $r->style_no);
            })
            ->when($r->order_no, function($q) use($r) {
                $q->where('order_no', $r->order_no);
            })
            ->where('status', 'approved')
            ->orWhere('status', 'confirmed')
            ->where('planning_month', 'like', "%{$month}%")
            ->orderBy('style_no')
            ->get();
            // dd(ProductionPlanning::get());

        // Filter options
        $lineOptions = Attribute::where('type', 4)->where('status', 'active')->orderBy('name')->get();
        $buyers = ProductionPlanning::with('style')->get()->pluck('style.buyer_name')->filter()->unique()->sort()->values();
        $styleNos = ProductionPlanning::distinct()->pluck('style_no')->filter()->sort()->values();
        $orderNos = ProductionPlanning::distinct()->pluck('order_no')->filter()->sort()->values();

        // Get filter options
        $lineOptions = Attribute::where('type', 4)->where('status', 'active')->orderBy('name')->get();
        $buyers = ProductionPlanning::whereHas('masterPlan', function($q) { $q->where('status', 'approved'); })
            ->with('style')
            ->get()
            ->pluck('style.buyer_name')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Check if print mode
        if ($r->print == 1) {
            return view(adminTheme().'productions.floor-planning.print-index', compact('plans', 'floorLines', 'lineOptions', 'buyers', 'styleNos', 'orderNos', 'month'));
        }

        return view(adminTheme().'productions.floor-planning.index', compact('plans', 'floorLines', 'lineOptions', 'buyers', 'styleNos', 'orderNos', 'month'));
    }

    public function floorPlanningAction(Request $r, $action, $id = null) // floor planning action
    {
        // ================= GET Productions for Create (Same format as edit) =================
        if($action == 'get-productions'){
            $masterPlan = MasterPlanning::with(['productions.sewingLines', 'productions.style'])->find($id);

            if(!$masterPlan) return '<tr><td colspan="20">Master Plan not found</td></tr>';

            $allLines = Attribute::where('type',4)->where('status','active')->orderBy('slug')->get();
            $productions = $masterPlan->productions;

            $html = '';

            foreach($productions as $plan){
                $html .= '<tr data-planid="'.$plan->id.'">';
                $html .= '<td>'.$plan->style_no.'</td>';
                $html .= '<td>'.($plan->style?->buyer_name ?? '--').'</td>';
                $html .= '<td>'.$plan->order_no.'</td>';
                $html .= '<td>'.($plan->color_name ?? '--').'</td>';
                $html .= '<td><input type="hidden" class="style_qty" value="'.($plan->color_qty ?? $plan->style_qty ?? 0).'">'.number_format($plan->color_qty ?? $plan->style_qty ?? 0).'</td>';
                $html .= '<td>'.($plan->style?->merchant_name ?? '--').'</td>';

                // Line columns
                foreach($allLines as $line){
                    $exSew = $plan->sewingLines->where('line_name',$line->slug)->first();
                    $html .= '<td>';
                    $html .= '<div class="d-flex flex-column">';
                    $html .= '<label><input type="checkbox" name="plans['.$plan->id.'][floor][]" value="'.$line->slug.'" class="lineCheckbox form-control form-control-sm" '.($exSew ? 'checked' : '').'></label>';
                    $html .= '<label style="font-size:0.8rem;" class="mb-0">Capacity</label>';
                    $html .= '<input type="number" name="plans['.$plan->id.'][capacity]['.$line->slug.']" value="'.($exSew->capacity_hour ?? $line->capacity ?? 0).'" class="lineCapacity mb-2 form-control form-control-sm">';
                    $html .= '<label style="font-size:0.8rem;" class="mb-0">Hours</label>';
                    $html .= '<input type="number" name="plans['.$plan->id.'][hours]['.$line->slug.']" value="'.($exSew->working_hours ?? 8).'" class="lineHours form-control form-control-sm">';
                    $html .= '<label style="font-size:0.8rem;" class="mb-0">Alloc. Qty</label>';
                    $html .= '<input type="number" name="plans['.$plan->id.'][allocation_qty]['.$line->slug.']" value="'.($exSew->allocation_qty ?? 0).'" class="allocationQty form-control form-control-sm">';
                    $html .= '</div>';
                    $html .= '</td>';
                }

                $html .= '<td>';
                $html .= '<input type="datetime-local" class="form-control form-control-sm updateDate sewingStarDate" value="'.($plan->sewing_start ? Carbon::parse($plan->sewing_start)->format('Y-m-d\TH:i') : '').'" data-name="sewing_start">';
                $html .= '</td>';
                $html .= '<td>';
                $html .= '<input type="datetime-local" readonly name="plans['.$plan->id.'][sewing_end]" class="form-control form-control-sm updateDate sewingEndDate" value="'.($plan->sewing_end ? Carbon::parse($plan->sewing_end)->format('Y-m-d\TH:i') : '').'" data-name="sewing_end">';
                $html .= '</td>';
                $html .= '<td class="totalTime"></td>';
                $html .= '<td class="hourTarget"></td>';
                $html .= '<td>';
                $html .= '<input type="number" name="plans['.$plan->id.'][extra_time]" class="extraTime form-control form-control-sm" value="'.($plan->extra_time ?? 0).'">';
                $html .= '</td>';
                $html .= '</tr>';
            }

            return $html;
        }

        // ================= CREATE =================
        if($action == 'create'){
            // Get approved master plans that need floor planning
            $masterPlans = MasterPlanning::with('productions.style', 'creator')
                ->where('status', 'approved')
                ->orWhere('status', 'confirmed')
                ->latest()
                ->get();

            return view(adminTheme().'productions.floor-planning.create', compact('masterPlans'));
        }

        if($action=='edit'){
            $masterPlan = MasterPlanning::find($id);
            $plans =ProductionPlanning::where('master_plan_id', $id)->get();
            return view(adminTheme().'productions.floor-planning.edit', compact('plans', 'masterPlan'));
        }

        if($action=='view'){
            $masterPlan = MasterPlanning::find($id);
            return view(adminTheme().'productions.floor-planning.view',compact('masterPlan'));
        }

        if($action=='date-update'){
            $plan = ProductionPlanning::find($id);
            $fields = [
                'sewing_start',
                'sewing_end',
            ];

            if (in_array($r->dataName, $fields)){
                $plan->{$r->dataName} = $r->dataValue; // Dynamic property
                $plan->save();
            }

            return response()->json(['success'=>true,'view'=>'']);
        }

        if ($action == 'update') {
            $masterPlan = MasterPlanning::find($id);
            // Update planning_month if provided
            if ($r->filled('planning_month')) {
                $masterPlan->planning_month = $r->planning_month;
                $masterPlan->save();
            }
            foreach($r->plans as $planId => $data){
                $plan = $masterPlan->productions->find($planId);
                if (!$plan) continue;

                // Plan-specific extra time
                $plan->extra_time = $data['extra_time'] ?? 0;
                $plan->sewing_end = $data['sewing_end'] ?? null;
                $plan->status     = 'confirmed';
                // Update planning_month for each plan
                if ($r->filled('planning_month')) {
                    $plan->planning_month = $r->planning_month;
                }
                $plan->save();

                /* ---------------------------
                FLOOR DATA COLLECTION
                ----------------------------*/
                $selectedFloorData = collect($data['floor'] ?? [])->map(function($floorSlug) use ($data) {
                    return [
                        'line'     => $floorSlug,
                        'capacity' => $data['capacity'][$floorSlug] ?? 0,
                        'whours'   => $data['hours'][$floorSlug] ?? 0,
                        'allocation_qty' => $data['allocation_qty'][$floorSlug] ?? 0,
                    ];
                });

                /* ---------------------------
                DELETE REMOVED FLOORS
                ----------------------------*/
                $existingFloors = $plan->sewingLines()->pluck('line_name')->toArray();
                $newFloors      = $selectedFloorData->pluck('line')->toArray();
                $toDelete       = array_diff($existingFloors, $newFloors);

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

                    $line = $plan->sewingLines()->where('line_name', $floorLine->slug)->first();
                    if (!$line) {
                        $line = new ProductionSewing();
                        $line->planning_id = $plan->id;
                        $line->floor_name  = $floorLine->name;
                        $line->line_name   = $floorLine->slug;
                    }

                    $line->style_no      = $plan->style_no;
                    $line->color_name    = $plan->color_name;
                    $line->capacity_hour = intval($floor['capacity']);
                    $line->working_hours = intval($floor['whours']);
                    $line->allocation_qty = intval($floor['allocation_qty']);
                    $line->save();
                }

                /* ---------------------------
                TOTAL HOURLY CAPACITY & WORKING TIME
                ----------------------------*/
                $plan->total_hourly_capacity = $plan->sewingLines()->sum('capacity_hour');

                $totalMinutes = $selectedFloorData->sum(function($floor){
                    return intval($floor['whours']) * 60;
                });

                $totalMinutes += intval($plan->extra_time);

                $hours   = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;

                $plan->total_working_time = "{$hours}h - {$minutes}m";
                $plan->save();
            }

            session()->flash('success', 'Floor Planning Updated');
            return redirect()->route('admin.floorPlanning');
        }

        if ($action == 'delete') {
            $plan = Production::find($id); // or $masterPlan->productions()->find($id)
            if (!$plan) {
                session()->flash('error', 'Production Plan not found');
                return redirect()->route('admin.floorPlanning');
            }

            // Delete related sewing lines first
            $plan->sewingLines()->delete();

            // Delete the production plan
            $plan->delete();

            session()->flash('success', 'Production Plan Deleted');
            return redirect()->route('admin.floorPlanning');
        }

        if($action == 'print'){
            $masterPlan = MasterPlanning::find($id);
            if (!$masterPlan) {
                session()->flash('error', 'Plan Not Found');
                return redirect()->route('admin.floorPlanning');
            }
            // Prepare summary data if needed
            // $totalCapacity = $plan->sewingLines->sum('capacity_hour');
            // $totalWorkingHours = $plan->sewingLines->sum('working_hours');
            $totalCapacity = 0;
            $totalWorkingHours = 0;

            return view(adminTheme().'productions.floor-planning.print', compact('masterPlan', 'totalCapacity', 'totalWorkingHours'));
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
                    ->select('id', 'style_no', 'color_name')
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
                    'color_name'    => $swing->color_name,
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

        // Update SMB, Operators, Helpers
        if ($action == "update-manpower") {
            $swing = ProductionSewing::findOrFail($r->swing_id);
            $field = $r->field;
            $value = $r->value;

            if (in_array($field, ['smb', 'operators', 'helpers'])) {
                $swing->update([$field => $value]);
            }

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
                ->orWhere('order_no', 'LIKE', "%{$r->search}%")
                ->orWhere('style_no', 'LIKE', "%{$r->search}%")
                ->orWhere('color_name', 'LIKE', "%{$r->search}%");
            });
        }

        $cuttings = $query->latest('cutting_date')->paginate(20);
        $pis = ProformaInvoice::whereNotNull('pi_no')->get();
        return view('admin.productions.cutting.index', compact('cuttings', 'pis'));
    }

    public function cuttingAction(Request $r, $action)
    {

        if ($action == 'get-orders') {
            // Get orders based on PI
            $orders = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id)
                    ->select('order_no', DB::raw('SUM(order_qty) as total_order_qty'))
                    ->groupBy('order_no')
                    ->get();

            return response()->json($orders);
        }

        if ($action == 'get-styles') {
            // Get styles based on PI and Order
            $styles = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id)
                    ->where('order_no', $r->order_no)
                    ->select('style_no', DB::raw('SUM(order_qty) as total_style_qty'))
                    ->groupBy('style_no')
                    ->get();

            return response()->json($styles);
        }

        if ($action == 'get-colors') {
            $colors = OrderDetailItem::where('order_no', $r->order_no)
                    ->where('style_no', $r->style_no)
                    ->select('color_name', DB::raw('SUM(qty) as total_color_qty'))
                    ->groupBy('color_name')
                    ->get();

            return response()->json($colors);
        }

        if($action == 'create'){
            $pi = ProformaInvoice::find($r->pi_no);
            Cutting::create([
                'pi_id'         => $pi->id,
                'pi_no'         => $pi->pi_no,
                'order_no'      => $r->order_no,
                'style_no'      => $r->style_no,
                'color_name'    => $r->color_name,
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
                'order_no'      => $r->order_no,
                'color_name'    => $r->color_name,
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

    // ================== FINISHING ==================
    public function finishing(Request $r)
    {
        $query = Finishing::query();

        if ($r->startDate && $r->endDate) {
            $query->whereBetween('finishing_date', [$r->startDate, $r->endDate]);
        }

        if ($r->search) {
            $query->where(function($q) use ($r) {
                $q->where('pi_no', 'LIKE', "%{$r->search}%")
                ->orWhere('order_no', 'LIKE', "%{$r->search}%")
                ->orWhere('style_no', 'LIKE', "%{$r->search}%")
                ->orWhere('color_name', 'LIKE', "%{$r->search}%");
            });
        }

        $finishings = $query->latest('finishing_date')->paginate(20);
        $pis = ProformaInvoice::whereNotNull('pi_no')->get();

        return view('admin.productions.finishing.index', compact('finishings', 'pis'));
    }

    public function finishingAction(Request $r, $action)
    {
        if ($action == 'get-orders') {
            $orders = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id)
                    ->select('order_no', DB::raw('SUM(order_qty) as total_order_qty'))
                    ->groupBy('order_no')
                    ->get();
            return response()->json($orders);
        }

        if ($action == 'get-styles') {
            $styles = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id)
                    ->where('order_no', $r->order_no)
                    ->select('style_no', DB::raw('SUM(order_qty) as total_style_qty'))
                    ->groupBy('style_no')
                    ->get();
            return response()->json($styles);
        }

        if ($action == 'get-colors') {
            $colors = OrderDetailItem::where('order_no', $r->order_no)
                    ->where('style_no', $r->style_no)
                    ->select('color_name', DB::raw('SUM(qty) as total_color_qty'))
                    ->groupBy('color_name')
                    ->get();

            return response()->json($colors);
        }

        if($action == 'create'){
            $pi = ProformaInvoice::find($r->pi_no);
            Finishing::create([
                'pi_id'         => $pi->id,
                'pi_no'         => $pi->pi_no,
                'order_no'      => $r->order_no,
                'style_no'      => $r->style_no,
                'color_name'    => $r->color_name,
                'finishing_qty' => $r->finishing_qty,
                'finishing_date'=> $r->finishing_date,
                'remarks'       => $r->remarks,
                'created_by'    => auth()->id(),
            ]);
            return redirect()->back()->with('success', 'Finishing Record Added Successfully');
        }

        if ($action == 'update') {
            $fin = Finishing::findorFail($r->id);
            $fin->update([
                'order_no'      => $r->order_no,
                'color_name'    => $r->color_name,
                'finishing_qty' => $r->finishing_qty,
                'finishing_date'=> $r->finishing_date,
                'remarks'       => $r->remarks,
            ]);
            return redirect()->back()->with('success', 'Finishing Record Updated Successfully');
        }

        if($action == 'delete'){
            $fin = Finishing::findorFail($r->id);
            $fin->delete();
            return redirect()->back()->with('success', 'Finishing Record Deleted Successfully');
        }

        return redirect()->route('admin.finishing');
    }

    // ================== IRON ==================
    public function iron(Request $r)
    {
        $query = Iron::query();

        if ($r->startDate && $r->endDate) {
            $query->whereBetween('iron_date', [$r->startDate, $r->endDate]);
        }

        if ($r->search) {
            $query->where(function($q) use ($r) {
                $q->where('pi_no', 'LIKE', "%{$r->search}%")
                ->orWhere('order_no', 'LIKE', "%{$r->search}%")
                ->orWhere('style_no', 'LIKE', "%{$r->search}%")
                ->orWhere('color_name', 'LIKE', "%{$r->search}%");
            });
        }

        $irons = $query->latest('iron_date')->paginate(20);
        $pis = ProformaInvoice::whereNotNull('pi_no')->get();

        return view('admin.productions.iron.index', compact('irons', 'pis'));
    }

    public function ironAction(Request $r, $action)
    {
        if ($action == 'get-orders') {
            $orders = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id)
                    ->select('order_no', DB::raw('SUM(order_qty) as total_order_qty'))
                    ->groupBy('order_no')
                    ->get();
            return response()->json($orders);
        }

        if ($action == 'get-styles') {
            $styles = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id)
                    ->where('order_no', $r->order_no)
                    ->select('style_no', DB::raw('SUM(order_qty) as total_style_qty'))
                    ->groupBy('style_no')
                    ->get();
            return response()->json($styles);
        }

        if ($action == 'get-colors') {
            $colors = OrderDetailItem::where('order_no', $r->order_no)
                    ->where('style_no', $r->style_no)
                    ->select('color_name', DB::raw('SUM(qty) as total_color_qty'))
                    ->groupBy('color_name')
                    ->get();

            return response()->json($colors);
        }

        if($action == 'create'){
            $pi = ProformaInvoice::find($r->pi_no);
            Iron::create([
                'pi_id'         => $pi->id,
                'pi_no'         => $pi->pi_no,
                'order_no'      => $r->order_no,
                'style_no'      => $r->style_no,
                'color_name'    => $r->color_name,
                'iron_qty'      => $r->iron_qty,
                'iron_date'     => $r->iron_date,
                'remarks'       => $r->remarks,
                'created_by'    => auth()->id(),
            ]);
            return redirect()->back()->with('success', 'Iron Record Added Successfully');
        }

        if ($action == 'update') {
            $irn = Iron::findorFail($r->id);
            $irn->update([
                'order_no'      => $r->order_no,
                'color_name'    => $r->color_name,
                'iron_qty'      => $r->iron_qty,
                'iron_date'     => $r->iron_date,
                'remarks'       => $r->remarks,
            ]);
            return redirect()->back()->with('success', 'Iron Record Updated Successfully');
        }

        if($action == 'delete'){
            $irn = Iron::findorFail($r->id);
            $irn->delete();
            return redirect()->back()->with('success', 'Iron Record Deleted Successfully');
        }

        return redirect()->route('admin.iron');
    }

    // ================== POLY ==================
    public function poly(Request $r)
    {
        $query = Poly::query();

        if ($r->startDate && $r->endDate) {
            $query->whereBetween('poly_date', [$r->startDate, $r->endDate]);
        }

        if ($r->search) {
            $query->where(function($q) use ($r) {
                $q->where('pi_no', 'LIKE', "%{$r->search}%")
                ->orWhere('order_no', 'LIKE', "%{$r->search}%")
                ->orWhere('style_no', 'LIKE', "%{$r->search}%")
                ->orWhere('color_name', 'LIKE', "%{$r->search}%");
            });
        }

        $polies = $query->latest('poly_date')->paginate(20);
        $pis = ProformaInvoice::whereNotNull('pi_no')->get();

        return view('admin.productions.poly.index', compact('polies', 'pis'));
    }

    public function polyAction(Request $r, $action)
    {
        if ($action == 'get-orders') {
            $orders = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id)
                    ->select('order_no', DB::raw('SUM(order_qty) as total_order_qty'))
                    ->groupBy('order_no')
                    ->get();
            return response()->json($orders);
        }

        if ($action == 'get-styles') {
            $styles = ProformaInvoiceItem::where('proforma_invoice_id', $r->pi_id)
                    ->where('order_no', $r->order_no)
                    ->select('style_no', DB::raw('SUM(order_qty) as total_style_qty'))
                    ->groupBy('style_no')
                    ->get();
            return response()->json($styles);
        }

        if ($action == 'get-colors') {
            $colors = OrderDetailItem::where('order_no', $r->order_no)
                    ->where('style_no', $r->style_no)
                    ->select('color_name', DB::raw('SUM(qty) as total_color_qty'))
                    ->groupBy('color_name')
                    ->get();

            return response()->json($colors);
        }

        if($action == 'create'){
            $pi = ProformaInvoice::find($r->pi_no);
            Poly::create([
                'pi_id'         => $pi->id,
                'pi_no'         => $pi->pi_no,
                'order_no'      => $r->order_no,
                'style_no'      => $r->style_no,
                'color_name'    => $r->color_name,
                'poly_qty'      => $r->poly_qty,
                'poly_date'     => $r->poly_date,
                'remarks'       => $r->remarks,
                'created_by'    => auth()->id(),
            ]);
            return redirect()->back()->with('success', 'Poly Record Added Successfully');
        }

        if ($action == 'update') {
            $ply = Poly::findorFail($r->id);
            $ply->update([
                'order_no'      => $r->order_no,
                'color_name'    => $r->color_name,
                'poly_qty'      => $r->poly_qty,
                'poly_date'     => $r->poly_date,
                'remarks'       => $r->remarks,
            ]);
            return redirect()->back()->with('success', 'Poly Record Updated Successfully');
        }

        if($action == 'delete'){
            $ply = Poly::findorFail($r->id);
            $ply->delete();
            return redirect()->back()->with('success', 'Poly Record Deleted Successfully');
        }

        return redirect()->route('admin.poly');
    }

    public function yarnBooking(Request $r)
    {
        $query = YarnBooking::with('pi');

        // ----------------------------
        // SEARCH (Buyer Name, Booking No, PI No, Fabrication)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;

            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    "CAST(booking_no AS UNSIGNED) = ?",
                    [(int) $search]
                )
                ->orWhere('supplier', 'like', "%{$search}%")
                ->orWhere('fabric_type', 'like', "%{$search}%");
            })
            ->orWhereHas('pi', function ($q_pi) use ($search) {
                $q_pi->where('buyer_name', 'like', "%{$search}%")
                    ->orWhere('pi_no', 'like', "%{$search}%");
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
                        'order_no'    => $item['order_no'],
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

    public function yarnReceive(Request $r)
    {
        $query = YarnReceive::with(['bookingRow', 'bookingRow.pi']);

        // ----------------------------
        // SEARCH (receive_no, booking_no, chalan_no, supplier, pi_no)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;

            $query->where(function ($q) use ($search) {

                // receive_no: ignore left 0
                $q->whereRaw(
                    "LPAD(receive_no, CHAR_LENGTH(?), '0') LIKE ?",
                    [$search, "%{$search}%"]
                )

                // booking_no: ignore left 0
                ->orWhereRaw(
                    "LPAD(booking_no, CHAR_LENGTH(?), '0') LIKE ?",
                    [$search, "%{$search}%"]
                )

                // other normal fields
                ->orWhere('chalan_no', 'like', "%{$search}%")
                ->orWhere('supplier', 'like', "%{$search}%")

                // pi relation search
                ->orWhereHas('bookingRow.pi', function ($q_pi) use ($search) {
                    $q_pi->where('pi_no', 'like', "%{$search}%");
                });
            });
        }

        // ----------------------------
        // DATE RANGE FILTER
        // ----------------------------
        if ($r->startDate) {
            $query->whereDate('receive_date', '>=', $r->startDate);
        }

        if ($r->endDate) {
            $query->whereDate('receive_date', '<=', $r->endDate);
        }

        // ----------------------------
        // GROUP BY RECEIVE NO (SUMMARY)
        // ----------------------------
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
            ->groupBy(
                'receive_no',
                'pi_id',
                'booking_no',
                'receive_date',
                'chalan_no',
                'supplier'
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

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


    public function knittingBooking(Request $r)
    {
        // ১. কুয়েরি শুরু (PI রিলেশনসহ Eager Load করা হয়েছে)
        $query = KnittingBooking::with(['pi']);

        // ২. সার্চ ফিল্টার (Knit Booking No, Yarn Booking No, PI No, Fabric Type দিয়ে সার্চ)
        if ($r->search) {
            $search = $r->search;

            $query->where(function ($q) use ($search) {

                // booking_no: left zero ignore (string based)
                $q->whereRaw(
                    "LPAD(booking_no, CHAR_LENGTH(?), '0') LIKE ?",
                    [$search, "%{$search}%"]
                )

                // normal fields
                ->orWhere('yarn_booking_no', 'like', "%{$search}%")
                ->orWhere('fabric_type', 'like', "%{$search}%")

                // PI relation search
                ->orWhereHas('pi', function ($q_pi) use ($search) {
                    $q_pi->where('buyer_name', 'like', "%{$search}%")
                        ->orWhere('pi_no', 'like', "%{$search}%");
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
        $query = KnittingReceive::with(['pi']);

        // ----------------------------
        // SEARCH (receive_no, knit_booking_no, chalan_no, PI No, Buyer)
        // ----------------------------
        if ($r->search) {
            $search = $r->search;

            $query->where(function ($q) use ($search) {

                // receive_no: ignore left zeros
                $q->whereRaw(
                    "LPAD(receive_no, CHAR_LENGTH(?), '0') LIKE ?",
                    [$search, "%{$search}%"]
                )

                // knit_booking_no: ignore left zeros
                ->orWhereRaw(
                    "LPAD(knit_booking_no, CHAR_LENGTH(?), '0') LIKE ?",
                    [$search, "%{$search}%"]
                )

                // other normal fields
                ->orWhere('chalan_no', 'like', "%{$search}%")

                // PI relation search
                ->orWhereHas('pi', function ($q_pi) use ($search) {
                    $q_pi->where('pi_no', 'like', "%{$search}%")
                        ->orWhere('buyer_name', 'like', "%{$search}%");
                });
            });
        }

        // ----------------------------
        // DATE RANGE FILTER
        // ----------------------------
        if ($r->startDate) {
            $query->whereDate('receive_date', '>=', $r->startDate);
        }
        if ($r->endDate) {
            $query->whereDate('receive_date', '<=', $r->endDate);
        }

        // ----------------------------
        // GROUP BY receive_no
        // ----------------------------
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
            // 1. Fetch all receive rows
            $recvItems = KnittingReceive::where('receive_no', $id)->get();
            // dd($recvItems);
            $receive = $recvItems->first();

            if (!$receive) {
                return redirect()->back()->with('error', 'Receive record not found.');
            }

            // 2. Fetch all knitting bookings for this PI
            $knitItems = KnittingBooking::where('pi_id', $receive->pi_id)->get();

            // 3. Map saved receive values
            foreach ($knitItems as $item) {
                $savedRow = $recvItems->where('knit_id', $item->id)->first();
                if ($savedRow) {
                    $item->current_roll_qty = $savedRow->roll_qty;
                    $item->current_weight   = $savedRow->weight;
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


    public function dyeingBooking(Request $r)
    {
        // ১. রিলেশনসহ কুয়েরি শুরু
        $query = DyeingBooking::with(['pi']);

        // ২. সার্চ লজিক
        if ($r->search) {
            $search = $r->search;
            // জিরো বাদ দিয়ে শুধুমাত্র সংখ্যা বের করা (যেমন: 00001 থেকে 1)
            $cleanSearch = ltrim($search, '0');

            $query->where(function($q) use ($search, $cleanSearch) {
                $q->where('booking_no', 'LIKE', "%{$search}%")
                // ডাটাবেসের জিরো বাদ দিয়ে সার্চ (ইস্যু ২ সমাধান)
                ->orWhereRaw("TRIM(LEADING '0' FROM booking_no) LIKE ?", ["%{$cleanSearch}%"])
                ->orWhere('style', 'LIKE', "%{$search}%")
                ->orWhere('buyer_name', 'LIKE', "%{$search}%")
                ->orWhereHas('pi', function ($q_pi) use ($search) {
                    $q_pi->where('pi_no', 'like', "%{$search}%");
                });
            });
        }

        // ৩. ডেট ফিল্টার
        if ($r->startDate) {
            $query->whereDate('created_at', '>=', $r->startDate);
        }
        if ($r->endDate) {
            $query->whereDate('created_at', '<=', $r->endDate);
        }

        // ৪. গ্রুপ বাই লজিক (ইস্যু ১ এর জন্য pi_id যুক্ত করা হয়েছে)
        $bookings = $query->select(
                                'booking_no',
                                DB::raw('MAX(pi_id) as pi_id'), // PI ID নিতে হবে
                                DB::raw('MAX(style) as style'),
                                DB::raw('MAX(buyer_name) as buyer_name'),
                                DB::raw('MAX(status) as status'),
                                DB::raw('MAX(dyeing_unit) as dyeing_unit'),
                                DB::raw('SUM(required_qty) as total_booking_qty'),
                                DB::raw('COUNT(id) as total_items_count'),
                                DB::raw('MAX(created_by) as created_by'),
                                DB::raw('MAX(created_at) as created_at')
                            )
                            ->groupBy('booking_no')
                            ->orderBy('created_at', 'DESC')
                            ->paginate(20);

        $pis = ProformaInvoice::whereNotNull('pi_no')->get();

        return view(adminTheme() . 'productions.dyeing-booking.index', compact('bookings', 'pis'));
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
            // dd($r->all());
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
                        'dyeing_unit'       => $r->dyeing_unit ?? null,
                        'status'            => $r->status,
                        'created_by'        => auth()->id(),
                        'updated_by'        => auth()->id(),
                    ]);
                }

                if ($r->action === 'update') {
                    // dd($r->all());
                    // Only update existing records (no create)
                    $booking = DyeingBooking::find($item['id']);

                    if ($booking) {
                        $booking->update([
                            'pi_id'             => $pi->id,
                            'required_qty'      => $item['requisition_qty'],
                            'expected_delivery' => $r->expected_delivery ?? null,
                            'buyer_name'        => $r->buyer_name ?? null,
                            'remarks'           => $r->remarks ?? null,
                            'dyeing_unit'       => $r->dyeing_unit ?? null,
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

            $items = $pi->items->flatMap(function ($piItem) {
                return $piItem->detailItems();
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


    public function dyeingReceive(Request $r)
    {
        // ১. কুয়েরি শুরু (PI রিলেশনসহ Eager Load)
        $query = DyeingReceive::with(['pi']);

        // ২. সার্চ লজিক
        if ($r->search) {
            $search = $r->search;
            $cleanSearch = ltrim($search, '0'); // left zero ignore for booking_no

            $query->where(function($q) use ($search, $cleanSearch) {
                $q->where('receive_no', 'LIKE', "%{$search}%")
                ->orWhere('booking_no', 'LIKE', "%{$search}%")
                ->orWhereRaw("TRIM(LEADING '0' FROM booking_no) LIKE ?", ["%{$cleanSearch}%"])
                ->orWhere('style', 'LIKE', "%{$search}%")
                ->orWhere('challan_no', 'LIKE', "%{$search}%") // challan search

                // relation: buyer_name & pi_no
                ->orWhereHas('pi', function ($q_pi) use ($search) {
                    $q_pi->where('pi_no', 'LIKE', "%{$search}%")
                            ->orWhere('buyer_name', 'LIKE', "%{$search}%");
                });
            });
        }

        // ৩. ডেট রেঞ্জ ফিল্টার
        if ($r->startDate) {
            $query->whereDate('receive_date', '>=', $r->startDate);
        }
        if ($r->endDate) {
            $query->whereDate('receive_date', '<=', $r->endDate);
        }

        // ৪. গ্রুপ বাই লজিক
        $bookings = $query->select(
                'receive_no',
                'booking_no',
                DB::raw('MAX(id) as id'),
                DB::raw('MAX(pi_id) as pi_id'),
                DB::raw('MAX(receive_date) as receive_date'),
                DB::raw('MAX(challan_no) as challan_no'),
                DB::raw('SUM(receive_qty) as total_rcv_qty'),
                DB::raw('COUNT(id) as total_items'),
                DB::raw('MAX(created_by) as created_by'),
                DB::raw('MAX(created_at) as created_at')
            )
            ->groupBy('receive_no', 'booking_no')
            ->orderBy('created_at', 'DESC')
            ->paginate(20);

        return view(adminTheme() . 'productions.dyeing-receive.index', compact('bookings'));
    }

    public function dyeingReceiveAction(Request $r, $action, $id = null)
    {
        /* ===============================
            1. CREATE PAGE
        ================================*/
        if ($action === 'create') {

            $pis = ProformaInvoice::whereNotNull('pi_no')
                ->whereHas('dyeingBookings')
                ->get();

            return view(adminTheme().'productions.dyeing-receive.edit', [
                'pis'     => $pis,
                'items'   => [],
                'receive' => null,
                'action'  => 'store',
            ]);
        }

        /* ===============================
            2. STORE
        ================================*/
        if ($action === 'store') {
            $r->validate([
                'pi_id'        => 'required',
                'booking_no'   => 'required',
                'receive_date' => 'required|date',
                'items'        => 'required|array',
            ]);

            DB::beginTransaction();
            try {

                $receiveNo = (DyeingReceive::max('receive_no') ?? 0) + 1;

                $booking = DyeingBooking::where('booking_no', $r->booking_no)->firstOrFail();

                foreach ($r->items as $item) {

                    if (!isset($item['receive_qty']) || $item['receive_qty'] <= 0) {
                        continue;
                    }

                    DyeingReceive::create([
                        'receive_no'       => $receiveNo,
                        'booking_no'       => $r->booking_no,
                        'pi_id'            => $r->pi_id,
                        'booking_item_id'  => $item['id'],
                        'style'            => $item['style'],
                        'color'            => $item['color'],
                        'receive_qty'      => $item['receive_qty'],
                        'fabric_type'      => $booking->fabric_type,
                        'composition'      => $booking->composition,
                        'challan_no'       => $r->challan_no,
                        'receive_date'     => $r->receive_date,
                        'remarks'          => $r->remarks,
                        'created_by'       => auth()->id(),
                    ]);

                    DyeingBooking::where('id', $item['id'])
                        ->increment('received_qty', $item['receive_qty']);
                }

                DB::commit();
                return redirect()
                    ->route('admin.dyeingReceive')
                    ->with('success', 'Dyeing Items Received Successfully');

            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', $e->getMessage());
            }
        }

        /* ===============================
            3. EDIT PAGE
        ================================*/
        if ($action === 'edit' && $id) {

            $receive = DyeingReceive::where('receive_no', $id)->firstOrFail();
            $items   = DyeingReceive::where('receive_no', $id)->get();
            $pis     = ProformaInvoice::whereNotNull('pi_no')->get();

            return view(adminTheme().'productions.dyeing-receive.edit', [
                'receive' => $receive,
                'items'   => $items,
                'pis'     => $pis,
                'action'  => 'update',
            ]);
        }

        /* ===============================
            4. UPDATE
        ================================*/
        if ($action === 'update' && $id) {

            $r->validate([
                'receive_date' => 'required|date',
                'items'        => 'required|array',
            ]);

            DB::beginTransaction();
            try {

                $oldItems = DyeingReceive::where('receive_no', $id)->get();

                // rollback old qty
                foreach ($oldItems as $old) {
                    DyeingBooking::where('id', $old->booking_item_id)
                        ->decrement('received_qty', $old->receive_qty);
                }

                // update & re-add
                foreach ($r->items as $it) {

                    $receive = DyeingReceive::findOrFail($it['id']);

                    $receive->update([
                        'receive_qty'  => $it['receive_qty'],
                        'challan_no'   => $r->challan_no,
                        'receive_date' => $r->receive_date,
                        'remarks'      => $r->remarks,
                        'updated_by'   => auth()->id(),
                    ]);

                    DyeingBooking::where('id', $receive->booking_item_id)
                        ->increment('received_qty', $it['receive_qty']);
                }

                DB::commit();
                return redirect()
                    ->route('admin.dyeingReceive')
                    ->with('success', 'Updated Successfully');

            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', $e->getMessage());
            }
        }

        /* ===============================
            5. AJAX: PI SELECT
        ================================*/
        if ($action === 'pi-select') {

            $items = DyeingBooking::where('pi_id', $r->pi_id)->get();

            if ($items->isEmpty()) {
                return response()->json(['success' => false]);
            }

            $html = view(
                adminTheme().'productions.dyeing-receive.includes.items',
                ['items' => $items, 'action' => 'create']
            )->render();

            return response()->json([
                'success'         => true,
                'html'            => $html,
                'booking_no'      => $items->first()->booking_no,
                'booking_no_show' => $items->first()->getBookingNo(),
                'buyer'           => $items->first()->pi->buyer->name ?? '',
            ]);
        }

        /* ===============================
            6. DELETE (GROUP)
        ================================*/
        if ($action === 'delete' && $id) {

            $receives = DyeingReceive::where('receive_no', $id)->get();

            foreach ($receives as $receive) {
                DyeingBooking::where('id', $receive->booking_item_id)
                    ->decrement('received_qty', $receive->receive_qty);

                $receive->delete();
            }

            return back()->with('success', 'Receive Deleted Successfully');
        }
    }

    public function piWiseFabricStatus(Request $r)
    {
        // ১. AJAX সার্চের জন্য (যখন ইউজার ইনপুটে টাইপ করবে)
        if ($r->ajax()) {
            $query = \App\Models\ProformaInvoice::whereNotNull('pi_no');

            // যদি সার্চ কি-ওয়ার্ড থাকে
            if ($r->has('search') && !empty($r->search)) {
                $query->where(function($q) use ($r) {
                    $q->where('pi_no', 'like', '%' . $r->search . '%')
                    ->orWhere('buyer_name', 'like', '%' . $r->search . '%');
                });
            }

            $pis = $query->orderBy('id', 'desc')->limit(10)->get(['id', 'pi_no', 'buyer_name']);
            return response()->json($pis);
        }

        // ২. যদি পিআই আইডি (pi_id) থাকে, তাহলে ডাটা লোড হবে
        if ($r->has('pi_id')) {
            $piId = $r->pi_id;
            $pi = \App\Models\ProformaInvoice::with([
                'buyer',
                'items',
                'yarnBookings.receives',
                'dyeingBookings'
            ])->find($piId);

            if (is_null($pi)) return redirect()->back()->with('error', 'P.I. Fabric status not found');

            // ক্যালকুলেশন
            $yarnReceivesSum = \App\Models\YarnReceive::where('pi_id', $piId)->sum('receive_qty');
            $knittingBookings = \App\Models\KnittingBooking::where('pi_id', $piId)->get();
            $knittingReceivesSum = \App\Models\KnittingReceive::where('pi_id', $piId)->sum('weight');
            $dyeingReceivesSum = \App\Models\DyeingReceive::where('pi_id', $piId)->sum('receive_qty');

            $data = compact('pi', 'yarnReceivesSum', 'knittingBookings', 'knittingReceivesSum', 'dyeingReceivesSum', 'piId');

            // প্রিন্ট নাকি নরমাল ভিউ
            if ($r->has('print')) {
                return view(adminTheme().'productions.fabric-status.statusPrint', $data);
            } else {
                return view(adminTheme() . 'productions.fabric-status.fabricStatus', $data);
            }
        }

        // ৩. ডিফল্টভাবে খালি পেজ দেখাবে
        return view(adminTheme() . 'productions.fabric-status.fabricStatus');
    }




}

