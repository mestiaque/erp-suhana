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


}