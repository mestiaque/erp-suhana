<?php

namespace App\Http\Controllers\Admin;

use DB;
use Str;
use Auth;
use File;
use Image;
use Session;
use Validator;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Media;
use App\Models\Order;
use Redirect,Response;
use App\Models\Attribute;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisition;
use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisitionItem;

class PurchasesController extends Controller
{

    //Purchase Items Functions Start
    public function purchasesItems(Request $r){

      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Post::latest()->where('type',3)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

            if($r->action==1){
              $data->status='active';
              $data->save();
            }elseif($r->action==2){
              $data->status='inactive';
              $data->save();
            }elseif($r->action==5){

              $medias =Media::latest()->where('src_type',3)->where('src_id',$data->id)->get();
              foreach($medias as $media){
                if(File::exists($media->file_url)){
                  File::delete($media->file_url);
                }
                $media->delete();
              }

              $data->delete();
            }

        }

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }

        return redirect()->back();
      }

      //Filter Action End

      $goodsItems=Post::latest()->where('type',3)->where('status','<>','temp')
        ->where(function($q) use ($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status);
          }

      })
      ->select(['id','name','slug','type','description','category_id','unit_id','created_at','addedby_id','status','fetured'])
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      //Total Count Results
      $report = Post::where('type',3)->where('status','<>','temp')
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();
      $units =Attribute::latest()->where('type',6)->where('status','active')->select(['id','name'])->get();
      $categories =Attribute::where('type',7)->where('status','active')->select(['id','name'])->get();
      return view(adminTheme().'purchases-items.goodsItems',compact('goodsItems','units','categories','report'));

    }

    public function purchasesItemsAction(Request $r,$action,$id=null){
      // Add Item Unit Action
      if($action=='addUnit' || $action=='deleteUnit' || $action=='updateUnit'){

        if($action=='addUnit'){

          $hasUnit =Attribute::where('type',6)->where('name',$r->name)->first();
          if($hasUnit){
            return Response::json(['status'=>'error','message'=>'This Unit Name Already Exists']);
          }
          $unit =new Attribute();
          $unit->name=$r->name;
          $unit->description=$r->description;
          $unit->type =6;
          $unit->status ='active';
          $unit->addedby_id =Auth::id();
          $unit->save();

          $slug =Str::slug($r->name);
          if($slug==null){
            $unit->slug=$unit->id;
          }else{
            if(Attribute::where('type',6)->where('slug',$slug)->where('id','<>',$unit->id)->count() >0){
            $unit->slug=$slug.'-'.$unit->id;
            }else{
            $unit->slug=$slug;
            }
          }
          $unit->save();
        }

        if($action=='updateUnit'){
          $unit =Attribute::where('type',6)->find($id);
          if($unit){

          $hasUnit =Attribute::where('type',6)->where('id','<>',$unit->id)->where('name',$r->name)->first();
          if($hasUnit){
            return Response::json(['status'=>'error','message'=>'This Unit Name Already Exists']);
          }


            $unit->name=$r->name;
            $unit->description=$r->description;
            $unit->editedby_id =Auth::id();
            $slug =Str::slug($r->name);
            if($slug==null){
              $unit->slug=$unit->id;
            }else{
              if(Attribute::where('type',6)->where('slug',$slug)->where('id','<>',$unit->id)->count() >0){
              $unit->slug=$slug.'-'.$unit->id;
              }else{
              $unit->slug=$slug;
              }
            }
            $unit->save();
          }
        }

        if($action=='deleteUnit'){
          $unit =Attribute::where('type',6)->find($id);
          if($unit){
            $unit->delete();
          }
        }
        $units =Attribute::latest()->where('type',6)->where('status','active')->select(['id','name'])->get();
        $view =view(adminTheme().'purchases-items.includes.unitsTable',compact('units'))->render();
        return Response::json(['status'=>'success','view'=>$view]);
      }

      // Add Item Category Action
      if($action=='addCtg' || $action=='deleteCtg' || $action=='updateCtg'){

        if($action=='addCtg'){

          $hasCtg =Attribute::where('type',7)->where('name',$r->name)->first();
          if($hasCtg){
            return Response::json(['status'=>'error','message'=>'This Category Name Already Exists']);
          }
          $ctg =new Attribute();
          $ctg->name=$r->name;
          $ctg->description=$r->description;
          $ctg->type =7;
          $ctg->status ='active';
          $ctg->addedby_id =Auth::id();
          $ctg->save();

          $slug =Str::slug($r->name);
          if($slug==null){
            $ctg->slug=$ctg->id;
          }else{
            if(Attribute::where('type',7)->where('slug',$slug)->where('id','<>',$ctg->id)->count() >0){
            $ctg->slug=$slug.'-'.$ctg->id;
            }else{
            $ctg->slug=$slug;
            }
          }
          $ctg->save();
        }

        if($action=='updateCtg'){
          $ctg =Attribute::where('type',7)->find($id);
          if($ctg){

          $hasCtg =Attribute::where('type',7)->where('id','<>',$ctg->id)->where('name',$r->name)->first();
          if($hasCtg){
            return Response::json(['status'=>'error','message'=>'This Category Name Already Exists']);
          }
            $ctg->name=$r->name;
            $ctg->description=$r->description;
            $ctg->editedby_id =Auth::id();
            $slug =Str::slug($r->name);
            if($slug==null){
              $ctg->slug=$ctg->id;
            }else{
              if(Attribute::where('type',7)->where('slug',$slug)->where('id','<>',$ctg->id)->count() >0){
              $ctg->slug=$slug.'-'.$ctg->id;
              }else{
              $ctg->slug=$slug;
              }
            }
            $ctg->save();
          }
        }

        if($action=='deleteCtg'){
          $ctg =Attribute::where('type',7)->find($id);
          if($ctg){
            $ctg->delete();
          }
        }

        $categories =Attribute::where('type',7)->where('status','active')->select(['id','name'])->get();
        $view =view(adminTheme().'purchases-items.includes.categoryTable',compact('categories'))->render();
        return Response::json(['status'=>'success','view'=>$view]);

      }

      // Add Item Action Start
      if($action=='create'){
        $check = $r->validate([
            'name' => 'required|max:100',
            'unit_id' => 'nullable|numeric',
            'category_id' => 'nullable|numeric',
            'description' => 'nullable|max:1000',
        ]);

        $item =Post::where('type',3)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$item){
          $item =new Post();
        }

        $item->name=$r->name;
        $item->unit_id=$r->unit_id;
        $item->category_id=$r->category_id;
        $item->description=$r->description;
        $item->type =3;
        $item->status ='active';
        $item->addedby_id =Auth::id();
        $item->save();

         $slug =Str::slug($r->name);
         if($slug==null){
          $item->slug=$item->id;
         }else{
          if(Post::where('type',3)->where('slug',$slug)->where('id','<>',$item->id)->count() >0){
          $item->slug=$slug.'-'.$item->id;
          }else{
          $item->slug=$slug;
          }
        }
        $item->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }
      // Add Designation Action End

      $item =Post::where('type',3)->find($id);
      if(!$item){
        Session()->flash('error','This Item Are Not Found');
        return redirect()->route('admin.purchasesItems');
      }

      //Check Authorized User
      // $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
      // if($allPer && $designation->addedby_id!=Auth::id()){
      //   Session()->flash('error','You are unauthorized Try!!');
      //   return redirect()->route('admin.purchasesItems');
      // }

      // Update Designation Action Start
      if($action=='update'){

          $check = $r->validate([
              'name' => 'required|max:191',
              'unit_id' => 'nullable|numeric',
              'category_id' => 'nullable|numeric',
              'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);

          $item->name=$r->name;
          $item->unit_id=$r->unit_id;
          $item->category_id=$r->category_id;
          $item->description=$r->description;

           ///////Image UploadStart////////////

            if($r->hasFile('image')){
              $file =$r->image;
              $src  =$item->id;
              $srcType  =3;
              $fileUse  =1;
              $author=Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author);
            }

            ///////Image Upload End////////////

             $slug =Str::slug($r->name);
             if($slug==null){
              $item->slug=$item->id;
             }else{
              if(Post::where('type',3)->where('slug',$slug)->where('id','<>',$item->id)->count() >0){
              $item->slug=$slug.'-'.$item->id;
              }else{
              $item->slug=$slug;
              }
            }
            $item->status =$r->status?'active':'inactive';
            $item->fetured =$r->fetured?1:0;
            $item->editedby_id =Auth::id();
            $item->save();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();

      }
      // Update Designation Action Start

      // Delete Designation Action Start
      if($action=='delete'){
          $medias =Media::latest()->where('src_type',1)->where('src_id',$item->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }

            $item->delete();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->route('admin.purchasesItems');
      }
      // Delete Designation Action End

      return redirect()->back();

    }




    public function purchasesStocks(Request $r){



        return view(adminTheme().'purchases-items.purchasesStocks');
    }



    public function purchasesRequisitions(Request $r)
    {
        // Permission Check
        $permissions = json_decode(Auth::user()->permission->permission, true)['purchases'] ?? [];

        // Bulk Actions
        if($r->action && $r->checkid){
            $datas = PurchaseRequisition::whereIn('id',$r->checkid)->get();

            foreach($datas as $data){
                switch($r->action){
                    case 1: $data->status='pending'; break;
                    case 2: $data->status='approved'; break;
                    case 3: $data->status='rejected'; break;
                    case 4: $data->status='trash'; break;
                    case 5:
                        $data->items()->delete();
                        $data->delete();
                        continue 2; // skip save for deleted
                }
                $data->save();
            }

            Session()->flash('success','Action Successfully Completed!');
            return redirect()->back();
        }

        // Fetch Requisitions
        $requisitions = PurchaseRequisition::latest()
            ->where(function($q) use($r){
                if($r->search){
                    $q->where('requisition_no','LIKE','%'.$r->search.'%');
                    $q->orWhereHas('supplier', function($qq) use($r){
                        $qq->where('factory_name','LIKE','%'.$r->search.'%');
                    });
                }
                if($r->startDate || $r->endDate){
                    $from = $r->startDate ?: Carbon::now()->format('Y-m-d');
                    $to = $r->endDate ?: Carbon::now()->format('Y-m-d');
                    $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                }
                if($r->status){
                    $q->where('status',$r->status);
                }else{
                    $q->where('status','<>','trash');
                }
            })
            ->paginate(25)
            ->appends([
                'search'=>$r->search,
                'status'=>$r->status,
                'startDate'=>$r->startDate,
                'endDate'=>$r->endDate,
            ]);

        // Total Counts
        $totals = DB::table('purchase_requisitions')
            ->selectRaw("count(case when status != 'trash' then 1 end) as total")
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when status = 'approved' then 1 end) as approved")
            ->selectRaw("count(case when status = 'rejected' then 1 end) as rejected")
            ->selectRaw("count(case when status = 'trash' then 1 end) as trash")
            ->first();

        return view(adminTheme().'purchases.requisitions.index', compact('requisitions','totals'));
    }

    public function purchasesRequisitionsAction(Request $r, $action, $id=null)
    {
        // Create Requisition
        if($action=='create'){
            $requisition = PurchaseRequisition::where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$requisition){
                $requisition = new PurchaseRequisition();
                $requisition->status='temp';
                $requisition->addedby_id = Auth::id();
                $requisition->created_date = now()->format('Ymd');
                $requisition->save();
            }
            $requisition->requisition_no = Carbon::now()->format('Ymd').$requisition->id;
            $requisition->save();

            return redirect()->route('admin.purchasesRequisitionsAction',['edit',$requisition->id]);
        }

        // Fetch Requisition
        $requisition = PurchaseRequisition::find($id);
        if(!$requisition){
            Session()->flash('error','Requisition Not Found');
            return redirect()->route('purchasesRequisitions');
        }

        // View PDF
        if($action=='pdf'){
            $pdf = PDF::loadView(adminTheme().'purchases.pdfRequisition', compact('requisition'));
            return $pdf->stream('requisition.pdf');
        }

        // View Requisition
        if($action=='view'){
            return view(adminTheme().'purchases.requisitions.view', compact('requisition'));
        }

        // Add / Search Supplier
        if($action=='add-supplier'){
            $supplier = Supplier::find($r->supplier_id);
            if($supplier){
                $requisition->supplier_id = $supplier->id;
                $requisition->save();
            }
            $view = view(adminTheme().'purchases.includes.requisitionItems', compact('requisition'))->render();
            return response()->json(['success'=>true, 'view'=>$view]);
        }

        // Search Item
        if($action=='search-item'){
            $items = Post::where('type',3)->where('status','active')
                ->when($r->search, function($q) use($r){
                    $q->where('name','like','%'.$r->search.'%');
                })->limit(10)->get();

            $search = view(adminTheme().'purchases.includes.searchItems', compact('items','requisition'))->render();
            return response()->json(['success'=>true, 'view'=>$search]);
        }

        // Add / Update / Remove Items
        if(in_array($action,['add-item','update-item','remove-item'])){
            if($action=='add-item'){
                $item = new PurchaseRequisitionItem();
                $item->requisition_id = $requisition->id;
                $item->addedby_id = Auth::id();
                $item->save();
            }

            if($action=='update-item'){
                $item = PurchaseRequisitionItem::find($r->item_id);
                if($item){
                    $item->material_id = $r->material_id ?: null;
                    $item->material_name = $r->material_name ?: null;
                    $item->qty = $r->qty ?: 0;
                    $item->unit = $r->unit ?: null;
                    $item->save();
                }
            }

            if($action=='remove-item'){
                $item = PurchaseRequisitionItem::find($r->item_id);
                if($item) $item->delete();
            }

            // Update totals
            $requisition->save();

            $view = view(adminTheme().'purchases..requisitions.includes.items', compact('requisition'))->render();
            return response()->json(['success'=>true,'view'=>$view]);
        }

        // Update Requisition
        if($action=='update'){
            $r->validate([
                'status'=>'nullable|max:20',
                'created_at'=>'required|date',
                'note'=>'nullable',
            ]);

            $requisition->status = $r->status ?: $requisition->status;
            $requisition->note = $r->note;
            $requisition->created_at = $r->created_at ?: Carbon::now();
            $requisition->save();

            Session()->flash('success','Requisition Updated Successfully');
            return redirect()->route('purchasesRequisitionsAction',['view',$requisition->id]);
        }

        // Delete Requisition
        if($action=='delete'){
            $requisition->items()->delete();
            $requisition->delete();
            Session()->flash('success','Requisition Deleted Successfully');
            return redirect()->back();
        }

        $departments = Attribute::where('type', 3)->get(['id', 'name']);
        $requisition = PurchaseRequisition::with('items')->findOrFail($id);

        return view(adminTheme().'purchases.requisitions.edit', compact('requisition', 'departments', 'requisition'));
    }




    // ================================
    //  LIST PAGE
    // ================================
    public function purchasesOrders(Request $r)
    {
        if ($r->action && $r->checkid) {
            $orders = PurchaseOrder::whereIn('id', $r->checkid)->get();

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

        $orders = PurchaseOrder::latest()
            ->where(function ($q) use ($r) {
                if ($r->search) {
                    $q->where('order_no', 'LIKE', '%' . $r->search . '%');
                    $q->orWhereHas('supplier', function ($qq) use ($r) {
                        $qq->where('supplier_name', 'LIKE', '%' . $r->search . '%');
                    });
                }

                if ($r->startDate || $r->endDate) {
                    $from = $r->startDate ?: now()->format('Y-m-d');
                    $to = $r->endDate ?: now()->format('Y-m-d');
                    $q->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
                }

                if ($r->status) {
                    $q->where('status', $r->status);
                } else {
                    $q->where('status', '<>', 'trash');
                }
            })
            ->paginate(25)
            ->appends($r->all());

        $totals = DB::table('purchase_orders')
            ->selectRaw("count(case when status != 'trash' then 1 end) as total")
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when status = 'approved' then 1 end) as approved")
            ->selectRaw("count(case when status = 'rejected' then 1 end) as rejected")
            ->selectRaw("count(case when status = 'trash' then 1 end) as trash")
            ->first();

        return view(adminTheme().'purchases.orders.index', compact('orders', 'totals'));
    }

    // ================================
    //  CREATE / EDIT ACTION
    // ================================
    public function purchasesOrdersAction(Request $r, $action, $id = null)
    {
        // CREATE
        if ($action == 'create') {
            $order = PurchaseOrder::where('status', 'temp')->where('addedby_id', Auth::id())->first();

            if (!$order) {
                $order = new PurchaseOrder();
                $order->status = 'temp';
                $order->addedby_id = Auth::id();
                $order->created_date = now()->format('Ymd');
                $order->save();
            }

            $order->order_no = now()->format('Ymd') . $order->id;
            $order->save();

            return redirect()->route('admin.purchasesOrdersAction', ['edit', $order->id]);
        }

        // FIND ORDER
        $order = PurchaseOrder::find($id);
        if (!$order) {
            session()->flash('error', 'Order Not Found');
            return redirect()->route('purchaseOrders');
        }

        // PDF
        if ($action == 'pdf') {
            $pdf = PDF::loadView(adminTheme().'purchases.pdfOrder', compact('order'));
            return $pdf->stream('purchase_order.pdf');
        }

        // VIEW
        if ($action == 'view') {
            return view(adminTheme().'purchases.orders.view', compact('order'));
        }

        // ADD COMPANY
        if ($action == 'add-supplier') {
            $supplier = Supplier::find($r->supplier_id);
            if ($supplier) {
                $order->supplier_id = $supplier->id;
                $order->save();
            }

            $view = view(adminTheme().'purchases.orders.includes.items', compact('order'))->render();
            return response()->json(['success' => true, 'view' => $view]);
        }

        // SEARCH MATERIAL
        if ($action == 'search-material') {
            $materials = Post::where('type', 3)->where('status', 'active')
                ->when($r->search, fn($q) => $q->where('name', 'like', '%' . $r->search . '%'))
                ->limit(10)->get();

            $search = view(adminTheme().'purchases.orders.includes.searchMaterials', compact('materials', 'order'))->render();
            return response()->json(['success' => true, 'view' => $search]);
        }

        // ITEM CRUD
        if (in_array($action, ['add-item', 'update-item', 'remove-item'])) {

            if ($action == 'add-item') {
                $item = new PurchaseOrderItem();
                $item->order_id = $order->id;
                $item->addedby_id = Auth::id();
                $item->save();
            }

            if ($action == 'update-item') {
                $item = PurchaseOrderItem::find($r->item_id);
                if ($item) {
                    $item->material_id = $r->material_id ?: null;
                    $item->material_name = $r->material_name ?: null;
                    $item->qty = $r->qty ?: 0;
                    $item->unit = $r->unit ?: null;
                    $item->price = $r->price ?: 0;
                    $item->save();
                }
            }

            if ($action == 'remove-item') {
                PurchaseOrderItem::where('id', $r->item_id)->delete();
            }

            $order->save();

            $view = view(adminTheme().'purchases.orders.includes.items', compact('order'))->render();
            return response()->json(['success' => true, 'view' => $view]);
        }

        // UPDATE ORDER
        if ($action == 'update') {
            $r->validate([
                'status' => 'nullable|max:20',
                'created_at' => 'required|date',
                'note' => 'nullable',
            ]);

            $order->status = $r->status ?: $order->status;
            $order->note = $r->note;
            $order->created_at = $r->created_at ?: now();
            $order->save();

            session()->flash('success', 'Purchase Order Updated');
            return redirect()->route('admin.purchasesOrdersAction', ['view', $order->id]);
        }

        // DELETE
        if ($action == 'delete') {
            $order->items()->delete();
            $order->delete();
            session()->flash('success', 'Purchase Order Deleted');
            return redirect()->back();
        }

        // LOAD EDIT PAGE
        $departments = Attribute::where('type', 3)->get(['id', 'name']);
        $order = PurchaseOrder::with('items')->findOrFail($id);
        $suppliers = User::where('supplier', 1)->get(['id', 'name']);

        return view(adminTheme().'purchases.orders.edit', compact('order', 'departments', 'suppliers'));
    }


}
