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
      ->select(['id','name','slug','type','description','created_at','addedby_id','status','fetured'])
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      //Total Count Results
      $report = Post::where('type',3)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'purchases-items.goodsItems',compact('goodsItems','report'));

    }

    public function purchasesItemsAction(Request $r,$action,$id=null){
      // Add Designation Action Start
      if($action=='create'){
        $check = $r->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
        ]);

        $designation =Attribute::where('type',2)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$designation){
          $designation =new Attribute();
        }

        $designation->name=$r->name;
        $designation->description=$r->description;
        $designation->type =2;
        $designation->status ='active';
        $designation->addedby_id =Auth::id();
        $designation->save();

         $slug =Str::slug($r->name);
         if($slug==null){
          $designation->slug=$designation->id;
         }else{
          if(Attribute::where('type',2)->where('slug',$slug)->whereNotIn('id',[$designation->id])->count() >0){
          $designation->slug=$slug.'-'.$designation->id;
          }else{
          $designation->slug=$slug;
          }
        }
        $designation->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }
      // Add Designation Action End

      $designation =Attribute::where('type',2)->find($id);
      if(!$designation){
        Session()->flash('error','This Designation Are Not Found');
        return redirect()->route('admin.purchasesItems');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
      if($allPer && $designation->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.purchasesItems');
      }

      // Update Designation Action Start
      if($action=='update'){

          $check = $r->validate([
              'name' => 'required|max:191',
              'seo_title' => 'nullable|max:200',
              'seo_desc' => 'nullable|max:250',
              'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
              'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);

          $designation->name=$r->name;
          $designation->short_description=$r->short_description;
          $designation->description=$r->description;
          $designation->seo_title=$r->seo_title;
          $designation->short_description=$r->short_description;
          $designation->seo_keyword=$r->seo_keyword;

           ///////Image UploadStart////////////

            if($r->hasFile('image')){
              $file =$r->image;
              $src  =$designation->id;
              $srcType  =3;
              $fileUse  =1;
              $author=Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author);
            }

            ///////Image Upload End////////////

            ///////Banner Upload End////////////

            if($r->hasFile('banner')){

              $file =$r->banner;
              $src  =$designation->id;
              $srcType  =3;
              $fileUse  =2;
              $author=Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author);

            }

            ///////Banner Upload End////////////

             $slug =Str::slug($r->name);
             if($slug==null){
              $designation->slug=$designation->id;
             }else{
              if(Attribute::where('type',2)->where('slug',$slug)->whereNotIn('id',[$designation->id])->count() >0){
              $designation->slug=$slug.'-'.$designation->id;
              }else{
              $designation->slug=$slug;
              }
            }
            $designation->status =$r->status?'active':'inactive';
            $designation->fetured =$r->fetured?1:0;
            $designation->editedby_id =Auth::id();
            $designation->save();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();

      }
      // Update Designation Action Start

      // Delete Designation Action Start
      if($action=='delete'){
          $medias =Media::latest()->where('src_type',3)->where('src_id',$designation->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }

            $designation->delete();

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
                    $q->orWhereHas('company', function($qq) use($r){
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

        // Add / Search Company
        if($action=='add-company'){
            $company = Company::find($r->company_id);
            if($company){
                $requisition->company_id = $company->id;
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


}
