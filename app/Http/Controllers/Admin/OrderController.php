<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Media;
use App\Models\Attribute;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
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
                        ->orWhere('mobile', 'LIKE', '%' . $r->search . '%');
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
            ->select(['id', 'name', 'email', 'mobile', 'created_at', 'company_name', 'address_line1', 'addedby_id', 'status'])
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

        return view(adminTheme().'orders.buyers.users', compact('users', 'total'));
    }

    public function buyersAction(Request $r, $action, $id = null)
    {
        // Add New Buyer Start
        if ($action == 'create' && $r->isMethod('post')) {

            $check = $r->validate([
                'name' => 'required|max:100',
                'company_name' => 'nullable|max:100',
                'email_mobile' => 'required|max:100',
                'address' => 'nullable|max:500',
            ]);

            $user = User::where('email', $r->email_mobile)
                ->orWhere('mobile', $r->email_mobile)
                ->first();

            if ($user) {
                $user->buyer = true;
                $user->customer = false;
                $user->save();
                Session()->flash('success', 'User found! Now marked as Buyer.');
            } else {

                $password = Str::random(8);

                $user = new User();
                $user->name = $r->name;

                if (filter_var($r->email_mobile, FILTER_VALIDATE_EMAIL)) {
                    $user->email = $r->email_mobile;
                } else {
                    $user->mobile = $r->email_mobile;
                }

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

            return view(adminTheme().'orders.buyers.viewUser', compact('user', 'orders', 'transactions', 'accountMethods', 'paymentMethods'));
        }


        // Update Buyer
        if ($action == 'update' && $r->isMethod('post')) {

            $check = $r->validate([
                'name' => 'required|max:100',
                'email' => 'nullable|max:100|unique:users,email,' . $user->id,
                'mobile' => 'required|max:20|unique:users,mobile,' . $user->id,
                'address' => 'nullable|max:200',
                'company_name' => 'nullable|max:200',
                'created_at' => 'nullable|date|max:50',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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


        return view(adminTheme().'orders.buyers.editUser', compact('user'));
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

        return view(adminTheme().'orders.master-data.'.$view, compact('data','report'));
    }


    public function garmentsOrders(Request $r)
    {
        // BULK ACTIONS
        if ($r->action && $r->checkid) {

            $orders = GarmentOrder::whereIn('id', $r->checkid)->get();

            foreach ($orders as $data) {
                switch ($r->action) {
                    case 1: $data->status = 'pending'; break;
                    case 2: $data->status = 'approved'; break;
                    case 3: $data->status = 'rejected'; break;
                    case 4: $data->status = 'trash'; break;

                    case 5:
                        $data->items()->delete();
                        $data->delete();
                        continue 2;
                }
                $data->save();
            }

            session()->flash('success', 'Action Completed Successfully!');
            return redirect()->back();
        }

        // LIST + FILTERS
        $orders = GarmentOrder::orderBy('id', 'desc')
            ->where('status', '<>', 'temp')

            ->where(function($q) use ($r){

                // SEARCH
                if ($r->search) {
                    $search = $r->search;

                    $q->where(function($qq) use ($search){
                        $qq->where('order_no', 'LIKE', "%{$search}%")
                            ->orWhereHas('buyer', function($b) use ($search){
                                $b->where('users.buyer', 1)
                                ->where('users.name','LIKE',"%{$search}%");
                            });
                    });
                }

                // DATE RANGE
                if ($r->startDate || $r->endDate) {
                    $from = $r->startDate ?: now()->format('Y-m-d');
                    $to   = $r->endDate   ?: now()->format('Y-m-d');

                    $q->whereDate('created_at','>=',$from)
                    ->whereDate('created_at','<=',$to);
                }

                // STATUS FILTER
                if ($r->status) {
                    $q->where('status', $r->status);
                } else {
                    $q->where('status','<>','trash');
                }
            })

            ->paginate(25)
            ->appends($r->all());


        // TOTAL COUNTS
        $totals = DB::table('garment_orders')->where('status','<>','temp')
            ->selectRaw("count(case when status != 'trash' then 1 end) as total")
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when status = 'approved' then 1 end) as approved")
            ->selectRaw("count(case when status = 'rejected' then 1 end) as rejected")
            ->selectRaw("count(case when status = 'trash' then 1 end) as trash")
            ->first();

        return view(adminTheme().'garments.orders.index', compact('orders','totals'));
    }

    public function garmentsOrdersAction(Request $r, $action, $id = null)
    {
        // CREATE NEW ORDER
        if ($action == 'create') {

            $order = GarmentOrder::where('status','temp')
                    ->where('addedby_id',Auth::id())
                    ->first();

            if (!$order) {
                $order = new GarmentOrder();
                $order->status = 'temp';
                $order->addedby_id = Auth::id();
                $order->created_at = now();
                $order->save();
            }

            $order->currency = general()->currency;
            $order->order_no = now()->format('ymd') . $order->id;
            $order->save();

            return redirect()->route('admin.garmentsOrdersAction',['edit',$order->id]);
        }

        // LOAD ORDER
        $order = GarmentOrder::find($id);
        if (!$order) {
            session()->flash('error','Order Not Found');
            return redirect()->route('admin.garmentsOrders');
        }

        // PDF
        if ($action == 'pdf') {
            $pdf = PDF::loadView(adminTheme().'garments.pdfOrder', compact('order'));
            return $pdf->stream('garment_order.pdf');
        }

        // VIEW
        if ($action == 'view') {
            return view(adminTheme().'garments.orders.view', compact('order'));
        }

        // ADD BUYER
        if ($action == 'add-buyer') {

            $buyer = Buyer::find($r->buyer_id);

            if ($buyer) {
                $order->buyer_id = $buyer->id;
                $order->save();
            }

            $view = view(adminTheme().'garments.orders.includes.items', compact('order'))->render();
            return response()->json(['success'=>true,'view'=>$view]);
        }

        // SEARCH PRODUCT
        if ($action == 'search-item') {

            $products = Product::where('type',3)
                ->where('status','active')
                ->when($r->search, fn($q)=>$q->where('name','like',"%$r->search%"))
                ->limit(20)
                ->get();

            $search = view(adminTheme().'garments.orders.includes.searchProducts', compact('products','order'))->render();
            return response()->json(['success'=>true,'view'=>$search]);
        }

        // ITEM CRUD
        if (in_array($action,['add-item','update-item','remove-item'])) {

            if ($action == 'add-item') {
                $item = new GarmentOrderItem();
                $item->order_id = $order->id;
                $item->addedby_id = Auth::id();
                $item->save();
            }

            if ($action == 'update-item') {

                $item = GarmentOrderItem::find($r->item_id);
                if ($item) {

                    if ($r->name == 'product_name')
                        $item->product_name = $r->data ?: null;

                    if ($r->name == 'qty')
                        $item->qty = $r->data ?: 0;

                    if ($r->name == 'unit')
                        $item->unit = $r->data ?: null;

                    if ($r->name == 'price')
                        $item->price = $r->data ?: 0;

                    $item->total_price = $item->qty * $item->price;
                    $item->save();

                    // UPDATE ORDER TOTALS
                    $order->grand_total = $order->items()->sum('total_price');
                    $order->total_qty   = $order->items()->sum('qty');
                    $order->due_amount  = $order->grand_total - $order->paid_amount;
                    $order->save();
                }
            }

            if ($action == 'remove-item') {
                GarmentOrderItem::where('id',$r->item_id)->delete();
            }

            $products = Product::where('type',3)->where('status','active')->limit(20)->get();
            $view = view(adminTheme().'garments.orders.includes.items', compact('products','order'))->render();
            return response()->json(['success'=>true,'view'=>$view]);
        }

        // UPDATE ORDER
        if ($action == 'update') {

            $r->validate([
                'buyer_id' => 'required|numeric',
                'status'   => 'required|max:20',
                'currency' => 'required|max:5',
                'created_at' => 'required|date',
                'note'     => 'nullable',
            ]);

            $buyer = User::where('buyer',1)->find($r->buyer_id);
            if (!$buyer) {
                session()->flash('error','Buyer Not Found');
                return back();
            }

            $order->buyer_id       = $buyer->id;
            $order->currency       = $r->currency;
            $order->buyer_name     = $buyer->name;
            $order->buyer_email    = $buyer->email;
            $order->buyer_mobile   = $buyer->mobile;
            $order->buyer_address  = $buyer->address;

            $order->status         = $r->status;
            $order->note           = $r->note;

            $createDate = $r->created_at
                ? Carbon::parse($r->created_at . ' ' . now()->format('H:i:s'))
                : Carbon::now();

            if (!$createDate->isSameDay($order->created_at))
                $order->created_at = $createDate;

            $order->save();

            session()->flash('success','Garment Order Updated');
            return redirect()->route('admin.garmentsOrdersAction',['view',$order->id]);
        }

        // DELETE ORDER
        if ($action == 'delete') {
            $order->items()->delete();
            $order->delete();

            session()->flash('success','Garment Order Deleted');
            return redirect()->back();
        }

        // EDIT PAGE
        $buyers = User::where('buyer',1)->get(['id','name']);
        $products = Product::where('type',3)->where('status','active')->limit(20)->get();

        return view(adminTheme().'garments.orders.edit', compact('order','buyers','products'));
    }





}
