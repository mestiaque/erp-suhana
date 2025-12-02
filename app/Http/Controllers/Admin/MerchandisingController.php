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
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function proformaInvoice(Request $r)
    {
        // -----------------------------
        // QUERY SAMPLES
        // -----------------------------
        $samples = Sample::orderBy('id', 'desc')
            ->whereIn('status', ['confirmed', 'completed'])
            ->where(function($q) use ($r) {

                // SEARCH
                if ($r->search) {
                    $search = $r->search;
                    $q->where(function($qq) use ($search) {
                        $qq->where('id', 'LIKE', "%{$search}%")  // Sample ID
                        ->orWhere('buyer_name', 'LIKE', "%{$search}%")
                        ->orWhere('style', 'LIKE', "%{$search}%")
                        ->orWhere('id', 'LIKE', "%{$search}%")
                        ->orWhere('merchant_name', 'LIKE', "%{$search}%")
                        ->orWhere('invoice_no', 'LIKE', "%{$search}%")
                        ->orWhere('bin_number', 'LIKE', "%{$search}%")
                        ->orWhere('job_number', 'LIKE', "%{$search}%");
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
        $totals = Sample::whereIn('status', ['confirmed','completed'])
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("COUNT(CASE WHEN pi_status = 'pending' THEN 1 END) AS pending")
            ->selectRaw("COUNT(CASE WHEN pi_status = 'confirmed' THEN 1 END) AS confirmed")
            ->selectRaw("COUNT(CASE WHEN pi_status = 'approved' THEN 1 END) AS approved")
            ->selectRaw("COUNT(CASE WHEN pi_status = 'cancelled' THEN 1 END) AS cancelled")
            ->first();

        return view(adminTheme().'merchandising.pi.index', compact('samples', 'totals'));
    }

    public function proformaInvoiceAction(Request $r, $action, $id = null)
    {
        // FIND SAMPLE
        $sample = Sample::find($id);
        if (!$sample) {
            session()->flash('error', 'PI Not Found');
            return redirect()->route('admin.proformaInvoice');
        }

        if (!in_array($sample->pi_status, ['pending', 'confirmed']) && $action == ['edit', 'update-item', 'update-head']) {
            session()->flash('error', 'PI is already confirmed and cannot be edited or deleted.');
            return redirect()->route('admin.proformaInvoice');
        }

        // VIEW
        if ($action == 'view') {
            return view(adminTheme().'merchandising.pi.view', compact('sample'));
        }

        // ITEM CRUD (Add/Update/Remove)
        if (in_array($action, ['add-item', 'update-item', 'remove-item', 'update-head'])) {

            if ($action == 'update-head') {
                $sample = Sample::find($id);
                if (!$sample || !$r->field) return redirect()->back();
                $field = $r->field;
                $value = $r->value;
                $sample->$field = $value;
                $sample->save();
            }

            if ($action == 'update-item') {
                $item = SampleItem::find($r->item_id);
                if ($item) {
                    if ($item && $r->field) {
                        $field = $r->field;
                        $item->$field = $r->value;
                        $item->amount = $item->unit_price*$item->quantity;
                        $item->save();
                        if($r->field == 'unit_price' || $r->field == 'discount'){
                            $totalAmount = $sample?->items?->sum('amount') ?? 0;
                            $totalDiscount = $sample?->items?->sum('discount') ?? 0;
                            $sample->update([ 'total_bill'=> ($totalAmount-$totalDiscount) ]);
                        }
                    }
                    $item->save();
                }
            }

            $items = $sample->items;
            $view = view(adminTheme().'merchandising.pi.includes.items', compact('sample','items'))->render();
            return response()->json(['success' => true, 'view' => $view]);
        }

        // UPDATE SAMPLE
        if ($action == 'update') {
            $r->validate([
                'pi_status' => 'required|string|max:20',
            ]);
            $buyer = User::find($r->buyer);

            $sample->pi_status = $r->status ?? $sample->pi_status;
            $sample->save();

            session()->flash('success', 'PI Updated Successfully');
            return redirect()->route('admin.proformaInvoiceAction', ['view', $sample->id]);
        }

        // LOAD EDIT PAGE
        $items = $sample->items;

        return view(adminTheme().'merchandising.pi.edit', compact('sample','items'));
    }



}
