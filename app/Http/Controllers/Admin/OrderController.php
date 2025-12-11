<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Media;
use App\Models\Order;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\OrderItem;
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
    public function products(Request $r)
    {
        // Bulk actions
        if ($r->action && $r->checkid) {
            $products = Product::whereIn('id', $r->checkid)->get();

            foreach ($products as $p) {
                if ($r->action == 1) { // Activate
                    $p->status = 1;
                    $p->save();
                } elseif ($r->action == 2) { // Deactivate
                    $p->status = 0;
                    $p->save();
                } elseif ($r->action == 5) { // Delete
                    $p->delete();
                }
            }

            Session()->flash('success', 'Action completed successfully!');
            return redirect()->back();
        }

        // Product list with search/filter
        $products = Product::latest()
            ->where(function ($q) use ($r) {
                if ($r->search) {
                    $q->where('sku', 'LIKE', '%' . $r->search . '%')
                        ->orWhere('name', 'LIKE', '%' . $r->search . '%');
                }
                if ($r->status) {
                    $q->where('status', $r->status == 'inactive' ? 0 : 1);
                }
            })
            ->paginate(25)
            ->appends($r->all());

        // Master data for dropdowns
        $styles = Attribute::where('type', 14)->where('status', 'active')->get(); // style
        $sizes = Attribute::where('type', 11)->where('status', 'active')->get();  // size
        $fabrics = Attribute::where('type', 13)->where('status', 'active')->get(); // fabric
        $colors = Attribute::where('type', 12)->where('status', 'active')->get(); // color

        return view(adminTheme() . 'orders.products.product', compact('products', 'styles', 'sizes', 'fabrics', 'colors'));
    }

    // Create Product
    public function productsAction(Request $r, $action, $id = null)
    {
        if ($action == 'create' && $r->isMethod('post')) {
            $r->validate([
                'sku' => 'required|max:50|unique:products,sku',
                'name' => 'required|max:100',
                'price' => 'nullable|numeric',
            ]);

            $product = new Product();
            $product->sku = $r->sku;
            $product->name = $r->name;
            $product->style_id = $r->style_id;
            $product->size_id = $r->size_id;
            $product->fabric_id = $r->fabric_id;
            $product->color_id = $r->color_id;
            $product->price = $r->price ?: 0;
            $product->status = 1;
            $product->addedby_id = auth()->id();
            $product->save();

            Session()->flash('success', 'Product created successfully!');
            return redirect()->back();
        }

        // Edit product view
        $product = Product::find($id);
        if (!$product) {
            Session()->flash('error', 'Product not found!');
            return redirect()->back();
        }

        // Update product
        if ($action == 'update' && $r->isMethod('post')) {
            $r->validate([
                'sku' => 'required|max:50|unique:products,sku,' . $product->id,
                'name' => 'required|max:100',
                'price' => 'nullable|numeric',
            ]);

            $product->sku = $r->sku;
            $product->name = $r->name;
            $product->style_id = $r->style_id;
            $product->size_id = $r->size_id;
            $product->fabric_id = $r->fabric_id;
            $product->color_id = $r->color_id;
            $product->price = $r->price ?: 0;
            $product->status = $r->status ? 1 : 0;
            $product->save();

            Session()->flash('success', 'Product updated successfully!');
            return redirect()->back();
        }

        // Delete product
        if ($action == 'delete') {
            $product->delete();
            Session()->flash('success', 'Product deleted successfully!');
            return redirect()->back();
        }

        return view(adminTheme() . 'products.edit', compact('product'));
    }


    public function orders(Request $r)
    {
        // BULK ACTIONS
        if ($r->action && $r->checkid) {

            $orders = Order::whereIn('id', $r->checkid)->get();

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
        $orders = Order::orderBy('id', 'desc')
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

        return view(adminTheme().'orders.index', compact('orders','totals'));
    }

    public function ordersAction(Request $r, $action, $id = null)
    {
        // CREATE NEW ORDER
        if ($action == 'create') {

            $order = Order::where('order_status','temp')
                    ->where('addedby_id',Auth::id())
                    ->first();

            if (!$order) {
                $order = new Order();
                $order->order_status = 'temp';
                $order->addedby_id = Auth::id();
                $order->created_at = now();
                $order->save();
            }

            $order->currency = general()->currency;
            $order->order_no = now()->format('ymd') . $order->id;
            $order->save();

            return redirect()->route('admin.ordersAction',['edit',$order->id]);
        }

        // LOAD ORDER
        $order = Order::find($id);
        if (!$order) {
            session()->flash('error','Order Not Found');
            return redirect()->route('admin.orders');
        }

        // PDF
        if ($action == 'pdf') {
            $pdf = PDF::loadView(adminTheme().'pdfOrder', compact('order'));
            return $pdf->stream('garment_order.pdf');
        }

        // VIEW
        if ($action == 'view') {
            return view(adminTheme().'orders.view', compact('order'));
        }

        // ADD BUYER
        if ($action == 'add-buyer') {

            $buyer = User::find($r->buyer_id);

            if ($buyer) {
                $order->buyer_id = $buyer->id;
                $order->save();
            }

            $view = view(adminTheme().'orders.includes.items', compact('order'))->render();
            return response()->json(['success'=>true,'view'=>$view]);
        }

        // SEARCH PRODUCT
        if ($action == 'search-item') {

            $products = Attribute::where('type',13)
                ->where('status','active')
                ->when($r->search, fn($q)=>$q->where('name','like',"%$r->search%"))
                ->limit(20)
                ->get();

            $search = view(adminTheme().'orders.includes.searchProducts', compact('products','order'))->render();
            return response()->json(['success'=>true,'view'=>$search]);
        }

        // ITEM CRUD
        if (in_array($action,['add-item','update-item','remove-item'])) {

            if ($action == 'add-item') {
                $item = new OrderItem();
                $item->order_id = $order->id;
                $item->addedby_id = Auth::id();
                $item->save();
            }

            if ($action == 'update-item') {

                $item = OrderItem::find($r->item_id);
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
                OrderItem::where('id',$r->item_id)->delete();
            }

            $products = Attribute::where('type',13)->where('status','active')->limit(20)->get();
            $view = view(adminTheme().'orders.includes.items', compact('products','order'))->render();
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
            return redirect()->route('admin.ordersAction',['view',$order->id]);
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
        $products = Attribute::where('type',13)->where('status','active')->limit(20)->get();

        return view(adminTheme().'orders.edit', compact('order','buyers','products'));
    }





}
