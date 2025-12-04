<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Media;
use App\Models\Order;
use App\Models\Sample;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\SampleItem;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use App\Models\ProformaInvoice;
use Illuminate\Support\Facades\DB;
use App\Models\ProformaInvoiceItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class MerchandisingController extends Controller
{
    public function buyers(Request $r)
    {
        // Filter Actions Start
        if ($r->action) {
            if ($r->checkid) {

                $datas = User::where('buyer', true)
                    ->whereIn('status', [0, 1])
                    ->whereIn('id', $r->checkid)
                    ->get();

                foreach ($datas as $data) {

                    if ($r->action == 1) {
                        $data->status = 1;
                        $data->save();
                    } elseif ($r->action == 2) {
                        $data->status = 0;
                        $data->save();
                    } elseif ($r->action == 5) {
                        $userFiles = Media::latest()
                            ->where('src_type', 7) // buyer file srcType=7
                            ->where('src_id', $data->id)
                            ->get();

                        foreach ($userFiles as $media) {
                            if (File::exists($media->file_url)) {
                                File::delete($media->file_url);
                            }
                            $media->delete();
                        }

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
            ->where('buyer', true)
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
                'search' => $r->search,
                'status' => $r->status,
                'startDate' => $r->startDate,
                'endDate' => $r->endDate,
            ]);

        // Total Count Results
        $total = DB::table('users')
            ->where('buyer', true)
            ->whereIn('status', [0, 1])
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 1 then 1 end) as active")
            ->selectRaw("count(case when status = 0 then 1 end) as inactive")
            ->first();

        return view(adminTheme().'merchandising.buyers.users', compact('users', 'total'));
    }

    public function buyersAction(Request $r, $action, $id = null)
    {
        // Add New Buyer Start
        if ($action == 'create' && $r->isMethod('post')) {
            $check = $r->validate([
                'name'         => 'required|max:100',
                'company_name' => 'nullable|max:100',
                'email'        => 'nullable|email|max:100|unique:users,email',
                'mobile'       => 'nullable|max:100',
                'country'      => 'nullable|max:100',
                'address'      => 'nullable|max:500',
            ]);
            $user = User::where('email', $r->email)->first();
            if ($user) {
                $user->buyer = true;
                $user->customer = false;
                $user->save();
                if($r->has('api')){
                    return response()->json([
                        'success' => true,
                        'msg' => "User found! Now marked as Buyer",
                        'buyer_created' => true,
                        'id' => $user->id,
                        'name' => $user->name,
                    ]);
                }
                Session()->flash('success', 'User found! Now marked as Buyer.');
            } else {

                $password = Str::random(8);
                $user = new User();
                $user->name = $r->name;
                $user->mobile = $r->mobile;
                $user->email = $r->email;
                $user->country_text = $r->country;
                $user->company_name = $r->company_name;
                $user->address_line1 = $r->address;
                $user->password_show = $password;
                $user->password = Hash::make($password);

                $user->buyer = true;
                $user->customer = false;
                $user->save();
                if($r->has('api')){
                    return response()->json([
                        'success' => true,
                        'msg' => "Buyer registered successfully",
                        'buyer_created' => true,
                        'id' => $user->id,
                        'name' => $user->name,
                    ]);
                }
                Session()->flash('success', 'Buyer registered successfully!');
            }

            return redirect()->route('admin.buyersAction', ['view', $user->id]);
        }
        // Add New Buyer End


        $user = User::where('buyer', true)
            ->whereIn('status', [0, 1])
            ->find($id);

        if (!$user) {
            Session()->flash('error', 'Buyer not found');
            return redirect()->route('admin.buyers');
        }


        // View Buyer
        if ($action == 'view') {

            $orders = $user->orders()->whereIn('status', ['approved'])
                ->paginate(10, ['*'], 'orders_page');

            $paymentMethods = Attribute::latest()
                ->where('type', 9)
                ->where('status', 'active')
                ->select(['id', 'name', 'amount'])
                ->get();

            $accountMethods = Attribute::latest()
                ->where('type', 10)
                ->where('status', 'active')
                ->where('addedby_id', Auth::id())
                ->select(['id', 'name', 'amount'])
                ->get();

            $transactions = Transaction::where('user_id', $user->id)
                ->where('type', 4) // buyer payment
                ->orderBy('id', 'desc')
                ->paginate(10, ['*'], 'trans_page');

            return view(adminTheme().'merchandising.buyers.viewUser', compact('user', 'orders', 'transactions', 'accountMethods', 'paymentMethods'));
        }


        // Update Buyer
        if ($action == 'update' && $r->isMethod('post')) {

            $check = $r->validate([
                'name'         => 'required|max:100',
                'email'        => 'required|email|max:100|unique:users,email,' . $user->id,
                'mobile'       => 'nullable|max:100',
                'country'      => 'nullable|max:100',
                'address'      => 'nullable|max:200',
                'company_name' => 'nullable|max:200',
                'created_at'   => 'nullable|date|max:50',
                'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $createDate = $r->created_at ?
                Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) :
                Carbon::now();

            if (!$createDate->isSameDay($user->created_at)) {
                $user->created_at = $createDate;
            }

            $user->name = $r->name;
            $user->mobile = $r->mobile;
            $user->email = $r->email;
            $user->company_name = $r->company_name;
            $user->address_line1 = $r->address;
            $user->country_text = $r->country;



            // Image Upload
            if ($r->hasFile('image')) {
                $file = $r->image;
                uploadFile($file, $user->id, 6, 1, Auth::id()); // src_type=7 for buyer
            }

            $user->status = $r->status ? true : false;
            $user->save();

            Session()->flash('success', 'Buyer updated successfully!');
            return redirect()->back();
        }


        // Delete Buyer
        if ($action == 'delete') {

            $userFiles = Media::latest()
                ->where('src_type', 7)
                ->where('src_id', $user->id)
                ->get();

            foreach ($userFiles as $media) {
                if (File::exists($media->file_url)) {
                    File::delete($media->file_url);
                }
                $media->delete();
            }

            $user->delete();

            Session()->flash('success', 'Buyer deleted successfully!');
            return redirect()->back();
        }


        // Buyer Payment (optional, like supplier)
        if ($action == 'payment') {
            // If want payment module here, I will generate similar code like supplier.
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
                        continue 2; // skip save
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
                $sample = new Sample();
                $sample->status = 'temp';
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
                        $sample->buyer_id = $buyer->id;
                        $sample->buyer_name = $buyer->name;
                    }
                }
                if ($field == 'merchant') {
                    if ($merchant = User::find($value)) {
                        $sample->merchant_id = $merchant->id;
                        $sample->merchant_name = $merchant->name;
                    }
                }

                // Style update with unique check
                elseif ($field == 'style') {
                    $existsStyle = Sample::where('style', $value)->where('id', '<>', $sample->id)->exists();
                    if ($existsStyle) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This style already exists',
                            'field' => 'style' // যেই input field
                        ]);
                    }
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
                $item = new SampleItem();
                $item->sample_id = $sample->id;
                $item->save();
            }

            if ($action == 'update-item') {
                $item = SampleItem::find($r->item_id);
                if ($item) {
                    if ($item && $r->field) {
                        $field = $r->field;
                        $item->$field = $r->value;
                        $item->save();
                        if($r->field == 'quantity'){
                            $totalQty = $sample?->items?->sum('quantity') ?? 0;
                            $sample->update([ 'total_qty'=>$totalQty ]);
                            return response()->json([
                                'success' => false,
                                'qty' => $totalQty
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
            $view = view(adminTheme().'merchandising.samples.includes.items', compact('sample','items'))->render();
            return response()->json(['success' => true, 'view' => $view]);
        }

        // UPDATE SAMPLE
        if ($action == 'update') {
            $r->validate([
                'buyer' => 'required|numeric',
                'style' => 'required|string|max:255',
                'type' => 'nullable|string|max:255',
                'status' => 'required|string|max:20',
            ]);
            $buyer = User::find($r->buyer);

            $existsStyle = Sample::where('style', $r->style)->where('id', '<>', $sample->id)->exists();

            if ($existsStyle) {
                session()->flash('info', 'This style already exists');
                return redirect()->back();  // Save unnecessary
            }

            if (count($sample->items) == 0) {
                session()->flash('info', 'No items found for this sample');
                return redirect()->back();  // Save unnecessary
            }

            $sample->buyer_id = $buyer->id ?? $sample->buyer_id;
            $sample->buyer_name = $buyer->name ?? $sample->buyer_name;

            $sample->type = $r->type ?? $sample->type;
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
        // -----------------------------
        // QUERY SAMPLES
        // -----------------------------
        $orderDetails = OrderDetails::orderBy('id', 'desc')
            ->where('status', '<>', 'temp')
            ->where(function($q) use ($r) {

                // SEARCH
                if ($r->search) {
                    $search = $r->search;
                    $q->where(function($qq) use ($search) {
                        $qq->where('id', 'LIKE', "%{$search}%")  // Sample ID
                        ->orWhere('buyer_name', 'LIKE', "%{$search}%")
                        ->orWhere('style_no', 'LIKE', "%{$search}%")
                        ->orWhere('id', 'LIKE', "%{$search}%")
                        ->orWhere('company_name', 'LIKE', "%{$search}%")
                        ->orWhere('invoice_no', 'LIKE', "%{$search}%")
                        ->orWhere('order_no', 'LIKE', "%{$search}%")
                        ->orWhere('composition', 'LIKE', "%{$search}%")
                        ->orWhere('fabrication', 'LIKE', "%{$search}%")
                        ->orWhere('gsm', 'LIKE', "%{$search}%")
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

                if ($r->shipmnetStartDate || $r->shipmnetEndDate) {
                    $sfrom = $r->shipmnetStartDate ?: now()->format('Y-m-d');
                    $sto   = $r->shipmnetEndDate ?: now()->format('Y-m-d');

                    $q->whereDate('shipment_date', '>=', $sfrom)
                    ->whereDate('shipment_date', '<=', $sto);
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
        $totals = OrderDetails::whereNotIn('status', ['trash', 'temp'])
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN status = 'confirmed' THEN 1 END) AS confirmed")
            ->selectRaw("COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed")
            ->selectRaw("COUNT(CASE WHEN status = 'canceled' THEN 1 END) AS canceled")
            ->first();

        return view(adminTheme().'merchandising.orderDetails.index', compact('orderDetails', 'totals'));
    }

    public function orderDetailsAction(Request $r, $action, $id = null)
    {
        // CREATE SAMPLE
        if ($action == 'create') {
            $orderDetails = OrderDetails::where('status', 'temp')->where('addedby_id', Auth::id())->first();

            if (!$orderDetails) {
                $orderDetails = new OrderDetails();
                $orderDetails->status = 'temp';
                $orderDetails->addedby_id = Auth::id();
                $orderDetails->created_at = now();
                $orderDetails->save();
            }

            return redirect()->route('admin.orderDetailsAction', ['edit', $orderDetails->id]);
        }

        // FIND SAMPLE
        $orderDetails = OrderDetails::find($id);
        if (!$orderDetails) {
            session()->flash('error', 'Order Details Not Found');
            return redirect()->route('admin.orderDetails');
        }

        if (!in_array($orderDetails->status, ['temp', 'pending']) && $action == ['edit', 'delete', 'update-head']) {
            session()->flash('error', 'Order Details is already confirmed and cannot be edited or deleted.');
            return redirect()->route('admin.orderDetails');
        }

        // VIEW
        if ($action == 'view') {
            return view(adminTheme().'merchandising.orderDetails.view', compact('orderDetails'));
        }

        // ITEM CRUD (Add/Update/Remove)
        if (in_array($action, ['update-head'])) {

            if ($action == 'update-head') {

                $orderDetails = OrderDetails::find($id);
                if (!$orderDetails || !$r->field) return redirect()->back();

                $field = $r->field;
                $value = $r->value;

                // Buyer Update
                if ($field == 'buyer') {
                    if ($buyer = User::find($value)) {
                        $orderDetails->buyer_id = $buyer->id;
                        $orderDetails->buyer_name = $buyer->name;
                    }
                }
                // Merchant Update
                elseif ($field == 'merchant') {
                    if ($merchant = User::find($value)) {
                        $orderDetails->merchant_id = $merchant->id;
                        $orderDetails->merchant_name = $merchant->name;
                    }
                }
                // Style (unique check)
                elseif ($field == 'style_no') {
                    $existsStyle = OrderDetails::where('style_no', $value)
                                ->where('id', '!=', $orderDetails->id)
                                ->exists();

                    if ($existsStyle) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This style already exists',
                            'field' => 'style'
                        ]);
                    }
                    $orderDetails->style_no = $value;
                }
                // Other fields
                else {
                    $orderDetails->$field = $value;
                }

                $orderDetails->save();
            }

            return response()->json(['success' => true]);
        }


        // UPDATE SAMPLE
        if ($action == 'update') {
            $r->validate([
                'buyer' => 'required',
                'merchant' => 'required',
                'style_no' => 'required|string|max:255',
                'status' => 'required|string|max:20',
            ]);
            $buyer = User::find($r->buyer);
            $merchant = User::find($r->merchant);

            $existsStyle = OrderDetails::where('style_no', $r->style_no)->where('id', '<>', $orderDetails->id)->exists();

            if ($existsStyle) {
                session()->flash('info', 'This style already exists');
                return redirect()->back();  // Save unnecessary
            }

            $orderDetails->buyer_id      = $buyer->id ?? $orderDetails->buyer_id;
            $orderDetails->buyer_name    = $buyer->name ?? $orderDetails->buyer_name;
            $orderDetails->merchant_id   = $merchant->id ?? $orderDetails->merchant_id;
            $orderDetails->merchant_name = $merchant->name ?? $orderDetails->merchant_name;
            $orderDetails->status        = $r->status ?? $orderDetails->status;
            $orderDetails->save();

            session()->flash('success', 'Order Details Updated Successfully');
            return redirect()->route('admin.orderDetails');
        }

        // DELETE SAMPLE
        if ($action == 'delete') {
            $orderDetails->delete();
            session()->flash('success', 'Order Details Deleted Successfully');
            return redirect()->back();
        }

        // LOAD EDIT PAGE
        $items = $orderDetails->items;

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

        return view(adminTheme().'merchandising.orderDetails.edit', compact('orderDetails','items', 'buyers', 'merchandisers'));
    }

    public function proformaInvoice(Request $r)
    {
        // -----------------------------
        // QUERY PROFORMA INVOICES
        // -----------------------------
        $pis = ProformaInvoice::with(['buyer', 'merchant', 'user', 'items'])
            ->orderBy('id', 'desc')
            ->where('status', '<>', 'temp')
            ->when($r->search, function ($q) use ($r) {
                $search = $r->search;
                $q->where(function ($qq) use ($search) {
                    $qq->where('order_no', 'LIKE', "%{$search}%")
                        ->orWhere('remarks', 'LIKE', "%{$search}%")
                        ->orWhere('buyer_name', 'LIKE', "%{$search}%")
                        ->orWhere('merchant_name', 'LIKE', "%{$search}%");
                });
            })
            ->when($r->startDate || $r->endDate, function ($q) use ($r) {
                $from = $r->startDate ?: now()->format('Y-m-d');
                $to   = $r->endDate ?: now()->format('Y-m-d');
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->when($r->status, function ($q) use ($r) {
                $q->where('status', $r->status);
            })
            ->paginate(25)
            ->appends($r->all());

        // -----------------------------
        // TOTAL COUNTS
        // -----------------------------
        $totals = ProformaInvoice::selectRaw("COUNT(*) AS total")
            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN status = 'confirmed' THEN 1 END) AS confirmed")
            ->selectRaw("COUNT(CASE WHEN status = 'approved' THEN 1 END) AS approved")
            ->selectRaw("COUNT(CASE WHEN status = 'cancel' THEN 1 END) AS cancel")
            ->first();

        return view(
            adminTheme().'merchandising.pi.index',
            compact('pis', 'totals')
        );
    }


    public function proformaInvoiceAction(Request $r, $action, $id = null)
    {
        // -------------------------------
        // CREATE SAMPLE PI
        // -------------------------------
        if ($action == 'create') {
            $pi = ProformaInvoice::where('status', 'temp')->where('addedby_id', Auth::id())->first();
            if (!$pi) {
                $pi = new ProformaInvoice();
                $pi->status = 'temp';
                $pi->addedby_id = Auth::id();
            }
            $pi->created_at = now();
            $pi->save();
            return redirect()->route('admin.proformaInvoiceAction', ['edit', $pi->id]);
        }

        // -------------------------------
        // FIND PI
        // -------------------------------
        $pi = ProformaInvoice::with('items')->find($id);
        if (!$pi) {
            session()->flash('error', 'Proforma Invoice Not Found');
            return redirect()->route('admin.proformaInvoice');
        }

        // -------------------------------
        // VIEW PI
        // -------------------------------
        if ($action == 'view') {
            return view(adminTheme().'merchandising.pi.view', compact('pi'));
        }
        
        if ($action == 'invoice') {
            return view(adminTheme().'merchandising.pi.piInvoice', compact('pi'));
        }

        // -------------------------------
        // PO SELECT via AJAX
        // -------------------------------
        if ($action == 'po-select') {
            $orders = OrderDetails::where('order_no', $r->order_no)->get()->makeHidden(['id']);
            $orders = $orders->map(function ($o) {
                unset($o->id);
                return $o;
            });

            if (count($orders) < 1) {
                return response()->json([
                    'success' => false,
                    'html' => '<tr><td colspan="10" class="text-center">Order not found</td></tr>'
                ]);
            }

            // Render items partial
            $html = view(adminTheme().'merchandising.pi.includes.items', [
                'order' => $orders->first(),
                'items' => $orders
            ])->render();

            return response()->json(['success' => true, 'html' => $html, 'order' => $orders->first()]);
        }

        // -------------------------------
        // UPDATE PI
        // -------------------------------
        if ($action == 'update') {

            /* ---------------------------
                VALIDATION
            --------------------------- */
            $r->validate([
                'order_no' => 'required|string',
                'status' => 'required|string',
                'remarks' => 'nullable|string',

                'items' => 'required|array',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.total_price' => 'required|numeric|min:0',
                'items.*.commission' => 'nullable|numeric|min:0',
                'items.*.total_commission' => 'required|numeric|min:0',
                'items.*.color_qty' => 'required|numeric|min:0',
                'items.*.commission_type' => 'required|string',
            ],[
                'order_no.required' => 'Order number is required',
                'items.*.unit_price.required' => 'Unit price is required',
                'items.*.color_qty.required' => 'Color quantity is required',
                'items.*.commission_type.required' => 'Commission type must be selected',
            ]);


            /* ---------------------------
                FETCH ORDER INFO
            --------------------------- */
            $order = OrderDetails::firstWhere('order_no', $r->order_no);

            if (!$order) {
                return back()->with('error', 'Order not found for this Order No.');
            }


            /* ---------------------------
                CALCULATE TOTALS
            --------------------------- */
            $total_qty = collect($r->items)->sum('color_qty');
            $total_bill = collect($r->items)->sum('total_price');
            $total_commission = collect($r->items)->sum('total_commission');


            /* ---------------------------
                UPDATE MAIN PI
            --------------------------- */
            $pi->order_no = $r->order_no;
            $pi->buyer_id = $order->buyer_id;
            $pi->buyer_name = $order->buyer_name;
            $pi->merchant_name = $order->merchant_name;
            $pi->merchant_id = $order->merchant_id;
            $pi->remarks = $r->remarks;
            $pi->status = $r->status;
            $pi->editedby_id = auth()->id();

            $pi->total_qty = $total_qty;
            $pi->total_bill = $total_bill;
            $pi->total_commission = $total_commission;

            $pi->save();


            /* ---------------------------
                ITEMS UPDATE / CREATE
            --------------------------- */
            if ($r->has('items')) {
                foreach ($r->items as $itemData) {
                    // Update existing item
                    if (!empty($itemData['id']) && ($itemData['method'] ?? '') == 'update') {

                        $item = ProformaInvoiceItem::find($itemData['id']);

                        if ($item) {
                            $item->update([
                                'unit_price' => $itemData['unit_price'],
                                'total_price' => $itemData['total_price'],
                                'commission_type' => $itemData['commission_type'],
                                'commission' => $itemData['commission'],
                                'total_commission' => $itemData['total_commission'],
                                'color_qty' => $itemData['color_qty'],
                                'shipment_date' => $itemData['shipment_date'],
                                'editedby_id' => auth()->id(),
                            ]);
                            orderDetails::firstWhere('style_no', $item->style_no)->update(['total_bill'=> ($item->total_price - $item->total_commission) ]);
                        }

                    } else {
                        // Create new item
                        ProformaInvoiceItem::create([
                            'proforma_invoice_id' => $pi->id,
                            'composition' => $itemData['composition'],
                            'fabrication' => $itemData['fabrication'],
                            'gsm' => $itemData['gsm'] ?? null,
                            'style_no' => $itemData['style_no'],
                            'color_name' => $itemData['color_name'],
                            'color_qty' => $itemData['color_qty'],
                            'unit_price' => $itemData['unit_price'],
                            'total_price' => $itemData['total_price'],
                            'commission_type' => $itemData['commission_type'],
                            'commission' => $itemData['commission'],
                            'total_commission' => $itemData['total_commission'],
                            'shipment_date' => $itemData['shipment_date'],
                            'addedby_id' => auth()->id(),
                        ]);
                        orderDetails::firstWhere('style_no', $itemData['style_no'])->update(['total_bill'=> ( $itemData['total_price'] - $itemData['total_commission']) ]);
                    }
                }
            }


            /* ---------------------------
                SUCCESS
            --------------------------- */
            session()->flash('success', 'Proforma Invoice Updated Successfully');
            return redirect()->route('admin.proformaInvoiceAction', ['invoice', $pi->id]);
        }

        if($action=='delete'){
            $pi->items()->delete();
            $pi->delete();
            Session()->flash('success','Proforma Invoice Deleted Successfully');
            return redirect()->back();
        }


        // -------------------------------
        // LOAD EDIT PAGE
        // -------------------------------
        $orders = OrderDetails::where('status', 'confirmed')->get()->unique('order_no');
        $items = $pi->items;

        return view(adminTheme().'merchandising.pi.edit', compact('pi', 'items', 'orders'));
    }



}
