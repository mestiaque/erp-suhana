<?php

namespace App\Http\Controllers\Admin;

use DB;
use Str;
use Auth;
use File;
use Hash;
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
use App\Models\PurchaseReceive;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReceiveItem;
use App\Models\PurchaseRequisition;
use App\Models\MeterialStock;
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


        $goodsItems =Post::latest()->where('type',3)->where('status','active')
                        ->where(function($q) use ($r) {

                          if($r->category_id){
                              $q->where('category_id',$r->category_id);
                          }
                      })
                      ->select(['id','name','slug','type','description','category_id','unit_id','quantity','created_at','addedby_id','status','fetured'])
                      ->paginate(25)->appends([
                        'category_id'=>$r->category_id,
                      ]);

        $categories =Attribute::where('type',7)->where('status','active')->select(['id','name'])->get();
        $branches =Attribute::where('type',0)->where('status','active')->select(['id','name'])->get();
        return view(adminTheme().'purchases-items.purchasesStocks',compact('categories','goodsItems','branches'));
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
        $requisitions = PurchaseRequisition::latest()->where('status','<>','temp')
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
        $totals = DB::table('purchase_requisitions')->where('status','<>','temp')
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
                $requisition->save();
            }
            $requisition->created_at = now()->format('Ymd');
            $requisition->department_id = Auth::user()->department_id;
            $requisition->requisition_no = $requisition->created_at->format('Ymd').$requisition->id;
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
                    if($r->name=='product_name'){
                      $item->material_name = $r->data ?: null;
                    }
                    if($r->name=='qty'){
                      $item->qty = $r->data?:0;
                    }
                    if($r->name=='unit'){
                      $item->unit = $r->data ?: null;
                    }
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
                'department_id'=>'required|numeric',
                'created_at'=>'required|date',
                'expected_date'=>'required|date',
                'note'=>'nullable',
            ]);

            $requisition->department_id = $r->department_id;
            $requisition->status = $r->status?:'pending';
            $requisition->note = $r->note;
            $requisition->expected_date = $r->expected_date ?: Carbon::now();
            $requisition->created_at = $r->created_at ?: Carbon::now();
            $requisition->save();

            Session()->flash('success','Requisition Updated Successfully');
            return redirect()->route('admin.purchasesRequisitionsAction',['view',$requisition->id]);
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

        return view(adminTheme().'purchases.requisitions.edit', compact('requisition', 'departments',));
    }

    public function suppliers(Request $r){
         //Filter Actions Start
        if($r->action){
        if($r->checkid){
        $datas=User::where('supplier',true)->whereIn('status',[0,1])
                ->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

            if($r->action==1){
              $data->status=1;
              $data->save();
            }elseif($r->action==2){
              $data->status=0;
              $data->save();
            }elseif($r->action==5){

              $userFiles =Media::latest()->where('src_type',6)->where('src_id',$data->id)->get();
              foreach ($userFiles as $media) {
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

      $users =User::latest()->where('supplier',true)->whereIn('status',[0,1])
      ->where(function($q) use($r) {
          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
              $q->orWhere('email','LIKE','%'.$r->search.'%');
              $q->orWhere('mobile','LIKE','%'.$r->search.'%');
          }
          if($r->status){
            $q->where('status',$r->status=='inactive'?0:1);
          }
          if($r->startDate || $r->endDate)
          {
              if($r->startDate){
                  $from =$r->startDate;
              }else{
                  $from=Carbon::now()->format('Y-m-d');
              }

              if($r->endDate){
                  $to =$r->endDate;
              }else{
                  $to=Carbon::now()->format('Y-m-d');
              }

              $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
          }

      })
      ->select(['id','name','email','mobile','created_at','company_name','address_line1','addedby_id','status'])
        ->paginate(25)->appends([
          'search'=>$r->search,
          'status'=>$r->status,
          'startDate'=>$r->startDate,
          'endDate'=>$r->endDate,
        ]);

      //Total Count Results
      $total = DB::table('users')->whereIn('status',[0,1])->where('supplier',true)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 1 then 1 end) as active")
      ->selectRaw("count(case when status = 0 then 1 end) as inactive")
      ->first();

      return view(adminTheme().'suppliers.users',compact('users','total'));
    }

    public function suppliersAction(Request $r,$action,$id=null){

      //Add New User Start
      if($action=='create' && $r->isMethod('post')){
        $check = $r->validate([
          'name' => 'required|max:100',
          'company_name' => 'nullable|max:100',
          'email_mobile' => 'required|max:100',
          'address' => 'nullable|max:500',
        ]);

        $user =User::where('email',$r->email_mobile)->orWhere('mobile',$r->email_mobile)->first();
        if($user){
          $user->supplier=true;
          $user->save();
          Session()->flash('success','User Already Register! Now you are Supplier.');
        }else{
          $password=Str::random(8);
          $user =new User();
          $user->name =$r->name;
          if(filter_var($r->email_mobile, FILTER_VALIDATE_EMAIL)){
            $user->email =$r->email_mobile;
          }else{
            $user->mobile =$r->email_mobile;
          }
          $user->company_name=$r->company_name;
          $user->address_line1=$r->address;
          $user->password_show=$password;
          $user->password=Hash::make($password);
          $user->supplier=true;
          $user->save();

          Session()->flash('success','Supplier Are Successfully Register Done!');
        }


        return redirect()->route('admin.suppliersAction',['view',$user->id]);
      }
        //Add New User End


      $user=User::where('supplier',true)->whereIn('status',[0,1])->find($id);
      if(!$user){
        Session()->flash('error','This Supplier Are Not Found');
        return redirect()->route('admin.suppliers');
      }

      if($action=='view'){
        $orders =$user->orders()->whereIn('order_type',['purchase_order'])
                ->paginate(10);
        return view(adminTheme().'suppliers.viewUser',compact('user','orders'));
      }

      //Update User Profile Start
      if($action=='update' && $r->isMethod('post')){

          $check = $r->validate([
                'name' => 'required|max:100',
                'email' => 'nullable|max:100|unique:users,email,'.$user->id,
                'mobile' => 'required|max:20|unique:users,mobile,'.$user->id,
                'address' => 'nullable|max:200',
                'company_name' => 'nullable|max:200',
                'created_at' => 'nullable|date|max:50',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

          $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();
          if (!$createDate->isSameDay($user->created_at)) {
              $user->created_at = $createDate;
          }

          $user->name =$r->name;
          $user->mobile =$r->mobile;
          $user->email =$r->email;
          $user->company_name =$r->company_name;
          $user->address_line1 =$r->address;
          ///////Image UploadStart////////////
          if($r->hasFile('image')){
            $file =$r->image;
            $src  =$user->id;
            $srcType  =6;
            $fileUse  =1;
            $author =Auth::id();
            uploadFile($file,$src,$srcType,$fileUse,$author);
          }
          ///////Image Upload End////////////
          $user->status=$r->status?true:false;
          $user->save();

          Session()->flash('success','Your Updated Are Successfully Done!');
          return redirect()->back();

        }
        //Update User Profile End

        //Delete User Start
        if($action=='delete'){

          $userFiles =Media::latest()->where('src_type',6)->where('src_id',$user->id)->get();
          foreach ($userFiles as $media) {
              if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                }
              $media->delete();
          }
          $user->delete();
          Session()->flash('success','User Are Deleted Successfully Deleted!');
          return redirect()->back();
        }
        //Delete User End

      return view(adminTheme().'suppliers.editUser',compact('user'));

    }

    
    public function suppliersLegers(){

      return view(adminTheme().'suppliers.supplierLadgers');
    }


    public function purchasesReports(Request $r){

      $orders =PurchaseOrder::latest()
              ->where(function($q) use ($r){
                  if($r->startDate || $r->endDate){
                      $from = $r->startDate ?: Carbon::now()->format('Y-m-d');
                      $to = $r->endDate ?: Carbon::now()->format('Y-m-d');
                      $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                  }
                  if($r->supplier_id){
                      $q->where('supplier_id',$r->supplier_id);
                  }
              })
              ->paginate(25)->appends($r->all());

      $suppliers =User::where('supplier',true)->where('status',1)
                  ->select(['id','name','company_name'])->get();

      return view(adminTheme().'purchases.reports.purchasesReports',compact('orders','suppliers'));
    }

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
            ->where('status', '<>', 'temp')

            ->where(function($q) use ($r){

                // SEARCH
                if ($r->search) {

                    $search = $r->search;

                    $q->where(function($qq) use ($search){

                        // order_no
                        $qq->where('order_no', 'LIKE', "%{$search}%")

                        // supplier name (fixed)
                        ->orWhereHas('supplier', function($sup) use ($search){
                            $sup->where('users.supplier', 1)       // FIX 1
                                ->where('users.name','LIKE',"%{$search}%"); // FIX 2
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

                // STATUS
                if ($r->status) {
                    $q->where('status', $r->status);
                } else {
                    $q->where('status', '<>', 'trash');
                }

            })

            ->paginate(25)
            ->appends($r->all());

        // TOTAL COUNTS (unchanged)
        $totals = DB::table('purchase_orders')->where('status', '<>', 'temp')
            ->selectRaw("count(case when status != 'trash' then 1 end) as total")
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when status = 'approved' then 1 end) as approved")
            ->selectRaw("count(case when status = 'rejected' then 1 end) as rejected")
            ->selectRaw("count(case when status = 'trash' then 1 end) as trash")
            ->first();

        return view(adminTheme().'purchases.orders.index', compact('orders', 'totals'));
    }


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
        if ($action == 'search-item') {
            $goods = Post::where('type', 3)->where('status', 'active')
                ->when($r->search, fn($q) => $q->where('name', 'like', '%' . $r->search . '%'))
                ->limit(20)->get();

            $search = view(adminTheme().'purchases.orders.includes.searchGoods', compact('goods', 'order'))->render();
            return response()->json(['success' => true, 'view' => $search]);
        }

        // ITEM CRUD
        if (in_array($action, ['add-material','add-item', 'update-item', 'remove-item'])) {

            if ($action == 'add-material') {
                $item =$order->items()->where('material_id',$r->item_id)->first();
                if(!$item){
                  $item = new PurchaseOrderItem();
                  $item->order_id = $order->id;
                  $item->material_id = $r->item_id?: null;
                }
                $item->material_name = $item->material?$item->material->name: null;
                if($item->material){
                  $item->unit = $item->material->unit?$item->material->unit->name: null;
                }
                $item->addedby_id = Auth::id();
                $item->save();
            }

            if ($action == 'add-item') {
                $item = new PurchaseOrderItem();
                $item->order_id = $order->id;
                $item->addedby_id = Auth::id();
                $item->save();
            }

            if ($action == 'update-item') {
                $item = PurchaseOrderItem::find($r->item_id);
                if ($item) {
                    if($r->name == 'material_name')
                    {
                      $item->material_name = $r->data ?: null;
                    }
                    if($r->name == 'qty')
                    {
                      $item->qty = $r->data ?: 0;
                    }
                    if($r->name == 'unit')
                    {
                      $item->unit = $r->data ?: null;
                    }
                    if($r->name == 'price')
                    {
                      $item->price = $r->data ?: 0;
                    }
                    $item->total_price = $item->qty * $item->price;
                    $item->save();

                    $order->grand_total = $order->items()->sum('total_price');
                    $order->due_amount = $order->grand_total - $order->paid_amount;
                    $order->total_qty = $order->items()->sum('qty');
                    $order->save();
                }
            }

            if ($action == 'remove-item') {
                PurchaseOrderItem::where('id', $r->item_id)->delete();
            }

            $goods = Post::where('type', 3)->where('status', 'active')->limit(20)->get(['id', 'name','unit_id']);
            $view = view(adminTheme().'purchases.orders.includes.items', compact('goods','order'))->render();
            return response()->json(['success' => true, 'view' => $view]);
        }

        // UPDATE ORDER
        if ($action == 'update') {
            $r->validate([
                'supplier_id' => 'nullable|numeric',
                'status' => 'required|max:20',
                'created_at' => 'required|date',
                'note' => 'nullable',
            ]);

            $supplier =User::where('supplier',true)->find($r->supplier_id);
            if(!$supplier){
              session()->flash('error', 'Supplier are not found');
              return back();
            }
            $order->supplier_id = $supplier->id;
            $order->supplier_name = $supplier->name;
            $order->supplier_email = $supplier->email;
            $order->supplier_mobile = $supplier->mobile;
            $order->supplier_address = $supplier->address_line1;
            $order->status = $r->status?:'pending';
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

        $suppliers = User::where('supplier', 1)->get(['id', 'name','company_name']);
        $goods = Post::where('type', 3)->where('status', 'active')->limit(20)->get(['id', 'name','unit_id']);
        return view(adminTheme().'purchases.orders.edit', compact('order','suppliers','goods'));
    }


    public function purchasesReceived(Request $r)
    {
        $purchases = PurchaseOrder::latest()->limit(10)->get(['id','order_no']);
        $branches = Attribute::where('type', 0)->where('status','active')->get(['id','name']);
        if ($r->action && $r->checkid) {
            $receives = PurchaseReceive::whereIn('id', $r->checkid)->get();
            foreach ($receives as $data) {
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

        $receives = PurchaseReceive::latest()->where('status','<>','trash')
            ->where(function($q) use ($r){
                if ($r->search){
                    $q->where('purchase_receive_no','LIKE','%'.$r->search.'%');
                    $q->orWhere('purchase_no','LIKE','%'.$r->search.'%');
                    $q->orWhereHas('purchase', fn($qq)=>$qq->where('order_no','LIKE','%'.$r->search.'%'));
                }
                if ($r->startDate || $r->endDate){
                    $from = $r->startDate ?: now()->format('Y-m-d');
                    $to = $r->endDate ?: now()->format('Y-m-d');
                    $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
                }
                if ($r->status){
                    $q->where('status',$r->status);
                }else{
                    $q->where('status','<>','trash');
                }
            })
            ->paginate(25)
            ->appends($r->all());

        $totals = DB::table('purchase_receives')
            ->where('status','<>','trash')
            ->selectRaw("count(*) as total")
            ->selectRaw("count(case when status='pending' then 1 end) as pending")
            ->selectRaw("count(case when status='approved' then 1 end) as approved")
            ->selectRaw("count(case when status='rejected' then 1 end) as rejected")
            ->selectRaw("count(case when status='trash' then 1 end) as trash")
            ->first();

        return view(adminTheme().'purchases.receives.index', compact('receives','totals', 'purchases', 'branches'));
    }

    public function purchasesReceivedAction(Request $r, $action, $id = null){
        // -----------------------
        // AJAX CREATE VIA MODAL
        // -----------------------
        if ($action == 'create' && $r->ajax()) {

            $purchase = PurchaseOrder::where('order_no', $r->purchase_no)->first();

            if (!$purchase) {
                return response()->json(['success' => false, 'message' => 'Purchase Number Not Found']);
            }

            // Check if receive already exists
            $exist = PurchaseReceive::where('purchase_id', $purchase->id)
                                    ->where('addedby_id', Auth::id())
                                    ->where('status', 'temp')
                                    ->first();
            if ($exist) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('admin.purchasesReceivedAction', ['edit', $exist->id])
                ]);
            }
            // Create Receive
            $receive = PurchaseReceive::create([
                'purchase_id'         => $purchase->id,
                'branch_id'           => $r->branch_id,
                'purchase_no'         => $purchase->order_no,
                'challan_no'          => null,
                'purchase_receive_no' => now()->format('ymdhis').$purchase->id,
                'status'              => 'temp',
                'addedby_id'          => Auth::id()
            ]);

            // Add items
            foreach ($purchase->items as $item) {
                PurchaseReceiveItem::create([
                    'purchase_receive_id' => $receive->id,
                    'purchase_id'         => $purchase->id,
                    'purchase_item_id'    => $item->id,
                    'material_id'    => $item->material_id,
                    'material_name'  => $item->material_name,
                    'received_qty'        => 0
                ]);
            }

            return response()->json([
                'success' => true,
                'redirect' => route('admin.purchasesReceivedAction', ['edit', $receive->id])
            ]);
        }

        // -----------------------
        // LIVE SEARCH PURCHASE ORDER
        // -----------------------
        if ($action == 'search-purchase' && $r->ajax()) {
            $purchases = PurchaseOrder::where('order_no', 'like', '%'.$r->search.'%')
                ->latest()
                ->limit(10)
                ->get(['id','order_no']);

            if(count($purchases) < 1)  return response()->json(['error' => true, 'message' => 'Purchase Number Not Found']);
            $html = view(adminTheme().'purchases.receives.includes.searchResults', compact('purchases'))->render();

            return response()->json(['success'=>true,'view'=>$html]);
        }

        // -----------------------
        // EDIT RECEIVE
        // -----------------------
        if ($action == 'edit') {
            $receive = PurchaseReceive::with('items.orderItem.product')->findOrFail($id);
            return view(adminTheme().'purchases.receives.edit', compact('receive'));
        }

        // -----------------------
        // ITEM CRUD (AJAX)
        // -----------------------
        if (in_array($action, ['update-item'])) {
            $receive = PurchaseReceive::with('items')->findOrFail($id);

            if ($action == 'update-item') {
                $item = PurchaseReceiveItem::find($r->item_id);
                if ($item) {
                    // Fetch corresponding purchase order item
                    $orderItem = PurchaseOrderItem::find($item->purchase_item_id);

                    $receivedQty = floatval($r->received_qty ?? 0);
                    $maxQty = $orderItem ? floatval($orderItem->qty) : 0;

                    // Prevent received qty > order qty
                    if ($receivedQty > $maxQty) {
                        $receivedQty = $maxQty;
                    }


                    $item->received_qty = $receivedQty;
                    $item->save();
                }
            }

            // $view = view(adminTheme().'purchases.receives.includes.items', compact('receive'))->render();
            return response()->json(['success'=>true]);
        }

        // -----------------------
        // UPDATE RECEIVE
        // -----------------------
        if ($action == 'update') {
            $receive = PurchaseReceive::findOrFail($id);
            $r->validate([
                'challan_no' => 'required',
            ]);

            foreach($receive->items as $item){
              $purchase =$item->purchase;
              $qty = $r['qty_'.$item->id] ?? 0;

              if($qty > 0 && $purchase){
                
                $stock =MeterialStock::where('branch_id',$receive->branch_id)->where('meterial_id',$item->material_id)->first();
                if(!$stock){
                  $stock =new MeterialStock();
                  $stock->branch_id =$receive->branch_id;
                  $stock->meterial_id =$item->material_id;
                  $stock->save();
                }

                $oldQty =$item->received_qty;
                if($oldQty > $qty){
                  $needQty =$oldQty - $qty;
                  //Received Update
                  $data =$purchase->items()->find($item->purchase_item_id);
                  if($data){
                    if($stock->quantity >= $needQty){
                      //Reveived Data update
                      $item->received_qty =$qty;
                      $item->save();

                      //Purchsee Data update
                      $data->received_qty -=$needQty;
                      $data->save();

                      //Branch wise quantity update
                      $stock->quantity -=$needQty;
                      $stock->save();
                    }
                  }

                }elseif($oldQty < $qty){
                  $needQty =$qty - $oldQty;
                  //Received Update
                  $data =$purchase->items()->find($item->purchase_item_id);
                  if($data){
                    $data->received_qty +=$needQty;
                    if($data->received_qty <=  $data->qty){

                      //Reveived Data update
                      $item->received_qty =$qty;
                      $item->save();

                      //Purchsee Data update
                      $data->save();
                      
                      //Branch wise quantity update
                      $stock->quantity +=$needQty;
                      $stock->save();
                    }
                  }
                
                }

                //Meterial quantity update
                $meterial =$item->meterial;
                if($meterial){
                  $meterial->quantity=$meterial->materialStockQty();
                  $meterial->save();
                }

              }
            }

            $receive->status = "approved";
            $receive->note = $r->note;
            $receive->challan_no = $r->challan_no;
            $receive->created_at = $r->created_at ?: now();
            $receive->save();

            session()->flash('success','Purchase Receive Updated');
            return redirect()->route('admin.purchasesReceivedAction',['view',$receive->id]);
        }

        // -----------------------
        // DELETE RECEIVE
        // -----------------------
        if ($action == 'delete') {
            $receive = PurchaseReceive::findOrFail($id);
            $receive->items()->delete();
            $receive->delete();
            session()->flash('success','Purchase Receive Deleted');
            return redirect()->back();
        }

        if ($action == 'view') {
            $receive = PurchaseReceive::with('items.orderItem.product')->findOrFail($id);
            return view(adminTheme().'purchases.receives.view', compact('receive'));
        }
    }


    public function billPayment(Request $r)
    {
        // Totals Count
        $totals = PurchaseOrder::selectRaw("count(*) as total")
            ->selectRaw("count(case when payment_status='due' then 1 end) as due")
            ->selectRaw("count(case when payment_status='partial' then 1 end) as partial")
            ->selectRaw("count(case when payment_status='paid' then 1 end) as paid")
            ->first();

        // Main Query
        $purchases = PurchaseOrder::with('supplier')
            ->where('status', 'approved')
            ->where('due_amount', '>', 0)
            ->when($r->status, function($q) use ($r){
                $q->where('payment_status', $r->status);
            })

            ->when($r->search, function($query) use ($r){

                $search = $r->search;

                $query->where(function($q) use ($search){

                    $q->where('order_no', 'LIKE', '%'.$search.'%')
                    ->orWhere('supplier_name', 'LIKE', '%'.$search.'%');
                });

            })

            ->when(($r->startDate || $r->endDate), function($q) use ($r){
                $from = $r->startDate ?: now()->format('Y-m-d');
                $to   = $r->endDate ?: now()->format('Y-m-d');
                $q->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);
            })

            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($r->all());
            // return $purchases;

        return view(adminTheme().'purchases.bill-payments.index', compact('purchases','totals'));
    }



    public function billPaymentAction($action, $id = null)
    {
        $request = request();
        if ($action == 'can-pay-info') {

            $purchase = PurchaseOrder::findOrFail($id);
            $canInfo = collect([
                'can_pay_by' => $purchase->canPayBy?->name,
                'can_pay_at' => $purchase->can_pay_at,
                'can_pay_note' => $purchase->can_pay_note,
            ]);
             return view(adminTheme().'purchases.bill-payments.can-pay-info', compact('canInfo'));
        }
        if ($action == 'pay') {

            $purchase = PurchaseOrder::findOrFail($id);
            $paymentMethods =Attribute::latest()->where('type',9)->where('status','active')->select(['id','name','amount'])->get();
            $accountMethods =Attribute::latest()->where('type',10)->where('status','active')->where('addedby_id',Auth::id())->select(['id','name','amount'])->get();
            $transactions = Transaction::where('src_id', $purchase->id)->where('type', 3)->latest()->get();

            return view(adminTheme().'purchases.bill-payments.view', compact('purchase', 'paymentMethods', 'accountMethods', 'transactions'));
        }

        if ($action == 'save') {
            $purchase = PurchaseOrder::findOrFail($id);

            $pay_amount = floatval(request()->pay_amount);

            // Update due & paid
            $purchase->due_amount = max(0, $purchase->due_amount - $pay_amount);
            $purchase->paid_amount += $pay_amount;

            // Update payment_status
            if ($purchase->due_amount <= 0) {
                $purchase->payment_status = 'paid';
                $purchase->due_amount = 0;
            } elseif ($purchase->due_amount < $purchase->grand_total) {
                $purchase->payment_status = 'partial';
            } else {
                $purchase->payment_status = 'due';
            }

            $paymentMethods =Attribute::find(request()->payment_method_id);
            $transactionData = [
                            "src_id"            => $purchase->id,
                            "user_id"           => $purchase->supplier_id,

                            "billing_name"      =>  $purchase->supplier_name ?? null,
                            "billing_mobile"    =>  $purchase->supplier_mobile ?? null,
                            "billing_email"     =>  $purchase->supplier_email ?? null,
                            "billing_address"   =>  $purchase->supplier_address ?? null,
                            "billing_note"      =>  request()->note ?? null,
                            "type"              => 3,
                            "account_id"        => request()->account_id ?? null,
                            "transection_id"    => date('Ymd') . random_int(1000, 9999),
                            "payment_method"    => $paymentMethods->name,
                            "payment_method_id" => $paymentMethods->id,
                            "amount"      => request()->pay_amount,
                            "currency"          => "BDT",
                            "status"            => "Pending",
                            "addedby_id"        => Auth::user()->id,
                        ];


            // Save payment history
            Transaction::create( $transactionData );

            $purchase->save();

            return redirect()->back()->with('success', 'Payment Successful!');
        }

        if ($action == 'update'){

            $transaction = Transaction::findOrFail($id);
            $purchase = PurchaseOrder::findOrFail($transaction->src_id);

            $old_amount = floatval($transaction->amount);
            $new_amount = floatval($request->pay_amount);

            // Adjust purchase paid & due
            $purchase->paid_amount = ($purchase->paid_amount - $old_amount) + $new_amount;
            $purchase->due_amount = max(0, $purchase->grand_total - $purchase->paid_amount);

            // Update payment status
            if ($purchase->due_amount <= 0) {
                $purchase->payment_status = 'paid';
                $purchase->due_amount = 0;
            } elseif ($purchase->due_amount < $purchase->grand_total) {
                $purchase->payment_status = 'partial';
            } else {
                $purchase->payment_status = 'due';
            }

            // Update account method & payment method
            $paymentMethod = Attribute::find($request->payment_method_id);

            // Update transaction
            $transaction->update([
                "amount"            => $new_amount,
                "account_id"        => $request->account_id,
                "payment_method_id" => $paymentMethod->id,
                "payment_method"    => $paymentMethod->name,
                "billing_note"      => $request->note,
            ]);

            $purchase->save();

            return back()->with('success', 'Payment Updated Successfully!');
        }

        if ($action == 'delete'){
            $transaction = Transaction::findOrFail($id);
            $purchase = PurchaseOrder::findOrFail($transaction->src_id);

            $amount = floatval($transaction->amount);

            // Reverse the payment
            $purchase->paid_amount -= $amount;
            $purchase->due_amount = max(0, $purchase->grand_total - $purchase->paid_amount);

            // Update payment status
            if ($purchase->paid_amount <= 0) {
                $purchase->payment_status = 'due';
            } elseif ($purchase->paid_amount < $purchase->grand_total) {
                $purchase->payment_status = 'partial';
            } else {
                $purchase->payment_status = 'paid';
            }

            $purchase->save();

            $transaction->delete();

            return back()->with('success', 'Payment Updated Successfully!');
        }
    }





}
