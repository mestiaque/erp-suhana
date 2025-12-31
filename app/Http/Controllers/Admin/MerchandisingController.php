<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Media;
use App\Models\Order;
use App\Models\Budget;
use App\Models\Sample;
use App\Models\Cutting;
use App\Models\Product;
use App\Models\BudgetCm;
use App\Models\Attribute;
use App\Models\BudgetTest;
use App\Models\BudgetYarn;
use App\Models\SampleItem;
use App\Models\OrderDetail;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\BudgetDyeing;
use App\Models\SewingOutput;
use Illuminate\Http\Request;
use App\Models\BudgetSummary;
use App\Models\BudgetKnitting;
use App\Models\BudgetAccessory;
use App\Models\OrderDetailItem;
use App\Models\ProformaInvoice;
use App\Models\BudgetAccessories;
use Illuminate\Support\Facades\DB;
use App\Models\ProformaInvoiceItem;
use App\Http\Controllers\Controller;
use App\Models\BudgetProductionCost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\BudgetPrintEmbroidery;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MerchandisingController extends Controller
{
    public function buyers(Request $r)
    {
                // Filter Actions Start
        if ($r->action) {
            if ($r->checkid) {

                $datas = User::filterByType('buyer')
                    ->whereIn('status', [0, 1])
                    ->whereIn('id', $r->checkid)
                    ->withTrashed()
                    ->get();

                foreach ($datas as $data) {

                    if ($r->action == 1) {
                        $data->status = 1;
                        $data->save();
                    } elseif ($r->action == 2) {
                        $data->status = 0;
                        $data->save();
                    } elseif ($r->action == 5) {
                        // $userFiles = Media::latest()
                        //     ->where('src_type', 7)  // buyer file srcType=7
                        //     ->where('src_id', $data->id)
                        //     ->get();

                        // foreach ($userFiles as $media) {
                        //     if (File::exists($media->file_url)) {
                        //         File::delete($media->file_url);
                        //     }
                        //     $media->delete();
                        // }

                        $data->delete();
                    }
                }

                Session()->flash('success', 'Action Successfully Completed!');
            } else {
                Session()->flash('info', 'Please select minimum one item.');
            }

            return redirect()->back();
        }
                // Filter Actions End


        $users = User::latest()
            ->filterByType('buyer')
            ->whereIn('status', [0, 1])
            ->where(function ($q) use ($r) {

                if ($r->search) {
                    $q->where('name', 'LIKE', '%' . $r->search . '%')
                        ->orWhere('email', 'LIKE', '%' . $r->search . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $r->search . '%')
                        ->orWhere('country_text', 'LIKE', '%' . $r->search . '%');
                }
                if ($r->status) {
                    $q->where('status', $r->status == 'inactive' ? 0 : 1);
                }

                if ($r->startDate || $r->endDate) {

                    $from = $r->startDate ?: Carbon::now()->format('Y-m-d');
                    $to   = $r->endDate ?: Carbon::now()->format('Y-m-d');

                    $q->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                }
            })
            ->select(['id', 'name', 'email', 'mobile', 'created_at', 'company_name', 'address_line1', 'addedby_id', 'status', 'country_text'])
            ->paginate(25)
            ->appends([
                'search'    => $r->search,
                'status'    => $r->status,
                'startDate' => $r->startDate,
                'endDate'   => $r->endDate,
            ]);

                // Total Count Results
        $total = User::withTrashed()->filterByType('buyer')
            ->whereIn('status', [0, 1])
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 1 then 1 end) as active")
            ->selectRaw("count(case when status = 0 then 1 end) as inactive")
            ->selectRaw("count(case when deleted_at IS NOT NULL then 1 end) as deleted")
            ->first();

        if ($r->view == 'deleted') {

            $users = User::onlyTrashed()
                ->filterByType('buyer')
                ->latest()
                ->paginate(12)->appends($r->all());;

            return view(adminTheme().'merchandising.buyers.users_deleted', compact('users','total'));
        }

        return view(adminTheme().'merchandising.buyers.users', compact('users', 'total'));
    }

    public function buyersAction(Request $r, $action, $id=null)
    {
        /* ================= CREATE BUYER ================= */
        if ($action=='create' && $r->isMethod('post')) {

            $r->validate([
                'name'         => 'required|max:100',
                'company_name' => 'nullable|max:100',
                'email'        => 'nullable|email|max:100',
                'mobile'       => 'nullable|max:100',
                'country'      => 'nullable|max:100',
                'address'      => 'nullable|max:500',
            ]);

            // Check active user
            // $existsUser = User::where(function ($q) use ($r) {

            //         if (!empty($r->email)) {
            //             $q->where('email', $r->email);
            //         }

            //         if (!empty($r->mobile)) {
            //             $q->orWhere('mobile', $r->mobile);
            //         }

            //     })->first();

            // if ($existsUser) {
            //     if($r->has('api')){
            //         return response()->json([
            //             'success'       => false,
            //             'msg'           => "his email or mobile alrady used.",
            //             'buyer_created' => false,
            //         ]);
            //     }
            //     Session()->flash('error','This email or mobile alrady used.');
            //     return redirect()->back();
            // }

            // Create new buyer
            $password = Str::random(8);
            $user = new User();
            $user->name          = $r->name;
            $user->mobile        = $r->mobile;
            $user->email         = $r->email ?? null;
            $user->company_name  = $r->company_name;
            $user->address_line1 = $r->address;
            $user->country_text  = $r->country;
            $user->password_show = $password;
            $user->password      = Hash::make($password);
            $user->setTypes('buyer');
            $user->save();
            if($r->has('api')){
                return response()->json([
                    'success'       => true,
                    'msg'           => "Buyer registered successfully",
                    'buyer_created' => true,
                    'id'            => $user->id,
                    'name'          => $user->name,
                ]);
            }

            Session()->flash('success','Buyer registered successfully!');
            return redirect()->route('admin.buyersAction',['edit',$user->id]);
        }
        /* ================= CREATE END ================= */



        /* ================= RESTORE ================= */
        if ($action=='restore') {
            $user = User::onlyTrashed()->filterByType('buyer')->find($id);
            if (!$user) {
                Session()->flash('error','Buyer not found in trash.');
                return redirect()->route('admin.buyers',['edit'=>'deleted']);
            }
            $user->restore();
            Session()->flash('success','Buyer restored successfully!');
            return redirect()->route('admin.buyers');
        }


        /* ================= FORCE DELETE ================= */
        if ($action=='force-delete') {
            $user = User::onlyTrashed()->filterByType('buyer')->find($id);
            if (!$user) {
                Session()->flash('error','Buyer not found in trash.');
                return redirect()->route('admin.buyers',['edit'=>'deleted']);
            }
            $files = Media::where('src_type',7)->where('src_id',$user->id)->get();
            foreach ($files as $file) {
                if (File::exists($file->file_url)) {
                    File::delete($file->file_url);
                }
                $file->delete();
            }
            $user->forceDelete();
            Session()->flash('success','Buyer permanently deleted!');
            return redirect()->route('admin.buyers',['edit'=>'deleted']);
        }

        /* ================= FIND BUYER ================= */
        $user = User::filterByType('buyer')->find($id);
        if (!$user) {
            Session()->flash('error','Buyer not found.');
            return redirect()->route('admin.buyers');
        }


        /* ================= VIEW BUYER ================= */
        if ($action == 'view') {
            $orders = ProformaInvoice::where('buyer_id', $user->id)->get();
            return view(adminTheme().'merchandising.buyers.viewUser', compact('user','orders'));
        }


        /* ================= UPDATE BUYER ================= */
        if ($action=='update' && $r->isMethod('post')) {

            $r->validate([
                'name'         => 'required|max:100',
                'email'        => 'nullable|email|max:100|unique:users,email,'.$user->id,
                'mobile'       => 'nullable|max:100',
                'company_name' => 'nullable|max:200',
                'address'      => 'nullable|max:500',
                'country'      => 'nullable|max:100',
                'created_at'   => 'nullable|date',
                'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user->name          = $r->name;
            $user->email         = $r->email;
            $user->mobile        = $r->mobile;
            $user->company_name  = $r->company_name;
            $user->address_line1 = $r->address;
            $user->country_text  = $r->country;

            if ($r->hasFile('image')) {
                uploadFile($r->image, $user->id, 7, 1, Auth::id()); // src_type=7
            }

            $user->status = $r->status ? 1 : 0;
            $user->setTypes('buyer');
            $user->save();

            Session()->flash('success','Buyer updated successfully!');
            return redirect()->back();
        }


        /* ================= SOFT DELETE ================= */
        if ($action == 'delete') {

            // $files = Media::where('src_type',7)->where('src_id',$user->id)->get();
            // foreach ($files as $file) {
            //     if (File::exists($file->file_url)) {
            //         File::delete($file->file_url);
            //     }
            //     $file->delete();
            // }

            $user->deleted_at = Carbon::now();
            $user->deleted_by = Auth::id();
            $user->save();

            Session()->flash('success','Buyer deleted successfully!');
            return redirect()->route('admin.buyers');
        }

        return view(adminTheme().'merchandising.buyers.editUser', compact('user'));
    }


    public function samples(Request $r)
    {
                // -----------------------------
                // BULK ACTION
                // -----------------------------
        if ($r->action && $r->checkid) {
            $samples = Sample::whereIn('id', $r->checkid)->get();

            foreach ($samples as $sample) {
                switch ($r->action) {
                    case 'pending':
                        $sample->status = 'pending';
                        break;
                    case 'confirmed':
                        $sample->status = 'confirmed';
                        break;
                    case 'completed':
                        $sample->status = 'completed';
                        break;
                    case 'cancel':
                        $sample->status = 'cancel';
                        break;
                    case 'delete':
                        $sample->items()->delete();
                        $sample->delete();
                        continue 2;  // skip save
                }
                $sample->save();
            }

            session()->flash('success', 'Action Completed Successfully!');
            return redirect()->back();
        }

                // -----------------------------
                // QUERY SAMPLES
                // -----------------------------
        $samples = Sample::orderBy('id', 'desc')
            ->where('status', '<>', 'temp')
            ->where(function($q) use ($r) {

                        // SEARCH
                if ($r->search) {
                    $search = $r->search;
                    $q->where(function($qq) use ($search) {
                        $qq->where('id', 'LIKE', "%{$search}%")  // Sample ID
                        ->orWhere('buyer_name', 'LIKE', "%{$search}%")
                        ->orWhere('style', 'LIKE', "%{$search}%")
                        ->orWhere('id', 'LIKE', "%{$search}%")
                        ->orWhere('merchant_name', 'LIKE', "%{$search}%");
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
                } else {
                    $q->where('status', '<>', 'trash');
                }
            })
            ->paginate(25)
            ->appends($r->all());

                // -----------------------------
                // TOTAL COUNTS
                // -----------------------------
        $totals = Sample::whereNotIn('status', ['trash', 'temp'])
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN status = 'confirmed' THEN 1 END) AS confirmed")
            ->selectRaw("COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed")
            ->selectRaw("COUNT(CASE WHEN status = 'cancel' THEN 1 END) AS cancel")
            ->first();

        $buyers = User::latest()
            ->where('buyer', true)
            ->whereIn('status', [0, 1])
            ->select(['id', 'name', 'company_name'])
            ->get();

        return view(adminTheme().'merchandising.samples.index', compact('samples', 'totals', 'buyers'));
    }

    public function samplesAction(Request $r, $action, $id = null)
    {
                // CREATE SAMPLE
        if ($action == 'create') {
            $sample = Sample::where('status', 'temp')->where('created_by', Auth::id())->first();

            if (!$sample) {
                $sample             = new Sample();
                $sample->status     = 'temp';
                $sample->created_by = Auth::id();
                $sample->created_at = now();
                $sample->save();
            }

            return redirect()->route('admin.samplesAction', ['edit', $sample->id]);
        }

                // FIND SAMPLE
        $sample = Sample::find($id);
        if (!$sample) {
            session()->flash('error', 'Sample Not Found');
            return redirect()->route('admin.samples');
        }

        if (!in_array($sample->status, ['temp', 'pending']) && $action == ['edit', 'delete', 'add-item', 'update-item', 'remove-item', 'update-head']) {
            session()->flash('error', 'Sample is already confirmed and cannot be edited or deleted.');
            return redirect()->route('admin.samples');
        }

                // VIEW
        if ($action == 'view') {
            return view(adminTheme().'merchandising.samples.view', compact('sample'));
        }

                // ITEM CRUD (Add/Update/Remove)
        if (in_array($action, ['add-item', 'update-item', 'remove-item', 'update-head'])) {

            if ($action == 'update-head') {

                $sample = Sample::find($id);
                if (!$sample || !$r->field) return redirect()->back();

                $field = $r->field;
                $value = $r->value;

                        // Buyer Update
                if ($field == 'buyer') {
                    if ($buyer = User::find($value)) {
                        $sample->buyer_id   = $buyer->id;
                        $sample->buyer_name = $buyer->name;
                    }
                }
                if ($field == 'merchant') {
                    if ($merchant = User::find($value)) {
                        $sample->merchant_id   = $merchant->id;
                        $sample->merchant_name = $merchant->name;
                    }
                }

                        // Style update with unique check
                elseif ($field == 'style') {
                    $existsStyle = Sample::where('style', $value)->where('id', '<>', $sample->id)->exists();
                    // if ($existsStyle) {
                    //     return response()->json([
                    //         'success' => false,
                    //         'message' => 'This style already exists',
                    //         'field'   => 'style'                       // যেই input field
                    //     ]);
                    // }
                    $sample->style = $value;
                }

                        // Default update for all other fields
                else {
                    $sample->$field = $value;
                }

                        // Final single save
                $sample->save();
            }

            if ($action == 'add-item') {
                $item            = new SampleItem();
                $item->sample_id = $sample->id;
                $item->save();
            }

            if ($action == 'update-item') {
                $item = SampleItem::find($r->item_id);
                if ($item) {
                    if ($item && $r->field) {
                        $field        = $r->field;
                        $item->$field = $r->value;
                        $item->save();
                        if($r->field == 'quantity'){
                            $totalQty = $sample?->items?->sum('quantity') ?? 0;
                            $sample->update([ 'total_qty'=>$totalQty ]);
                            return response()->json([
                                'success' => false,
                                'qty'     => $totalQty
                            ]);
                        }
                    }
                    $item->save();
                }
            }

            if ($action == 'remove-item') {
                SampleItem::where('id', $r->item_id)->delete();
            }

            $items = $sample->items;
            $view  = view(adminTheme().'merchandising.samples.includes.items', compact('sample','items'))->render();
            return response()->json(['success' => true, 'view' => $view]);
        }

                // UPDATE SAMPLE
        if ($action == 'update') {
            $r->validate([
                'buyer'  => 'required|numeric',
                'style'  => 'required|string|max:255',
                'type'   => 'nullable|string|max:255',
                'status' => 'required|string|max:20',
            ]);
            $buyer = User::find($r->buyer);

            $existsStyle = Sample::where('style', $r->style)->where('id', '<>', $sample->id)->exists();

            // if ($existsStyle) {
            //     session()->flash('info', 'This style already exists');
            //     return redirect()->back();  // Save unnecessary
            // }

            if (count($sample->items) == 0) {
                session()->flash('info', 'No items found for this sample');
                return redirect()->back();  // Save unnecessary
            }

            $sample->buyer_id   = $buyer->id ?? $sample->buyer_id;
            $sample->buyer_name = $buyer->name ?? $sample->buyer_name;

            $sample->type   = $r->type ?? $sample->type;
            $sample->status = $r->status ?? $sample->status;
            $sample->save();

            session()->flash('success', 'Sample Updated Successfully');
            return redirect()->route('admin.samplesAction', ['view', $sample->id]);
        }

                // DELETE SAMPLE
        if ($action == 'delete') {
            $sample->items()->delete();
            $sample->delete();
            session()->flash('success', 'Sample Deleted Successfully');
            return redirect()->back();
        }

                // LOAD EDIT PAGE
        $items = $sample->items;

        $buyers = User::latest()
            ->where('buyer', true)
            ->whereIn('status', [0, 1])
            ->select(['id', 'name', 'company_name'])
            ->get();
        $merchandisers = User::latest()
            ->where('merchandiser', true)
            ->whereIn('status', [0, 1])
            ->select(['id', 'name'])
            ->get();

        return view(adminTheme().'merchandising.samples.edit', compact('sample','items', 'buyers', 'merchandisers'));
    }

    public function orderDetails(Request $r)
    {
        // ১. বেস কুয়েরি তৈরি করুন (যাতে ফিল্টারগুলো সবখানে সমানভাবে কাজ করে)
        $query = OrderDetail::orderBy('id', 'desc')
            ->where('status', '<>', 'temp')
            ->where(function($q) use ($r) {

                // SEARCH
                if ($r->search) {
                    $search = $r->search;
                    $q->where(function($qq) use ($search) {
                        $qq->where('id', 'LIKE', "%{$search}%")
                            ->orWhere('buyer_name', 'LIKE', "%{$search}%")
                            ->orWhere('style_no', 'LIKE', "%{$search}%")
                            ->orWhere('company_name', 'LIKE', "%{$search}%")
                            ->orWhere('invoice_no', 'LIKE', "%{$search}%")
                            ->orWhere('order_no', 'LIKE', "%{$search}%")
                            ->orWhere('fabrication', 'LIKE', "%{$search}%")
                            ->orWhere('merchant_name', 'LIKE', "%{$search}%");
                    });
                }

                // DATE RANGE
                if ($r->startDate || $r->endDate) {
                    $from = $r->startDate ?: now()->format('Y-m-d');
                    $to   = $r->endDate ?: now()->format('Y-m-d');
                    $q->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
                }

                // SHIPMENT DATE RANGE
                if ($r->shipmentStartDate || $r->shipmentEndDate) {
                    $sfrom = $r->shipmentStartDate ?: now()->format('Y-m-d');
                    $sto   = $r->shipmentEndDate ?: now()->format('Y-m-d');
                    $q->whereDate('shipment_date', '>=', $sfrom)
                    ->whereDate('shipment_date', '<=', $sto);
                }

                // STATUS
                if ($r->status) {
                    $q->where('status', $r->status);
                } else {
                    $q->where('status', '<>', 'trash');
                }
            });

        // ২. ফিল্টার করা ডাটা থেকে total_qty এর যোগফল বের করুন
        // মনে রাখবেন: sum('column_name') এখানে আপনার ডাটাবেসের একচুয়াল কলামের নাম দিন (যেমন: 'total_qty')
        $totalOrderQty = (clone $query)->sum('total_qty');

        // ৩. রেজাল্ট পেজিনেট করুন
        $orderDetails = $query->paginate(25)->appends($r->all());

        // ৪. স্ট্যাটাস অনুযায়ী কাউন্ট (এটি আগের মতোই থাকবে)
        $totals = OrderDetail::whereNotIn('status', ['trash', 'temp'])
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN status = 'confirmed' THEN 1 END) AS confirmed")
            ->selectRaw("COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed")
            ->selectRaw("COUNT(CASE WHEN status = 'cancelled' THEN 1 END) AS cancelled")
            ->first();

        $styles = OrderDetail::whereNotIn('status', ['trash', 'temp'])
                    ->distinct()
                    ->pluck('style_no')
                    ->toArray(); // নিশ্চিত করুন এটি একটি অ্যারে

        $sewingOutputs = SewingOutput::query()
            // Join এর বদলে LeftJoin ব্যবহার করুন যাতে ডাটা ম্যাচ না করলেও SewingOutput গুলো আসে
            ->leftJoin('production_plannings', 'sewing_outputs.planning_id', '=', 'production_plannings.id')
            ->whereIn('sewing_outputs.style_no', $styles)
            ->select(
                'sewing_outputs.style_no',
                DB::raw('SUM(sewing_outputs.production) as actual_production'),
                // style_qty যদি নাল থাকে তবে সেটিকে ০ হিসেবে ট্রিট করা
                DB::raw('IFNULL(MAX(production_plannings.style_qty), 0) as allowed_qty')
            )
            ->groupBy('sewing_outputs.style_no')
            ->get()
            ->mapWithKeys(function ($item) {
                // যদি allowed_qty ০ হয় (অর্থাৎ প্ল্যানিং টেবিল থেকে ডাটা পায়নি),
                // তবে আমরা অরিজিনাল প্রোডাকশনই দেখাবো (অথবা আপনার লজিক অনুযায়ী পরিবর্তন করতে পারেন)
                $allowed = $item->allowed_qty > 0 ? $item->allowed_qty : 999999999;

                $totalOutput = ($item->actual_production > $allowed)
                            ? $allowed
                            : $item->actual_production;

                return [
                    $item->style_no => [
                        'style_no' => $item->style_no,
                        'total_qty' => $totalOutput,
                        'actual' => $item->actual_production,
                        'allowed' => $item->allowed_qty
                    ]
                ];
            });

        $grandTotalSewingOutput = $sewingOutputs->sum(function ($item) {
            return $item['total_qty'];
        });


        $cuttingSummary = Cutting::query()
            ->leftJoin('proforma_invoice_items', function($join) {
                $join->on('cuttings.style_no', '=', 'proforma_invoice_items.style_no')
                    ->on('cuttings.pi_id', '=', 'proforma_invoice_items.proforma_invoice_id');
            })
            ->select(
                'cuttings.style_no',
                'cuttings.pi_id',
                DB::raw('SUM(cuttings.cutting_qty) as actual_cut'),
                DB::raw('IFNULL(MAX(proforma_invoice_items.order_qty), 0) as allowed_qty')
            )
            ->groupBy('cuttings.style_no', 'cuttings.pi_id')
            ->get()
            ->map(function ($item) {
                // যদি allowed_qty ০ হয় (অর্থাৎ PI আইটেমে ডাটা নেই), তবে অরিজিনাল কাটিংই দেখাবে
                $allowed = $item->allowed_qty > 0 ? $item->allowed_qty : 999999999;

                // যদি একচুয়াল কাটিং অর্ডারের চেয়ে বেশি হয়, তবে অর্ডার কোয়ান্টিটি (Allowed) নিবে
                $cappedCut = ($item->actual_cut > $allowed) ? $allowed : $item->actual_cut;

                return [
                    'capped_val' => $cappedCut
                ];
            });

        // ২. গ্র্যান্ড টোটাল বের করা (Capped Output)
        $grandTotalCuttingOutput = $cuttingSummary->sum('capped_val');


        $data = compact('orderDetails', 'totals', 'totalOrderQty', 'grandTotalSewingOutput', 'grandTotalCuttingOutput');

        if($r->has('print')){
            return view(adminTheme().'merchandising.orderDetails.printList', $data);
        } else {
            return view(adminTheme().'merchandising.orderDetails.index', $data);
        }
    }

    public function orderDetailsAction(Request $r, $action, $id = null)
    {
        /* ---------------------------------------------------------------------
        * UPDATE ORDER ITEM
        * ---------------------------------------------------------------------*/
        if ($action == 'update-item') {
            if (!$r->field) {
                return response()->json(['success' => false, 'message' => 'Invalid Request']);
            }
            $field = $r->field;
            $value = $r->value;

            $item = OrderDetailItem::find($id);
            if ($item) {
                if ($field == 'item_name') {
                    $item->item_name = $value ?: null;
                }
                if ($field == 'color_name') {
                    $item->color_name = $value ?: null;
                }

                if ($field == 'qty') {
                    $item->qty = $value ?: 0;
                }
                if ($field == 'composition') {
                    $item->composition = $value ?: null;
                }
                // Add other fields if needed
                $item->save();
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Item not found']);
        }

        /* ---------------------------------------------------------------------
        * REMOVE ORDER ITEM
        * ---------------------------------------------------------------------*/
        if ($action == 'remove-item') {
            $item = OrderDetailItem::find($id);
            if ($item) {
                $item->delete();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'message' => 'Item not found']);
        }

        /* ---------------------------------------------------------------------
        * CREATE TEMP ORDER
        * ---------------------------------------------------------------------*/
        if ($action == 'create') {
            $orderDetails = OrderDetail::where('status', 'temp')
                ->where('created_by', Auth::id())
                ->first();
            $lastOrder = OrderDetail::orderBy('id', 'desc')->first();

            if (!$orderDetails) {
                $orderDetails = OrderDetail::create([
                    'status'        => 'temp',
                    'created_by'    => Auth::id(),
                    'buyer_id'      => $lastOrder?->buyer_id,
                    'buyer_name'    => $lastOrder?->buyer_name,
                    'merchant_id'   => $lastOrder?->merchant_id,
                    'merchant_name' => $lastOrder?->merchant_name,
                    'company_name'  => $lastOrder?->company_name,
                ]);

                OrderDetailItem::create([
                    'order_detail_id' => $orderDetails->id
                ]);
            }

            return redirect()->route('admin.orderDetailsAction', ['edit', $orderDetails->id]);
        }

        /* ---------------------------------------------------------------------
        * FIND ORDER
        * ---------------------------------------------------------------------*/
        $orderDetails = OrderDetail::find($id);
        if (!$orderDetails) {
            session()->flash('error', 'Order Details Not Found');
            return redirect()->route('admin.orderDetails');
        }

        // LOCKED ORDERS ARE READ ONLY
        // if (!in_array($orderDetails->status, ['temp', 'pending']) &&
        //     in_array($action, ['edit', 'delete', 'update-head', 'update-items'])
        // ) {
        //     session()->flash('error', 'Order is confirmed & cannot be edited.');
        //     return redirect()->route('admin.orderDetails');
        // }

        /* ---------------------------------------------------------------------
        * UPDATE HEADER
        * ---------------------------------------------------------------------*/
        if ($action == 'update-head') {
            if (!$r->field) {
                return response()->json(['success' => false, 'message' => 'Invalid Request']);
            }

            $field = $r->field;
            $value = $r->value;

            if ($field == 'buyer') {
                if ($buyer = User::find($value)) {
                    $orderDetails->buyer_id = $buyer->id;
                    $orderDetails->buyer_name = $buyer->name;
                }
            } elseif ($field == 'merchant') {
                if ($merchant = User::find($value)) {
                    $orderDetails->merchant_id = $merchant->id;
                    $orderDetails->merchant_name = $merchant->name;
                }
            } else {
                $orderDetails->$field = $value;
            }

            $orderDetails->save();
            return response()->json(['success' => true]);
        }

        /* ---------------------------------------------------------------------
        * ADD ITEM
        * ---------------------------------------------------------------------*/
        if ($action === 'add-item') {
            $item = new OrderDetailItem();
            $item->order_detail_id = $orderDetails->id;
            $item->order_no = $orderDetails->order_no;
            $item->style_no = $orderDetails->style_no;
            $item->composition = $orderDetails->composition;
            $item->fabrication = $orderDetails->fabrication;
            $item->gsm = $orderDetails->gsm;
            $item->shipment_date = $orderDetails->shipment_date;
            $item->save();

            return response()->json(['success' => true, 'id' => $item->id]);
        }

        /* ---------------------------------------------------------------------
        * UPDATE ITEMS & MAIN ORDER
        * ---------------------------------------------------------------------*/
        if ($action === 'update') {

            $r->validate([
                'buyer'         => 'required|exists:users,id',
                'merchant'      => 'required|exists:users,id',
                'style_no'      => 'required|string|max:255',
                'order_no'      => 'required|string|max:255',
                'status'        => 'required|string|max:50',
                'shipment_date' => 'nullable|date',
                'fabrication'   => 'nullable|string|max:255',
                  // ITEM VALIDATION
                'item_name.*'   => 'nullable|string|max:255',
                'colors.*'      => 'required|string|max:100',
                'qtys.*'        => 'required|numeric|min:0',
                'gsm.*'         => 'required|string',
                'compositions.*'=> 'required|string',
            ]);

            DB::beginTransaction();
            try {
                // UPDATE HEADER
                $buyer = User::find($r->buyer);
                $merchant = User::find($r->merchant);

                $existingIds = [];
                if ($r->colors) {
                    foreach ($r->colors as $itemId => $color) {

                        $qty         = $r->qtys[$itemId] ?? 0;
                        $gsm         = $r->gsm[$itemId] ?? null;
                        $composition = $r->compositions[$itemId] ?? null;
                        $itemName    = $r->item_name[$itemId] ?? null;

                        if (!$color && !$qty) continue;

                        $data = [
                            'item_name'      => $itemName,
                            'color_name'     => $color,
                            'qty'            => $qty,
                            'gsm'            => $gsm,
                            'composition'    => $composition,
                            'order_no'       => $orderDetails->order_no,
                            'style_no'       => $orderDetails->style_no,
                            'fabrication'    => $orderDetails->fabrication,
                            'shipment_date'  => $orderDetails->shipment_date,
                        ];

                        // UPDATE EXISTING
                        if (is_numeric($itemId)) {
                            $item = OrderDetailItem::where('id', $itemId)
                                ->where('order_detail_id', $orderDetails->id)
                                ->first();

                            if ($item) {
                                $item->update($data);
                                $existingIds[] = $item->id;
                            }
                        }
                        // CREATE NEW
                        else {
                            $item = $orderDetails->items()->create($data);
                            $existingIds[] = $item->id;
                        }
                    }
                }

                // UPDATE HEADER
                $orderDetails->update([
                    'buyer_id'      => $buyer?->id,
                    'buyer_name'    => $buyer?->name,
                    'merchant_id'   => $merchant?->id,
                    'merchant_name' => $merchant?->name,
                    'style_no'      => $r->style_no,
                    'order_no'      => $r->order_no,
                    'status'        => $r->status,
                    'shipment_date' => $r->shipment_date,
                    'fabrication'   => $r->fabrication,
                    'total_qty'     => $orderDetails->items()->sum('qty'),
                ]);

                // DELETE REMOVED ITEMS
                if (count($existingIds)) {
                    OrderDetailItem::where('order_detail_id', $orderDetails->id)
                        ->whereNotIn('id', $existingIds)
                        ->delete();
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                session()->flash('error', 'Something went wrong: ' . $e->getMessage());
                return back();
            }

            session()->flash('success', 'Order & Items Updated Successfully!');
            return redirect()->route('admin.orderDetails');
        }

        /* ---------------------------------------------------------------------
        * DELETE ORDER
        * ---------------------------------------------------------------------*/
        if ($action == 'delete') {
            $orderDetails->items()->delete();
            $orderDetails->delete();
            session()->flash('success', 'Order Deleted Successfully');
            return back();
        }

        /* ---------------------------------------------------------------------
        * LOAD EDIT PAGE
        * ---------------------------------------------------------------------*/
        $items = $orderDetails->items;
        $buyers = User::where('buyer', true)->whereIn('status', [0,1])->get();
        $merchandisers = User::where('merchandiser', true)->whereIn('status', [0,1])->get();

        return view(adminTheme().'merchandising.orderDetails.edit',
            compact('orderDetails','items','buyers','merchandisers')
        );
    }

    public function proformaInvoice(Request $r)
    {
        $pis = ProformaInvoice::with(['buyer','merchant','items'])
            ->where('status','<>','temp')
            ->orderByDesc('id')
            ->when($r->search, function ($q) use ($r) {
                $search = $r->search;
                $q->where(function ($qq) use ($search) {
                    $qq->where('order_no','LIKE',"%{$search}%")
                    ->orWhere('pi_no','LIKE',"%{$search}%")
                    ->orWhere('buyer_name','LIKE',"%{$search}%")
                    ->orWhere('merchant_name','LIKE',"%{$search}%");
                });
            })
            ->when($r->startDate || $r->endDate, function ($q) use ($r) {
                $q->whereBetween('created_at', [
                    $r->startDate ?: now()->format('Y-m-d'),
                    $r->endDate   ?: now()->format('Y-m-d')
                ]);
            })
            ->when($r->status, fn($q)=>$q->where('status',$r->status))
            ->paginate(25)
            ->appends($r->all());

        $totals = ProformaInvoice::whereNotIn('status',['temp'])
            ->selectRaw("COUNT(*) total")
            ->selectRaw("SUM(status='pending') pending")
            ->selectRaw("SUM(status='confirmed') confirmed")
            ->selectRaw("SUM(status='approved') approved")
            ->selectRaw("SUM(status='cancelled') cancelled")
            ->first();

        return view(adminTheme().'merchandising.pi.index', compact('pis','totals'));
    }

    public function proformaInvoiceAction(Request $r, $action, $id = null)
    {
        /* ---------------------------------------------------------------------
        * CREATE TEMP PROFORMA INVOICE
        * ---------------------------------------------------------------------*/
        if ($action == 'create') {
            $pi = ProformaInvoice::firstOrCreate(
                ['status' => 'temp', 'created_by' => auth()->id()],
                ['created_at' => now()]
            );

            return redirect()->route('admin.proformaInvoiceAction', ['edit', $pi->id]);
        }
        /* ---------------------------------------------------------------------
        * FIND PROFORMA INVOICE
        * ---------------------------------------------------------------------*/
        $pi = ProformaInvoice::with('items', 'order')->find($id);

        if (!$pi) {
            session()->flash('error', 'Proforma Invoice Not Found');
            return redirect()->route('admin.proformaInvoice');
        }

        /* ---------------------------------------------------------------------
        * VIEW PI
        * ---------------------------------------------------------------------*/
        if ($action === 'view') {
            return view(adminTheme().'merchandising.pi.view', compact('pi'));
        }

        /* ---------------------------------------------------------------------
        * VIEW PI AS INVOICE
        * ---------------------------------------------------------------------*/
        if ($action === 'invoice') {
            return view(adminTheme().'merchandising.pi.piInvoice', compact('pi'));
        }

        /* ---------------------------------------------------------------------
        * AJAX: SELECT ORDERS BY BUYER
        * ---------------------------------------------------------------------*/
        if ($action == 'buyer-select') {

            $orders = OrderDetail::where('buyer_id', $r->buyer_id)
                ->where('status', '<>', 'temp')
                ->select('order_no')
                ->distinct()
                ->get();
            if ($orders->isEmpty()) {
                return response()->json(['success' => false]);
            }

            $html = '<option value="">-- Select Order Number --</option>';
            foreach ($orders as $order) {
                $html .= '<option value="'.$order->order_no.'">'.$order->order_no.'</option>';
            }

            return response()->json([
                'success' => true,
                'html'    => $html
            ]);
        }

        /* ---------------------------------------------------------------------
        * AJAX: SELECT ORDER ITEMS BY ORDER NO
        * ---------------------------------------------------------------------*/
        if ($action == 'po-select') {
            $orders = OrderDetail::where('order_no', $r->order_no)
                // ->where('status', 'confirmed')
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'html' => '<tr><td colspan="10" class="text-center">Order not found</td></tr>'
                ]);
            }

            $html = view(adminTheme().'merchandising.pi.includes.items', [
                'items' => $orders
            ])->render();

            return response()->json([
                'success' => true,
                'html'    => $html,
                'order'   => $orders->first()
            ]);
        }


        /* ---------------------------------------------------------------------
        * UPDATE PROFORMA INVOICE
        * ---------------------------------------------------------------------*/
        if ($action === 'update') {
            // dd($r->all());
            $r->validate([
                // 'buyer_id'            => 'required|exists:users,id',
                // 'order_no'            => 'nullable|string',
                'status'              => 'required|string',
                'remarks'             => 'nullable|string',
                'items'               => 'required|array',
                'items.*.order_no'    => 'required|string',
                'items.*.unit_price'  => 'required|numeric|min:0',
                'items.*.total_price' => 'required|numeric|min:0',
                'items.*.order_qty'   => 'required|numeric|min:0',
                'items.*.uom'         => 'required',
            ]);

            DB::transaction(function () use ($r, $pi) {

                // FETCH ORDER & BUYER
                $buyer = User::findOrFail($r->buyer_id);

                // CALCULATE TOTALS
                $total_qty  = collect($r->items)->sum('order_qty');
                $total_bill = collect($r->items)->sum('total_price');

                // FILTER TERMS
                $terms = collect($r->input('terms', []))
                    ->filter(fn($t) => isset($t['checked']) && $t['checked'] === 'on')
                    ->mapWithKeys(fn($t) => [$t['key'] => $t['value']])
                    ->toArray();

                $orderNos = collect($r->items)
                            ->pluck('order_no')
                            ->unique()
                            ->values()
                            ->implode(',');

                // UPDATE MAIN PI
                $pi->update([
                    // 'order_id'      => $order->id,
                    'order_no' => $orderNos,
                    'buyer_id'      => $buyer->id,
                    'buyer_name'    => $buyer->name,
                    // 'merchant_id'   => $order->merchant_id,
                    // 'merchant_name' => $order->merchant_name,
                    'remarks'       => $r->remarks,
                    'status'        => $r->status,
                    'order_date'    => $r->order_date ?? $pi->order_date,
                    'pi_no'         => $r->pi_no ?? $pi->pi_no,
                    'terms'         => json_encode($terms),
                    'applicant'               => $r->applicant ?? null,
                    'applicant_bank'          => $r->applicant_bank ?? null,
                    'first_beneficiary'       => $r->first_beneficiary ?? null,
                    'first_beneficiary_bank'  => $r->first_beneficiary_bank ?? null,
                    'second_beneficiary'      => $r->second_beneficiary ?? null,
                    'second_beneficiary_bank' => $r->second_beneficiary_bank ?? null,
                    'notify_party'            => $r->notify_party ?? null,
                    'created_at'                 =>$r->created_at ?? $pi->created_at,
                    'total_qty'  => $total_qty,
                    'total_bill' => $total_bill,
                    'edited_by' => auth()->id(),
                ]);

                // UPDATE OR CREATE ITEMS
                $keepIds = [];
                foreach ($r->items as $row) {
                    $order = OrderDetail::where('order_no', $row['order_no'])->firstOrFail();
                    $item = ProformaInvoiceItem::updateOrCreate(
                        ['id' => $row['id'] ?? null],
                        [
                            'proforma_invoice_id' => $pi->id,
                            'order_no'    => $order->order_no,
                            'style_no'    => $row['style_no'],
                            'item_name'   => $row['item_name'] ?? null,
                            'color_name'  => $row['color_name'] ?? null,
                            'composition' => $row['composition'] ?? null,
                            'fabrication' => $row['fabrication'] ?? null,
                            'gsm'         => $row['gsm'] ?? null,
                            'shipment_date'=> $row['shipment_date'] ?? null,
                            'order_qty'   => $row['order_qty'],
                            'unit_price'  => $row['unit_price'],
                            'total_price' => $row['total_price'],
                            'uom' => $row['uom'],
                            'status'      => 'active',
                            'created_by'  => auth()->id(),
                            'edited_by' => auth()->id(),
                        ]
                    );
                    $keepIds[] = $item->id;
                }

                // DELETE REMOVED ITEMS
                ProformaInvoiceItem::where('proforma_invoice_id', $pi->id)
                    ->whereNotIn('id', $keepIds)
                    ->delete();
            });

            session()->flash('success', 'Proforma Invoice Updated Successfully');
            return redirect()->route('admin.proformaInvoiceAction', ['invoice', $pi->id]);
        }

        /* ---------------------------------------------------------------------
        * DELETE PROFORMA INVOICE
        * ---------------------------------------------------------------------*/
        if ($action === 'delete') {
            $pi->items()->delete();
            $pi->delete();
            session()->flash('success','Proforma Invoice Deleted Successfully');
            return redirect()->back();
        }

        /* ---------------------------------------------------------------------
        * LOAD EDIT PAGE
        * ---------------------------------------------------------------------*/
        $piOrder = ProformaInvoice::whereNotNull('order_no')->pluck('order_no')->toArray();

        // $orders = OrderDetail::where('status', 'confirmed')
        //     ->whereNotIn('order_no', $piOrder)
        //     ->get()
        //     ->unique('order_no');
        $orders = OrderDetail::where('buyer_id', $pi->buyer_id)
                ->where('status', '<>', 'temp')
                ->select('order_no')
                ->distinct()
                ->get();

        $buyers = User::with('orderDetails')
            ->whereHas('orderDetails')
            // ->filterByType('buyer')
            ->whereIn('status', [0,1])
            ->get();

        $items = $pi->items;

        return view(adminTheme().'merchandising.pi.edit', compact('pi','items','orders','buyers'));
    }

    public function manageAttribute(Request $r, $action = null, $id = null)
    {
        $view = $r->route()->defaults['view'] ?? null;
        $type = $r->route()->defaults['type'] ?? null;

        // --- Handle POST Actions (add/update/delete + bulk actions) ---
        if($r->isMethod('post') || $r->action){
            // Bulk actions (activate/inactivate/delete)
            if($r->action && $r->checkid){
                $datas = Attribute::where('type', $type)->whereIn('id', $r->checkid)->get();
                foreach($datas as $data){
                    if($r->action == 1) { $data->status = 'active'; $data->save(); }
                    elseif($r->action == 2) { $data->status = 'inactive'; $data->save(); }
                    elseif($r->action == 5) {
                        $medias = Media::where('src_type',0)->where('src_id',$data->id)->get();
                        foreach($medias as $media){
                            if(File::exists($media->file_url)) File::delete($media->file_url);
                            $media->delete();
                        }
                        $data->delete();
                    }
                }
                Session()->flash('success','Action Successfully Completed!');
                return redirect()->route('admin.' . $view);
            }

            // Single actions (add/update/delete)
            if($action){
                // Add
                if($action=='add'){
                    $exists = Attribute::where('type',$type)->where('name',$r->name)->first();
                    if($exists){ Session()->flash('info','This Name Already Exists'); return redirect()->route('admin.' . $view); }

                    $data = new Attribute();
                    $data->name = $r->name;
                    $data->description = $r->description;
                    $data->short_description = $r?->color ?? null;
                    $data->type = $type;
                    $data->status = 'active';
                    $data->addedby_id = Auth::id();
                    $data->save();

                    $slug = Str::slug($r->name) ?: $data->id;
                    $data->slug = Attribute::where('type',$type)->where('slug',$slug)->where('id','<>',$data->id)->exists() ? $slug.'-'.$data->id : $slug;
                    $data->save();

                    Session()->flash('success','Item Added Successfully');
                    return redirect()->route('admin.' . $view);

                }

                // Update
                if($action=='update'){
                    $data = Attribute::where('type',$type)->find($id);
                    if(!$data){ Session()->flash('info','Item Not Found'); return redirect()->route('admin.' . $view); }

                    $exists = Attribute::where('type',$type)->where('id','<>',$data->id)->where('name',$r->name)->first();
                    if($exists){ Session()->flash('info','This Name Already Exists'); return redirect()->route('admin.' . $view); }

                    $data->name = $r->name;
                    $data->description = $r->description;
                    $data->short_description = $r?->color ?? null ;
                    $data->editedby_id = Auth::id();

                    $slug = Str::slug($r->name) ?: $data->id;
                    $data->slug = Attribute::where('type',$type)->where('slug',$slug)->where('id','<>',$data->id)->exists() ? $slug.'-'.$data->id : $slug;
                    $data->save();

                    Session()->flash('success','Item Updated Successfully');
                    return redirect()->route('admin.' . $view);
                }

                // Delete
                if($action=='delete'){
                    $data = Attribute::where('type',$type)->find($id);
                    if(!$data){ Session()->flash('info','Item Not Found'); return redirect()->route('admin.' . $view); }
                    $data->delete();
                    Session()->flash('success','Item Deleted Successfully');
                    return redirect()->route('admin.' . $view);
                }
            }
        }

        // --- Fetch Data for GET Requests ---
        $data = Attribute::latest()
            ->where('type',$type)
            ->where('status','<>','temp')
            ->when($r->search, fn($q) => $q->where('name','LIKE','%'.$r->search.'%'))
            ->when($r->status, fn($q) => $q->where('status',$r->status))
            ->select(['id','name','created_at','status','description', 'short_description'])
            ->paginate(25)
            ->appends(['search'=>$r->search,'status'=>$r->status]);

        // Report (optional, shared across all)
        $report = Attribute::where('type',$type)
            ->where('status','<>','temp')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 'active' then 1 end) as active")
            ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
            ->first();

        return view(adminTheme().'merchandising.masterData.'.$view, compact('data','report'));
    }


    public function fabricStatus($id = 1) // ডিফল্ট ১ ধরা হয়েছে
    {
        dd(1);
        $pi = ProformaInvoice::with([
            'buyer',
            'items',
            'yarnBookings',
            'dyeingBookings'
        ])->findOrFail($id);

        return view(adminTheme().'merchandising.booking.status', compact('pi'));
    }

    public function booking(Request $r)
    {
        $bookings = Booking::orderBy('id', 'desc')
            ->when($r->search, function($q) use ($r){
                $search = $r->search;
                $q->where(function($qq) use ($search){
                    $qq->where('booking_no','LIKE',"%{$search}%")
                        ->orWhere('buyer_name','LIKE',"%{$search}%")
                        ->orWhere('style_no','LIKE',"%{$search}%")
                        ->orWhere('merchant_name','LIKE',"%{$search}%");
                });
            })
            ->when($r->startDate || $r->endDate, function($q) use ($r){
                $from = $r->startDate ?: now()->format('Y-m-d');
                $to   = $r->endDate   ?: now()->format('Y-m-d');
                $q->whereBetween('booking_date', [$from,$to]);
            })
            ->paginate(25)
            ->appends($r->all());

        return view(adminTheme().'merchandising.booking.index', compact('bookings'));
    }

    public function bookingAction(Request $r, $action, $id = null)
    {
        /* -----------------------------------------------------------------
        * CREATE TEMP BOOKING
        * -----------------------------------------------------------------*/
        if($action === 'create'){
            $buyers = User::filterByType('buyer')->whereIn('status',[0,1])->get();
            $merchandisers = User::filterByType('merchandiser')->whereIn('status',[0,1])->get();
            $pis =ProformaInvoice::get();
            return view(adminTheme().'merchandising.booking.edit', compact('buyers','merchandisers', 'pis'));
            $lastBooking = Booking::orderBy('id','desc')->first();

            $booking = Booking::create([
                'status'        => 'temp',
                'created_by'    => Auth::id(),
                'buyer_id'      => $lastBooking?->buyer_id,
                'buyer_name'    => $lastBooking?->buyer_name,
                'merchant_id'   => $lastBooking?->merchant_id,
                'merchant_name' => $lastBooking?->merchant_name,
                'company_name'  => $lastBooking?->company_name,
            ]);

            BookingItem::create(['booking_id' => $booking->id]);

            return redirect()->route('admin.booking.action', ['edit', $booking->id]);
        }

        /* -----------------------------------------------------------------
        * FIND BOOKING
        * -----------------------------------------------------------------*/
        $booking = Booking::find($id);
        if(!$booking){
            session()->flash('error','Booking Not Found');
            return redirect()->route('admin.booking.index');
        }

        // LOCKED BOOKINGS ARE READ ONLY
        if(!in_array($booking->status,['temp','pending']) && in_array($action,['edit','delete','update','update-items'])){
            session()->flash('error','Booking is confirmed & cannot be edited.');
            return redirect()->route('admin.booking.index');
        }

        /* -----------------------------------------------------------------
        * UPDATE HEADER
        * -----------------------------------------------------------------*/
        if($action === 'update-head'){
            if(!$r->field) return response()->json(['success'=>false,'message'=>'Invalid Request']);
            $field = $r->field;
            $value = $r->value;

            if($field === 'buyer'){
                if($buyer = User::find($value)){
                    $booking->buyer_id = $buyer->id;
                    $booking->buyer_name = $buyer->name;
                }
            } elseif($field === 'merchant'){
                if($merchant = User::find($value)){
                    $booking->merchant_id = $merchant->id;
                    $booking->merchant_name = $merchant->name;
                }
            } else {
                $booking->$field = $value;
            }

            $booking->save();
            return response()->json(['success'=>true]);
        }

        /* -----------------------------------------------------------------
        * ADD ITEM
        * -----------------------------------------------------------------*/
        if($action === 'add-item'){
            $item = new BookingItem();
            $item->booking_id = $booking->id;
            $item->style_no   = $booking->style_no;
            $item->booking_no = $booking->booking_no;
            $item->save();

            return response()->json(['success'=>true,'id'=>$item->id]);
        }

        /* -----------------------------------------------------------------
        * UPDATE ITEMS & MAIN BOOKING
        * -----------------------------------------------------------------*/
        if($action === 'update'){
            $r->validate([
                'buyer'      => 'required|exists:users,id',
                'merchant'   => 'required|exists:users,id',
                'booking_no' => 'required|string|max:255',
                'style_no'   => 'required|string|max:255',
                'booking_date'=> 'required|date',
                'items.*.color_name' => 'nullable|string|max:100',
                'items.*.qty'        => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();
            try{
                $buyer = User::find($r->buyer);
                $merchant = User::find($r->merchant);

                // Update booking header
                $booking->update([
                    'buyer_id'      => $buyer->id,
                    'buyer_name'    => $buyer->name,
                    'merchant_id'   => $merchant->id,
                    'merchant_name' => $merchant->name,
                    'booking_no'    => $r->booking_no,
                    'style_no'      => $r->style_no,
                    'booking_date'  => $r->booking_date,
                    'composition'   => $r->composition,
                    'status'        => $r->status,
                ]);

                $existingIds = [];
                if($r->items){
                    foreach($r->items as $itemId => $row){
                        if(is_numeric($itemId)){
                            $item = BookingItem::where('id',$itemId)
                                ->where('booking_id',$booking->id)
                                ->first();
                            if($item){
                                $item->update([
                                    'color_name' => $row['color_name'] ?? null,
                                    'qty'        => $row['qty'] ?? 0,
                                    'style_no'   => $booking->style_no,
                                ]);
                                $existingIds[] = $item->id;
                            }
                        } else {
                            $item = $booking->items()->create([
                                'color_name' => $row['color_name'] ?? null,
                                'qty'        => $row['qty'] ?? 0,
                                'style_no'   => $booking->style_no,
                            ]);
                            $existingIds[] = $item->id;
                        }
                    }
                }

                // Delete removed items
                if(count($existingIds)){
                    BookingItem::where('booking_id',$booking->id)
                        ->whereNotIn('id',$existingIds)
                        ->delete();
                }

                DB::commit();
            } catch(\Exception $e){
                DB::rollback();
                session()->flash('error','Something went wrong: '.$e->getMessage());
                return back();
            }

            session()->flash('success','Booking & Items Updated Successfully');
            return redirect()->route('admin.booking.index');
        }

        /* -----------------------------------------------------------------
        * DELETE BOOKING
        * -----------------------------------------------------------------*/
        if($action === 'delete'){
            $booking->items()->delete();
            $booking->delete();
            session()->flash('success','Booking Deleted Successfully');
            return back();
        }

        /* -----------------------------------------------------------------
        * LOAD EDIT PAGE
        * -----------------------------------------------------------------*/
        $items = $booking->items;
        $buyers = User::where('buyer',true)->whereIn('status',[0,1])->get();
        $merchandisers = User::where('merchandiser',true)->whereIn('status',[0,1])->get();

        return view(adminTheme().'merchandising.booking.edit', compact('booking','items','buyers','merchandisers'));
    }


    ///////////////////////////////////
    // BUDGET MODULE STARTS HERE //
    ///////////////////////////////////
    public function budget(Request $r)
    {
        $budgets = Budget::orderByDesc('id')
            ->when($r->search, function($q) use ($r){
                $search = $r->search;
                $q->where(function($qq) use ($search){
                    $qq->where('pi_no','LIKE',"%{$search}%")
                        ->orWhere('buyer','LIKE',"%{$search}%");
                });
            })
            ->when($r->startDate || $r->endDate, function($q) use ($r){
                $from = $r->startDate ?: now()->format('Y-m-d');
                $to   = $r->endDate   ?: now()->format('Y-m-d');
                $q->whereBetween('created_at', [$from,$to]);
            })
            ->paginate(25)
            ->appends($r->all());

        return view(adminTheme().'merchandising.budget.index', compact('budgets'));
    }

    public function budgetAction(Request $r, $action, $id = null)
    {
        /* =========================================================
        | CREATE (LOAD CREATE PAGE)
        ========================================================= */
        if ($action === 'create') {

            $pisAll    = ProformaInvoice::whereNotNull('pi_no')->whereIn('status',[0,1])->get();
            $pis = $pisAll->map(function($pi) {
                            return [
                                'id'           => $pi->id,
                                'pi_no'        => $pi->pi_no,
                                'buyer_name'   => $pi->buyer_name,
                                'total_qty'    => $pi->items->sum('order_qty'),          // sum of all items qty
                                'total_bill'   => $pi->items->sum('total_price'),       // sum of all items amount
                                'style_count'  => $pi->items->unique('style_no')->count(), // unique style_no count
                                'order_count'  => $pi->items->unique('order_no')->count(), // unique order_no count
                            ];
                        });
            return view(adminTheme().'merchandising.budget.edit', compact('pis'));
        }

        /* =========================================================
        | FIND BUDGET
        ========================================================= */
        if ($id) {
            $budget = Budget::find($id);
            if (!$budget) {
                session()->flash('error','Budget Not Found');
                return redirect()->route('admin.budget.index');
            }
        }

        /* =========================================================
        | STORE (SAVE NEW BUDGET)
        ========================================================= */
        if ($action === 'store') {
            DB::beginTransaction();

            try {
                $data = $r->all();

                $budgetData = $data['budget'];
                if (!isset($r->budget['pi_no']) || empty($r->budget['pi_no'])) {
                    throw new \Exception('Budget PI No is required.');
                }
                $pi = ProformaInvoice::find($budgetData['pi_no']);
                if (!$pi) {
                    throw new \Exception('Associated Proforma Invoice not found.');
                }

                $budget = Budget::create([
                    'pi_id' => $pi ? $pi->id : null,
                    'pi_no' => $pi ? $pi->pi_no : $budgetData['pi_no'] ?? null,
                    'buyer' => $budgetData['buyer'] ?? null,
                    'total_po' => $budgetData['total_pos'] ?? null,
                    'total_style' => $budgetData['total_styles'] ?? null,
                    'item' => $budgetData['item'] ?? null,
                    'lc_no' => $budgetData['lc_no'] ?? null,
                    'lc_value' => $budgetData['lc_value'] ?? null,
                    'lc_date' => $budgetData['lc_date'] ?? null,
                    'shipment_date' => $budgetData['ship_date'] ?? null,
                    'pi_value' => $budgetData['pi_value'] ?? null,
                    'total_qty' => $budgetData['total_qty'] ?? null,
                    'created_at' => now(),
                    'created_by' => auth()->user()->id ?? null,
                ]);

                // Save budget sections
                $this->saveBudgetSection($budget, $r->yarn_desc ?? [], BudgetYarn::class);
                $this->saveBudgetSection($budget, $r->knitting_desc ?? [], BudgetKnitting::class);
                $this->saveBudgetSection($budget, $r->dyeing_desc ?? [], BudgetDyeing::class);
                $this->saveBudgetSection($budget, $r->accessories_desc ?? [], BudgetAccessory::class);
                $this->saveBudgetSection($budget, $r->print_emb_desc ?? [], BudgetPrintEmbroidery::class);
                $this->saveBudgetSection($budget, $r->cm_desc ?? [], BudgetCm::class);
                $this->saveAllBudgetTests($budget, $r->test_desc);
                $this->saveSummary($budget, $r->summary);
                $this->saveProductionCost($budget, $r->prod_cost);

                DB::commit();

                return redirect()->route('admin.budgetAction', ['view', $budget->id])
                                ->with('success', 'Budget saved successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()
                                ->with('error', 'Failed to save budget: ' . $e->getMessage())
                                ->withInput();
            }
        }

        /* =========================================================
        | UPDATE
        ========================================================= */
        if ($action === 'update') {
            DB::beginTransaction();
            try {
                $budget->update([
                    'updated_by'     => auth()->id(),
                    'updated_at'     => now(),
                ]);
                // Delete old sections safely
                $budget->yarns()->delete();
                $budget->knittings()->delete();
                $budget->dyeings()->delete();
                $budget->accessories()->delete();
                $budget->printEmbroidery()->delete();
                $budget->cms()->delete();
                $budget->tests()->delete();
                // Reinsert updated sections using saveBudgetSection
                $this->saveBudgetSection($budget, $r->yarn_desc ?? [], BudgetYarn::class);
                $this->saveBudgetSection($budget, $r->knitting_desc ?? [], BudgetKnitting::class);
                $this->saveBudgetSection($budget, $r->dyeing_desc ?? [], BudgetDyeing::class);
                $this->saveBudgetSection($budget, $r->accessories_desc ?? [], BudgetAccessory::class);
                $this->saveBudgetSection($budget, $r->print_emb_desc ?? [], BudgetPrintEmbroidery::class);
                $this->saveBudgetSection($budget, $r->cm_desc ?? [], BudgetCm::class);
                $this->saveAllBudgetTests($budget, $r->test_desc);
                $this->saveSummary($budget, $r->summary);
                $this->saveProductionCost($budget, $r->prod_cost);

                DB::commit();

                return redirect()->route('admin.budgetAction', ['view', $budget->id])->with('success', 'Budget updated successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Failed to update budget: ' . $e->getMessage())->withInput();
            }
        }


        /* =========================================================
        | VIEW
        ========================================================= */
        if ($action === 'view') {

            if (!$budget) {
                return redirect()->back()->with('error', 'Budget not found');
            }
            return view(adminTheme().'merchandising.budget.view', compact('budget'));
        }

        /* =========================================================
        | DELETE
        ========================================================= */
        if ($action === 'delete') {

            if (!$budget) {
                return redirect()->back()->with('error', 'Budget not found');
            }

            try {
                DB::beginTransaction();
                $budget->yarns()->delete();
                $budget->knittings()->delete();
                $budget->dyeings()->delete();
                $budget->accessories()->delete();
                $budget->printEmbroidery()->delete();
                $budget->cms()->delete();
                $budget->tests()->delete();
                $budget->summary()->delete();
                $budget->productionCosts()->delete();
                // Soft delete
                $budget->delete();

                DB::commit();

                return redirect()->back()->with('success','Budget deleted successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error','Failed to delete budget: '.$e->getMessage());
            }
        }

        /* =========================================================
        | EDIT PAGE
        ========================================================= */
        $budget->load(['yarns','knittings','accessories']);

        return view(adminTheme().'merchandising.budget.edit', compact('budget'));
    }

    protected function saveBudgetSection($budget, array $sectionData, string $modelClass, string $budgetKey = 'budget_id', bool $deleteOld = true)
    {
        // dd($sectionData);
        if ($deleteOld) {
            $modelClass::where($budgetKey, $budget->id)->delete();
        }

        if (empty($sectionData['description'])) {
            return;
        }

        $lastItemTotalIndex   = array_key_last($sectionData['item_total'] ?? []);
        $lastPercentIndex     = array_key_last($sectionData['percent'] ?? []);
        $lastCompanyIndex     = array_key_last($sectionData['company_name'] ?? []);
        $lastPaymentIndex     = array_key_last($sectionData['payment_value'] ?? []);

        foreach ($sectionData['description'] as $index => $description) {
            $modelClass::create([
                $budgetKey => $budget->id,
                'description' => $description,
                'supplier' => $sectionData['supplier'][$index] ?? null,
                'qty' => $sectionData['qty'][$index] ?? null,
                'unit_price' => $sectionData['unit_price'][$index] ?? null,
                'ttl_usd' => $sectionData['ttl_usd'][$index] ?? null,
                // 🔥 always last value
                'item_total'    => $sectionData['item_total'][$lastItemTotalIndex] ?? null,
                'percent'       => $sectionData['percent'][$lastPercentIndex] ?? null,
                'company_name'  => $sectionData['company_name'][$lastCompanyIndex] ?? null,
                'payment_value' => $sectionData['payment_value'][$lastPaymentIndex] ?? null,
            ]);
        }
    }

    protected function saveAllBudgetTests($budget, array $data)
    {
        // আগের সব delete
        BudgetTest::where('budget_id', $budget->id)->delete();

        $sections = [
            'test',
            'buying_commission',
            'local_transportation',
            'bank_commercial',
            'commission_percent',
            'freight',
        ];

        foreach ($sections as $key) {

            if (empty($data[$key])) {
                continue;
            }

            foreach ($data[$key] as $index => $desc) {
                if (!$desc) continue;

                BudgetTest::create([
                    'budget_id'   => $budget->id,
                    'key'         => $key, // 🔥 test / buying_commission etc
                    'description' => $desc,
                    'supplier'    => $data[$key . '_supplier'][$index] ?? null,
                    'qty'         => $data[$key . '_qty'][$index] ?? 0,
                    'unit_price'  => $data[$key . '_unit_price'][$index] ?? 0,
                    'ttl_usd'     => $data[$key . '_ttl_usd'][$index] ?? 0,
                    'item_total'   => $data[$key . '_item_total'][$index] ?? 0,
                    'percent'      => $data[$key . '_percent'][$index] ?? 0,
                    'company_name' => $data[$key . '_company'][$index] ?? null,
                    'payment_value'=> $data[$key . '_payment_value'][$index] ?? 0,
                ]);
            }
        }
    }

    protected function saveSummary($budget, array $data)
    {
        // percent clean helper
        $cleanPercent = function ($value) {
            if ($value === null) return 0;
            return (float) str_replace('%', '', $value);
        };

        $payload = [
            'budget_id'                     => $budget->id,
            'total_expenditure'             => $data['total_expenditure'] ?? 0,
            'percent_of_total'              => $cleanPercent($data['expenditure_percent'] ?? 0),
            'reservation'                   => $data['reservation'] ?? 0,
            'btb_percent'                   => $data['btb_percent'] ?? 0,
            'btb_value'                     => $data['btb_value'] ?? 0,
            'cash_percent'                  => $data['cash_percent'] ?? 0,
            'cash_value'                    => $data['cash_value'] ?? 0,
            'bbcl_yarn_dyeing_print_access' => $data['bbcl_yarn_dyeing_print_access'] ?? 0,
            'bbcl_knitting'                 => $data['bbcl_knitting'] ?? 0,
        ];

        // update or create
        BudgetSummary::updateOrCreate(
            ['budget_id' => $budget->id], // where
            $payload                     // data
        );
    }

    protected function saveProductionCost($budget, array $data)
    {
        // যদি data multiple rows হয়
        foreach ($data['item'] as $index => $item) {
            $payload = [
                'budget_id'     => $budget->id,
                'item'          => $item ?? null,
                'machine_use'   => $data['machine_use'][$index] ?? 0,
                'ocost'         => $data['ocost'][$index] ?? 0,
                'total_cost'    => $data['total_cost'][$index] ?? 0,
                'product_day'   => $data['product_day'][$index] ?? 0,
                'cm_doz'        => $data['cm_doz'][$index] ?? 0,
            ];

            // Update or create by budget_id + item (or any unique combination)
            BudgetProductionCost::updateOrCreate(
                [
                    'budget_id' => $budget->id,
                ],
                $payload
            );
        }
    }

    ///////////////////////////////////
    // BUDGET MODULE ENDS HERE //
    ///////////////////////////////////

}
