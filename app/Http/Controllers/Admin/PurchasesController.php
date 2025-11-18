<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Str;
use File;
use DB;
use Image;
use Session;
use Validator;
use Redirect,Response;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\Media;
use App\Models\Transaction;
use App\Models\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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


}