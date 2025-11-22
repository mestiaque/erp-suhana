<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Str;
use Hash;
use File;
use DB;
use Pdf;
use Image;
use Artisan;
use Session;
use Validator;
use Redirect,Response;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\Company;
use App\Models\CompanyPerson;
use App\Models\CompanyMachinery;
use App\Models\ReffMember;
use App\Models\Transaction;
use App\Models\PostExtra;
use App\Models\Service;
use App\Models\Task;
use App\Models\Visit;
use App\Models\Note;
use App\Models\Lead;
use App\Models\LeadPerson;
use App\Models\Meeting;
use App\Models\Salary;
use App\Models\Expense;
use App\Models\Review;
use App\Models\General;
use App\Models\Country;
use App\Models\UserLocation;
use App\Models\Media;
use App\Models\Attribute;
use App\Models\Permission;
use App\Models\PostAttribute;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AdminController extends Controller
{

    public function resizeImage($loadImage,$type,$w,$h){

        $img =	$type.$loadImage->file_rename;
        $fullpath ="public/".$loadImage->file_path.'/'.$img;
        $path = public_path($loadImage->file_path.'/');

        $image = Image::make($loadImage->file_url);
        $image->fit($w,$h);
        $image->save($path.$img);

        if($type=='sm'){
           $loadImage->file_url_sm=$fullpath;
           $loadImage->save();
        }elseif($type=='md'){
           $loadImage->file_url_md=$fullpath;
           $loadImage->save();
        }elseif($type=='lg'){
           $loadImage->file_url_lg=$fullpath;
           $loadImage->save();
        }

        return true;
    }

    public function dashboard(){

        $reports =[
            'total_expenses' => Expense::sum('amount'),
        ];

        return view('Admin.dashboard',compact('reports'));
    }


    public function myProfile(Request $r){
      $user =Auth::user();
      return view(adminTheme().'users.myProfile',compact('user'));
    }

    public function editProfile(Request $r){

      $user =Auth::user();
      if($r->isMethod('post')){
        if($r->actionType=='profile'){
          $check = $r->validate([
            'name' => 'required|max:100',
            'email' => 'required|max:100|unique:users,email,'.$user->id,
            'mobile' => 'nullable|max:20|unique:users,mobile,'.$user->id,
            'gender' => 'nullable|max:10',
            'address' => 'nullable|max:191',
            'division' => 'nullable|numeric',
            'district' => 'nullable|numeric',
            'city' => 'nullable|numeric',
            'postal_code' => 'nullable|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);

          $user->name =$r->name;
          $user->mobile =$r->mobile;
          $user->email =$r->email;
          $user->gender =$r->gender;
          $user->address_line1 =$r->address;
          $user->division =$r->division;
          $user->district =$r->district;
          $user->city =$r->city;
          $user->postal_code =$r->postal_code;
          ///////Image UploadStart////////////
          if($r->hasFile('image')){
            $file =$r->image;
            $src  =$user->id;
            $srcType  =6;
            $fileUse  =1;
            $author=Auth::id();

            $loadImage =uploadFile($file,$src,$srcType,$fileUse,$author);
            // //Resize Image
            // $w=250;
            // $h=250;
            // $type='md';
            // $this->resizeImage($loadImage,$type,$w,$h);

            // //Resize Image
            // $w=50;
            // $h=50;
            // $type='sm';
            // $this->resizeImage($loadImage,$type,$w,$h);

            // //Resize Image
            // $w=400;
            // $h=400;
            // $type='lg';
            // $this->resizeImage($loadImage,$type,$w,$h);

          }
          ///////Image Upload End////////////
          $user->save();

          Session()->flash('success','Your Updated Are Successfully Done!');

        }
        if($r->actionType=='change-password'){

          $check = $r->validate([
              'old_password' => 'required|string|min:8',
              'password' => 'required|string|min:8|confirmed|different:old_password',
          ]);

          if(Hash::check($r->old_password, $user->password)){
            $user->password_show=$r->password;
            $user->password=Hash::make($r->password);
            $user->update();
            Session()->flash('success','Your Are Successfully Done');
          }else{
            Session()->flash('error','Current Password Are Not Match');
          }
        }
        return back();
      }

      return view(adminTheme().'users.editProfile',compact('user'));

    }

    public function reminders(Request $r){
        $user =Auth::user();
        $allPer = empty(json_decode($user->permission->permission, true)['meetings']['all']);
        $allPer2 = empty(json_decode($user->permission->permission, true)['tasks']['all']);
        $allPer3 = empty(json_decode($user->permission->permission, true)['visits']['all']);
        $allPer4 = empty(json_decode($user->permission->permission, true)['company']['all']);


        $meetings=Meeting::whereDate('created_at', '>=', Carbon::today())->latest()->limit(10)
                    ->where(function($q) use($allPer) {
                          if($allPer){
                             $q->where('host_id',auth::id());
                            }
                    })
                    ->whereNotIn('status',['Completed','Canceled'])
                    ->get();

        $tasks=Task::whereDate('due_date', '>=', Carbon::today())
                    ->where(function($q) use($allPer2) {
                          if($allPer2){
                             $q->where('assignby_id',auth::id());
                            }
                    })
                    ->whereNotIn('status',['Completed','Canceled'])
                    ->get();

        $visits=Visit::whereDate('created_at', '>=', Carbon::today())
                    ->where(function($q) use($allPer3) {
                          if($allPer3){
                             $q->where('assignby_id',auth::id());
                            }
                    })
                    ->whereNotIn('status',['Completed','Canceled'])
                    ->get();

        $dueCollects=Transaction::latest()->where('type',0)
                    // ->whereDate('created_at', '>=', Carbon::today())
                    ->where(function($q) use($allPer4) {
                            if($allPer4){
                             $q->where('addedby_id',auth::id());
                            }
                    })
                    ->where('status','pending')
                    ->get();

        $services=Service::latest()
                    ->where(function($q) use($allPer4) {
                            if($allPer4){
                             $q->where('employee_id',auth::id());
                            }
                    })
                    ->whereIn('status',['open','processing'])
                    ->get();


        return view(adminTheme().'users.reminders',compact('user','meetings','tasks','visits','dueCollects','services'));
    }


    //Medias Library Route
    public function medies(Request $r){

    //Check Authorized User
    $allPer = empty(json_decode(Auth::user()->permission->permission, true)['medies']['all']);

    //Media Delete All Selected Images Start
    if($r->actionType=='allDelete'){

      $check = $r->validate([
          'mediaid.*' => 'required|numeric',
      ]);

      for ($i=0; $i < count($r->mediaid); $i++) {
        $media =Media::find($r->mediaid[$i]);
        if($media){

          if($allPer && $media->addedby_id!=Auth::id()){
            //You are unauthorized Try!!;
          }else{

            if(File::exists($media->file_url)){
                File::delete($media->file_url);
            }
            $media->delete();

          }

        }
      }

      Session()->flash('success','Your Are Successfully Deleted');
      return redirect()->back();
    }

    //Media Delete All Selected Images End


    $medies =Media::latest()->where('src_type',0)
    ->where(function($q) use ($r,$allPer) {

      // Check Permission
      if($allPer){
        $q->where('addedby_id',auth::id());
      }

    })
    ->select(['id','file_url','file_size','file_type','file_name','alt_text','caption','description','addedby_id'])
    ->paginate(50);

    if($r->ajax())
      {

          return Response()->json([
              'success' => true,
              'view' => View(adminTheme().'medies.includes.mediesAll',[
                  'medies'=>$medies
              ])->render()
          ]);
      }

    return view(adminTheme().'medies.medies',compact('medies'));
  }

  public function mediesCreate(Request $r){

      $check = $r->validate([
          'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf,docx,zip,rar,mp4,webm,mov,wmv,mp3|max:25600',
      ]);

      if(!$check){
          Session::flash('error','Need To validation');
          return back();
      }

     $files=$r->file('images');
      if($files){
          foreach($files as $file){

              $file =$file;
              $src  =null;
              $srcType  =0;
              $fileUse  =0;
              $fileStatus=false;
              $author=Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author,$fileStatus);

          }
      }

    Session()->flash('success','Your Are Successfully Done');
     return redirect()->back();

  }

  public function mediesEdit(Request $r, $id){
    $media =Media::find($id);
    if(!$media){
      Session()->flash('error','This File Are Not Found');
      return redirect()->back();
    }

    if($media->src_type==0){
      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['medies']['all']);
      if($allPer && $media->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.medies');
      }
    }

    if($r->isMethod('post')){
         $media->alt_text=$r->alt_text;
         $media->caption=$r->caption;
         $media->description=$r->description;
         $media->editedby_id=auth::id();
         $media->save();
         Session()->flash('success','Your Are Successfully Done');
         return redirect()->back();
    }

    return view(adminTheme().'medies.mediaImageEdit',compact('media'));
  }


  public function mediesDelete(Request $request,$id){

     if($request->ajax())
    {

        $media =Media::find($id);
        if(!$media){
        Session()->flash('error','This File Are Not Found');
        return Response()->json([
                'success' => false
            ]);
        }

        if(File::exists($media->file_url)){
            File::delete($media->file_url);
        }
        if(File::exists($media->file_url_sm)){
            File::delete($media->file_url_sm);
        }
        if(File::exists($media->file_url_md)){
            File::delete($media->file_url_md);
        }
        if(File::exists($media->file_url_lg)){
            File::delete($media->file_url_lg);
        }
        $media->delete();
        return Response()->json([
                'success' => true
            ]);
    }

  }

  //Medias Library Route End

  // Page Management Function Start

  public function pages(Request $r){

    $allPer = empty(json_decode(Auth::user()->permission->permission, true)['pages']['all']);
      // Filter Action Start

    if($r->action){
      if($r->checkid){

      $datas=Post::latest()->where('type',0)->whereIn('id',$r->checkid)->get();

      foreach($datas as $data){
        if($allPer && $data->addedby_id!=Auth::id()){
          // You are unauthorized Try!!
        }else{

          if($r->action==1){
            $data->status='active';
            $data->save();
          }elseif($r->action==2){
            $data->status='inactive';
            $data->save();
          }elseif($r->action==3){
            $data->fetured=true;
            $data->save();
          }elseif($r->action==4){
            $data->fetured=false;
            $data->save();
          }elseif($r->action==5){
            //Page Extra Data Delete
            PostExtra::where('type',0)->where('src_id',$data->id)->delete();

            //Page Media File Delete
            $medias =Media::latest()->where('src_type',1)->where('src_id',$data->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }

            $data->delete();

          }

        }


      }

      Session()->flash('success','Action Successfully Completed!');

      }else{
        Session()->flash('info','Please Need To Select Minimum One Post');
      }

      return redirect()->back();
    }

    //Filter Action End

    $pages=Post::latest()->where('type',0)->where('status','<>','temp')
    ->where(function($q) use ($r,$allPer) {

        if($r->search){
            $q->where('name','LIKE','%'.$r->search.'%');
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

        if($r->status){
           $q->where('status',$r->status);
        }

      // Check Permission
      if($allPer){
       $q->where('addedby_id',auth::id());
      }

    })
    ->select(['id','name','slug','view','type','template','created_at','addedby_id','status','fetured'])
    ->paginate(25)->appends([
      'search'=>$r->search,
      'status'=>$r->status,
      'startDate'=>$r->startDate,
      'endDate'=>$r->endDate,
    ]);

    //Total Count Results
    $totals = DB::table('posts')->where('status','<>','temp')
    ->where('type',0)
    ->selectRaw('count(*) as total')
    ->selectRaw("count(case when status = 'active' then 1 end) as active")
    ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
    ->first();

    return view(adminTheme().'pages.pagesAll',compact('pages','totals'));

  }

  public function pagesAction(Request $r,$action,$id=null){

    if($action=='create'){
        $page =Post::where('type',0)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$page){
          $page =new Post();
          $page->type =0;
          $page->status ='temp';
          $page->addedby_id =Auth::id();
        }
        $page->created_at =Carbon::now();
        $page->save();

        return redirect()->route('admin.pagesAction',['edit',$page->id]);
      }
      $page =Post::find($id);
      if(!$page){
        Session()->flash('error','This Page Are Not Found');
        return redirect()->route('admin.pages');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['pages']['all']);
      if($allPer && $page->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.pages');
      }

      if($action=='update' && $r->isMethod('post')){

        $check = $r->validate([
            'name' => 'required|max:191',
            'template' => 'nullable|max:100',
            'seo_title' => 'nullable|max:120',
            'seo_description' => 'nullable|max:200',
            'seo_keyword' => 'nullable|max:300',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $page->name=$r->name;
        $page->short_description=$r->short_description;
        $page->description=$r->description;
        $page->seo_title=$r->seo_title;
        $page->seo_description=$r->seo_description;
        $page->seo_keyword=$r->seo_keyword;
        $page->template=$r->template?:null;
        ///////Image Upload Start////////////
        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$page->id;
          $srcType  =1;
          $fileUse  =1;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        ///////Image Upload End////////////

        ///////Image Upload Start////////////
        if($r->hasFile('banner')){
          $file =$r->banner;
          $src  =$page->id;
          $srcType  =1;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        ///////Image Upload End////////////

        $slug =Str::slug($r->name);
        if($slug==null){
          $page->slug=$page->id;
        }else{
          if(Post::where('type',0)->where('slug',$slug)->whereNotIn('id',[$page->id])->count() >0){
          $page->slug=$slug.'-'.$page->id;
          }else{
          $page->slug=$slug;
          }
        }

        if($r->created_at){
          $page->created_at =$r->created_at;
        }
        $page->status =$r->status?'active':'inactive';
        $page->fetured =$r->fetured?1:0;
        $page->editedby_id =Auth::id();
        $page->save();

        //Gallery posts
        if($r->galleries){

          $page->postTags()->whereNotIn('reff_id',$r->galleries)->delete();

          for ($i=0; $i < count($r->galleries); $i++) {
            $tag = $page->postTags()->where('reff_id',$r->galleries[$i])->first();

              if($tag){}else{
              $tag =new PostAttribute();
              $tag->type=2;
              $tag->src_id=$page->id;
              $tag->reff_id=$r->galleries[$i];
              }
              $tag->save();
         }
       }else{
        $page->postTags()->delete();
       }


        Session()->flash('success','Your Are Successfully Done');
        return redirect()->back();

      }

      if($action=='delete'){

        //Page Extra Data Delete
        PostExtra::where('type',0)->where('src_id',$page->id)->delete();

        //Page Media File Delete
        $medies =Media::where('src_type',1)->where('src_id',$page->id)->get();
        foreach ($medies as  $media) {
            if(File::exists($media->file_url)){
                File::delete($media->file_url);
            }
            $media->delete();
        }

        //Page Delete
        $page->delete();
        Session()->flash('success','Your Are Successfully Done');
        return redirect()->back();

      }

      $extraDatas=PostExtra::where('src_id',$id)->get();

      $galleries=Attribute::latest()->where('type',4)->where('status','<>','temp')->where('parent_id',null)
      ->select(['id','name'])
      ->get();

      return view(adminTheme().'pages.pageEdit',compact('page','extraDatas','galleries'));
  }


// Page Management Function End

    // Sales Management Function
    public function sales(Request $r){

        if(
            empty(json_decode(Auth::user()->permission->permission, true)['sales']['add']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['sales']['view']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['sales']['delete']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['sales']['report'])
        ){
          return  abort(401);
        }


        // Filter Action Start
            if($r->action){

                if($r->checkid){

                    $datas=Order::latest()->where('order_type','sale_invoices')->whereIn('id',$r->checkid)->get();

                    if($r->action==1){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='pending';
                                $data->save();
                            }
                        }
                    }elseif($r->action==2){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='confirmed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==3){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='completed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==4){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='cancelled';
                                $data->save();
                            }
                        }
                    }elseif($r->action==5){


                        foreach($datas as $data){
                            if($data->order_status=='trash'){
                            foreach($data->hasSubInvoices as $inv){
                                $inv->items()->delete();
                                $inv->delete();
                            }
                            $data->items()->delete();
                            $data->delete();
                            }else{
                            $data->order_status='trash';
                            $data->save();
                            }
                        }
                    }

                Session()->flash('success','Action Successfully Completed!');

            }else{
              Session()->flash('info','Please Need To Select Minimum One Post');
            }

            return redirect()->back();
          }

        // Filter Action Start


        $invoices =Order::latest()->where('order_type','sale_invoices')->where('parent_id',null)->where('order_status','<>','temp')
            ->where(function($q) use ($r) {

                if($r->search){
                    $q->where('invoice','LIKE','%'.$r->search.'%');
                    $q->orWhereHas('company',function($qq) use($r){
                          $qq->where('name','LIKE','%'.$r->search.'%');
                      });
                    $q->orWhereHas('marchantize',function($qq) use($r){
                          $qq->where('name','LIKE','%'.$r->search.'%');
                      });
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

                if($r->status){
                   $q->where('order_status',$r->status);
                }else{
                   $q->where('order_status','<>','trash');
                }

            })
            ->paginate(25)->appends([
              'search'=>$r->search,
              'status'=>$r->status,
              'startDate'=>$r->startDate,
              'endDate'=>$r->endDate,
            ]);



        //Total Count Results
        $totals = DB::table('orders')
        ->where('order_type','sale_invoices')->where('parent_id',null)->where('order_status','<>','temp')
        ->selectRaw("count(case when order_status != 'trash' then 1 end) as total")
        ->selectRaw("count(case when order_status = 'pending' then 1 end) as pending")
        ->selectRaw("count(case when order_status = 'confirmed' then 1 end) as confirmed")
        ->selectRaw("count(case when order_status = 'completed' then 1 end) as completed")
        ->selectRaw("count(case when order_status = 'cancelled' then 1 end) as cancelled")
        ->selectRaw("count(case when order_status = 'trash' then 1 end) as trash")
        ->first();

        return view(adminTheme().'sales.invoices',compact('invoices','totals'));
    }

    public function salesAction(Request $r,$action,$id=null){

        //Add Service  Start
        if($action=='create'){

            $invoice =Order::where('order_type','sale_invoices')->where('order_status','temp')->where('addedby_id',Auth::id())->first();
            if(!$invoice){
                $invoice =new Order();
                $invoice->order_type ='sale_invoices';
                $invoice->order_status ='temp';
                $invoice->addedby_id =Auth::id();
                $invoice->save();
            }
            $invoice->note =general()->pi_terms_condition;
            $invoice->created_at =Carbon::now();
            $invoice->invoice =Carbon::now()->format('Ymd').$invoice->id;
            $invoice->save();

            return redirect()->route('admin.salesAction',['edit',$invoice->id]);
        }
        //Add Service  End

        $invoice =Order::where('order_type','sale_invoices')->find($id);
        if(!$invoice){
            Session()->flash('error','This PI Invoices Are Not Found');
            return redirect()->route('admin.sales');
        }


        if($action=='invoice-pdf'){

            $invoices =array($invoice);

            $pdf = PDF::loadView(adminTheme().'pi-invoices.pdfPiInvoices', compact('invoices'));

            return $pdf->stream('invoice.pdf');
        }

        if($action=='view'){
            return view(adminTheme().'sales.viewInvoice',compact('invoice'));
        }


        if($action=='add-company'){
            $data =Company::latest()->where('status','active')->find($r->company_id);
            if($data){
                $invoice->company_id=$data->id;
                $invoice->name=$data->factory_name;
                $invoice->mobile=$data->owner_mobile;
                $invoice->email=$data->owner_email;
                $invoice->address=$data->company_address;
                $invoice->save();
            }

            $view =view(adminTheme().'sales.includes.orderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);
        }

        if($action=='search-goods'){

            $services =Post::latest()->where('type',3)->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'sales.includes.searchGoods',compact('services','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='search-company'){

            $companies =Company::latest()->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('factory_name','like','%'.$r->search.'%')->orWhere('owner_name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'sales.includes.searchCompany',compact('companies','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='add-item' || $action=='add-goods' || $action=='remove-item' || $action=='update-item' || $action=='update-paymentmode' || $action=='update-currencymode' || $action=='emi-update' || $action=='remove-installment'){

            if($action=='add-item'){
                $item =new OrderItem();
                $item->order_id=$invoice->id;
                $item->status=$invoice->status;
                $item->addedby_id=Auth::id();
                $item->save();
            }

            if($action=='update-paymentmode'){
                $invoice->emi_status =$r->data=='EMI'?true:false;
                if($invoice->due_date==null && $invoice->emi_status){
                    $invoice->due_date=$invoice->created_at->addMonth();
                }else{
                    $invoice->due_date=null;
                }
                $invoice->save();
            }

            if($action=='update-currencymode'){
                $invoice->currency =$r->data=='USD'?'USD':'BDT';
                $invoice->save();

                $invoice->transectionsAll()
                ->where('billing_reason', 'like', '%Installment%')
                ->whereIn('status', ['pending'])
                ->update(['currency' => $invoice->currency]);

            }

            if($action=='remove-installment'){
                $data =$invoice->transectionsAll()->whereIn('status',['pending'])->find($r->installment_id);
                if($data){
                    $data->delete();
                }
            }

            if($action=='emi-update'){

                $emiTime =$r->emi_time?:0;
                if($emiTime > 0 && $r->emi_amount > 0){
                    $amount =$r->emi_amount/$emiTime;
                    $createDate = $invoice->due_date ? Carbon::parse($r->due_date . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();
                    for($i=0;$i < $emiTime; $i++ ){
                        $paymentDate = Carbon::parse($createDate)->addMonth($i);
                        $transfer =new Transaction();
                        $transfer->type=0;
                        $transfer->src_id=$invoice->id;
                        $transfer->account_id=null;
                        $transfer->billing_name=$invoice->name;
                        $transfer->billing_mobile=$invoice->mobile;
                        $transfer->billing_email=$invoice->email;
                        $transfer->billing_address=$invoice->fullAddress();
                        $transfer->payment_method_id=null;
                        $transfer->amount=$amount;
                        $transfer->currency=$invoice->currency;
                        $transfer->billing_note=null;
                        $transfer->billing_reason ='Installment Pay';
                        $transfer->status ='pending';
                        $transfer->addedby_id =Auth::id();
                        $transfer->created_at = $paymentDate;
                        $transfer->save();
                    }
                }
            }

            if($action=='add-goods'){
                $service =Post::latest()->where('type',3)->where('status','active')->find($r->service_id);
                if($service){
                    $item =$invoice->items()->where('src_id',$service->id)->first();
                    if(!$item){
                        $item =new OrderItem();
                        $item->order_id=$invoice->id;
                        $item->src_id=$service->id;
                        $item->quantity=1;
                        $item->description=$service->name;
                        $item->unit=$service->unit?$service->unit->name:null;
                        $item->price=$service->item_price?:0;
                        $item->final_price =$item->price*$item->quantity;
                        $item->status=$invoice->status;
                        $item->addedby_id=Auth::id();
                        $item->save();
                    }
                }
            }

            if($action=='remove-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    $item->delete();
                }
            }

            if($action=='update-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    if($r->name=='product_name' || $r->name=='description' || $r->name=='unit' || $r->name=='price' || $r->name=='quantity'){
                      if($r->name=='price' || $r->name=='quantity'){
                      $item[$r->name]=$r->data?:0;
                      }else{
                      $item[$r->name]=$r->data?:null;
                      }

                      if($r->name=='price' || $r->name=='quantity'){
                        $item->final_price =$item->price*$item->quantity;
                      }
                      $item->save();
                    }
                }


                $invoice->total_items=$invoice->items()->count();
                $invoice->total_qty=$invoice->items()->sum('quantity');
                $invoice->total_price=$invoice->items()->sum('final_price');
                $invoice->grand_total=$invoice->items()->sum('final_price');
                $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
                $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
                $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
                if($invoice->paid_amount >= $invoice->grand_total){
                    $invoice->payment_status='paid';
                }elseif($invoice->paid_amount > 0){
                    $invoice->payment_status='partial';
                }else{
                    $invoice->payment_status='unpaid';
                }
                $invoice->save();

                return Response()->json([
                'success' => true,
                ]);
            }

            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();

            $view =view(adminTheme().'sales.includes.orderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);

        }

        if($action=='update'){

            $check = $r->validate([
                'status' => 'nullable|max:20',
                // 'invoice' => 'required|max:100',
                'created_at' => 'required|date',
                'terms_conditions' => 'nullable',
                'remark' => 'nullable',
            ]);

            // $invoice->invoice=$r->pi_no;
            $invoice->created_at=$r->created_at?:Carbon::now();
            $invoice->note=$r->terms_conditions;
            $invoice->remark=$r->remark;
            if($invoice->hasLcOrders->count() > 0){
            }else{
                $invoice->order_status=$r->status?:'pending';
            }
            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();

            Session()->flash('success','Your Are Successfully Updated');
            return redirect()->route('admin.salesAction',['view',$invoice->id]);

        }

        if($action=='delete'){
            if($invoice->order_status=='trash'){
                foreach($invoice->hasSubInvoices as $inv){
                    $inv->items()->delete();
                    $inv->delete();
                }

                $invoice->items()->delete();
                $invoice->delete();
            }else{
               $invoice->order_status='trash';
               $invoice->save();
            }

            Session()->flash('success','Your Are Successfully Deleted');
            return redirect()->back();
        }

        $merchandisers =Attribute::latest()->where('type',4)->where('status','active')->limit(10)->get();
        $companies =Company::latest()->where('status','active')->limit(10)->get();



      return view(adminTheme().'sales.editInvoices',compact('invoice','merchandisers','companies'));
    }



    // Invoice Management Function
    public function piInvoices(Request $r){

        if(
            empty(json_decode(Auth::user()->permission->permission, true)['pi']['add']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['pi']['view']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['pi']['delete']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['pi']['report'])
        ){
          return  abort(401);
        }


        // Filter Action Start
            if($r->action){

                if($r->checkid){

                    $datas=Order::latest()->where('order_type','pi_invoices')->whereIn('id',$r->checkid)->get();

                    if($r->action==1){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='pending';
                                $data->save();
                            }
                        }
                    }elseif($r->action==2){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='confirmed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==3){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='completed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==4){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='cancelled';
                                $data->save();
                            }
                        }
                    }elseif($r->action==5){


                        foreach($datas as $data){
                            if($data->order_status=='trash'){
                            foreach($data->hasSubInvoices as $inv){
                                $inv->items()->delete();
                                $inv->delete();
                            }
                            $data->items()->delete();
                            $data->delete();
                            }else{
                            $data->order_status='trash';
                            $data->save();
                            }
                        }
                    }elseif($r->action==6){
                    return redirect()->route('admin.piInvoicesAction',['multi-invoice-pdf','invoices'=>$r->checkid]);
                    }

                Session()->flash('success','Action Successfully Completed!');

            }else{
              Session()->flash('info','Please Need To Select Minimum One Post');
            }

            return redirect()->back();
          }

        // Filter Action Start


        $invoices =Order::latest()->where('order_type','pi_invoices')->where('parent_id',null)->where('order_status','<>','temp')
            ->where(function($q) use ($r) {

                if($r->search){
                    $q->where('invoice','LIKE','%'.$r->search.'%');
                    $q->orWhereHas('company',function($qq) use($r){
                          $qq->where('name','LIKE','%'.$r->search.'%');
                      });
                    $q->orWhereHas('marchantize',function($qq) use($r){
                          $qq->where('name','LIKE','%'.$r->search.'%');
                      });
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

                if($r->status){
                   $q->where('order_status',$r->status);
                }else{
                   $q->where('order_status','<>','trash');
                }

            })
            ->paginate(25)->appends([
              'search'=>$r->search,
              'status'=>$r->status,
              'startDate'=>$r->startDate,
              'endDate'=>$r->endDate,
            ]);



        //Total Count Results
        $totals = DB::table('orders')
        ->where('order_type','pi_invoices')->where('parent_id',null)->where('order_status','<>','temp')
        ->selectRaw("count(case when order_status != 'trash' then 1 end) as total")
        ->selectRaw("count(case when order_status = 'pending' then 1 end) as pending")
        ->selectRaw("count(case when order_status = 'confirmed' then 1 end) as confirmed")
        ->selectRaw("count(case when order_status = 'completed' then 1 end) as completed")
        ->selectRaw("count(case when order_status = 'cancelled' then 1 end) as cancelled")
        ->selectRaw("count(case when order_status = 'trash' then 1 end) as trash")
        ->first();

        return view(adminTheme().'pi-invoices.piInvoices',compact('invoices','totals'));
    }

    public function piInvoicesAction(Request $r,$action,$id=null){

        if($action=='multi-invoice-pdf'){
            $invoices=Order::latest()->where('order_type','pi_invoices')->whereIn('id',$r->invoices)->get();

            return view(adminTheme().'pi-invoices.viewPiMultiInvoice',compact('invoices'));

            // $pdf = PDF::loadView(adminTheme().'pi-invoices.pdfPiInvoices', compact('invoices'));
            // return $pdf->stream('invoice.pdf');
        }

      //Add Service  Start
        if($action=='create'){

          $invoice =Order::where('order_type','pi_invoices')->where('order_status','temp')->where('addedby_id',Auth::id())->first();
          if(!$invoice){
            $invoice =new Order();
            $invoice->order_type ='pi_invoices';
            $invoice->order_status ='temp';
            $invoice->addedby_id =Auth::id();
            $invoice->save();
          }
          $invoice->note =general()->pi_terms_condition;
          $invoice->created_at =Carbon::now();
          $invoice->invoice =Carbon::now()->format('Ymd').$invoice->id;
          $invoice->save();

          return redirect()->route('admin.piInvoicesAction',['edit',$invoice->id]);
        }
        //Add Service  End

        $invoice =Order::where('order_type','pi_invoices')->find($id);
        if(!$invoice){
            Session()->flash('error','This PI Invoices Are Not Found');
            return redirect()->route('admin.piInvoices');
        }

        if($action=='re-generate-invoice'){

            $reInvoice =new Order();
            $reInvoice->order_type ='pi_invoices';
            $reInvoice->order_status =$invoice->order_status;
            $reInvoice->marchantizer_id=$invoice->marchantizer_id;
            $reInvoice->company_id=$invoice->company_id;
            $reInvoice->note =$invoice->note;
            $reInvoice->total_items=$invoice->total_items;
            $reInvoice->total_qty=$invoice->total_qty;
            $reInvoice->total_price=$invoice->total_price;
            $reInvoice->grand_total=$invoice->grand_total;
            $reInvoice->created_at =Carbon::now();
            $reInvoice->addedby_id =Auth::id();
            $reInvoice->save();

            $reInvoice->invoice =$reInvoice->incrementNumberInString();
            $reInvoice->save();

            foreach($invoice->items as $item){
                $reItem =new OrderItem();
                $reItem->order_id=$reInvoice->id;
                $reItem->status=$reInvoice->status;
                $reItem->product_name=$item->product_name;
                $reItem->description=$item->description;
                $reItem->unit=$item->unit;
                $reItem->price=$item->price?:0;
                $reItem->quantity=$item->quantity?:0;
                $reItem->final_price=$item->final_price?:0;
                $reItem->addedby_id=Auth::id();
                $reItem->save();
            }

            $invoice->parent_id=$reInvoice->id;
            $invoice->save();
            if($invoice->hasSubInvoices->count() > 0){
                $invoice->hasSubInvoices()->update(['parent_id' => $reInvoice->id]);
            }

            Session()->flash('success','This PI Invoice Re Generated Successfully Done!');
            return redirect()->route('admin.piInvoicesAction',['edit',$reInvoice->id]);
        }


        if($action=='re-pi-primary'){
            $hasInvoice =$invoice->hasSubInvoices()->find($r->re_pi_id);
            if($hasInvoice){

                if($invoice->hasSubInvoices()->where('id','<>',$hasInvoice->id)->count() > 0){
                    $invoice->hasSubInvoices()->where('id','<>',$hasInvoice->id)->update(['parent_id' => $hasInvoice->id]);
                }
                $invoice->parent_id=$hasInvoice->id;
                $invoice->save();
                $hasInvoice->parent_id=null;
                $hasInvoice->save();

                Session()->flash('success','This PI Invoice Make Primary Successfully Done!');
                return redirect()->route('admin.piInvoicesAction',['edit',$hasInvoice->id]);
            }
            Session()->flash('error','This PI Invoice Are Not found!');
            return redirect()->back();
        }

        if($action=='re-pi-delete'){
            $hasInvoice =$invoice->hasSubInvoices()->find($r->re_pi_id);
            if($hasInvoice){
                $hasInvoice->items()->delete();
                $hasInvoice->delete();
                Session()->flash('success','Re Generate PI Invoice Are Deleted!');
                return redirect()->route('admin.piInvoicesAction',['edit',$invoice->id]);
            }
            Session()->flash('error','This PI Invoice Are Not found!');
            return redirect()->back();
        }

        if($action=='invoice-pdf'){

            $invoices =array($invoice);

            $pdf = PDF::loadView(adminTheme().'pi-invoices.pdfPiInvoices', compact('invoices'));

            return $pdf->stream('invoice.pdf');
        }

        if($action=='view'){
            return view(adminTheme().'pi-invoices.viewPiInvoice',compact('invoice'));
        }

        if($action=='add-merchandiser'){

            $data =Attribute::latest()->where('type',4)->where('status','active')->find($r->merchandiser_id);
            if($data){
                $invoice->marchantizer_id=$data->id;
                $invoice->invoice =$invoice->incrementNumberInString();
                $invoice->save();
            }

            $view =view(adminTheme().'pi-invoices.includes.piOrderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);


        }

        if($action=='search-marchantizer'){

            $merchandisers =Attribute::latest()->where('type',4)->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'pi-invoices.includes.searchMarchantizer',compact('merchandisers','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='add-company'){

            $data =Company::latest()->where('status','active')->find($r->company_id);
            if($data){
                $invoice->company_id=$data->id;
                $invoice->save();
            }

            $view =view(adminTheme().'pi-invoices.includes.piOrderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);
        }

        if($action=='search-goods'){

            $services =Post::latest()->where('type',3)->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'pi-invoices.includes.searchGoods',compact('services','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='search-company'){

            $companies =Company::latest()->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('factory_name','like','%'.$r->search.'%')->orWhere('owner_name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'pi-invoices.includes.searchCompany',compact('companies','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='add-item' || $action=='add-goods' || $action=='remove-item' || $action=='update-item'){

            if($action=='add-item'){
                $item =new OrderItem();
                $item->order_id=$invoice->id;
                $item->status=$invoice->status;
                $item->addedby_id=Auth::id();
                $item->save();
            }

            if($action=='add-goods'){
                $service =Post::latest()->where('type',3)->where('status','active')->find($r->service_id);
                if($service){
                    $item =$invoice->items()->where('src_id',$service->id)->first();
                    if(!$item){
                        $item =new OrderItem();
                        $item->order_id=$invoice->id;
                        $item->src_id=$service->id;
                        $item->quantity=1;
                        $item->description=$service->name;
                        $item->unit=$service->unit?$service->unit->name:null;
                        $item->price=$service->item_price?:0;
                        $item->final_price =$item->price*$item->quantity;
                        $item->status=$invoice->status;
                        $item->addedby_id=Auth::id();
                        $item->save();
                    }
                }
            }

            if($action=='remove-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    $item->delete();
                }
            }

            if($action=='update-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    if($r->name=='product_name' || $r->name=='description' || $r->name=='unit' || $r->name=='price' || $r->name=='quantity' || $r->name=='delivered_at'){
                      if($r->name=='price' || $r->name=='quantity'){
                      $item[$r->name]=$r->data?:0;
                      }else{
                      $item[$r->name]=$r->data?:null;
                      }

                      if($r->name=='price' || $r->name=='quantity'){
                        $item->final_price =$item->price*$item->quantity;
                      }
                      $item->save();
                    }
                }


                $invoice->total_items=$invoice->items()->count();
                $invoice->total_qty=$invoice->items()->sum('quantity');
                $invoice->total_price=$invoice->items()->sum('final_price');
                $invoice->grand_total=$invoice->items()->sum('final_price');
                $invoice->save();

                return Response()->json([
                'success' => true,
                ]);
            }

            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->save();

            $view =view(adminTheme().'pi-invoices.includes.piOrderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);

        }

        if($action=='update'){

            $check = $r->validate([
                'status' => 'nullable|max:20',
                // 'invoice' => 'required|max:100',
                'created_at' => 'required|date',
                'terms_conditions' => 'nullable',
                'remark' => 'nullable',
            ]);

            // $invoice->invoice=$r->pi_no;
            $invoice->created_at=$r->created_at?:Carbon::now();
            $invoice->note=$r->terms_conditions;
            $invoice->remark=$r->remark;
            if($invoice->hasLcOrders->count() > 0){
            }else{
                $invoice->order_status=$r->status?:'pending';
            }
            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->save();

            Session()->flash('success','Your Are Successfully Updated');
            return redirect()->back();

        }

        if($action=='delete'){
            if($invoice->order_status=='trash'){
                foreach($invoice->hasSubInvoices as $inv){
                    $inv->items()->delete();
                    $inv->delete();
                }

                $invoice->items()->delete();
                $invoice->delete();
            }else{
               $invoice->order_status='trash';
               $invoice->save();
            }

            Session()->flash('success','Your Are Successfully Deleted');
            return redirect()->back();
        }

        $merchandisers =Attribute::latest()->where('type',4)->where('status','active')->limit(10)->get();
        $companies =Company::latest()->where('status','active')->limit(10)->get();



      return view(adminTheme().'pi-invoices.editPiInvoices',compact('invoice','merchandisers','companies'));
    }



    public function piReports(Request $r){

        $invoices =null;
        $totalPi=0;
        $totalValue=0;
        $openLC=0;
        $pendingLC=0;

        if($r->search || $r->company || $r->merchandiser || $r->startDate || $r->endDate || $r->status){

            $invoicesR =Order::latest()->where('order_type','pi_invoices')->where('order_status','<>','temp')
                ->where(function($q) use ($r) {

                    if($r->search){
                        $q->where('invoice','LIKE','%'.$r->search.'%');
                    }

                    if($r->status){
                        $q->where('order_status',$r->status);
                    }

                    if($r->company){
                        $q->where('company_id',$r->company);
                    }

                    if($r->merchandiser){
                        $q->where('marchantizer_id',$r->merchandiser);
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

                });

            $invoices =$invoicesR->get();

            $totalPi=$invoicesR->count();
            $totalValue=$invoicesR->sum('grand_total');
            $openLC=$invoicesR->whereHas('hasLcOrders')->count();
            $pendingLC=$totalPi - $invoicesR->whereHas('hasLcOrders')->count();


        }

        $merchandisers =Attribute::latest()->where('type',4)->where('status','active')->select(['id','name'])->get();
        $companies =Attribute::latest()->where('type',1)->where('status','active')->select(['id','name'])->get();


        $reports =array(
                'totalPi'=>$totalPi,
                'totalPiValue'=>$totalValue,
                'openLc'=>$openLC,
                'pendingLc'=>$pendingLC,
            );

        return view(adminTheme().'pi-invoices.reportsPiInvoices',compact('invoices','merchandisers','companies','reports'));
    }


    public function deliveryPlan(Request $r){

        $items =OrderItem::latest()->whereHas('order',function($q){
                $q->where('order_type','pi_invoices')->whereIn('order_status',['completed','confirmed']);
            })
            ->where('delivered_at','<>',null)
            ->get();


        return view(adminTheme().'pi-invoices.deliveryInvoices',compact('items'));
    }


// Invoice Management Function


    // Quotation Management Function
    public function quotations(Request $r){

        if(
            empty(json_decode(Auth::user()->permission->permission, true)['quotation']['add']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['quotation']['view']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['quotation']['delete']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['quotation']['report'])
        ){
          return  abort(401);
        }


        // Filter Action Start
            if($r->action){

                if($r->checkid){

                    $datas=Order::latest()->where('order_type','quotation_order')->whereIn('id',$r->checkid)->get();

                    if($r->action==1){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='pending';
                                $data->save();
                            }
                        }
                    }elseif($r->action==2){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='confirmed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==3){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='completed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==4){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='cancelled';
                                $data->save();
                            }
                        }
                    }elseif($r->action==5){


                        foreach($datas as $data){
                            if($data->order_status=='trash'){
                            foreach($data->hasSubInvoices as $inv){
                                $inv->items()->delete();
                                $inv->delete();
                            }
                            $data->items()->delete();
                            $data->delete();
                            }else{
                            $data->order_status='trash';
                            $data->save();
                            }
                        }
                    }

                Session()->flash('success','Action Successfully Completed!');

            }else{
              Session()->flash('info','Please Need To Select Minimum One Post');
            }

            return redirect()->back();
          }

        // Filter Action Start


        $invoices =Order::latest()->where('order_type','quotation_order')->where('parent_id',null)->where('order_status','<>','temp')
            ->where(function($q) use ($r) {

                if($r->search){
                    $q->where('invoice','LIKE','%'.$r->search.'%');
                    $q->orWhereHas('company',function($qq) use($r){
                          $qq->where('name','LIKE','%'.$r->search.'%');
                      });
                    $q->orWhereHas('marchantize',function($qq) use($r){
                          $qq->where('name','LIKE','%'.$r->search.'%');
                      });
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

                if($r->status){
                   $q->where('order_status',$r->status);
                }else{
                   $q->where('order_status','<>','trash');
                }

            })
            ->paginate(25)->appends([
              'search'=>$r->search,
              'status'=>$r->status,
              'startDate'=>$r->startDate,
              'endDate'=>$r->endDate,
            ]);



        //Total Count Results
        $totals = DB::table('orders')
        ->where('order_type','quotation_order')->where('parent_id',null)->where('order_status','<>','temp')
        ->selectRaw("count(case when order_status != 'trash' then 1 end) as total")
        ->selectRaw("count(case when order_status = 'pending' then 1 end) as pending")
        ->selectRaw("count(case when order_status = 'confirmed' then 1 end) as confirmed")
        ->selectRaw("count(case when order_status = 'completed' then 1 end) as completed")
        ->selectRaw("count(case when order_status = 'cancelled' then 1 end) as cancelled")
        ->selectRaw("count(case when order_status = 'trash' then 1 end) as trash")
        ->first();

        return view(adminTheme().'quotations.piInvoices',compact('invoices','totals'));
    }

    public function quotationsAction(Request $r,$action,$id=null){

        if($action=='multi-invoice-pdf'){
            $invoices=Order::latest()->where('order_type','quotation_order')->whereIn('id',$r->invoices)->get();

            return view(adminTheme().'pi-invoices.viewPiMultiInvoice',compact('invoices'));

            // $pdf = PDF::loadView(adminTheme().'pi-invoices.pdfPiInvoices', compact('invoices'));
            // return $pdf->stream('invoice.pdf');
        }

      //Add Service  Start
        if($action=='create'){

          $invoice =Order::where('order_type','quotation_order')->where('order_status','temp')->where('addedby_id',Auth::id())->first();
          if(!$invoice){
            $invoice =new Order();
            $invoice->order_type ='quotation_order';
            $invoice->order_status ='temp';
            $invoice->addedby_id =Auth::id();
            $invoice->save();
          }
          $invoice->note =general()->pi_terms_condition;
          $invoice->created_at =Carbon::now();
          $invoice->invoice =Carbon::now()->format('Ymd').$invoice->id;
          $invoice->save();

          return redirect()->route('admin.quotationsAction',['edit',$invoice->id]);
        }
        //Add Service  End

        $invoice =Order::where('order_type','quotation_order')->find($id);
        if(!$invoice){
            Session()->flash('error','This Quotation Are Not Found');
            return redirect()->route('admin.quotations');
        }

        if($action=='invoice-pdf'){

            $invoices =array($invoice);

            $pdf = PDF::loadView(adminTheme().'quotations.pdfPiInvoices', compact('invoices'));

            return $pdf->stream('invoice.pdf');
        }

        if($action=='view'){

            return view(adminTheme().'quotations.viewPiInvoice',compact('invoice'));
        }


        if($action=='add-company'){

            $data =Company::latest()->where('status','active')->find($r->company_id);
            if($data){
                $invoice->company_id=$data->id;
                $invoice->save();
            }

            $view =view(adminTheme().'quotations.includes.piOrderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);
        }

        if($action=='search-goods'){

            $services =Post::latest()->where('type',3)->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'quotations.includes.searchGoods',compact('services','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='search-company'){

            $companies =Company::latest()->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('factory_name','like','%'.$r->search.'%')->orWhere('owner_name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'quotations.includes.searchCompany',compact('companies','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='add-item' || $action=='add-goods' || $action=='remove-item' || $action=='update-item'){

            if($action=='add-item'){
                $item =new OrderItem();
                $item->order_id=$invoice->id;
                $item->status=$invoice->status;
                $item->addedby_id=Auth::id();
                $item->save();
            }

            if($action=='add-goods'){
                $service =Post::latest()->where('type',3)->where('status','active')->find($r->service_id);
                if($service){
                    $item =$invoice->items()->where('src_id',$service->id)->first();
                    if(!$item){
                        $item =new OrderItem();
                        $item->order_id=$invoice->id;
                        $item->src_id=$service->id;
                        $item->quantity=1;
                        $item->description=$service->name;
                        $item->unit=$service->unit?$service->unit->name:null;
                        $item->price=$service->item_price?:0;
                        $item->final_price =$item->price*$item->quantity;
                        $item->status=$invoice->status;
                        $item->addedby_id=Auth::id();
                        $item->save();
                    }
                }
            }

            if($action=='remove-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    $item->delete();
                }
            }

            if($action=='update-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    if($r->name=='product_name' || $r->name=='description' || $r->name=='unit' || $r->name=='price' || $r->name=='quantity'){
                      if($r->name=='price' || $r->name=='quantity'){
                      $item[$r->name]=$r->data?:0;
                      }else{
                      $item[$r->name]=$r->data?:null;
                      }

                      if($r->name=='price' || $r->name=='quantity'){
                        $item->final_price =$item->price*$item->quantity;
                      }
                      $item->save();
                    }
                }


                $invoice->total_items=$invoice->items()->count();
                $invoice->total_qty=$invoice->items()->sum('quantity');
                $invoice->total_price=$invoice->items()->sum('final_price');
                $invoice->grand_total=$invoice->items()->sum('final_price');
                $invoice->save();

                return Response()->json([
                'success' => true,
                ]);
            }

            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->save();

            $view =view(adminTheme().'quotations.includes.piOrderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);

        }

        if($action=='update'){

            $check = $r->validate([
                'status' => 'nullable|max:20',
                'created_at' => 'required|date',
                'terms_conditions' => 'nullable',
                'remark' => 'nullable',
            ]);

            $invoice->created_at=$r->created_at?:Carbon::now();
            $invoice->note=$r->terms_conditions;
            $invoice->remark=$r->remark;
            $invoice->order_status=$r->status?:'pending';
            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->save();

            Session()->flash('success','Your Are Successfully Updated');
            return redirect()->route('admin.quotationsAction',['view',$invoice->id]);
            return redirect()->back();

        }

        if($action=='delete'){
            if($invoice->order_status=='trash'){
                foreach($invoice->hasSubInvoices as $inv){
                    $inv->items()->delete();
                    $inv->delete();
                }

                $invoice->items()->delete();
                $invoice->delete();
            }else{
               $invoice->order_status='trash';
               $invoice->save();
            }

            Session()->flash('success','Your Are Successfully Deleted');
            return redirect()->back();
        }

        $merchandisers =Attribute::latest()->where('type',4)->where('status','active')->limit(10)->get();
        $companies =Company::latest()->where('status','active')->limit(10)->get();



      return view(adminTheme().'quotations.editPiInvoices',compact('invoice','merchandisers','companies'));
    }


    // Quotation Management Function
    public function billCollection(Request $r){

        // Filter Action Start
            if($r->action){

                if($r->checkid){

                    $datas=Order::latest()->where('order_type','bill_order')->whereIn('id',$r->checkid)->get();

                    if($r->action==1){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='pending';
                                $data->save();
                            }
                        }
                    }elseif($r->action==2){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='confirmed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==3){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='completed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==4){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='cancelled';
                                $data->save();
                            }
                        }
                    }elseif($r->action==5){


                        foreach($datas as $data){
                            if($data->order_status=='trash'){
                            foreach($data->hasSubInvoices as $inv){
                                $inv->items()->delete();
                                $inv->delete();
                            }
                            $data->items()->delete();
                            $data->delete();
                            }else{
                            $data->order_status='trash';
                            $data->save();
                            }
                        }
                    }

                Session()->flash('success','Action Successfully Completed!');

            }else{
              Session()->flash('info','Please Need To Select Minimum One Post');
            }

            return redirect()->back();
          }

        // Filter Action Start


        $billcollections =Transaction::where('type','0')->whereIn('status',['success'])
        ->whereHas('sale', function ($query)use($r) {
            $query->where('invoice', 'like', '%'.$r->search.'%')->orWhere('billing_name', 'like', '%'.$r->search.'%');
        })
        ->where(function($q)use($r){
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
        });

        if($r->export=='report'){

            $billcollections =$billcollections->get();

            return view(adminTheme().'bill-collections.exportInvoices',compact('billcollections'));
        }else{
            $billcollections =$billcollections->paginate(10);
        }

        return view(adminTheme().'bill-collections.invoices',compact('billcollections'));
    }

    public function billDueCollection(Request $r){

        // Filter Action Start
            if($r->action){

                if($r->checkid){

                    $datas=Order::latest()->where('order_type','bill_order')->whereIn('id',$r->checkid)->get();

                    if($r->action==1){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='pending';
                                $data->save();
                            }
                        }
                    }elseif($r->action==2){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='confirmed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==3){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='completed';
                                $data->save();
                            }
                        }
                    }elseif($r->action==4){
                        foreach($datas as $data){
                            if($data->hasLcOrders->count()==0){
                                $data->order_status='cancelled';
                                $data->save();
                            }
                        }
                    }elseif($r->action==5){


                        foreach($datas as $data){
                            if($data->order_status=='trash'){
                            foreach($data->hasSubInvoices as $inv){
                                $inv->items()->delete();
                                $inv->delete();
                            }
                            $data->items()->delete();
                            $data->delete();
                            }else{
                            $data->order_status='trash';
                            $data->save();
                            }
                        }
                    }

                Session()->flash('success','Action Successfully Completed!');

            }else{
              Session()->flash('info','Please Need To Select Minimum One Post');
            }

            return redirect()->back();
          }

        // Filter Action Start


        $billcollections =Transaction::where('type','0')->whereIn('status',['pending'])
        ->whereHas('sale', function ($query)use($r) {
            $query->where('invoice', 'like', '%'.$r->search.'%')->orWhere('billing_name', 'like', '%'.$r->search.'%');
        })
        ->where(function($q)use($r){

                if($r->due_type=='over'){
                    $toDate =Carbon::now();
                    $q->whereDate('created_at','<',$toDate);
                }elseif($r->due_type=='next'){
                    $toDate =Carbon::now();
                    $q->whereDate('created_at','>=',$toDate);
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
        });

        if($r->export=='report'){

            $billcollections = $billcollections->get();

            return view(adminTheme().'bill-collections.exportDueInvoice',compact('billcollections'));

        }else{
            $billcollections = $billcollections->paginate(10);
        }




        return view(adminTheme().'bill-collections.dueInvoices',compact('billcollections'));
    }

    public function billCollectionAction(Request $r,$action,$id=null){

      //Add Service  Start
        if($action=='create'){
            $check = $r->validate([
                'invoice' => 'required|max:100',
            ]);

            $invoice =Order::where('order_type','sale_invoices')->where('invoice',$r->invoice)->first();
            if(!$invoice){
                Session()->flash('error','This Invoice Are Not Found');
                return redirect()->route('admin.billCollection');
            }

          return redirect()->route('admin.billCollectionAction',['edit',$invoice->id]);
        }
        //Add Service  End

        $invoice =Order::where('order_type','sale_invoices')->find($id);
        if(!$invoice){
            Session()->flash('error','This Invoice Are Not Found');
            return redirect()->route('admin.billCollection');
        }

        if($action=='invoice-pdf'){

            $invoices =array($invoice);

            $pdf = PDF::loadView(adminTheme().'bill-collections.pdfPiInvoices', compact('invoices'));

            return $pdf->stream('invoice.pdf');
        }

        if($action=='view'){

            return view(adminTheme().'bill-collections.viewPiInvoice',compact('invoice'));
        }


        if($action=='payment-reset'){
            if($r->trans_id==0){
                $datas =$invoice->transectionsAll()->whereIn('status',['success'])->get();
                foreach($datas as $data){
                    $account =$data->accountMethod;
                    if($account){
                        if($data->currency=='USD'){
                        $account->usd_amount -=$data->amount;
                        }else{
                        $account->amount -=$data->amount;
                        }
                        $account->save();
                    }
                    $data->account_id=null;
                    $data->payment_method_id=null;
                    $data->status='cancelled';
                    $data->save();
                }
            }else{
                $transfer =$invoice->transectionsAll()->whereIn('status',['success'])->find($r->trans_id);
                if(!$transfer){
                    Session()->flash('error','Transection Are Not found');
                    return redirect()->back();
                }

                $account =$transfer->accountMethod;
                if($account){
                    if($transfer->currency=='USD'){
                        $account->usd_amount -=$transfer->amount;
                    }else{
                        $account->amount -=$transfer->amount;
                    }
                    $account->save();
                }
                $transfer->account_id=null;
                $transfer->payment_method_id=null;
                $transfer->status ='pending';
                $transfer->save();
            }


            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();


            Session()->flash('success','Your payment are reset success!');
            return redirect()->back();

        }
        if($action=='payment-delete'){
            $transfer =$invoice->transectionsAll()->find($r->trans_id);
            if(!$transfer){
                Session()->flash('error','Transection Are Not found');
                return redirect()->back();
            }

            $account =$transfer->accountMethod;
            if($account){
                if($transfer->currency=='USD'){
                    $account->usd_amount -=$transfer->amount;
                }else{
                    $account->amount -=$transfer->amount;
                }
                $account->save();
            }
            $transfer->delete();

            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();

            Session()->flash('success','Your payment are Cancelled!');
            return redirect()->back();

        }
        if($action=='payment-pay'){
            $transfer =$invoice->transectionsAll()->whereIn('status',['pending'])->find($r->trans_id);
            if(!$transfer){
                Session()->flash('error','Transection installment Are Not found');
                return redirect()->back();
            }

            $check = $r->validate([
                'created_at' => 'required|date',
                // 'amount' => 'required|numeric',
                'account' => 'required|numeric',
                'method' => 'required|numeric',
                'note' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $account =Attribute::where('type',10)->where('status','active')->find($r->account);
            if(!$account){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }
            $amount =$transfer->amount;
            if($transfer->currency=='USD'){
                $account->usd_amount +=$amount;
            }else{
                $account->amount +=$amount;
            }
            $account->save();

            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $transfer->account_id=$account->id;
            $transfer->billing_name=$invoice->name;
            $transfer->billing_mobile=$invoice->mobile;
            $transfer->billing_email=$invoice->email;
            $transfer->billing_address=$invoice->fullAddress();
            $transfer->payment_method_id=$r->method?:null;
            $transfer->billing_note=$r->note;
            $transfer->status ='success';
            $transfer->addedby_id =Auth::id();
            $transfer->created_at = $createDate;
            $transfer->save();

            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$transfer->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();

            Session()->flash('success','Your payment are Successfully Updated');
            return redirect()->back();


        }

        if($action=='payment-down'){

            $check = $r->validate([
                'created_at' => 'required|date',
                // 'amount' => 'required|numeric',
                'account' => 'required|numeric',
                'method' => 'required|numeric',
                'note' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $account =Attribute::where('type',10)->where('status','active')->find($r->account);
            if(!$account){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }

            $amount =$invoice->grand_total - $invoice->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->sum('amount');
            $collectPayment =$invoice->transectionsAll()->where('billing_reason','not like','%Installment%')->whereIn('status',['success'])->sum('amount');
            if($collectPayment >= $amount){
                Session()->flash('error','Down payment already paid');
                return redirect()->back();
            }

            if($invoice->currency=='USD'){
                $account->usd_amount +=$amount;
            }else{
                $account->amount +=$amount;
            }
            $account->save();

            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $transfer =new Transaction();
            $transfer->type=0;
            $transfer->src_id=$invoice->id;
            $transfer->account_id=$account->id;
            $transfer->billing_name=$invoice->name;
            $transfer->billing_mobile=$invoice->mobile;
            $transfer->billing_email=$invoice->email;
            $transfer->billing_address=$invoice->fullAddress();
            $transfer->payment_method_id=$r->method?:null;
            $transfer->amount=$amount;
            $transfer->currency=$invoice->currency?:'BDT';
            $transfer->billing_note=$r->note;
            $transfer->billing_reason ='Bill Pay';
            $transfer->status ='success';
            $transfer->addedby_id =Auth::id();
            $transfer->created_at = $createDate;
            $transfer->save();

            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$transfer->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();

            Session()->flash('success','Your payment are Successfully Updated');
            return redirect()->back();
        }

        if($action=='payment-received'){

            $check = $r->validate([
                'created_at' => 'required|date',
                // 'amount' => 'required|numeric',
                'transection_id' => 'required|numeric',
                'account' => 'required|numeric',
                'method' => 'required|numeric',
                'note' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $transfer =$invoice->transectionsAll()->where('status','pending')->find($r->transection_id);
            if(!$transfer){
                Session()->flash('error','Transection Are Not found');
                return redirect()->back();
            }

            $account =Attribute::where('type',10)->where('status','active')->find($r->account);
            if(!$account){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }

            $amount =$transfer->amount;
            $collectPayment =$invoice->transectionsAll()->where('billing_reason','not like','%Installment%')->whereIn('status',['success'])->sum('amount');
            if($collectPayment >= $amount){
                Session()->flash('error','Down payment already paid');
                return redirect()->back();
            }

            if($invoice->currency=='USD'){
                $account->usd_amount +=$amount;
            }else{
                $account->amount +=$amount;
            }
            $account->save();

            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $transfer->account_id=$account->id;
            $transfer->billing_name=$invoice->name;
            $transfer->billing_mobile=$invoice->mobile;
            $transfer->billing_email=$invoice->email;
            $transfer->billing_address=$invoice->fullAddress();
            $transfer->payment_method_id=$r->method?:null;
            $transfer->currency=$invoice->currency?:'BDT';
            $transfer->billing_note=$r->note;
            $transfer->status ='success';
            $transfer->editedby_id =Auth::id();
            $transfer->created_at = $createDate;
            $transfer->save();

            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$transfer->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();

            Session()->flash('success','Your payment are Successfully Updated');
            return redirect()->back();
        }


        if($action=='payment-create'){

            $check = $r->validate([
                'created_at' => 'required|date',
                'amount' => 'required|numeric',
                'account' => 'required|numeric',
                'method' => 'required|numeric',
                'note' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $account =Attribute::where('type',10)->where('status','active')->find($r->account);
            if(!$account){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }


            $amount =$r->amount?:0;
            if($invoice->currency=='USD'){
                $account->usd_amount +=$amount;
            }else{
                $account->amount +=$amount;
            }
            $account->save();


            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $transfer =new Transaction();
            $transfer->type=0;
            $transfer->src_id=$invoice->id;
            $transfer->account_id=$account->id;
            $transfer->billing_name=$invoice->name;
            $transfer->billing_mobile=$invoice->mobile;
            $transfer->billing_email=$invoice->email;
            $transfer->billing_address=$invoice->fullAddress();
            $transfer->payment_method_id=$r->method?:null;
            $transfer->amount=$amount;
            $transfer->currency=$invoice->currency?:'BDT';
            $transfer->billing_note=$r->note;
            $transfer->billing_reason ='Bill Pay';
            $transfer->status ='success';
            $transfer->addedby_id =Auth::id();
            $transfer->created_at = $createDate;
            $transfer->save();

            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$transfer->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();

            Session()->flash('success','Your payment are Successfully Updated');
            return redirect()->back();

        }

        if($action=='add-company'){

            $data =Company::latest()->where('status','active')->find($r->company_id);
            if($data){
                $invoice->company_id=$data->id;
                $invoice->save();
            }

            $view =view(adminTheme().'bill-collections.includes.piOrderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);
        }

        if($action=='search-goods'){

            $services =Post::latest()->where('type',3)->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'bill-collections.includes.searchGoods',compact('services','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='search-company'){

            $companies =Company::latest()->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('factory_name','like','%'.$r->search.'%')->orWhere('owner_name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'bill-collections.includes.searchCompany',compact('companies','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='add-item' || $action=='add-goods' || $action=='remove-item' || $action=='update-item'){

            if($action=='add-item'){
                $item =new OrderItem();
                $item->order_id=$invoice->id;
                $item->status=$invoice->status;
                $item->addedby_id=Auth::id();
                $item->save();
            }

            if($action=='add-goods'){
                $service =Post::latest()->where('type',3)->where('status','active')->find($r->service_id);
                if($service){
                    $item =$invoice->items()->where('src_id',$service->id)->first();
                    if(!$item){
                        $item =new OrderItem();
                        $item->order_id=$invoice->id;
                        $item->src_id=$service->id;
                        $item->quantity=1;
                        $item->description=$service->name;
                        $item->unit=$service->unit?$service->unit->name:null;
                        $item->price=$service->item_price?:0;
                        $item->final_price =$item->price*$item->quantity;
                        $item->status=$invoice->status;
                        $item->addedby_id=Auth::id();
                        $item->save();
                    }
                }
            }

            if($action=='remove-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    $item->delete();
                }
            }

            if($action=='update-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    if($r->name=='product_name' || $r->name=='description' || $r->name=='unit' || $r->name=='price' || $r->name=='quantity'){
                      if($r->name=='price' || $r->name=='quantity'){
                      $item[$r->name]=$r->data?:0;
                      }else{
                      $item[$r->name]=$r->data?:null;
                      }

                      if($r->name=='price' || $r->name=='quantity'){
                        $item->final_price =$item->price*$item->quantity;
                      }
                      $item->save();
                    }
                }


                $invoice->total_items=$invoice->items()->count();
                $invoice->total_qty=$invoice->items()->sum('quantity');
                $invoice->total_price=$invoice->items()->sum('final_price');
                $invoice->grand_total=$invoice->items()->sum('final_price');
                $invoice->save();

                return Response()->json([
                'success' => true,
                ]);
            }

            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->save();

            $view =view(adminTheme().'bill-collections.includes.orderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);

        }

        if($action=='update'){

            $check = $r->validate([
                'status' => 'nullable|max:20',
                'created_at' => 'required|date',
                'terms_conditions' => 'nullable',
                'remark' => 'nullable',
            ]);

            $invoice->created_at=$r->created_at?:Carbon::now();
            $invoice->note=$r->terms_conditions;
            $invoice->remark=$r->remark;
            $invoice->order_status=$r->status?:'pending';
            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->save();

            Session()->flash('success','Your Are Successfully Updated');
            return redirect()->route('admin.billCollectionAction',['view',$invoice->id]);
            return redirect()->back();

        }

        if($action=='delete'){
            if($invoice->order_status=='trash'){
                foreach($invoice->hasSubInvoices as $inv){
                    $inv->items()->delete();
                    $inv->delete();
                }

                $invoice->items()->delete();
                $invoice->delete();
            }else{
               $invoice->order_status='trash';
               $invoice->save();
            }

            Session()->flash('success','Your Are Successfully Deleted');
            return redirect()->back();
        }

        $merchandisers =Attribute::latest()->where('type',4)->where('status','active')->limit(10)->get();
        $companies =Company::latest()->where('status','active')->limit(10)->get();

        $paymentMethods =Attribute::latest()->where('type',9)->where('status','active')->select(['id','name','amount'])->get();
        $accountMethods =Attribute::latest()->where('type',10)->where('status','active')->where('addedby_id',Auth::id())->select(['id','name','amount'])->get();

        return view(adminTheme().'bill-collections.editInvoices',compact('invoice','paymentMethods','accountMethods'));
    }


    // LC Management Function
    public function lcInvoices(Request $r){

        if(
            empty(json_decode(Auth::user()->permission->permission, true)['pi']['add']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['pi']['view']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['pi']['delete']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['pi']['report'])
        ){
          return  abort(401);
        }

        // Filter Action Start
            if($r->action){

                if($r->checkid){

                    if($r->action==1){
                    $datas=Order::latest()->where('order_type','lc_invoices')->whereIn('id',$r->checkid)->update(['order_status' => 'pending']);
                    }elseif($r->action==2){
                    $datas=Order::latest()->where('order_type','lc_invoices')->whereIn('id',$r->checkid)->update(['order_status' => 'confirmed']);
                    }elseif($r->action==3){
                    $datas=Order::latest()->where('order_type','lc_invoices')->whereIn('id',$r->checkid)->update(['order_status' => 'completed']);
                    }elseif($r->action==4){
                    $datas=Order::latest()->where('order_type','lc_invoices')->whereIn('id',$r->checkid)->update(['order_status' => 'cancelled']);
                    }elseif($r->action==5){
                    $datas=Order::latest()->where('order_type','lc_invoices')->whereIn('id',$r->checkid)->get();
                        foreach($datas as $data){
                            if($data->order_status=='trash'){
                                $data->items()->delete();
                                $data->delete();
                            }else{
                                $data->order_status='trash';
                                $data->save();
                            }
                        }
                    }elseif($r->action==6){
                    return redirect()->route('admin.lcInvoicesAction',['multi-invoice-pdf','invoices'=>$r->checkid]);
                    }

                Session()->flash('success','Action Successfully Completed!');

            }else{
              Session()->flash('info','Please Need To Select Minimum One Post');
            }

            return redirect()->back();
          }

        // Filter Action Start

        $invoices =Order::latest()->where('order_type','lc_invoices')->where('order_status','<>','temp')
            ->where(function($q) use ($r) {

                if($r->search){
                    $q->where(function($qq)use($r){
                        $qq->where('invoice','LIKE','%'.$r->search.'%')->orWhereHas('bank',function($qq) use($r){
                          $qq->where('name','LIKE','%'.$r->search.'%');
                        });
                    });
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

                if($r->status){
                   $q->where('order_status',$r->status);
                }else{
                  $q->where('order_status','<>','trash');
                }

            })
            ->paginate(25)->appends([
              'search'=>$r->search,
              'status'=>$r->status,
              'startDate'=>$r->startDate,
              'endDate'=>$r->endDate,
            ]);


        //Total Count Results
        $totals = DB::table('orders')
        ->where('order_type','lc_invoices')->where('order_status','<>','temp')
        ->selectRaw("count(case when order_status != 'trash' then 1 end) as total")
        ->selectRaw("count(case when order_status = 'confirmed' then 1 end) as confirmed")
        ->selectRaw("count(case when order_status = 'trash' then 1 end) as trash")
        ->first();

        return view(adminTheme().'lc-invoices.lcInvoices',compact('invoices','totals'));
    }

    public function lcInvoicesAction(Request $r,$action,$id=null){
        if($action=='multi-invoice-pdf'){
            $invoices=Order::latest()->where('order_type','lc_invoices')->whereIn('id',$r->invoices)->get();

            $pdf = PDF::loadView(adminTheme().'lc-invoices.pdfLcInvoices', compact('invoices'));
            return $pdf->stream('invoice.pdf');
        }

      //Add Service  Start
        if($action=='create'){

          $invoice =Order::where('order_type','lc_invoices')->where('order_status','temp')->where('addedby_id',Auth::id())->first();
          if(!$invoice){
            $invoice =new Order();
            $invoice->order_type ='lc_invoices';
            $invoice->order_status ='temp';
            $invoice->addedby_id =Auth::id();
            $invoice->save();
          }
          $invoice->created_at =Carbon::now();
          $invoice->save();

          return redirect()->route('admin.lcInvoicesAction',['edit',$invoice->id]);
        }
        //Add Service  End

        $invoice =Order::where('order_type','lc_invoices')->find($id);
        if(!$invoice){
            Session()->flash('error','This LC Invoices Are Not Found');
            return redirect()->route('admin.lcInvoices');
        }

        if($action=='view'){

            return view(adminTheme().'lc-invoices.viewLcInvoice',compact('invoice'));
        }

        if($action=='search-goods'){

            $services =Post::latest()->where('type',3)->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'lc-invoices.includes.searchGoods',compact('services','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='search-company'){

            $companies =Company::latest()->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('factory_name','like','%'.$r->search.'%')->orWhere('owner_name','like','%'.$r->search.'%');
                }
            })->limit(10)->get();

            $search =view(adminTheme().'lc-invoices.includes.searchCompany',compact('companies','invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $search,
            ]);
        }

        if($action=='add-company'){

            $data =Company::latest()->where('status','active')->find($r->company_id);
            if($data){
                $invoice->company_id=$data->id;
                $invoice->save();
            }

            $view =view(adminTheme().'lc-invoices.includes.orderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);
        }


        if($action=='add-item' || $action=='add-goods' || $action=='remove-item' || $action=='update-item'){

            if($action=='add-item'){
                $item =new OrderItem();
                $item->order_id=$invoice->id;
                $item->status=$invoice->status;
                $item->addedby_id=Auth::id();
                $item->save();
            }

            if($action=='add-goods'){
                $service =Post::latest()->where('type',3)->where('status','active')->find($r->service_id);
                if($service){
                    $item =$invoice->items()->where('src_id',$service->id)->first();
                    if(!$item){
                        $item =new OrderItem();
                        $item->order_id=$invoice->id;
                        $item->src_id=$service->id;
                        $item->quantity=1;
                        $item->description=$service->name;
                        $item->unit=$service->unit?$service->unit->name:null;
                        $item->price=$service->item_price?:0;
                        $item->final_price =$item->price*$item->quantity;
                        $item->status=$invoice->status;
                        $item->addedby_id=Auth::id();
                        $item->save();
                    }
                }
            }

            if($action=='remove-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    $item->delete();
                }
            }

            if($action=='update-item'){
                $item =$invoice->items()->find($r->item_id);
                if($item){
                    if($r->name=='product_name' || $r->name=='description' || $r->name=='unit' || $r->name=='price' || $r->name=='quantity'){
                      if($r->name=='price' || $r->name=='quantity'){
                      $item[$r->name]=$r->data?:0;
                      }else{
                      $item[$r->name]=$r->data?:null;
                      }

                      if($r->name=='price' || $r->name=='quantity'){
                        $item->final_price =$item->price*$item->quantity;
                      }
                      $item->save();
                    }
                }


                $invoice->total_items=$invoice->items()->count();
                $invoice->total_qty=$invoice->items()->sum('quantity');
                $invoice->total_price=$invoice->items()->sum('final_price');
                // $invoice->grand_total=$invoice->items()->sum('final_price');
                $invoice->save();

                return Response()->json([
                'success' => true,
                ]);
            }

            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            // $invoice->grand_total=$invoice->items()->sum('final_price');
            $invoice->save();

            $view =view(adminTheme().'lc-invoices.includes.orderItems',compact('invoice'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);

        }

        if($action=='update'){

            $check = $r->validate([
                'lc_open_bank' => 'nullable|max:100',
                'total_amount' => 'nullable|numeric',
                'lc_value_rate' => 'nullable|numeric',
                'lc_total_value' => 'nullable|numeric',
                'lc_no' => 'required|max:100',
                'created_at' => 'required|date',
                'submited_date' => 'nullable|date',
                'estimated_date' => 'nullable|date',
                'status' => 'nullable|max:20',
                'note' => 'nullable|max:2000',
            ]);

            $invoice->invoice=$r->lc_no;
            $invoice->lc_open_bank=$r->lc_open_bank;
            $invoice->grand_total=$r->total_amount?:0;
            $invoice->lc_value_rate=$r->lc_value_rate?:0;
            $invoice->lc_total_value=$r->lc_total_value?:0;
            $invoice->created_at=$r->created_at?:Carbon::now();
            $invoice->pending_at=$r->submited_date;
            $invoice->maturity_at=$r->estimated_date;
            $invoice->note=$r->note;
            $invoice->order_status=$r->status?:'confirmed';

            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->save();

            Session()->flash('success','Your Are Successfully Updated');
            return redirect()->back();

        }


        if($action=='delete'){


            if($invoice->order_status=='trash'){
                $invoice->items()->delete();
                $invoice->delete();
            }else{
                foreach($invoice->items()->whereHas('piOrder')->get() as $item){
                    $data =$item->piOrder;
                    $data->order_status='confirmed';
                    $data->save();
                }
                $invoice->order_status='trash';
                $invoice->save();
            }

            Session()->flash('success','Your Are Successfully Deleted');
            return redirect()->back();
        }

        $pinumbers =Order::latest()->where('order_type','pi_invoices')->where('order_status','confirmed')->select(['id','invoice'])->limit(10)->get();
        $banks =Attribute::latest()->where('type',9)->where('status','<>','temp')->where('fetured',true)->select(['id','name','description'])->get();
        $companies =Company::latest()->where('status','active')->limit(10)->get();

      return view(adminTheme().'lc-invoices.editLcInvoices',compact('invoice','pinumbers','companies'));
    }

    public function lcReports(Request $r){

        $invoices =null;
        if($r->search || $r->bank || $r->startDate || $r->endDate){

            $invoices = Order::latest()->where('order_type','lc_invoices')->where('order_status','<>','temp')
                ->where(function($q) use ($r) {

                    if($r->search){
                        $q->where('invoice','LIKE','%'.$r->search.'%');
                    }

                    if($r->bank){
                        $q->where('bank_id',$r->bank);
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
                ->get();

        }

        $merchandisers =Attribute::latest()->where('type',4)->where('status','active')->select(['id','name'])->get();
        $banks =Attribute::latest()->where('type',9)->where('status','active')->where('fetured',true)->select(['id','name','description'])->get();

        return view(adminTheme().'lc-invoices.reportsLcInvoices',compact('invoices','banks'));
    }
// LC Management Function

// Expensess Management Function

    public function expenses(Request $r){

        if(
            empty(json_decode(Auth::user()->permission->permission, true)['expenses']['add']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['expenses']['delete'])
        ){
          return  abort(401);
        }

        // Filter Action Start
            if($r->action){

                if($r->checkid){

                    $datas=Expense::latest()->whereIn('id',$r->checkid)->get();
                    foreach($datas as $data){

                        if($r->action==1){
                          $data->status='active';
                          $data->save();
                        }elseif($r->action==2){
                          $data->status='inactive';
                          $data->save();
                        }elseif($r->action==5){

                          if($method=$data->account){
                            $method->amount +=$data->amount;
                            $method->save();
                          }
                          if($trans =$data->transection){
                              $trans->delete();
                          }

                          $medias =Media::latest()->where('src_type',8)->where('src_id',$data->id)->get();
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

        $expenses =Expense::latest()->where('status','<>','temp')
                    ->where(function($q) use ($r) {

                              if($r->search){
                                  $q->where('name','LIKE','%'.$r->search.'%');
                              }

                              if($r->status){
                                 $q->where('status',$r->status);
                              }

                        })
                        ->paginate(25)->appends([
                          'search'=>$r->search,
                          'status'=>$r->status,
                        ]);

        $report = [
                'today_expenses' => numberFormat(
                    Expense::where('status', '<>', 'temp')
                        ->whereDate('created_at', Carbon::today())
                        ->sum('amount'),
                    2
                ),

                'monthly_expenses' => numberFormat(
                    Expense::where('status', '<>', 'temp')
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount'),
                    2
                ),
            ];

        $expenseTypes =Attribute::where('type',5)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $paymentMethods =Attribute::where('type',9)->where('status','active')->orderBy('name')->select(['id','name','amount'])->get();
        $accountMethods =Attribute::where('type',10)->where('status','active')->where('addedby_id',Auth::id())->orderBy('name')->select(['id','name','amount'])->get();
        $branches =Attribute::where('type',0)->where('status','active')->orderBy('name')->select(['id','name'])->get();

        return view(adminTheme().'expenses.expensesAll',compact('expenses','report','expenseTypes','paymentMethods','accountMethods','branches'));
    }


    public function expensesAction(Request $r,$action,$id=null){

        //Add Service  Start
        if($action=='create'){

            $check = $r->validate([
                'expense_type' => 'required|numeric',
                'payment' => 'required|numeric',
                'account' => 'required|numeric',
                'branch_id' => 'required|numeric',
                'amount' => 'required|numeric',
                // 'title' => 'required|max:100',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);


            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $method =Attribute::where('type',10)->where('status','active')->find($r->account);
            if(!$method){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }
            if($r->amount > $method->amount){
                Session()->flash('error','Account Balance Are Not Available');
                return redirect()->back();
            }

            $expense =new Expense();
            $expense->category_id=$r->expense_type;
            $expense->method_id=$r->payment;
            $expense->account_id=$method->id;
            $expense->branch_id=$r->branch_id;
            $expense->title=$r->title;
            $expense->amount=$r->amount;
            $expense->description=$r->description;
            $expense->status ='active';
            $expense->addedby_id =Auth::id();
            $expense->created_at = $createDate;
            $expense->save();

            $method->amount -=$expense->amount;
            $method->save();

            $transection =new Transaction();
            $transection->type=5;
            $transection->src_id=$expense->id;
            $transection->payment_method_id=$expense->account_id;
            $transection->amount=$expense->amount;
            $transection->status ='success';
            $transection->addedby_id =Auth::id();
            $transection->created_at =$expense->created_at;
            $transection->balance =$method->amount;
            $transection->save();

            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$expense->id;
              $srcType  =8;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }
        //Add Service  End

        $expense =Expense::find($id);
        if(!$expense){
            Session()->flash('error','This Expense Are Not Found');
            return redirect()->route('admin.expenses');
        }

        if($action=='update'){

            $check = $r->validate([
                'expense_type' => 'required|numeric',
                'payment' => 'required|numeric',
                'branch_id' => 'required|numeric',
                // 'title' => 'required|max:100',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);
            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();


            $expense->category_id=$r->expense_type;
            $expense->method_id=$r->payment;
            $expense->branch_id=$r->branch_id;
            $expense->title=$r->title;
            $expense->description=$r->description;
            $expense->status =$r->status?'active':'inactive';
            $expense->editedby_id =Auth::id();
            if (!$createDate->isSameDay($expense->created_at)) {
                $expense->created_at = $createDate;
            }
            $expense->save();

            if($transection = $expense->transection){
                $transection->created_at =$expense->created_at;
                $transection->save();
            }

            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$expense->id;
              $srcType  =8;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }


        if($action=='delete'){

            $medias =Media::latest()->where('src_type',8)->where('src_id',$expense->id)->get();
              foreach($medias as $media){
                if(File::exists($media->file_url)){
                  File::delete($media->file_url);
                }
                $media->delete();
              }
            if($expense->transection){
                $expense->transection->delete();
            }
            $expense->delete();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }

        return view(adminTheme().'expenses.expensesEdit',compact('expense','categories'));

    }


    public function expensesTypes(Request $r){

        if(
            empty(json_decode(Auth::user()->permission->permission, true)['expenses']['type'])
        ){
          return  abort(401);
        }

        // Filter Action Start
        if($r->action){

            if($r->checkid){

                $datas=Attribute::latest()->where('type',5)->whereIn('id',$r->checkid)->get();
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



        $categories =Attribute::where('type',5)->where('status','<>','temp')
            ->where(function($q) use ($r) {

                  if($r->search){
                      $q->where('name','LIKE','%'.$r->search.'%');
                  }

                  if($r->status){
                     $q->where('status',$r->status);
                  }

            })
            ->orderBy('name')
            ->select(['id','name','slug','description','created_at','addedby_id','status','fetured'])
                ->paginate(25)->appends([
                  'search'=>$r->search,
                  'status'=>$r->status,
                ]);

        return view(adminTheme().'expenses.expensesTypes',compact('categories'));
    }

    public function expensesTypesAction(Request $r,$action,$id=null){
        //Add Type  Start
        if($action=='create'){

            $check = $r->validate([
                'name' => 'required|max:100',
                'description' => 'nullable|max:1000',
            ]);

            $expenseType =Attribute::where('type',5)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$expenseType){
              $expenseType =new Attribute();
            }
            $expenseType->name=$r->name;
            $expenseType->description=$r->description;
            $expenseType->type =5;
            $expenseType->status ='active';
            $expenseType->addedby_id =Auth::id();
            $expenseType->save();

            $slug =Str::slug($r->name);
             if($slug==null){
              $expenseType->slug=$expenseType->id;
             }else{
              if(Attribute::where('type',5)->where('slug',$slug)->whereNotIn('id',[$expenseType->id])->count() >0){
              $expenseType->slug=$slug.'-'.$expenseType->id;
              }else{
              $expenseType->slug=$slug;
              }
            }
            $expenseType->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();
        }
        //Add Type  End

        $expenseType =Attribute::where('type',5)->find($id);
        if(!$expenseType){
            Session()->flash('error','This Expense Type Are Not Found');
            return redirect()->route('admin.expensesTypes');
        }

        // Update Department Action Start
        if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
            'created_at' => 'required|date',
        ]);

        $expenseType->name=$r->name;
        $expenseType->description=$r->description;
        $slug =Str::slug($r->name);
         if($slug==null){
          $expenseType->slug=$expenseType->id;
         }else{
          if(Attribute::where('type',5)->where('slug',$slug)->whereNotIn('id',[$expenseType->id])->count() >0){
          $expenseType->slug=$slug.'-'.$expenseType->id;
          }else{
          $expenseType->slug=$slug;
          }
        }

        $expenseType->status =$r->status?'active':'inactive';
        $expenseType->fetured =$r->fetured?1:0;
        $expenseType->editedby_id =Auth::id();
        $expenseType->created_at =$r->created_at?:Carbon::now();
        $expenseType->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update Department Action End


      // Delete Department Action Start
      if($action=='delete'){
        $medias =Media::latest()->where('src_type',3)->where('src_id',$expenseType->id)->get();
        foreach($medias as $media){
          if(File::exists($media->file_url)){
            File::delete($media->file_url);
          }
          $media->delete();
        }

        $expenseType->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.expensesTypes');

      }
      // Delete Department Action End
      return redirect()->back();

    }

    public function expenseReports(Request $r){

        if($r->startDate){
            $from =Carbon::parse($r->startDate);
        }else{
            $from=Carbon::now();
        }

        if($r->endDate){
            $to =Carbon::parse($r->endDate);
        }else{
            $to=Carbon::now();
        }

        $expenses = Expense::latest()->where('status','active')
            ->where(function($q) use ($r) {

                if($r->search){
                    $q->where('member_id',$r->search);
                    // $q->where('name','LIKE','%'.$r->search.'%');
                }

                if($r->expense_type){
                    $q->where('category_id',$r->expense_type);
                }

                if($r->branch_id){
                    $q->where('branch_id',$r->branch_id);
                }

                if($r->method){
                    $q->where('method_id',$r->method);
                }
            })
            ->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)
            ->get();


        $expenseTypes =Attribute::where('type',5)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $branches =Attribute::where('type',0)->where('status','active')->orderBy('name')->select(['id','name'])->get();

        return view(adminTheme().'expenses.expenseReports',compact('expenses','expenseTypes','from','to','branches'));
    }

    // Services Management Function
    public function balanceTransfers(Request $r){
        // Filter Action Start
        if($r->action){
            if($r->checkid){
                $datas=Transaction::where('type',4)->whereIn('id',$r->checkid)->get();
                foreach($datas as $data){
                      $data->delete();
                    }
            Session()->flash('success','Action Successfully Completed!');
        }else{
          Session()->flash('info','Please Need To Select Minimum One Data');
        }
        return redirect()->back();
      }

        $transections =Transaction::latest()->where('type',4)
                        ->where(function($q) use ($r) {
                        if($r->account){
                            $q->where('payment_method_id',$r->account)->orWhere('src_id',$r->account);
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
                        ->paginate(10);
        $accountMethods =Attribute::latest()->where('type',10)->where('status','active')->select(['id','name','amount'])->get();
        return view(adminTheme().'accounts.balanceTransfers',compact('transections','accountMethods'));
    }

    public function balanceTransfersAction(Request $r,$action,$id=null){

        if($action=='create'){
            $check = $r->validate([
                'form_account' => 'required|numeric',
                'to_account' => 'required|numeric',
                'amount' => 'required|numeric',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);

            if($r->form_account==$r->to_account){
                Session()->flash('error','Same Account Balance Transfer Are Not Allow');
                return redirect()->back();
            }

            $formMethod =Attribute::where('type',10)->where('status','active')->find($r->form_account);
            if(!$formMethod){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }

            $toMethod =Attribute::where('type',10)->where('status','active')->find($r->to_account);
            if(!$toMethod){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }

            if($r->amount > $formMethod->amount){
                Session()->flash('error','Account Balance Are Not Available');
                return redirect()->back();
            }


            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $transfer =new Transaction();
            $transfer->type=4;
            $transfer->src_id=$formMethod->id;
            $transfer->payment_method_id=$toMethod->id;
            $transfer->amount=$r->amount;
            $transfer->billing_note=$r->description;
            $transfer->status ='success';
            $transfer->addedby_id =Auth::id();
            $transfer->created_at = $createDate;
            $transfer->save();

            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$transfer->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            $formMethod->amount -=$transfer->amount;
            $formMethod->save();

            $toMethod->amount +=$transfer->amount;
            $toMethod->save();

            $transfer->balance =$formMethod->amount;
            $transfer->save();

            Session()->flash('success','Your Are Successfully Transfer');
            return redirect()->back();

        }




    }

    public function deposits(Request $r){

        // Filter Action Start
        if($r->action){
            if($r->checkid){
                $datas=Transaction::where('type',1)->whereIn('id',$r->checkid)->get();
                foreach($datas as $data){
                    if($r->action==5){

                       if($method =$data->account){
                         $method->amount -=$data->amount;
                         $method->save();
                       }

                      $medias =Media::latest()->where('src_type',9)->where('src_id',$data->id)->get();
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

        $transections =Transaction::latest()->where('type',1)->where('status','<>','temp')
                        ->where(function($q) use ($r) {

                            if($r->search){
                                $q->where('transection_id','LIKE','%'.$r->search.'%');
                            }

                            if($r->account){
                                $q->where('src_id',$r->account);
                            }

                            if($r->method){
                                $q->where('payment_method_id',$r->method);
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
                        ->paginate(10);
        $paymentMethods =Attribute::latest()->where('type',9)->where('status','active')->select(['id','name'])->get();
        $accountMethods =Attribute::latest()->where('type',10)->where('status','active')->select(['id','name'])->get();
        return view(adminTheme().'accounts.deposits',compact('transections','paymentMethods','accountMethods'));
    }


    public function depositsAction(Request $r,$action,$id=null){
        //Add Service  Start
        if($action=='create'){

            $check = $r->validate([
                'account' => 'required|numeric',
                'payment' => 'required|numeric',
                'amount' => 'required|numeric',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);

            $account =Attribute::where('type',10)->where('status','active')->find($r->account);
            if(!$account){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }

            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $deposit =new Transaction();
            $deposit->type=1;
            $deposit->src_id=$r->payment;
            $deposit->account_id=$account->id;
            $deposit->payment_method_id=$r->payment;
            $deposit->amount=$r->amount;
            $deposit->billing_note=$r->description;
            $deposit->status ='success';
            $deposit->addedby_id =Auth::id();
            if (!$createDate->isSameDay($deposit->created_at)) {
                $deposit->created_at = $createDate;
            }
            $deposit->save();

            $account->amount +=$deposit->amount;
            $account->save();

            $deposit->balance =$account->amount;
            $deposit->transection_id =$deposit->created_at->format('ymd').$deposit->id;
            $deposit->save();



            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$deposit->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }
        //Add Service  End

        $deposit =Transaction::where('type',1)->find($id);
        if(!$deposit){
            Session()->flash('error','This Deposit Are Not Found');
            return redirect()->route('admin.deposits');
        }


        if($action=='update'){
            $check = $r->validate([
                'payment' => 'required|numeric',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);
            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $deposit->src_id=$r->payment;
            $deposit->payment_method_id=$r->payment;
            $deposit->billing_note=$r->description;
            $deposit->editedby_id =Auth::id();
            if (!$createDate->isSameDay($deposit->created_at)) {
                $deposit->created_at = $createDate;
            }
            $deposit->save();


            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$deposit->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }


        return redirect()->back();

    }


    public function withdrawal(Request $r){

        // Filter Action Start
        if($r->action){
            if($r->checkid){
                $datas=Transaction::where('type',1)->whereIn('id',$r->checkid)->get();
                foreach($datas as $data){
                    if($r->action==5){

                       if($method =$data->account){
                         $method->amount +=$data->amount;
                         $method->save();
                       }

                      $medias =Media::latest()->where('src_type',9)->where('src_id',$data->id)->get();
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

        $transections =Transaction::latest()->where('type',6)->where('status','<>','temp')
                        ->where(function($q) use ($r) {

                            if($r->search){
                                $q->where('transection_id','LIKE','%'.$r->search.'%');
                            }

                            if($r->account){
                                $q->where('account_id',$r->account);
                            }

                            if($r->method){
                                $q->where('payment_method_id',$r->method);
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
                        ->paginate(10);
        $paymentMethods =Attribute::latest()->where('type',9)->where('status','active')->select(['id','name'])->get();
        $accountMethods =Attribute::latest()->where('type',10)->where('status','active')->select(['id','name'])->get();
        return view(adminTheme().'accounts.withdrawal',compact('transections','paymentMethods','accountMethods'));
    }


    public function withdrawalAction(Request $r,$action,$id=null){
        //Add Service  Start
        if($action=='create'){

            $check = $r->validate([
                'account' => 'required|numeric',
                'payment' => 'required|numeric',
                'amount' => 'required|numeric',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);

            $account =Attribute::where('type',10)->where('status','active')->find($r->account);
            if(!$account){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }

            if($r->amount > $account->amount){
                 Session()->flash('error','This Account balance Are Not available');
                 return redirect()->back();
            }

            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $withdrawal =new Transaction();
            $withdrawal->type=6;
            $withdrawal->src_id=$r->payment;
            $withdrawal->account_id=$account->id;
            $withdrawal->payment_method_id=$r->payment;
            $withdrawal->amount=$r->amount;
            $withdrawal->billing_note=$r->description;
            $withdrawal->status ='success';
            $withdrawal->addedby_id =Auth::id();
            if (!$createDate->isSameDay($withdrawal->created_at)) {
                $withdrawal->created_at = $createDate;
            }
            $withdrawal->save();

            $account->amount -=$withdrawal->amount;
            $account->save();

            $withdrawal->balance =$account->amount;
            $withdrawal->transection_id =$withdrawal->created_at->format('ymd').$withdrawal->id;
            $withdrawal->save();



            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$withdrawal->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }
        //Add Service  End

        $withdrawal =Transaction::where('type',6)->find($id);
        if(!$withdrawal){
            Session()->flash('error','This Withdrawal Are Not Found');
            return redirect()->route('admin.withdrawal');
        }


        if($action=='update'){
            $check = $r->validate([
                'payment' => 'required|numeric',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);
            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $withdrawal->src_id=$r->payment;
            $withdrawal->payment_method_id=$r->payment;
            $withdrawal->billing_note=$r->description;
            $withdrawal->editedby_id =Auth::id();
            if (!$createDate->isSameDay($withdrawal->created_at)) {
                $withdrawal->created_at = $createDate;
            }
            $withdrawal->save();


            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$withdrawal->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }


        return redirect()->back();

    }


    public function salarySheet(Request $r){

        if(
            empty(json_decode(Auth::user()->permission->permission, true)['salarySheet']['add']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['salarySheet']['view']) &&
            empty(json_decode(Auth::user()->permission->permission, true)['salarySheet']['delete'])
        ){
          return  abort(401);
        }

        $createDate  =$r->year?:Carbon::now()->format('Y');
        $salaries =Salary::latest()->whereYear('created_at',$createDate)->where(function($q)use($r){
            if($r->month){
            $q->whereMonth('created_at',$r->month);
            }

        })
        ->selectRaw('created_at, SUM(net_salary_amount) as total_salary, count(id) as employee')
        ->groupBy('created_at')->get();



        return view(adminTheme().'salary.salarySheet',compact('salaries'));
    }

    public function salarySheetAction(Request $r,$action,$id=null){

        if($action=='create'){

            $check = $r->validate([
                'month' => 'required|date',
            ]);

            $createDate =Carbon::parse($r->month);

            $employees =User::where('salary_amount','>',0)->where('status',true)->whereDate('created_at','<',Carbon::now())->get();

            foreach($employees as $employee){
                $data =Salary::whereMonth('created_at',$createDate->format('m'))->whereYear('created_at',$createDate->format('Y'))->where('user_id',$employee->id)->first();
                if(!$data){
                   $data =new Salary();
                   $data->user_id=$employee->id;
                   $data->salary_amount=$employee->salary_amount?:0;
                   $data->allowances_amount=$employee->allowances_amount?:0;
                   $data->deduction_amount=$employee->deduction_amount?:0;
                   $data->net_salary_amount=$data->salary_amount+$data->allowances_amount-$data->deduction_amount;
                   $data->addedby_id=Auth::id();
                   $data->status='unpaid';
                   $data->created_at=$createDate;
                   $data->save();
                }

                $data->employee_type=$employee->employment_status?:'Factory Employee';
                $data->salary_amount=$employee->salary_amount;

                if($data->employee_type=='Corporate Employee'){
                    $data->mobile_bill=$r->mobile_bill?:0;
                    $data->home_bill=$r->home_bill?:0;
                }else{
                    $data->total_working_day=general()->office_working_day?:0;
                    $data->employee_working_day=$data->total_working_day;
                    $data->bonus_working_day=0;

                    if($data->total_working_day > 0){
                        $data->working_day_rate=$data->salary_amount/$data->total_working_day;
                    }else{
                        $data->working_day_rate=0;
                    }
                    $data->woking_salary_amount=$data->working_day_rate*($data->employee_working_day+$data->bonus_working_day);
                    $data->over_time_hour=0;
                    $data->over_time_hour_rate=($data->salary_amount*60/100)/208*2;
                    $data->over_time_amount=$data->over_time_hour_rate*$data->over_time_hour;
                }
                $data->allowances_amount=0;
                $data->deduction_amount=0;
                $data->bonus_amount=0;
                if($data->employee_type=='Corporate Employee'){
                $data->net_salary_amount=($data->home_bill+$data->mobile_bill+$data->salary_amount+$data->allowances_amount+$data->bonus_amount) - ($data->deduction_amount);
                }else{
                $data->net_salary_amount=($data->woking_salary_amount+$data->over_time_amount+$data->allowances_amount+$data->bonus_amount) - ($data->deduction_amount);
                }

                $data->remarks=$r->remarks?:0;
                $data->status=$r->status=='paid'?'paid':'unpaid';
                $data->save();
            }

            return redirect()->route('admin.salarySheetAction',[$r->month]);

        }

        if($action=='export'){
            $createDate =Carbon::parse($id);

            $salaries=Salary::whereMonth('created_at',$createDate->format('m'))->whereYear('created_at',$createDate->format('Y'))->where('employee_type','<>','Corporate Employee')->get();
            $corporateSalaries = Salary::whereMonth('created_at',$createDate->format('m'))->whereYear('created_at',$createDate->format('Y'))->where('employee_type','Corporate Employee')->get();

            return view(adminTheme().'salary.salarySheetExport',compact('salaries','action','createDate','corporateSalaries'));
        }

        if($action=='print'){
            $createDate =Carbon::parse($id);

            $salaries=Salary::whereMonth('created_at',$createDate->format('m'))->whereYear('created_at',$createDate->format('Y'))->where('employee_type','<>','Corporate Employee')->get();
            $corporateSalaries = Salary::whereMonth('created_at',$createDate->format('m'))->whereYear('created_at',$createDate->format('Y'))->where('employee_type','Corporate Employee')->get();

            return view(adminTheme().'salary.salarySheetPrint',compact('salaries','action','createDate','corporateSalaries'));
        }


        if($action=='salary-update'){
            $salary =Salary::find($id);
            if(!$salary){
                Session()->flash('error','Employee Salary Are Not found');
                return redirect()->back();
            }

            $employee =$salary->user;
            if(!$employee){
                Session()->flash('error','This Employee Are Not found');
                return redirect()->back();
            }

            if(isset($r->loan_id)){
                for($i=0;$i< count($r->loan_id);$i++){
                    $loan =$employee->loans()->find($r->loan_id[$i]);
                    if($loan){
                       $loan->paid_balance+=$r->due_loan[$i];

                       if($loan->paid_balance >= $loan->amount){
                           $loan->status='paid';
                       }
                       $loan->balance=$loan->amount-$loan->paid_balance;
                       $loan->save();
                    }
                }
            }

            $salary->employee_type=$employee->employment_status?:'Factory Employee';
            $salary->salary_amount=$employee->salary_amount;

            if($salary->employee_type=='Corporate Employee'){
                $salary->mobile_bill=$r->mobile_bill?:0;
                $salary->home_bill=$r->home_bill?:0;
            }else{
                $salary->total_working_day=general()->office_working_day?:0;
                $salary->employee_working_day=$r->working_day?:0;
                $salary->bonus_working_day=$r->bonus_day?:0;

                if($salary->total_working_day > 0){
                    $salary->working_day_rate=$salary->salary_amount/$salary->total_working_day;
                }else{
                    $salary->working_day_rate=0;
                }
                $salary->woking_salary_amount=$salary->working_day_rate*($salary->employee_working_day+$salary->bonus_working_day);
                $salary->over_time_hour=$r->overtime_hours?:0;
                $salary->over_time_hour_rate=($salary->salary_amount*60/100)/208*2;
                $salary->over_time_amount=$salary->over_time_hour_rate*$salary->over_time_hour;
            }
            $salary->allowances_amount=$r->allowances_amount?:0;
            $salary->deduction_amount=$r->deduction_amount?:0;
            $salary->bonus_amount=0;
            if($salary->employee_type=='Corporate Employee'){
            $salary->net_salary_amount=($salary->home_bill+$salary->mobile_bill+$salary->salary_amount+$salary->allowances_amount+$salary->bonus_amount) - ($salary->deduction_amount);
            }else{
            $salary->net_salary_amount=($salary->woking_salary_amount+$salary->over_time_amount+$salary->allowances_amount+$salary->bonus_amount) - ($salary->deduction_amount);
            }

            $salary->remarks=$r->remarks?:0;
            $salary->status=$r->status=='paid'?'paid':'unpaid';
            $salary->save();



            Session()->flash('success','Action Successfully Completed!');
            return redirect()->back();
        }



        if($action=='update'){

            if(isset($r->checkid)){
                if($r->action==1){
                    Salary::latest()->whereIn('id',$r->checkid)->update(['status'=>'paid']);
                }elseif($r->action==2){
                    Salary::latest()->whereIn('id',$r->checkid)->update(['status'=>'unpaid']);
                }elseif($r->action==3){
                    Salary::latest()->whereIn('id',$r->checkid)->delete();
                }
                Session()->flash('success','Action Successfully Completed!');
            }else{
              Session()->flash('info','Please Need To Select Minimum One Post');
            }

            return redirect()->back();
        }

        $createDate =Carbon::parse($action);

        if(isset($r->employee_id)){

                for($i=0;$i<count($r->employee_id);$i++){
                    $employee =User::where('status',true)->whereDate('created_at','<',Carbon::now())->find($r->employee_id[$i]);
                    if($employee){
                        $data =Salary::whereMonth('created_at',$createDate->format('m'))->whereYear('created_at',$createDate->format('Y'))->where('user_id',$employee->id)->first();
                        if(!$data){
                           $data =new Salary();
                           $data->user_id=$employee->id;
                           $data->salary_amount=$employee->salary_amount?:0;
                           $data->allowances_amount=$employee->allowances_amount?:0;
                           $data->deduction_amount=$employee->deduction_amount?:0;
                           $data->net_salary_amount=$data->salary_amount+$data->allowances_amount-$data->deduction_amount;
                           $data->addedby_id=Auth::id();
                           $data->status='unpaid';
                           $data->created_at=$createDate;
                           $data->save();
                        }

                        $data->employee_type=$employee->employment_status?:'Factory Employee';
                        $data->salary_amount=$employee->salary_amount;

                        if($data->employee_type=='Corporate Employee'){
                            $data->mobile_bill=0;
                            $data->home_bill=0;
                        }else{
                            $data->total_working_day=general()->office_working_day?:0;
                            $data->employee_working_day=$data->total_working_day;
                            $data->bonus_working_day=0;

                            if($data->total_working_day > 0){
                                $data->working_day_rate=$data->salary_amount/$data->total_working_day;
                            }else{
                                $data->working_day_rate=0;
                            }
                            $data->woking_salary_amount=$data->working_day_rate*($data->employee_working_day+$data->bonus_working_day);
                            $data->over_time_hour=$r->overtime_hours?:0;
                            $data->over_time_hour_rate=($data->salary_amount*60/100)/208*2;;
                            $data->over_time_amount=$data->over_time_hour_rate*$data->over_time_hour;
                        }
                        $data->allowances_amount=0;
                        $data->deduction_amount=0;
                        $data->bonus_amount=0;
                        if($data->employee_type=='Corporate Employee'){
                        $data->net_salary_amount=($data->home_bill+$data->mobile_bill+$data->salary_amount+$data->allowances_amount+$data->bonus_amount) - ($data->deduction_amount);
                        }else{
                        $data->net_salary_amount=($data->woking_salary_amount+$data->over_time_amount+$data->allowances_amount+$data->bonus_amount) - ($data->deduction_amount);
                        }

                        $data->remarks=$r->remarks?:0;
                        $data->status=$r->status=='paid'?'paid':'unpaid';
                        $data->save();

                    }
                }
            Session()->flash('success','Employee added Successfully Completed!');
            return redirect()->back();
        }

        $employees =User::where('salary_amount','>',0)->where('status',true)->whereDate('created_at','<',Carbon::now())->select(['id','name','salary_amount'])->get();
        $salaries=Salary::whereMonth('created_at',$createDate->format('m'))->whereYear('created_at',$createDate->format('Y'))->where('employee_type','<>','Corporate Employee')->get();


        $corporateSalaries = Salary::whereMonth('created_at',$createDate->format('m'))->whereYear('created_at',$createDate->format('Y'))->where('employee_type','Corporate Employee')->get();

        return view(adminTheme().'salary.salarySheetEdit',compact('salaries','employees','action','createDate','corporateSalaries'));
    }

    public function paymentsMethods(Request $r){

        $paymentMethods =Attribute::latest()->where('type',9)->where('status','<>','temp')
            ->where(function($q) use ($r) {

                  if($r->search){
                      $q->where('name','LIKE','%'.$r->search.'%');
                  }

                  if($r->status){
                     $q->where('status',$r->status);
                  }

            })
            ->select(['id','name','slug','amount','description','created_at','addedby_id','status','fetured'])
                ->paginate(25)->appends([
                  'search'=>$r->search,
                  'status'=>$r->status,
                ]);

        return view(adminTheme().'accounts.paymentMethods',compact('paymentMethods'));
    }

    public function paymentsMethodsAction(Request $r,$action,$id=null){
        //Add Type  Start
        if($action=='create'){

            $check = $r->validate([
                'name' => 'required|max:100',
                'description' => 'nullable|max:1000',
            ]);

            $method =Attribute::where('type',9)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$method){
              $method =new Attribute();
            }
            $method->name=$r->name;
            $method->description=$r->description;
            $method->type =9;
            $method->status ='active';
            $method->addedby_id =Auth::id();
            $method->save();

            $slug =Str::slug($r->name);
             if($slug==null){
              $method->slug=$method->id;
             }else{
              if(Attribute::where('type',9)->where('slug',$slug)->whereNotIn('id',[$method->id])->count() >0){
              $method->slug=$slug.'-'.$method->id;
              }else{
              $method->slug=$slug;
              }
            }
            $method->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();
        }
        //Add Type  End

        $method =Attribute::where('type',9)->find($id);
        if(!$method){
            Session()->flash('error','This Method Type Are Not Found');
            return redirect()->route('admin.paymentsMethods');
        }

        // Update Department Action Start
        if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
            'created_at' => 'required|date',
        ]);

        $method->name=$r->name;
        $method->description=$r->description;
        $slug =Str::slug($r->name);
         if($slug==null){
          $method->slug=$method->id;
         }else{
          if(Attribute::where('type',9)->where('slug',$slug)->whereNotIn('id',[$method->id])->count() >0){
          $method->slug=$slug.'-'.$method->id;
          }else{
          $method->slug=$slug;
          }
        }

        $method->status =$r->status?'active':'inactive';
        $method->fetured =$r->lc_status?1:0;
        $method->editedby_id =Auth::id();
        $method->created_at =$r->created_at?:Carbon::now();
        $method->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update Department Action End


      // Delete Department Action Start
      if($action=='delete'){
        $medias =Media::latest()->where('src_type',3)->where('src_id',$method->id)->get();
        foreach($medias as $media){
          if(File::exists($media->file_url)){
            File::delete($media->file_url);
          }
          $media->delete();
        }

        $method->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.paymentsMethods');

      }
      // Delete Department Action End
      return redirect()->back();

    }

    public function accounts(Request $r){

        $accounts =Attribute::latest()->where('type',10)->where('status','<>','temp')
            ->where(function($q) use ($r) {
                  if($r->search){
                      $q->where('name','LIKE','%'.$r->search.'%');
                  }
                  if($r->status){
                     $q->where('status',$r->status);
                  }
            })
            ->select(['id','name','slug','amount','usd_amount','description','created_at','addedby_id','status','fetured'])
                ->paginate(25)->appends([
                  'search'=>$r->search,
                  'status'=>$r->status,
                ]);

        $adminUsers =User::where('admin',true)->select(['id','name','mobile'])->get();

        return view(adminTheme().'accounts.accountsMethods',compact('accounts','adminUsers'));
    }

    public function accountsAction(Request $r,$action,$id=null){
        //Add Type  Start
        if($action=='create'){

            $check = $r->validate([
                'name' => 'required|max:100',
                'account_owner' => 'required|numeric',
                'description' => 'nullable|max:1000',
            ]);

            $method =Attribute::where('type',10)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$method){
              $method =new Attribute();
            }
            $method->name=$r->name;
            $method->description=$r->description;
            $method->type =10;
            $method->status ='active';
            $method->addedby_id =$r->account_owner?:Auth::id();
            $method->save();

            $slug =Str::slug($r->name);
             if($slug==null){
              $method->slug=$method->id;
             }else{
              if(Attribute::where('type',10)->where('slug',$slug)->whereNotIn('id',[$method->id])->count() >0){
              $method->slug=$slug.'-'.$method->id;
              }else{
              $method->slug=$slug;
              }
            }
            $method->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();
        }
        //Add Type  End

        $method =Attribute::where('type',10)->find($id);
        if(!$method){
            Session()->flash('error','This Account Method Type Are Not Found');
            return redirect()->route('admin.accounts');
        }

        // Update Department Action Start
        if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
            'created_at' => 'required|date',
        ]);

        $method->name=$r->name;
        $method->description=$r->description;
        $slug =Str::slug($r->name);
         if($slug==null){
          $method->slug=$method->id;
         }else{
          if(Attribute::where('type',9)->where('slug',$slug)->whereNotIn('id',[$method->id])->count() >0){
          $method->slug=$slug.'-'.$method->id;
          }else{
          $method->slug=$slug;
          }
        }

        $method->status =$r->status?'active':'inactive';
        $method->fetured =$r->lc_status?1:0;
        $method->editedby_id =Auth::id();
        $method->created_at =$r->created_at?:Carbon::now();
        $method->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update Department Action End


      // Delete Department Action Start
      if($action=='delete'){
        $medias =Media::latest()->where('src_type',3)->where('src_id',$method->id)->get();
        foreach($medias as $media){
          if(File::exists($media->file_url)){
            File::delete($media->file_url);
          }
          $media->delete();
        }

        $method->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.accounts');

      }
      // Delete Department Action End

      $from = $r->startDate?Carbon::parse($r->startDate):Carbon::now()->subDays(30);

      $to = $r->endDate?Carbon::parse($r->endDate):Carbon::now();

      //$transections =Transaction::latest()->whereDate('created_at','>=',$from)->where('payment_method_id',$method->id)->whereDate('created_at','<=',$to)->whereIn('type',[0,1,2,3,4])->get();
      $transections = Transaction::whereDate('created_at', '>=', $from)
        ->whereDate('created_at', '<=', $to)
        // ->where('payment_method_id', $method->id)
        ->where('account_id', $method->id)
        //->where('type',1)
        ->whereIn('type', [0, 1, 6])
        ->get();

        $balance = 0;
        $transections->map(function ($t) use (&$balance) {
            if (in_array($t->type, [0, 1])) {
                $balance += $t->amount;
            }
            elseif ($t->type == 6) {
                $balance -= $t->amount;
            }
            $t->running_balance = $balance;
            return $t;
        });


      return view(adminTheme().'accounts.accountsMethodsView',compact('method','transections','from','to'));


    }


    // Services Management Function

    public function services(Request $r){

        if(
            empty(json_decode(Auth::user()->permission->permission, true)['product']['list'])
        ){
          return  abort(401);
        }


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
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){

              $medias =Media::latest()->where('src_type',1)->where('src_id',$data->id)->get();

              foreach($medias as $media){
                if(File::exists($media->file_url)){
                  File::delete($media->file_url);
                }
                $media->delete();
              }

              $data->serviceCtgs()->delete();
              $data->postTags()->delete();
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

      $services =Post::latest()->where('type',3)->where('status','<>','temp')
        ->where(function($q) use ($r) {

            if($r->search){
                $q->where('search_key','LIKE','%'.$r->search.'%');
            }

            if($r->category){
                $q->where('category_id',$r->category);
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

            if($r->status){
             $q->where('status',$r->status);
            }


        })
        ->select(['id','name','slug','view','unit_id','category_id','item_price','type','created_at','addedby_id','status','fetured'])
        ->paginate(25)->appends([
          'search'=>$r->search,
          'status'=>$r->status,
          'startDate'=>$r->startDate,
          'endDate'=>$r->endDate,
        ]);

        //Total Count Results
        $totals = DB::table('posts')
        ->where('type',3)->where('status','<>','temp')
        ->selectRaw('count(*) as total')
        ->selectRaw("count(case when status = 'active' then 1 end) as active")
        ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
        ->first();

        return view(adminTheme().'services.servicesAll',compact('services','totals'));
    }

    public function servicesAction(Request $r,$action,$id=null){
        if(
            empty(json_decode(Auth::user()->permission->permission, true)['product']['list'])
        ){
          return  abort(401);
        }
        //Add Service  Start
        if($action=='import'){

            if ($r->isMethod('post')) {

                $r->validate([
                    'product_file' => 'required|file|mimes:csv,txt|max:2048', // only CSV
                ]);

                $file = $r->file('product_file');
                if (($handle = fopen($file->getRealPath(), 'r')) === false) {
                    return back()->withErrors(['product_file' => 'Unable to open the file.']);
                }

                $header = fgetcsv($handle);
                if (!$header) {
                    return back()->withErrors(['product_file' => 'The CSV is empty.']);
                }

                $header = array_map('trim', $header);

                $expected = ['Name', 'Price'];
                foreach ($expected as $col) {
                    if (!in_array($col, $header)) {
                        return back()->withErrors(['product_file' => "Missing required column: $col"]);
                    }
                }

                $idxName  = array_search('Name', $header);
                $idxPrice = array_search('Price', $header);
                $idxUnit = array_search('Unit', $header);
                $idxCtg = array_search('Category', $header);
                $imported = 0;
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) < 1) continue;

                    $name = trim($row[$idxName] ?? '');
                    if ($name === '') {
                        continue;
                    }

                    $price         = is_numeric($row[$idxPrice] ?? null) ? (float)$row[$idxPrice] : 0;

                    $unitName = trim($row[$idxUnit] ?? '');
                    $unit =PostExtra::where('type',1)->where('name',$unitName)->first();
                    if($unitName){
                        if(!$unit){
                         $unit =new PostExtra();
                         $unit->type =1;
                         $unit->name =$unitName;
                         $unit->addedby_id =Auth::id();
                         $unit->status ='active';
                         $unit->save();
                        }
                    }

                    $ctgName = trim($row[$idxCtg] ?? '');
                    $ctg =Attribute::where('type',0)->where('name',$ctgName)->first();
                    if($ctgName){
                        if(!$ctg){
                         $ctg =new Attribute();
                         $ctg->type =0;
                         $ctg->parent_id =null;
                         $ctg->name =$ctgName;
                         $ctg->slug=Str::slug($ctg->name);
                         $ctg->addedby_id =Auth::id();
                         $ctg->status ='active';
                         $ctg->save();
                        }
                    }

                    $product = new Post();
                    $product->type =3;
                    $product->name        = $name;
                    $product->item_price  = $price;
                    $product->category_id =$ctg?$ctg->id:null;
                    $product->unit_id =$unit?$unit->id:null;
                    $product->status ='active';
                    $product->addedby_id =Auth::id();
                    $product->created_at =Carbon::now();
                    $product->save();


                    $slug =Str::slug($r->name);
                    if($slug==null){
                        $product->slug=$product->id;
                    }else{
                        if(Post::where('type',3)->where('slug',$slug)->whereNotIn('id',[$product->id])->count() >0){
                        $product->slug=$slug.'-'.$product->id;
                        }else{
                        $product->slug=$slug;
                        }
                    }
                    $product->save();

                    $imported++;
                }

                fclose($handle);

                return redirect()
                    ->back()
                    ->with('success', "Imported {$imported} products successfully.");
            }

            return view(adminTheme().'services.servicesImport');
        }

        if($action=='create'){
            $check = $r->validate([
                'name' => 'required|max:200',
                'price' => 'nullable|numeric',
                'unit' => 'nullable|numeric',
                'category' => 'nullable|numeric',
            ]);


            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $service =new Post();
            $service->type =3;
            $service->name=$r->name;
            $service->item_price=$r->price;
            $service->unit_id=$r->unit;
            $service->category_id=$r->category;
            $service->status =$r->status?'active':'inactive';
            $service->addedby_id =Auth::id();
            $service->created_at =$createDate;
            $service->save();

            $slug =Str::slug($r->name);
            if($slug==null){
                $service->slug=$service->id;
            }else{
                if(Post::where('type',3)->where('slug',$slug)->whereNotIn('id',[$service->id])->count() >0){
                $service->slug=$slug.'-'.$service->id;
                }else{
                $service->slug=$slug;
                }
            }
            $service->save();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();

        }
        //Add Service  End

        $service =Post::where('type',3)->find($id);
        if(!$service){
        Session()->flash('error','This Service Are Not Found');
        return redirect()->route('admin.services');
        }

        //Update Service  Start
        if($action=='update'){

            $check = $r->validate([
                'name' => 'required|max:191',
                'price' => 'nullable|numeric',
                'unit' => 'nullable|numeric',
                'category' => 'nullable|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'gallery_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if(!$check){
                Session::flash('error','Need To validation');
                return back();
            }

            $service->name=$r->name;
            $service->item_price=$r->price;
            $service->unit_id=$r->unit;
            $service->category_id=$r->category;
            $service->short_description=$r->short_description;
            $service->description=$r->description;
            $service->seo_title=$r->seo_title;
            $service->seo_description=$r->seo_description;
            $service->seo_keyword=$r->seo_keyword;

            ///////Image Upload End////////////
            if($r->hasFile('image')){
              $file =$r->image;
              $src  =$service->id;
              $srcType  =1;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            ///////Gallery Upload End////////////

            $files=$r->file('gallery_image');
            if($files){

                foreach($files as $file)
                {

                    $file =$file;
                    $src  =$service->id;
                    $srcType  =1;
                    $fileUse  =3;
                    $author=Auth::id();
                    $fileStatus=false;
                    uploadFile($file,$src,$srcType,$fileUse,$author,$fileStatus);
                }
            }

            ///////Gallery Upload End////////////

            $slug =Str::slug($r->name);
            if($slug==null){
            $service->slug=$service->id;
            }else{
            if(Post::where('type',3)->where('slug',$slug)->whereNotIn('id',[$service->id])->count() >0){
            $service->slug=$slug.'-'.$service->id;
            }else{
            $service->slug=$slug;
            }
            }
            if($r->created_at){
              $service->created_at =$r->created_at;
            }
            $service->status =$r->status?'active':'inactive';
            $service->fetured =$r->fetured?1:0;
            $service->editedby_id =Auth::id();
            $service->save();

            //Category posts
            if($r->categoryid){

            $service->serviceCtgs()->whereNotIn('reff_id',$r->categoryid)->delete();

            for ($i=0; $i < count($r->categoryid); $i++) {

            $ctg = $service->serviceCtgs()->where('reff_id',$r->categoryid[$i])->first();

            if($ctg){}else{
            $ctg =new PostAttribute();
            $ctg->src_id=$service->id;
            $ctg->reff_id=$r->categoryid[$i];
            $ctg->type=0;
            }
            $ctg->drag=$i;
            $ctg->save();
            }

            }else{
            $service->serviceCtgs()->delete();
            }

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();

        }
        //Update Service  End


        //Delete Service  Start
        if($action=='delete'){

            $medias =Media::latest()->where('src_type',1)->where('src_id',$service->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }

            $service->serviceCtgs()->delete();
            $service->postTags()->delete();
            $service->delete();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();

        }
        //Delete Service  End

        $categories =Attribute::where('type',0)->where('status','<>','temp')->where('parent_id',null)->get();
        $tags =Attribute::where('type',9)->where('status','<>','temp')->where('parent_id',null)->get();
        $brands =Attribute::where('type',2)->where('status','<>','temp')->where('parent_id',null)->get();

        return view(adminTheme().'services.servicesEdit',compact('service','categories','tags','brands'));


    }

    // Services Management Function End


    //Service Category Function
    public function productCategory(Request $r){

    $allPer = empty(json_decode(Auth::user()->permission->permission, true)['servicesCtg']['all']);
    // Filter Action Start

      if($r->action){
        if($r->checkid){

        $datas=Attribute::where('type',9)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

            if($r->action==1){
              $data->status='active';
              $data->save();
            }elseif($r->action==2){
              $data->status='inactive';
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){

              $medias =Media::latest()->where('src_type',3)->where('src_id',$data->id)->get();
              foreach($medias as $media){
                if(File::exists($media->file_url)){
                  File::delete($media->file_url);
                }
                $media->delete();
              }

              //Post Category sub Category replace
              foreach($data->subctgs as $subctg){
                $subctg->parent_id=$data->parent_id;
                $subctg->save();
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

    $categories =Attribute::latest()->where('type',6)->where('status','<>','temp')
    ->where(function($q) use ($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status);
          }
    })
    ->select(['id','name','slug','parent_id','view','type','created_at','addedby_id','status','fetured'])
        ->paginate(25)->appends([
          'search'=>$r->search,
          'status'=>$r->status,
        ]);

    //Total Count Results
    $totals = DB::table('attributes')
    ->where('type',6)->where('status','<>','temp')
    ->selectRaw('count(*) as total')
    ->selectRaw("count(case when status = 'active' then 1 end) as active")
    ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
    ->first();

        $parents =Attribute::where('type',6)->where('status','<>','temp')->where('parent_id',null)->get();
        return view(adminTheme().'services.category.servicesCategories',compact('categories','parents','totals'));

    }

    public function productCategoryAction(Request $r,$action,$id=null){

        //Add Service Category  Start
        if($action=='create'){

             $check = $r->validate([
                'title' => 'required|max:191',
                'parent_id' => 'nullable|numeric',
            ]);


            $category =new Attribute();
            $category->type =6;
            $category->name=$r->title;
            $category->parent_id=$r->parent_id;
            $category->status ='active';
            $category->addedby_id =Auth::id();
            $category->save();

            $slug =Str::slug($r->title);
            if($slug==null){
                $category->slug=$category->id;
            }else{
                if(Attribute::where('type',0)->where('slug',$slug)->whereNotIn('id',[$category->id])->count() >0){
                    $category->slug=$slug.'-'.$category->id;
                }else{
                    $category->slug=$slug;
                }
            }
            $category->save();
            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();

        }
        //Add Service Category  End

        $category =Attribute::where('type',6)->find($id);
        if(!$category){
        Session()->flash('error','This Category Are Not Found');
        return redirect()->route('admin.productCategory');
        }

        //Update Service Category  Start
        if($action=='update'){

            $check = $r->validate([
                'title' => 'required|max:191',
            ]);

            $category->name=$r->title;
            if($r->parent_id==$category->parent_id){}else{
              $category->parent_id=$r->parent_id;
            }

           $slug =Str::slug($r->title);
           if($slug==null){
            $category->slug=$category->id;
           }else{
            if(Attribute::where('type',0)->where('slug',$slug)->whereNotIn('id',[$category->id])->count() >0){
            $category->slug=$slug.'-'.$category->id;
            }else{
            $category->slug=$slug;
            }
           }
          $category->editedby_id =Auth::id();
          $category->save();

          Session()->flash('success','Your Are Successfully Done');
          return redirect()->back();

        }
        //Update Service Category  End

        //Delete Service Category  Start
        if($action=='delete'){
            //Category Media File Delete
            $medias =Media::latest()->where('src_type',3)->where('src_id',$category->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }

            //Service Category sub Category replace
            foreach($category->subctgs as $subctg){
              $subctg->parent_id=$category->parent_id;
              $subctg->save();
            }

            $category->delete();

           Session()->flash('success','Your Are Successfully Done');
           return redirect()->back();
        }
        //Delete Service Category  End

        $parents =Attribute::where('type',0)->where('status','<>','temp')->where('parent_id',null)->get();
        return view(adminTheme().'services.category.servicesCategoryEdit',compact('category','parents'));

    }

    //Service Category Function End



    //Product Unit Function
    public function productUnits(Request $r){

      if(
        empty(json_decode(Auth::user()->permission->permission, true)['productUnit']['list'])
        ){
          return  abort(401);
        }

      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=PostExtra::latest()->where('type',1)->whereIn('id',$r->checkid)->get();

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

      $productUnits=PostExtra::latest()->where('type',1)->where('status','<>','temp')
        ->where(function($q) use ($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status);
          }

      })
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      return view(adminTheme().'product-unit.productUnitAll',compact('productUnits'));

    }

    public function productUnitsAction(Request $r,$action,$id=null){
        if(
        empty(json_decode(Auth::user()->permission->permission, true)['productUnit']['list'])
        ){
          return  abort(401);
        }
      // Add Department Action Start
      if($action=='create'){

        $check = $r->validate([
            'unit' => 'required|max:100',
        ]);

        $hasUnit =PostExtra::where('type',1)->where('name',$r->unit)->first();
        if($hasUnit){
            Session()->flash('error','This Product Unit Are Already Used');
            return redirect()->back();
        }

        $unit =new PostExtra();
        $unit->name=$r->unit;
        $unit->type =1;
        $unit->status ='active';
        $unit->addedby_id =Auth::id();
        $unit->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }

      // Add Department Action End


      $unit =PostExtra::where('type',1)->find($id);
      if(!$unit){
        Session()->flash('error','This Product Unit Are Not Found');
        return redirect()->route('admin.productUnits');
      }

      // Update Department Action Start
      if($action=='update'){

        $check = $r->validate([
            'unit' => 'required|max:191',
        ]);
        $hasUnit =PostExtra::where('type',1)->where('id','<>',$unit->id)->where('name',$r->unit)->first();
        if($hasUnit){
            Session()->flash('error','This Product Unit Are Already Used');
            return redirect()->back();
        }

        $unit->name=$r->unit;
        $unit->editedby_id =Auth::id();
        $unit->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update Department Action End


      // Delete Department Action Start
      if($action=='delete'){
        $unit->delete();
        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.productUnits');

      }
      // Delete Department Action End
      return redirect()->back();

    }

    //Ref/Title list Function
    public function reffTitleList(Request $r){

      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=ReffMember::latest()->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

            if($r->action==1){
              $data->status='active';
              $data->save();
            }elseif($r->action==2){
              $data->status='inactive';
              $data->save();
            }elseif($r->action==5){
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

      $members=ReffMember::latest()->where('status','<>','temp')
        ->where(function($q) use ($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status);
          }

      })
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      //Total Count Results
      $totals = DB::table('reff_members')->where('status','<>','temp')
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'reffmembers.reffMemberAll',compact('members','totals'));

    }

    public function reffTitleListAction(Request $r,$action,$id=null){


        if($action=='search-reff'){

           $total=ReffMember::latest()->where('status','active')
                ->where(function($q){
                  if(request()->reff_search){
                      $q->where('name','LIKE','%'.request()->reff_search.'%');
                  }
                })
                ->limit(10)->count();

            $view =view(adminTheme().'reffmembers.includes.reffSearchResult')->render();

            return Response()->json([
                'success' => true,
                'total' => $total,
                'view' => $view,
            ]);

        }


      // Add Department Action Start
      if($action=='create'){

        $check = $r->validate([
            'name' => 'required|max:100',
        ]);

        $hasTitle =ReffMember::where('name',$r->name)->first();
        if($hasTitle){
            Session()->flash('error','This Reff/Title Are Already Used');
            return redirect()->back();
        }

        $title =new ReffMember();
        $title->name=$r->name;
        $title->status ='active';
        $title->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }

      // Add Department Action End


      $title =ReffMember::find($id);
      if(!$title){
        Session()->flash('error','This Reff/Title Are Not Found');
        return redirect()->route('admin.reffTitleList');
      }

      // Update Department Action Start
      if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:191',
        ]);
        $hasTitle =ReffMember::where('id','<>',$title->id)->where('name',$r->name)->first();
        if($hasTitle){
            Session()->flash('error','This Reff/Title Are Already Used');
            return redirect()->back();
        }

        $title->name=$r->name;
        $title->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update  Action End

        $from = $r->startDate?Carbon::parse($r->startDate):Carbon::now()->subDays(30);
        $to = $r->endDate?Carbon::parse($r->endDate):Carbon::now();

        $traddings =SupplierTrading::latest()->where('status','<>','temp')->where('type',2)->where('member_id',$title->id)
                        ->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to)
                        ->get();

        $expenses = Expense::latest()->where('status','active')->where('member_id',$title->id)
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                    ->get();

        return view(adminTheme().'reffmembers.reffmembersView',compact('title','expenses','traddings','from','to'));

    }

    public function ReffNewMember($title){
        $data=null;
        if($title){
            $data =new ReffMember();
            $data->name=$title;
            $data->status ='active';
            $data->save();
        }
        return $data;
    }

    //Department Function

    public function branchs(Request $r){


      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Attribute::latest()->where('type',3)->whereIn('id',$r->checkid)->get();

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

      $branchs=Attribute::latest()->where('type',0)->where('status','<>','temp')
        ->where(function($q) use ($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status);
          }

      })
      ->select(['id','name','slug','type','description','created_at','addedby_id','status'])
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      //Total Count Results
      $total = DB::table('attributes')->where('status','<>','temp')
      ->where('type',0)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'branchs.branchAll',compact('branchs','total'));

    }

    public function branchsAction(Request $r,$action,$id=null){
      // Add Department Action Start
      if($action=='create'){

        $check = $r->validate([
            'name' => 'required|max:100',
            'address' => 'nullable|max:500',
        ]);

        $store =Attribute::where('type',0)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$store){
          $store =new Attribute();
        }
        $store->name=$r->name;
        $store->description=$r->address;
        $store->type =0;
        $store->status ='active';
        $store->addedby_id =Auth::id();
        $store->save();

        $slug =Str::slug($r->name);
         if($slug==null){
          $store->slug=$store->id;
         }else{
          if(Attribute::where('type',0)->where('slug',$slug)->whereNotIn('id',[$store->id])->count() >0){
          $store->slug=$slug.'-'.$store->id;
          }else{
          $store->slug=$slug;
          }
        }
        $store->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }

      // Add Department Action End


      $store =Attribute::where('type',0)->find($id);
      if(!$store){
        Session()->flash('error','This Branch Are Not Found');
        return redirect()->route('admin.branchs');
      }

      //Check Authorized User
    //   $allPer = empty(json_decode(Auth::user()->permission->permission, true)['clients']['all']);
    //   if($allPer && $store->addedby_id!=Auth::id()){
    //     Session()->flash('error','You are unauthorized Try!!');
    //     return redirect()->route('admin.branchs');
    //   }

      // Update Department Action Start
      if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:100',
            'address' => 'nullable|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $store->name=$r->name;
        $store->description=$r->description;

        ///////Image UploadStart////////////

        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$store->id;
          $srcType  =3;
          $fileUse  =1;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }

        ///////Image Upload End////////////

        $slug =Str::slug($r->name);
         if($slug==null){
          $store->slug=$store->id;
         }else{
          if(Attribute::where('type',0)->where('slug',$slug)->whereNotIn('id',[$store->id])->count() >0){
          $store->slug=$slug.'-'.$store->id;
          }else{
          $store->slug=$slug;
          }
        }

        $store->status =$r->status?'active':'inactive';
        $store->fetured =$r->fetured?1:0;
        $store->editedby_id =Auth::id();
        $store->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update Department Action End


      // Delete Department Action Start
      if($action=='delete'){
        $medias =Media::latest()->where('src_type',3)->where('src_id',$store->id)->get();
        foreach($medias as $media){
          if(File::exists($media->file_url)){
            File::delete($media->file_url);
          }
          $media->delete();
        }

        $store->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.branchs');

      }
      // Delete Department Action End
      return redirect()->back();

    }

    //Department Function

    public function departments(Request $r){


      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Attribute::latest()->where('type',3)->whereIn('id',$r->checkid)->get();

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

      $departments=Attribute::latest()->where('type',3)->where('status','<>','temp')
        ->where(function($q) use ($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status);
          }

      })
      ->select(['id','name','slug','type','description','created_at','addedby_id','status'])
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      //Total Count Results
      $totals = DB::table('attributes')
      ->where('type',3)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'departments.departmentsAll',compact('departments','totals'));

    }

    public function departmentsAction(Request $r,$action,$id=null){
      // Add Department Action Start
      if($action=='create'){

        $check = $r->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
        ]);

        $department =Attribute::where('type',3)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$department){
          $department =new Attribute();
        }
        $department->name=$r->name;
        $department->description=$r->description;
        $department->type =3;
        $department->status ='active';
        $department->addedby_id =Auth::id();
        $department->save();

        $slug =Str::slug($r->name);
         if($slug==null){
          $department->slug=$department->id;
         }else{
          if(Attribute::where('type',3)->where('slug',$slug)->whereNotIn('id',[$department->id])->count() >0){
          $department->slug=$slug.'-'.$department->id;
          }else{
          $department->slug=$slug;
          }
        }
        $department->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }

      // Add Department Action End


      $department =Attribute::where('type',3)->find($id);
      if(!$department){
        Session()->flash('error','This Department Are Not Found');
        return redirect()->route('admin.departments');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['clients']['all']);
      if($allPer && $department->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.departments');
      }

      // Update Department Action Start
      if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:191',
            'seo_title' => 'nullable|max:200',
            'seo_desc' => 'nullable|max:250',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $department->name=$r->name;
        $department->short_description=$r->short_description;
        $department->description=$r->description;
        $department->seo_title=$r->seo_title;
        $department->short_description=$r->short_description;
        $department->seo_keyword=$r->seo_keyword;

        ///////Image UploadStart////////////

        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$department->id;
          $srcType  =3;
          $fileUse  =1;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }

        ///////Image Upload End////////////

        ///////Banner Upload End////////////

        if($r->hasFile('banner')){

          $file =$r->banner;
          $src  =$department->id;
          $srcType  =3;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);

        }

        ///////Banner Upload End////////////

        $slug =Str::slug($r->name);
         if($slug==null){
          $department->slug=$department->id;
         }else{
          if(Attribute::where('type',3)->where('slug',$slug)->whereNotIn('id',[$department->id])->count() >0){
          $department->slug=$slug.'-'.$department->id;
          }else{
          $department->slug=$slug;
          }
        }

        $department->status =$r->status?'active':'inactive';
        $department->fetured =$r->fetured?1:0;
        $department->editedby_id =Auth::id();
        $department->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update Department Action End


      // Delete Department Action Start
      if($action=='delete'){
        $medias =Media::latest()->where('src_type',3)->where('src_id',$department->id)->get();
        foreach($medias as $media){
          if(File::exists($media->file_url)){
            File::delete($media->file_url);
          }
          $media->delete();
        }

        $department->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.departments');

      }
      // Delete Department Action End
      return redirect()->back();

    }


    //Department Function End

    //Designation Function

    public function designations(Request $r){

      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Attribute::latest()->where('type',2)->whereIn('id',$r->checkid)->get();

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

      $designations=Attribute::latest()->where('type',2)->where('status','<>','temp')
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
      $totals = DB::table('attributes')
      ->where('type',2)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'designations.designationsAll',compact('designations','totals'));

    }

    public function designationsAction(Request $r,$action,$id=null){
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
        return redirect()->route('admin.designations');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
      if($allPer && $designation->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.designations');
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
            return redirect()->route('admin.brands');
      }
      // Delete Designation Action End

      return redirect()->back();

    }

    //Designation Function End


    //Companies Function
    public function companies(Request $r){

        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['company']['all']);


        // Filter Action Start
        if($r->action){
            if($r->checkid){

                $datas=Company::latest()->whereIn('id',$r->checkid)->get();

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


        $companies =Company::latest()->where('status','<>','temp')
                  ->where(function($q) use($r,$allPer) {
                      if($r->search){
                          $q->where('factory_name','LIKE','%'.$r->search.'%')
                          ->orWhere('owner_name','LIKE','%'.$r->search.'%')
                          ->orWhere('owner_mobile','LIKE','%'.$r->search.'%')
                          ->orWhere('owner_email','LIKE','%'.$r->search.'%');
                      }
                      if($r->division){
                          $q->where('division',$r->division);
                      }
                      if($r->district){
                          $q->where('district',$r->district);
                      }
                      if($r->city){
                          $q->where('city',$r->city);
                      }
                        if($r->deed_serial){
                            $q->where('deed_serial','LIKE','%'.$r->deed_serial.'%');
                        }
                        if($r->concern){
                            $concern = match($r->concern) {
                                'MMC' => 'MG Machineries Corporation',
                                'MTCI' => 'MG Training Centre Institute',
                                default  => 'Embroidery Machine Corporation',
                            };
                            $q->where('concern',$concern);
                        }
                      if($r->status){
                            $q->where('status',$r->status);
                        }

                        // Check Permission
                        if($allPer){
                         $q->where('addedby_id',auth::id());
                        }
                  });
                //   ->select(['id','name','slug','location','type','description','created_at','addedby_id','status','fetured'])



        if($r->export=='report'){

            $companies =$companies->get();

            return view(adminTheme().'companies.companiesExport',compact('companies'));
        }else{
            $companies=$companies->paginate(25)->appends($r->all());
        }

        //Total Count Results
      $totals = DB::table('companies')->where('status','<>','temp')
      ->where(function($q) use($allPer){
            if($allPer){
                $q->where('addedby_id',auth::id());
            }
      })
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();


      return view(adminTheme().'companies.companiesAll',compact('companies','totals'));
    }

    public function companiesAction(Request $r,$action,$id=null){

      if($action=='keyperson-list' || $action=='partner-list' || $action=='manager-list' || $action=='pm-list' || $action=='operator-list' || $action=='engineer-list'){

            $allPer = empty(json_decode(Auth::user()->permission->permission, true)['company']['all']);

            if($action=='operator-list'){

                $companies =CompanyPerson::latest()->where('type',0)->whereHas('company',function($q)use($r){
                                    if($r->search){
                                      $q->where('factory_name','LIKE','%'.$r->search.'%');
                                    }
                            })->get();

            }elseif($action=='partner-list'){
                $companies =CompanyPerson::latest()->where('type',2)->whereHas('company',function($q)use($r){
                                    if($r->search){
                                      $q->where('factory_name','LIKE','%'.$r->search.'%');
                                    }
                            })->get();
            }else{

                $companies =Company::latest()->where('status','<>','temp')
                      ->where(function($q) use($r,$allPer,$action) {
                            if($r->search){
                              $q->where('factory_name','LIKE','%'.$r->search.'%');
                            }

                            if($action=='pm-list'){
                               $q->whereNotNull('pm_name');
                            }else if($action=='partner-list'){
                                $q->whereNotNull('partner_name');
                            }else if($action=='keyperson-list'){
                                $q->whereNotNull('key_parson_name');
                            }else if($action=='manager-list'){
                                $q->whereNotNull('manager_name');
                            }else if($action=='operator-list'){
                                $q->whereNotNull('operator_name')->orWhere('operator2_name', '!=', '');
                            }else if($action=='engineer-list'){
                                $q->whereNotNull('engineer_name');
                            }else{
                                $q->where('id',000);
                            }


                            if($r->status){
                                $q->where('status',$r->status);
                            }
                            // Check Permission
                            if($allPer){
                             $q->where('addedby_id',auth::id());
                            }
                      })
                      ->get();
            }

            return view(adminTheme().'companies.companiesViewExport',compact('action','companies'));
      }


      //Create Company Start
      if($action=='create'){
        // $check = $r->validate([
        //     'name' => 'required|max:100',
        //     'short_name' => 'nullable|min:3|max:100',
        //     'address' => 'nullable|max:1000',
        // ]);

        // if($r->short_name){
        //     $hasName =Attribute::where('type',1)->where('slug',$r->short_name)->first();
        //     if($hasName){
        //         Session()->flash('error','This Company Short Name Are Already Used');
        //         return redirect()->back()->withInput();
        //     }
        // }

        // $company =Attribute::where('type',1)->where('status','temp')->where('addedby_id',Auth::id())->first();
        // if(!$company){
        // }

        $company =Company::where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$company){
            $company =new Company();
            $company->status ='temp';
            $company->addedby_id =Auth::id();
            $company->save();
        }

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->route('admin.companiesAction',['edit',$company->id]);
      }
      //Create Company End

      $company =Company::find($id);
      if(!$company){
        Session()->flash('error','This Company Are Not Found');
        return redirect()->route('admin.companies');
      }
       $message=null;
        //Check Authorized User
        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['company']['all']);
        if($allPer && $company->addedby_id!=Auth::id()){
          Session()->flash('error','You are unauthorized Try!!');
          return redirect()->route('admin.companies');
        }


        if($action=='add-partners' || $action=='delete-partners' || $action=='update-partners'){

            if($action=='add-partners'){

                $i =$company->persons()->where('type',2)->count();

                $partner =new CompanyPerson();
                $partner->type=2;
                $partner->company_id=$company->id;
                $partner->save();


                $view =view(adminTheme().'companies.includes.partner',compact('company','i','partner'))->render();
                return Response()->json([
                    'success' => true,
                    'view' => $view,
                ]);
            }


            if($action=='delete-partners'){
                $partner =$company->persons()->where('type',2)->find($r->partner_id);

                $status=false;
                if($partner){
                    $partner->delete();
                    $status=true;
                }

                return Response()->json([
                    'success' => $status,
                ]);
            }

            if($action=='update-partners'){
                $partner =$company->persons()->where('type',2)->find($r->partner_id);
                $status=false;
                if($partner){
                    $allowedFields = ['name', 'mobile', 'mobile2', 'email', 'designation', 'company_address','city','district','division','company_name','description'];
                    if (in_array($r->name, $allowedFields)) {
                        $partner->{$r->name} = $r->key;
                        $partner->save();
                        $status = true;
                    }
                }

                return Response()->json([
                    'success' => $status,
                ]);
            }



        }


        if($action=='add-visit'){

            $check = $r->validate([
                'assignee' => 'required|numeric',
                'visit_date' => 'required|date|max:100',
                'location' => 'required|max:100',
                'description' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $visit =new Visit();
            $visit->src_id =$company->id;
            $visit->assignby_id =$r->assignee;
            $visit->visit_date =$r->visit_date?:Carbon::now();
            $visit->description =$r->description;
            $visit->location =$r->location;
            $visit->addedby_id =Auth::id();
            $visit->status =$r->status?:'Scheduled';
            $visit->type =0;
            $visit->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$visit->id;
             $srcType  =11;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Visit Are added successfully done!');
            return redirect()->back();


        }

        if($action=='update-visit'){

             $check = $r->validate([
                'assignee' => 'required|numeric',
                'visit_date' => 'required|date|max:100',
                'location' => 'required|max:100',
                'description' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $visit =Visit::latest()->where('src_id',$company->id)->where('type',0)->find($r->visit_id);
            if(!$visit){
                Session()->flash('error','This Visit Are Not Found');
                return redirect()->back();
            }

            $visit->assignby_id =$r->assignee;
            $visit->visit_date =$r->visit_date?:Carbon::now();
            $visit->description =$r->description;
            $visit->location =$r->location;
            $visit->editedby_id =Auth::id();
            $visit->status =$r->status?:'Scheduled';
            $visit->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$visit->id;
             $srcType  =11;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Visit Are Updated successfully done!');
            return redirect()->back();
        }

        if($action=='delete-visit'){
            $visit =Visit::latest()->where('src_id',$company->id)->where('type',0)->find($r->visit_id);
            if(!$visit){
                Session()->flash('error','This Visit Are Not Found');
                return redirect()->back();
            }
             //Task Media File Delete
            $medies =Media::where('src_type',11)->where('src_id',$visit->id)->get();
            foreach ($medies as  $media){
                if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                }
                $media->delete();
            }
            $visit->delete();

            Session()->flash('success','Visit Are Successfully Deleted!');
            return redirect()->back();

        }

        if($action=='add-meeting'){

            $check = $r->validate([
                'host' => 'required|numeric',
                'name' => 'required|max:100',
                'date_time' => 'required|date',
                'location' => 'required|max:100',
                'meeting_type' => 'required|max:50',
                'description' => 'nullable|max:2000',
            ]);

            $meeting =new Meeting();
            $meeting->participants_id =json_encode(array($company->id));
            $meeting->host_id =$r->host;
            $meeting->name =$r->name;
            $meeting->created_at =$r->date_time?:Carbon::now();
            $meeting->location =$r->location;
            $meeting->meeting_type =$r->meeting_type;
            $meeting->status ='Scheduled';
            $meeting->description =$r->description;
            $meeting->addedby_id =Auth::id();
            $meeting->save();

            Session()->flash('success','Meeting Are added successfully done!');
            return redirect()->back();


        }

        if($action=='update-meeting'){

            $check = $r->validate([
                'host' => 'required|numeric',
                'name' => 'required|max:100',
                'status' => 'required|max:20',
                'date_time' => 'required|date',
                'location' => 'required|max:100',
                'meeting_type' => 'required|max:50',
                'description' => 'nullable|max:2000',
            ]);

            $meeting =Meeting::latest()->whereJsonContains('participants_id', (int) $company->id)->find($r->meeting_id);
            if(!$meeting){
                Session()->flash('error','This Meeting Are Not Found');
                return redirect()->back();
            }
            $meeting->host_id =$r->host;
            $meeting->name =$r->name;
            $meeting->created_at =$r->date_time?:Carbon::now();
            $meeting->location =$r->location;
            $meeting->meeting_type =$r->meeting_type;
            $meeting->status =$r->status;
            $meeting->description =$r->description;
            $meeting->editedby_id =Auth::id();
            $meeting->save();

            Session()->flash('success','Meeting Are Updated successfully done!');
            return redirect()->back();

        }

        if($action=='delete-meeting'){

            $meeting =Meeting::latest()->whereJsonContains('participants_id', (int) $company->id)->find($r->meeting_id);
            if(!$meeting){
                Session()->flash('error','This Meeting Are Not Found');
                return redirect()->back();
            }
            $meeting->delete();

            Session()->flash('success','Meeting Are Successfully Deleted!');
            return redirect()->back();

        }

        if($action=='add-note'){

            $check = $r->validate([
                'note' => 'nullable|max:500',
            ]);

            $note =new Note();
            $note->src_id =$company->id;
            // $note->assignby_id =$lead->assinee_id?:$lead->addedby_id;
            $note->description =$r->note;
            $note->addedby_id =Auth::id();
            $note->type =0;
            $note->save();

            Session()->flash('success','Note Are added successfully done!');
            return redirect()->back();

        }

        if($action=='update-note'){

            $check = $r->validate([
                'note' => 'nullable|max:500',
            ]);

            $note =Note::latest()->where('src_id',$company->id)->where('type',0)->find($r->note_id);
            if(!$note){
                Session()->flash('error','This Note Are Not Found');
                return redirect()->back();
            }

            $note->description =$r->note;
            $note->editedby_id =Auth::id();
            $note->save();

            Session()->flash('success','Note Are Updated successfully done!');
            return redirect()->back();


        }

        if($action=='delete-note'){

            $note =Note::latest()->where('src_id',$company->id)->where('type',0)->find($r->note_id);
            if(!$note){
                Session()->flash('error','This Note Are Not Found');
                return redirect()->back();
            }
            $note->delete();

            Session()->flash('success','Visit Are Successfully Deleted!');
            return redirect()->back();
        }

        if($action=='sale-delete'){
            $sale =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices')->find($r->sale_id);
            if(!$sale){
                Session()->flash('error','Sale Are not found');
                return back();
            }
            $sale->items()->delete();
            $sale->transectionsAll()->delete();
            $sale->delete();

            Session()->flash('success','Sale Are Deleted');
            return redirect()->back();
        }

        if($action=='sale-add'){


            $check = $r->validate([
                // 'sale_price' => 'required|numeric',
                // 'qunaity' => 'required|numeric',
                'itemId.*' => 'required',
                'paid_amount' => 'required|numeric',
                'emi_amount' => 'nullable|numeric',
                'emi_duration' => 'nullable|numeric',
                // 'description' => 'nullable',
                'created_at' => 'required|date',
            ]);

            $invoice =new Order();
            $invoice->order_type ='sale_invoices';
            $invoice->order_status ='confirmed';
            $invoice->save();

            $invoice->addedby_id =Auth::id();
            $invoice->company_id=$company->id;
            $invoice->name=$company->factory_name?:$company->owner_name;
            $invoice->mobile=$company->owner_mobile;
            $invoice->email=$company->owner_email;
            $invoice->address=$company->company_address;
            $invoice->payment_method='cash';
            $invoice->currency='BDT';
            $invoice->emi_amount=$r->emi_amount?:0;
            $invoice->emi_time=$r->emi_duration?:0;
            $invoice->pay_amount=$r->paid_amount?:0;
            if($invoice->emi_amount > 0 && $invoice->emi_time > 0){
             $invoice->emi_status=true;
            }else{
             $invoice->emi_status=false;
            }
            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();
            $invoice->created_at =$createDate;
            $invoice->invoice =Carbon::now()->format('Ymd').$invoice->id;
            $invoice->save();

            foreach ($r->itemId as $i => $it) {

                // Check if first character is '0'
                $isCustom = str_starts_with($it, '0');

                if($isCustom){
                    $item = new OrderItem();
                    $item->order_id = $invoice->id;
                    $item->src_id = null;
                }else{
                    $item = $invoice->items()->where('src_id',$it)->first(); // optional: existing order items if updating
                    if (!$item) {
                        $item = new OrderItem();
                        $item->order_id = $invoice->id;
                        $item->src_id =$it;
                    }
                }

                $item->quantity = isset($r->qty[$i]) ? (float)$r->qty[$i] : 1;
                $item->description = $r->title[$i] ?? null;
                $item->unit = 'pcs';
                $item->price = isset($r->price[$i]) ? (float)$r->price[$i] : 0;

                $item->final_price = $item->quantity * $item->price;
                $item->status = $invoice->status;
                $item->addedby_id = Auth::id();
                $item->save();
            }

            // foreach($r->itemId as $it){

            //     $item =$invoice->items()->first();
            //     if(!$item){
            //         $item =new OrderItem();
            //         $item->order_id=$invoice->id;
            //         $item->src_id=null;
            //     }
            //     $item->quantity=1;
            //     $item->description=$r->description;
            //     $item->unit=null;
            //     $item->price=$invoice->total_qty > 0?$invoice->total_price/$invoice->total_qty:0;
            //     $item->final_price =$invoice->total_price;
            //     $item->status=$invoice->status;
            //     $item->addedby_id=Auth::id();
            //     $item->save();
            // }

            if($r->paid_amount && $r->paid_amount > 0){
                $transfer =Transaction::where('type',0)->where('src_id',$invoice->id)->where('payment_method','Cash')->first();
                if(!$transfer){
                    $transfer =new Transaction();
                    $transfer->type=0;
                    $transfer->src_id=$invoice->id;
                    $transfer->user_id=$company->id;
                    $transfer->payment_method='Cash';
                }

                $transfer->account_id=null;
                $transfer->billing_name=$invoice->name;
                $transfer->billing_mobile=$invoice->mobile;
                $transfer->billing_email=$invoice->email;
                $transfer->billing_address=$invoice->fullAddress();
                $transfer->amount=$r->paid_amount?:0;
                $transfer->currency=$invoice->currency;
                $transfer->billing_note=null;
                $transfer->billing_reason ='Down Payment';
                $transfer->status ='pending';
                $transfer->addedby_id =Auth::id();
                $transfer->created_at = $invoice->created_at;
                $transfer->save();
            }


            if($invoice->emi_amount > 0 && $invoice->emi_time > 0){
                $amount =$invoice->emi_amount/$invoice->emi_time;
                $createDate = $invoice->created_at;
                for($i=0;$i < $invoice->emi_time; $i++ ){
                    $paymentDate = Carbon::parse($createDate)->addMonth($i+1);
                    $transfer =new Transaction();
                    $transfer->type=0;
                    $transfer->src_id=$invoice->id;
                    $transfer->user_id=$company->id;
                    $transfer->account_id=null;
                    $transfer->billing_name=$invoice->name;
                    $transfer->billing_mobile=$invoice->mobile;
                    $transfer->billing_email=$invoice->email;
                    $transfer->billing_address=$invoice->fullAddress();
                    $transfer->payment_method_id=null;
                    $transfer->amount=$amount;
                    $transfer->currency=$invoice->currency;
                    $transfer->billing_note=null;
                    $transfer->billing_reason ='Installment Pay';
                    $transfer->status ='pending';
                    $transfer->addedby_id =Auth::id();
                    $transfer->created_at = $paymentDate;
                    $transfer->save();
                }
            }

            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->total_price;
            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }

            $invoice->save();

            Session()->flash('success','Sale Are Successfully Done');
            return redirect()->back();
        }


        if($action=='sale-update'){
            $invoice =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices')->find($r->sale_id);
            if(!$invoice){
                Session()->flash('error','Sale Are not found');
                return back();
            }

            // return $r;
            // $invoice->emi_amount=$r->emi_amount?:0;
            // $invoice->emi_time=$r->emi_duration?:0;

            $invoice->pay_amount=$r->paid_amount?:0;
            if($invoice->emi_amount > 0 && $invoice->emi_time > 0){
             $invoice->emi_status=true;
            }else{
             $invoice->emi_status=false;
            }
            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();
            if(!$invoice->created_at->isSameDay($createDate)){
            $invoice->created_at =$createDate;
            }

            $invoice->invoice =Carbon::now()->format('Ymd').$invoice->id;
            $invoice->save();

            $submittedIds = [];
            foreach ($r->itemId as $i => $it) {
                if(!str_starts_with($it, '0')){
                    $submittedIds[] = $it;
                }
            }

            $invoice->items()->where('src_id',null)->delete();
            $invoice->items()->whereNotIn('src_id', $submittedIds)->delete();
            foreach ($r->itemId as $i => $it) {

                $isCustom = str_starts_with($it, '0');

                if($isCustom){
                    $item = new OrderItem();
                    $item->order_id = $invoice->id;
                    $item->src_id = null;
                }else{
                    $item = $invoice->items()->where('src_id', $it)->first();
                    if(!$item){
                        $item = new OrderItem();
                        $item->order_id = $invoice->id;
                        $item->src_id = $it;
                    }
                }

                $item->description = $r->title[$i] ?? null;
                $item->quantity = isset($r->qty[$i]) ? (float)$r->qty[$i] : 1;
                $item->price = isset($r->price[$i]) ? (float)$r->price[$i] : 0;
                $item->final_price = $item->quantity * $item->price;
                $item->status = $invoice->status;
                $item->addedby_id = Auth::id();
                $item->save();
            }

            // $item =$invoice->items()->first();
            // if(!$item){
            //     $item =new OrderItem();
            //     $item->order_id=$invoice->id;
            //     $item->src_id=null;
            // }
            // $item->quantity=1;
            // $item->description=$r->description;
            // $item->unit=null;
            // $item->price=$invoice->total_qty > 0?$invoice->total_price/$invoice->total_qty:0;
            // $item->final_price =$invoice->total_price;
            // $item->status=$invoice->status;
            // $item->addedby_id=Auth::id();
            // $item->save();



            // if($r->paid_amount && $r->paid_amount > 0){
            //     $transfer =Transaction::where('type',0)->where('src_id',$invoice->id)->where('payment_method','Cash')->first();
            //     if(!$transfer){
            //         $transfer =new Transaction();
            //         $transfer->type=0;
            //         $transfer->src_id=$invoice->id;
            //         $transfer->user_id=$company->id;
            //         $transfer->payment_method='Cash';
            //     }

            //     $transfer->account_id=null;
            //     $transfer->billing_name=$invoice->name;
            //     $transfer->billing_mobile=$invoice->mobile;
            //     $transfer->billing_email=$invoice->email;
            //     $transfer->billing_address=$invoice->fullAddress();
            //     $transfer->amount=$r->paid_amount?:0;
            //     $transfer->currency=$invoice->currency;
            //     $transfer->billing_note=null;
            //     $transfer->billing_reason ='Sale payment';
            //     $transfer->status ='success';
            //     $transfer->addedby_id =Auth::id();
            //     $transfer->created_at = $invoice->created_at;
            //     $transfer->save();
            // }

            $invoice->total_items=$invoice->items()->count();
            $invoice->total_qty=$invoice->items()->sum('quantity');
            $invoice->total_price=$invoice->items()->sum('final_price');
            $invoice->grand_total=$invoice->total_price;
            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }

            $invoice->save();

            Session()->flash('success','Sale Are Successfully Updated');
            return redirect()->back();




        }

        if($action=='sale-emi-collect'){
            $check = $r->validate([
                'created_at' => 'required|date',
                'emi_id' => 'required|numeric',
                'account' => 'required|numeric',
                'method' => 'required|numeric',
                'note' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $invoice =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices')->find($r->sale_id);
            if(!$invoice){
                Session()->flash('error','Sale Are not found');
                return back();
            }

            $transfer =$invoice->transectionsAll()->find($r->emi_id);
            if(!$transfer){
                Session()->flash('error','Sale Emi Are not found');
                return back();
            }

            $account =Attribute::where('type',10)->where('status','active')->find($r->account);
            if(!$account){
                Session()->flash('error','Account method Are Not found');
                return redirect()->back();
            }

            $amount =$transfer->amount;
            if($invoice->currency=='USD'){
                $account->usd_amount +=$amount;
            }else{
                $account->amount +=$amount;
            }
            $account->save();

            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $transfer->account_id=$account->id;
            $transfer->billing_name=$invoice->name;
            $transfer->billing_mobile=$invoice->mobile;
            $transfer->billing_email=$invoice->email;
            $transfer->billing_address=$invoice->fullAddress();
            $transfer->payment_method_id=$r->method?:null;
            $transfer->currency=$invoice->currency?:'BDT';
            $transfer->billing_note=$r->note;
            $transfer->status ='success';
            $transfer->editedby_id =Auth::id();
            $transfer->created_at = $createDate;
            $transfer->save();

            ///////Image Upload End////////////
            if($r->hasFile('attachment')){
              $file =$r->attachment;
              $src  =$transfer->id;
              $srcType  =9;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////

            $invoice->paid_amount=$invoice->transectionsSuccess()->where('type',0)->sum('amount');
            $invoice->due_amount=$invoice->grand_total > $invoice->paid_amount?$invoice->grand_total-$invoice->paid_amount:0;
            $invoice->extra_amount=$invoice->paid_amount > $invoice->grand_total ? $invoice->paid_amount - $invoice->grand_total:0;
            if($invoice->paid_amount >= $invoice->grand_total){
                $invoice->payment_status='paid';
            }elseif($invoice->paid_amount > 0){
                $invoice->payment_status='partial';
            }else{
                $invoice->payment_status='unpaid';
            }
            $invoice->save();

            Session()->flash('success','Your payment are Successfully Received');
            return redirect()->back();

        }

        if($action=='sale-emi-add' || $action=='sale-emi-remove' || $action=='sale-emi-date' || $action=='sale-emi-amount'){

            if($action=='sale-emi-add'){
                $sale =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices')->find($r->sale_id);
                if($sale){

                    $transfer =new Transaction();
                    $transfer->type=0;
                    $transfer->src_id=$sale->id;
                    $transfer->user_id=$company->id;
                    $transfer->account_id=null;
                    $transfer->billing_name=$sale->name;
                    $transfer->billing_mobile=$sale->mobile;
                    $transfer->billing_email=$sale->email;
                    $transfer->billing_address=$sale->fullAddress();
                    $transfer->payment_method_id=null;
                    $transfer->amount=0;
                    $transfer->currency=$sale->currency;
                    $transfer->billing_note=null;
                    $transfer->billing_reason ='Installment Pay';
                    $transfer->status ='pending';
                    $transfer->addedby_id =Auth::id();
                    $transfer->created_at = Carbon::now();
                    $transfer->save();


                }else{
                    $message ='<span style="color:red;">Sale Are not found</span>';
                }
            }

            if($action=='sale-emi-remove'){
                $sale =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices')->find($r->sale_id);
                if($sale){

                    $transfer =$sale->transectionsAll()->find($r->emi_id);
                    if($transfer){

                       $transfer->delete();


                        $sale->emi_amount=$sale->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->sum('amount');
                        if($sale->emi_amount > 0 && $sale->emi_time > 0){
                            $sale->emi_status=true;
                        }else{
                            $sale->emi_status=false;
                        }
                        $sale->paid_amount=$sale->transectionsSuccess()->where('type',0)->sum('amount');
                        $sale->due_amount=$sale->grand_total > $sale->paid_amount?$sale->grand_total-$sale->paid_amount:0;
                        $sale->extra_amount=$sale->paid_amount > $sale->grand_total ? $sale->paid_amount - $sale->grand_total:0;
                        if($sale->paid_amount >= $sale->grand_total){
                            $sale->payment_status='paid';
                        }elseif($sale->paid_amount > 0){
                            $sale->payment_status='partial';
                        }else{
                            $sale->payment_status='unpaid';
                        }

                        $sale->save();

                    }else{
                       $message ='<span style="color:red;">Sale EMI Are not found</span>';
                    }

                }else{
                    $message ='<span style="color:red;">Sale Are not found</span>';
                }
            }

            if($action=='sale-emi-date'){
                $sale =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices')->find($r->sale_id);
                if($sale){

                    $transfer =$sale->transectionsAll()->find($r->emi_id);
                    if($transfer){
                        if($r->key){
                            $transfer->created_at =Carbon::parse($r->key);
                            $transfer->save();
                        }
                    }else{
                       $message ='<span style="color:red;">Sale EMI Are not found</span>';
                    }

                }else{
                    $message ='<span style="color:red;">Sale Are not found</span>';
                }
            }

            if($action=='sale-emi-amount'){
                $sale =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices')->find($r->sale_id);
                if($sale){

                    $transfer =$sale->transectionsAll()->find($r->emi_id);
                    if($transfer){
                        $transfer->amount =$r->key?:0;
                        $transfer->save();

                        $sale->emi_amount=$sale->transectionsAll()->where('billing_reason','like','%Installment%')->whereIn('status',['pending','success'])->sum('amount');
                        if($sale->emi_amount > 0 && $sale->emi_time > 0){
                            $sale->emi_status=true;
                        }else{
                            $sale->emi_status=false;
                        }
                        $sale->paid_amount=$sale->transectionsSuccess()->where('type',0)->sum('amount');
                        $sale->due_amount=$sale->grand_total > $sale->paid_amount?$sale->grand_total-$sale->paid_amount:0;
                        $sale->extra_amount=$sale->paid_amount > $sale->grand_total ? $sale->paid_amount - $sale->grand_total:0;
                        if($sale->paid_amount >= $sale->grand_total){
                            $sale->payment_status='paid';
                        }elseif($sale->paid_amount > 0){
                            $sale->payment_status='partial';
                        }else{
                            $sale->payment_status='unpaid';
                        }

                        $sale->save();

                    }else{
                       $message ='<span style="color:red;">Sale EMI Are not found</span>';
                    }

                }else{
                    $message ='<span style="color:red;">Sale Are not found</span>';
                }
            }


            $view =view(adminTheme().'companies.includes.emiList',compact('company','sale','message'))->render();

            return Response()->json([
                'success' => true,
                'view' => $view,
            ]);

        }

        if($action=='service-update'){
            $check = $r->validate([
                'title' => 'nullable|max:100',
                'engineer' => 'nullable|numeric',
                'employee' => 'required|numeric',
                'description' => 'nullable',
                'status' => 'required|max:20',
                'created_at' => 'required|date',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:25600',
            ]);

            $service =Service::latest()->where('company_id',$company->id)->find($r->item_id);
            if(!$service){
                Session()->flash('error','This service Are Not Found');
                return redirect()->back();
            }

            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $service->engineer_id =$r->engineer;
            $service->employee_id =$r->employee;
            $service->company_id =$company->id;
            $service->title =$r->title;
            $service->description =$r->description;
            if (!$createDate->isSameDay($service->created_at)) {
                $service->created_at = $createDate;
            }
            $service->status =$r->status?:'open';
            $service->save();

            Session()->flash('success','Service Are Successfully Updated');
            return redirect()->back();

      }

        if($action=='service-add'){
        $check = $r->validate([
            'title' => 'nullable|max:100',
            'engineer' => 'nullable|numeric',
            'employee' => 'required|numeric',
            'description' => 'nullable',
            'status' => 'required|max:20',
            'created_at' => 'required|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:25600',
        ]);

        $item =OrderItem::whereHas('order',function($q)use($company){ $q->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices');})->find($r->item_id);
        if(!$item){
            Session()->flash('error','This product Are Not Found');
            return redirect()->back();
        }

        $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

        $service =new Service();
        $service->src_id =$item->id;
        $service->service_id =$item->src_id;
        $service->service_name =$item->description;
        $service->engineer_id =$r->engineer;
        $service->employee_id =$r->employee;
        $service->company_id =$company->id;
        $service->title =$r->title;
        $service->description =$r->description;
        $service->created_at = $createDate;
        $service->status =$r->status?:'open';
        $service->save();

        Session()->flash('success','Service Are Successfully Added');
        return redirect()->back();
      }

        if($action=='edit'){

            return view(adminTheme().'companies.companyEdit',compact('company'));
        }

        // Update Company Action Start
        if($action=='update'){

        $check = $r->validate([
            'deed_serial' => 'nullable|max:100',
            'factory_name' => 'nullable|max:200',
            'owner_name' => 'required|max:100',
            'concern' => 'required|max:100',
            'owner_designation' => 'nullable|max:100',
            'owner_mobile' => 'required|max:100',
            'owner_email' => 'nullable|max:100',
            'key_parson_name' => 'nullable|max:100',
            'key_parson_designation' => 'nullable|max:100',
            'key_parson_mobile' => 'nullable|max:100',
            'key_parson_whatsapp_mobile' => 'nullable|max:100',
            'key_parson_email' => 'nullable|max:100',
            'partner_name' => 'nullable|max:100',
            'partner_designation' => 'nullable|max:100',
            'partner_details' => 'nullable',
            'manager_name' => 'nullable|max:100',
            'manager_designation' => 'nullable|max:100',
            'manager_details' => 'nullable',
            'pm_name' => 'nullable|max:100',
            'pm_designation' => 'nullable|max:100',
            'pm_details' => 'nullable',
            'operator_name' => 'nullable|max:100',
            'operator_details' => 'nullable',
            'operator2_name' => 'nullable|max:100',
            'operator2_details' => 'nullable',
            'engineer_name' => 'nullable|max:100',
            'engineer_designation' => 'nullable|max:100',
            'engineer_details' => 'nullable',
            'company_address' => 'nullable',
            'google_map' => 'nullable',
            'customer_status' => 'nullable|max:100',
            'company_category' => 'nullable|max:100',
            'company_status' => 'nullable|max:100',
            'machine_quantity' => 'nullable|numeric',
            'brand_name' => 'nullable|max:200',
            'number_of_employee' => 'nullable|numeric',
            'next_visit_day' => 'nullable|numeric',
            'next_visit_date' => 'nullable|date',
            'requirement' => 'nullable',
            'remarks' => 'nullable',
            'status' => 'required|max:20',
            'created_at' => 'required|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:25600',
        ]);


        $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

        $company->deed_serial=$r->deed_serial;
        $company->concern=$r->concern;
        $company->factory_name=$r->factory_name;
        $company->owner_name=$r->owner_name;
        $company->owner_designation=$r->owner_designation;
        $company->owner_mobile=$r->owner_mobile;
        $company->owner_email=$r->owner_email;
        $company->division=$r->division;
        $company->district=$r->district;
        $company->city=$r->city;
        $company->key_parson_name=$r->key_parson_name;
        $company->key_parson_designation=$r->key_parson_designation;
        $company->key_parson_mobile=$r->key_parson_mobile;
        $company->key_parson_whatsapp_mobile=$r->key_parson_whatsapp_mobile;
        $company->key_parson_email=$r->key_parson_email;
        $company->partner_name=$r->partner_name;
        $company->partner_designation=$r->partner_designation;
        $company->partner_details=$r->partner_details;
        $company->manager_name=$r->manager_name;
        $company->manager_designation=$r->manager_designation;
        $company->manager_details=$r->manager_details;
        $company->pm_name=$r->pm_name;
        $company->pm_designation=$r->pm_designation;
        $company->pm_details=$r->pm_details;
        $company->operator_name=$r->operator_name;
        $company->operator_details=$r->operator_details;
        $company->operator2_name=$r->operator2_name;
        $company->operator2_details=$r->operator2_details;
        $company->engineer_name=$r->engineer_name;
        $company->engineer_designation=$r->engineer_designation;
        $company->engineer_details=$r->engineer_details;
        $company->company_address=$r->company_address;
        $company->google_map=$r->google_map;
        $company->customer_status=$r->customer_status;
        $company->company_category=$r->company_category;
        $company->company_status=$r->company_status;
        $company->machine_quantity=$r->machine_quantity;
        $company->brand_name=$r->brand_name;
        $company->number_of_employee=$r->number_of_employee;
        $company->next_visit_day=$r->next_visit_day;
        $company->next_visit_date=$r->next_visit_date;
        $company->requirement=$r->requirement;
        $company->remarks=$r->remarks;

        ///////Image UploadStart////////////

        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$company->id;
          $srcType  =3;
          $fileUse  =1;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }

        ///////Image Upload End////////////

        ///////Banner Upload End////////////

        if($r->hasFile('banner')){

          $file =$r->banner;
          $src  =$company->id;
          $srcType  =3;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);

        }

        ///////Banner Upload End////////////

        if($r->factory_name){
            $slug =strtoupper(Str::slug($r->factory_name));
            if($slug==null){
              $company->slug=$company->id;
            }else{
              if(Company::where('slug',$slug)->whereNotIn('id',[$company->id])->count() >0){
              $company->slug=$slug.'-'.$company->id;
              }else{
              $company->slug=$slug;
              }
            }
        }else{
           $company->slug=null;
        }

        if (!$createDate->isSameDay($company->created_at)) {
            $company->created_at = $createDate;
        }
        $company->status =$r->status?'active':'inactive';
        $company->editedby_id =Auth::id();
        $company->save();

        Session()->flash('success','Your Are Successfully Done');
        return redirect()->route('admin.companies');
        return redirect()->back();

      }
        // Update Company Action End


        if($action=='add-person' || $action=='remove-person' || $action=='update-person'){

        $type =$r->type?:0;

        if($action=='add-person'){
            $data =new CompanyPerson();
            $data->company_id =$company->id;
            $data->save();
        }

        if($action=='remove-person'){
            $data =$company->persons()->where('type',$type)->find($r->person_id);
            if($data){
                $data->delete();
            }
        }

        if($action=='update-person'){
            $data =$company->persons()->where('type',$type)->find($r->person_id);
            if($data && in_array($r->column, ['name', 'designation','mobile','email','description'])){
                $data[$r->column]=$r->key_value;
                $data->save();
            }
        }

        $view =View(adminTheme().'companies.includes.personList',compact('company','type'))->render();

        return Response()->json([
          'success' => true,
          'view' => $view,
        ]);

      }

        if($action=='add-machine' || $action=='remove-machine' || $action=='update-machine'){


        if($action=='add-machine'){
            $data =new CompanyMachinery();
            $data->company_id =$company->id;
            $data->save();
        }

        if($action=='remove-machine'){
            $data =$company->machinery()->find($r->machine_id);
            if($data){
                $data->delete();
            }
        }

        if($action=='update-machine'){
            $data =$company->machinery()->find($r->machine_id);
            if($data && in_array($r->column, ['name', 'brand_name','quantity','note'])){
                $data[$r->column]=$r->key_value;
                $data->save();
            }
        }

        $view =View(adminTheme().'companies.includes.machineList',compact('company'))->render();

        return Response()->json([
          'success' => true,
          'view' => $view,
        ]);

      }

        // Delete Company Action Start
        if($action=='delete'){
        $medias =Media::latest()->where('src_type',3)->where('src_id',$company->id)->get();
        foreach($medias as $media){
          if(File::exists($media->file_url)){
            File::delete($media->file_url);
          }
          $media->delete();
        }


        $company->machinery()->delete();
        foreach($company->sales as $sale){
          $sale->items()->delete();
          $sale->transectionsAll()->delete();
          $sale->delete();
        }
        $company->persons()->delete();
        $company->services()->delete();
        $company->notes()->delete();
        $company->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.companies');

      }
        // Delete Company Action End

        $sales =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices')->get();
        $quotations =Order::latest()->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','quotation_order')->get();
        $lcInvoices =Order::latest()->where('company_id',$company->id)->where('order_status','<>','temp')->where('order_type','lc_invoices')->get();
        $products =OrderItem::whereHas('order',function($q)use($company){ $q->where('company_id',$company->id)->where('order_status','confirmed')->where('order_type','sale_invoices');})->get();
        $engineers =Attribute::latest()->where('type',0)->where('status','active')->where('parent_id',null)->get();
        $meetings =Meeting::latest()->where('status','<>','temp')->whereJsonContains('participants_id', (int) $company->id)->get();


        // return $meetings;
        $services =Service::latest()->where('company_id',$company->id)->paginate(50);
        $visits =Visit::latest()->where('src_id',$company->id)->where('type',0)->paginate(50);
        $notes =Note::latest()->where('src_id',$company->id)->where('type',0)->paginate(50);
        $users =User::latest()->where('admin',true)->get();

        $paymentMethods =Attribute::latest()->where('type',9)->where('status','active')->select(['id','name','amount'])->get();
        $accountMethods =Attribute::latest()->where('type',10)->where('status','active')->where('addedby_id',Auth::id())->select(['id','name','amount'])->get();

        return view(adminTheme().'companies.companyView',compact('company','services','notes','visits','users','products','engineers','meetings','sales','quotations','lcInvoices','action','message','paymentMethods','accountMethods'));

    }

    //Companies Function End


    //Merchandisers Function Start

    public function merchandisers(Request $r){

      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Attribute::latest()->where('type',4)->whereIn('id',$r->checkid)->get();

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

      $merchandisers=Attribute::latest()->where('type',4)->where('status','<>','temp')->where('parent_id',null)
                    ->where(function($q) use($r) {
                          if($r->search){
                              $q->where('name','LIKE','%'.$r->search.'%');
                          }
                          if($r->status){
                            $q->where('status',$r->status);
                          }
                      })
                    ->select(['id','name','slug','location','description','type','created_at','addedby_id','status','fetured'])
                    ->paginate(25);


      //Total Count Results
      $totals = DB::table('attributes')
      ->where('type',4)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'merchandiser.merchandisersAll',compact('merchandisers','totals'));

    }

    public function merchandisersAction(Request $r,$action,$id=null){

      //Create Merchandiser Start
      if($action=='create'){
        $check = $r->validate([
            'name' => 'required|max:100',
            'short_name' => 'required|min:3|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|max:1000',
        ]);

        $hasName =Attribute::where('type',4)->where('slug',$r->short_name)->first();
        if($hasName){
            Session()->flash('error','This Merchandiser Short Name Are Already Used');
            return redirect()->back()->withInput();
        }

        $merchandiser =Attribute::where('type',4)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$merchandiser){
          $merchandiser =new Attribute();
        }


        $merchandiser->name=$r->name;
        $merchandiser->description=$r->description;
        $merchandiser->type =4;
        $merchandiser->status ='active';
        $merchandiser->addedby_id =Auth::id();
        $merchandiser->save();

        ///////Image UploadStart////////////

        if($r->hasFile('photo')){
          $file =$r->photo;
          $src  =$merchandiser->id;
          $srcType  =3;
          $fileUse  =1;
          $author =Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        ///////Image Upload End////////////


         $slug =strtoupper(Str::slug($r->short_name));
         if($slug==null){
          $merchandiser->slug=$merchandiser->id;
         }else{
          if(Attribute::where('type',4)->where('slug',$slug)->whereNotIn('id',[$merchandiser->id])->count() >0){
          $merchandiser->slug=$slug.'-'.$merchandiser->id;
          }else{
          $merchandiser->slug=$slug;
          }
        }
        $merchandiser->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }
      //Create Merchandiser End

      $merchandiser =Attribute::where('type',4)->find($id);
      if(!$merchandiser){
        Session()->flash('error','This Merchandiser Are Not Found');
        return redirect()->route('admin.merchandisers');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['galleries']['all']);
      if($allPer && $merchandiser->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.merchandisers');
      }

      //Update Merchandiser Start
      if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:191',
            'short_name' => 'required|min:3|max:100',
            'location' => 'nullable|max:200',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $hasName =Attribute::where('type',4)->where('id','<>',$merchandiser->id)->where('slug',$r->short_name)->first();
        if($hasName){
            Session()->flash('error','This Merchandiser Short Name Are Already Used');
            return redirect()->back()->withInput();
        }

        $merchandiser->name=$r->name;
        $merchandiser->description=$r->description;
        $merchandiser->location=$r->location;

        ///////Image UploadStart////////////

        if($r->hasFile('photo')){
          $file =$r->photo;
          $src  =$merchandiser->id;
          $srcType  =3;
          $fileUse  =1;
          $author =Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        ///////Image Upload End////////////

        ///////Banner Upload End////////////

        if($r->hasFile('banner')){

          $file =$r->banner;
          $src  =$merchandiser->id;
          $srcType  =3;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);

        }

        ///////Banner Upload End////////////

        $slug =strtoupper(Str::slug($r->short_name));
        if($slug==null){
          $merchandiser->slug=$merchandiser->id;
        }else{
          if(Attribute::where('type',4)->where('slug',$slug)->whereNotIn('id',[$merchandiser->id])->count() >0){
          $merchandiser->slug=$slug.'-'.$merchandiser->id;
          }else{
          $merchandiser->slug=$slug;
          }
        }
        $merchandiser->status =$r->status?'active':'inactive';
        $merchandiser->editedby_id =Auth::id();
        $merchandiser->save();


        Session()->flash('success','Your Are Successfully Update');
        return redirect()->back();

      }
      //Update Merchandiser End

      //Delete Merchandiser Start
      if($action=='delete'){

        //Merchandiser  Media all File Delete
        $galleryMedies =Media::where('src_type',3)->where('src_id',$merchandiser->id)->get();

        foreach ($galleryMedies as  $media) {
              if(File::exists($media->file_url)){
                  File::delete($media->file_url);
              }
              $media->delete();
          }

        $merchandiser->delete();

        Session()->flash('success','Your Are Successfully Done');
        return redirect()->route('admin.merchandisers');

      }
      //Delete Merchandiser End

      return redirect()->back();

    }

    //Merchandisers Function End

    //engineers Function Start

    public function engineers(Request $r){

        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['engineers']['all']);
      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Attribute::latest()->where('type',0)->whereIn('id',$r->checkid)->get();

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


      $suppliers=Attribute::latest()->where('type',0)->where('status','<>','temp')->where('parent_id',null)
                    ->where(function($q) use($r,$allPer) {
                          if($r->search){
                              $q->where('name','LIKE','%'.$r->search.'%')->orWhere('mobile','LIKE','%'.$r->search.'%');
                          }
                          if($r->division){
                              $q->where('division',$r->division);
                          }
                          if($r->district){
                              $q->where('district',$r->district);
                          }
                          if($r->city){
                              $q->where('city',$r->city);
                          }
                          if($r->status){
                            $q->where('status',$r->status);
                          }
                          // Check Permission
                            if($allPer){
                             $q->where('addedby_id',auth::id());
                            }
                      })
                    // ->select(['id','name','slug','amount','description','type','created_at','addedby_id','status','fetured'])
                    ->paginate(25);


      //Total Count Results
      $totals = DB::table('attributes')
      ->where('type',0)
      ->where(function($q) use($allPer) {
          if($allPer){
             $q->where('addedby_id',auth::id());
            }
      })
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'engineers.engineersAll',compact('suppliers','totals'));

    }

    public function engineersExport(Request $r){

        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['engineers']['all']);

        $suppliers=Attribute::latest()->where('type',0)->where('status','<>','temp')->where('parent_id',null)
                    ->where(function($q) use($r,$allPer) {
                          if($r->search){
                              $q->where('name','LIKE','%'.$r->search.'%')->orWhere('mobile','LIKE','%'.$r->search.'%');
                          }
                          if($r->division){
                              $q->where('division',$r->division);
                          }
                          if($r->district){
                              $q->where('district',$r->district);
                          }
                          if($r->city){
                              $q->where('city',$r->city);
                          }
                          if($r->status){
                            $q->where('status',$r->status);
                          }
                          // Check Permission
                            if($allPer){
                             $q->where('addedby_id',auth::id());
                            }
                      })
                    // ->select(['id','name','slug','amount','description','type','created_at','addedby_id','status','fetured'])
                    ->get();

        return view(adminTheme().'engineers.engineersExport',compact('suppliers'));

    }

    public function engineersAction(Request $r,$action,$id=null){

      //Create engineers Start
      if($action=='create'){

        $supplier =Attribute::where('type',0)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$supplier){
            $supplier =new Attribute();
            $supplier->type =0;
            $supplier->status ='temp';
            $supplier->addedby_id =Auth::id();
            $supplier->save();
        }

        return redirect()->route('admin.engineersAction',['edit',$supplier->id]);

      }
      //Create engineers End

      $supplier =Attribute::where('type',0)->find($id);
      if(!$supplier){
        Session()->flash('error','This Engineer Are Not Found');
        return redirect()->route('admin.engineers');
      }

      //Update engineers Start
      if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:100',
            'mobile' => 'required|max:100',
            'mobile2' => 'nullable|max:100',
            'whatsapp_mobile' => 'nullable|max:100',
            'email' => 'nullable|max:100',
            'experience' => 'nullable|numeric',
            'designation' => 'nullable|max:100',
            'division' => 'nullable|numeric',
            'district' => 'nullable|numeric',
            'city' => 'nullable|numeric',
            'postal_code' => 'nullable|max:100',
            'address' => 'nullable|max:200',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

        $supplier->name=$r->name;
        $supplier->mobile=$r->mobile;
        $supplier->mobile2=$r->mobile2;
        $supplier->whatsapp_mobile=$r->whatsapp_mobile;
        $supplier->email=$r->email;
        $supplier->experience=$r->experience;
        $supplier->short_description=$r->designation;
        $supplier->division=$r->division;
        $supplier->district=$r->district;
        $supplier->city=$r->city;
        $supplier->icon=$r->postal_code;
        $supplier->location=$r->address;

        ///////Image UploadStart////////////

        if($r->hasFile('photo')){
          $file =$r->photo;
          $src  =$supplier->id;
          $srcType  =3;
          $fileUse  =1;
          $author =Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        ///////Image Upload End////////////


        $slug =strtoupper(Str::slug($r->name));
        if($slug==null){
          $supplier->slug=$supplier->id;
        }else{
          if(Attribute::where('type',0)->where('slug',$slug)->whereNotIn('id',[$supplier->id])->count() >0){
          $supplier->slug=$slug.'-'.$supplier->id;
          }else{
          $supplier->slug=$slug;
          }
        }
        if (!$createDate->isSameDay($supplier->created_at)) {
            $supplier->created_at = $createDate;
        }
        $supplier->status =$r->status?'active':'inactive';
        $supplier->editedby_id =Auth::id();
        $supplier->save();


        Session()->flash('success','Your Are Successfully Update');
        return redirect()->route('admin.engineers');
        return redirect()->back();

      }
      //Update engineers End

      //Delete engineers Start
      if($action=='delete'){

        //engineers  Media all File Delete
        $galleryMedies =Media::where('src_type',3)->where('src_id',$supplier->id)->get();

        foreach ($galleryMedies as  $media) {
                $fileUrl =$media->file_url;
                $file0 = ltrim($fileUrl, '/');
                if (str_starts_with($file0, 'public/')) {
                    $file0 = substr($file0, 7);
                }
                $basePath = base_path();
                $fullPath = $basePath . '/public/' . $file0;
                if (File::exists($fullPath)){
                    File::delete($fullPath);
                }
                $media->delete();
          }

        $supplier->delete();

        Session()->flash('success','Your Are Successfully Done');
        return redirect()->route('admin.engineers');

      }
      //Delete engineers End

      return view(adminTheme().'engineers.engineersEdit',compact('supplier'));


    }

    //suppliers Function End

    //Leads Function Start
    public function leads(Request $r){

        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['leads']['all']);

        $leads = Lead::latest()
                    ->where('status','<>','temp')
                    ->where(function($q) use($r,$allPer){
                          if($r->search){
                              $q->where(function($qq)use($r){
                                  $qq->where('name','LIKE','%'.$r->search.'%')->orWhere('factory_name','LIKE','%'.$r->search.'%')->orWhere('mobile','LIKE','%'.$r->search.'%');
                              });
                          }
                          if($r->division){
                              $q->where('division',$r->division);
                          }
                          if($r->district){
                              $q->where('district',$r->district);
                          }
                          if($r->city){
                              $q->where('city',$r->city);
                          }
                          if($r->status){
                            $q->where('customer_status',$r->status);
                          }
                          if($r->employee){
                            $q->where('assinee_id',$r->employee);
                          }
                          if($r->concern){
                                $concern = match($r->concern) {
                                    'MMC' => 'MG Machineries Corporation',
                                    'MTCI' => 'MG Training Centre Institute',
                                    default  => 'Embroidery Machine Corporation',
                                };
                                $q->where('concern',$concern);
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

                          // Check Permission
                            if($allPer){
                             $q->where('addedby_id',auth::id());
                            }
                    });

            if($r->export=='report'){

                $leads =$leads->get();

                return view(adminTheme().'leads.leadsExport',compact('leads'));

            }else{
                $leads =$leads->paginate(25)->appends($r->all());
            }


        //Total Count Results
        $totals = DB::table('leads')
        ->selectRaw('count(*) as total')
        ->where(function($q) use($allPer) {
              if($allPer){
                 $q->where('addedby_id',auth::id());
                }
          })
        ->selectRaw("count(case when customer_status = 'Not Potential' then 1 end) as nonPotential")
        ->selectRaw("count(case when customer_status = 'Potential' then 1 end) as potential")
        ->selectRaw("count(case when customer_status = 'Very Potential' then 1 end) as veryPotential")
        ->first();

        return view(adminTheme().'leads.leadsList',compact('leads','totals'));
    }

    public function leadsAction(Request $r,$action,$id=null){

        if($action=='create'){
           $lead =Lead::where('status','temp')->where('addedby_id',Auth::id())->first();
           if(!$lead){
               $lead =new Lead();
               $lead->status ='temp';
               $lead->addedby_id =Auth::id();
               $lead->save();
           }
           $lead->created_at =Carbon::now();
           $lead->save();

           return redirect()->route('admin.leadsAction',['edit',$lead->id]);
        }

        $lead =Lead::find($id);
        if(!$lead){
            Session()->flash('error','This Lead Are Not Found');
            return redirect()->route('admin.leads');
        }

        if($action=='convert-success'){

            $company =Company::find($r->company);

            return view(adminTheme().'leads.leadsConvertSuccess',compact('lead','company'));
        }

        if($action=='convert'){

            if($r->isMethod('post')){

                $check = $r->validate([
                    'deed_serial' => 'nullable|max:100',
                    'concern' => 'required|max:100',
                    'factory_name' => 'required|max:200',
                    'owner_name' => 'required|max:100',
                    'owner_designation' => 'nullable|max:100',
                    'owner_mobile' => 'required|max:100',
                    'owner_email' => 'nullable|max:100',
                    'key_parson_name' => 'nullable|max:100',
                    'key_parson_designation' => 'nullable|max:100',
                    'key_parson_mobile' => 'nullable|max:100',
                    'key_parson_whatsapp_mobile' => 'nullable|max:100',
                    'key_parson_email' => 'nullable|max:100',
                    'partner_name' => 'nullable|max:100',
                    'partner_designation' => 'nullable|max:100',
                    'partner_details' => 'nullable',
                    'manager_name' => 'nullable|max:100',
                    'manager_designation' => 'nullable|max:100',
                    'manager_details' => 'nullable',
                    'pm_name' => 'nullable|max:100',
                    'pm_designation' => 'nullable|max:100',
                    'pm_details' => 'nullable',
                    'operator_name' => 'nullable|max:100',
                    'operator_details' => 'nullable',
                    'operator2_name' => 'nullable|max:100',
                    'operator2_details' => 'nullable',
                    'engineer_name' => 'nullable|max:100',
                    'engineer_designation' => 'nullable|max:100',
                    'engineer_details' => 'nullable',
                    'company_address' => 'nullable',
                    'google_map' => 'nullable',
                    'customer_status' => 'nullable|max:100',
                    'company_category' => 'nullable|max:100',
                    'company_status' => 'nullable|max:100',
                    'machine_quantity' => 'nullable|numeric',
                    'brand_name' => 'nullable|max:200',
                    'number_of_employee' => 'nullable|numeric',
                    'next_visit_day' => 'nullable|numeric',
                    'next_visit_date' => 'nullable|date',
                    'requirement' => 'nullable',
                    'remarks' => 'nullable',
                    'status' => 'required|max:20',
                    'created_at' => 'required|date',
                ]);


                $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();
                $company =new Company();
                $company->deed_serial=$r->deed_serial;
                $company->concern=$r->concern;
                $company->factory_name=$r->factory_name;
                $company->owner_name=$r->owner_name;
                $company->owner_designation=$r->owner_designation;
                $company->owner_mobile=$r->owner_mobile;
                $company->owner_email=$r->owner_email;
                $company->key_parson_name=$r->key_parson_name;
                $company->key_parson_designation=$r->key_parson_designation;
                $company->key_parson_mobile=$r->key_parson_mobile;
                $company->key_parson_whatsapp_mobile=$r->key_parson_whatsapp_mobile;
                $company->key_parson_email=$r->key_parson_email;
                $company->partner_name=$r->partner_name;
                $company->partner_designation=$r->partner_designation;
                $company->partner_details=$r->partner_details;
                $company->manager_name=$r->manager_name;
                $company->manager_designation=$r->manager_designation;
                $company->manager_details=$r->manager_details;
                $company->pm_name=$r->pm_name;
                $company->pm_designation=$r->pm_designation;
                $company->pm_details=$r->pm_details;
                $company->operator_name=$r->operator_name;
                $company->operator_details=$r->operator_details;
                $company->operator2_name=$r->operator2_name;
                $company->operator2_details=$r->operator2_details;
                $company->engineer_name=$r->engineer_name;
                $company->engineer_designation=$r->engineer_designation;
                $company->engineer_details=$r->engineer_details;
                $company->company_address=$r->company_address;
                $company->division=$r->division;
                $company->district=$r->district;
                $company->city=$r->city;
                $company->google_map=$r->google_map;
                $company->customer_status=$r->customer_status;
                $company->company_category=$r->company_category;
                $company->company_status=$r->company_status;
                $company->number_of_employee=$r->number_of_employee;
                // $company->next_visit_day=$r->next_visit_day;
                $company->next_visit_date=$r->next_visit_day;
                $company->requirement=$r->requirement;
                $company->remarks=$r->remarks;

                if($r->factory_name){
                    $slug =strtoupper(Str::slug($r->factory_name));
                    if($slug==null){
                      $company->slug=$company->id;
                    }else{
                      if(Company::where('slug',$slug)->whereNotIn('id',[$company->id])->count() >0){
                      $company->slug=$slug.'-'.$company->id;
                      }else{
                      $company->slug=$slug;
                      }
                    }
                }else{
                   $company->slug=null;
                }

                if (!$createDate->isSameDay($company->created_at)) {
                    $company->created_at = $createDate;
                }

                $company->status =$r->status?'active':'inactive';
                $company->addedby_id =$lead->assinee_id?:Auth::id();
                $company->lead_id =$lead->id;
                $company->save();

                //Operator list
                foreach($lead->persons()->get() as $i=>$person){
                    $data =new CompanyPerson();
                    $data->company_id =$company->id;
                    $data->name =$person->name;
                    $data->designation =$person->designation;
                    $data->mobile =$person->mobile;
                    $data->email =$person->email;
                    $data->type =$person->type;
                    $data->description =$person->description;
                    $data->save();
                }

                $lead->status ='Win';
                $lead->save();

                Session()->flash('success','Lead Convert Are successfully done!');
                return redirect()->route('admin.leadsAction',['convert-success',$lead->id,'company'=>$company->id]);

            }

            return view(adminTheme().'leads.leadsConvert',compact('lead'));
        }

        if($action=='call-editmeeting'){
            $meeting =$lead->meetings()->find($r->meeting_id);
            $view =View(adminTheme().'leads.includes.meetingEditForm',compact('lead','meeting'))->render();
            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);
        }
        if($action=='call-meeting'){

            $view =View(adminTheme().'leads.includes.meetingForm',compact('lead'))->render();
            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);
        }

        if($action=='meeting'){

            $check = $r->validate([
                'title' => 'required|max:100',
                'date_time' => 'required|date|max:100',
                'meeting_type' => 'required|max:20',
                'location' => 'nullable|max:100',
                'description' => 'nullable|max:500',
            ]);

            $meeting =new Meeting();
            $meeting->participants_id =json_encode(array($lead->id));
            $meeting->host_id =$lead->assinee_id?:$lead->addedby_id;
            $meeting->name =$r->title;
            $meeting->created_at =$r->date_time?:Carbon::now();
            $meeting->location =$r->location;
            $meeting->meeting_type =$r->meeting_type;
            $meeting->status =$r->status?:'Scheduled';
            $meeting->description =$r->description;
            $meeting->addedby_id =Auth::id();
            $meeting->type =1; //Lead Meeting
            $meeting->save();

            Session()->flash('success','Meeting Are added successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);

        }

        if($action=='meeting-update'){
            $meeting =$lead->meetings()->find($r->meeting_id);
            if(!$meeting){
                Session()->flash('error','This Meeting Are Not Found');
                return redirect()->back();
            }
            $check = $r->validate([
                'title' => 'required|max:100',
                'date_time' => 'required|date|max:100',
                'meeting_type' => 'required|max:20',
                'location' => 'nullable|max:100',
                'description' => 'nullable|max:500',
                'status' => 'required|max:20',
            ]);
            $meeting->name =$r->title;
            $meeting->created_at =$r->date_time?:Carbon::now();
            $meeting->location =$r->location;
            $meeting->meeting_type =$r->meeting_type;
            $meeting->status =$r->status?:'Scheduled';
            $meeting->description =$r->description;
            $meeting->editedby_id =Auth::id();
            $meeting->save();

            Session()->flash('success','Meeting Are Updated successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);
        }

        if($action=='call-edittask'){
            $task =$lead->tasks()->find($r->task_id);
            $view =View(adminTheme().'leads.includes.taskEditForm',compact('lead','task'))->render();
            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);
        }

        if($action=='call-task'){
            $view =View(adminTheme().'leads.includes.taskForm',compact('lead'))->render();
            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);
        }

        if($action=='task'){

            $check = $r->validate([
                'name' => 'required|max:100',
                'due_date' => 'required|date|max:100',
                'priority' => 'required|max:20',
                'description' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $task =new Task();
            $task->src_id =$lead->id;
            $task->assignby_id =$lead->assinee_id?:$lead->addedby_id;
            $task->name =$r->name;
            $task->priority =$r->priority;
            $task->due_date =$r->due_date?:Carbon::now();
            $task->description =$r->description;
            $task->addedby_id =Auth::id();
            $task->status ='pending';
            $task->type =1; //Lead Task
            $task->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$task->id;
             $srcType  =10;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Task Are added successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);

        }

        if($action=='task-update'){
            $task =$lead->tasks()->find($r->task_id);
            if(!$task){
                Session()->flash('error','This Task Are Not Found');
                return redirect()->back();
            }
            $check = $r->validate([
                'name' => 'required|max:100',
                'due_date' => 'required|date|max:100',
                'priority' => 'required|max:20',
                'description' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|max:20',
                'assinee_date' => 'required|date',
            ]);

            $task->name =$r->name;
            $task->priority =$r->priority;
            $task->due_date =$r->due_date?:Carbon::now();
            $task->description =$r->description;
            $task->editedby_id =Auth::id();
            $task->status =$r->status?:'pending';
            $task->created_at =$r->assinee_date?:Carbon::now();
            $task->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$task->id;
             $srcType  =10;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Task Are Updated Successfully Done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);

        }

        if($action=='call-editvisit'){
            $visit =$lead->visits()->find($r->visit_id);
            $view =View(adminTheme().'leads.includes.visitsEditForm',compact('lead','visit'))->render();
            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);
        }

        if($action=='call-visit'){
            $view =View(adminTheme().'leads.includes.visitForm',compact('lead'))->render();
            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);
        }

        if($action=='visit'){

            $check = $r->validate([
                'visit_date' => 'required|date|max:100',
                'location' => 'required|max:100',
                'description' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $visit =new Visit();
            $visit->src_id =$lead->id;
            $visit->assignby_id =$lead->assinee_id?:$lead->addedby_id;
            $visit->visit_date =$r->visit_date?:Carbon::now();
            $visit->description =$r->description;
            $visit->location =$r->location;
            $visit->addedby_id =Auth::id();
            $visit->status =$r->status?:'Scheduled';
            $visit->type =1; //Lead Visit
            $visit->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$visit->id;
             $srcType  =11;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Visit Are added successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);

        }

        if($action=='visit-update'){
            $visit =$lead->visits()->find($r->visit_id);
            if(!$visit){
                Session()->flash('error','This Visit Are Not Found');
                return redirect()->back();
            }
            $check = $r->validate([
                'visit_date' => 'required|date|max:100',
                'location' => 'required|max:100',
                'description' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $visit->visit_date =$r->visit_date?:Carbon::now();
            $visit->description =$r->description;
            $visit->location =$r->location;
            $visit->status =$r->status?:'Scheduled';
            $visit->editedby_id =Auth::id();
            $visit->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$visit->id;
             $srcType  =11;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Visit Are Updated Successfully Done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);

        }

        if($action=='attachment'){

            $check = $r->validate([
                'attachment' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$lead->id;
             $srcType  =12;
             $fileUse  =3;
             $author=Auth::id();
             $fileStatus=false;
             uploadFile($file,$src,$srcType,$fileUse,$author,$fileStatus);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Attachment Are added successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);

        }

        if($action=='call-deletenattachment'){
            $file =$lead->attachmentFiles->find($r->file_id);
            if(!$file){
                Session()->flash('error','This attachment Are Not Found');
                return redirect()->back();
            }

            if(File::exists($file->file_url)){
                File::delete($file->file_url);
            }
            $file->delete();

            Session()->flash('success','Attachment Are Deleted successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);
        }

        if($action=='call-editnote'){

            $note =$lead->notes()->find($r->note_id);
            $view =View(adminTheme().'leads.includes.noteEditForm',compact('lead','note'))->render();
            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);
        }

        if($action=='call-note'){
            $view =View(adminTheme().'leads.includes.noteForm',compact('lead'))->render();
            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);
        }

        if($action=='note'){

            $check = $r->validate([
                'description' => 'nullable|max:500',
            ]);

            $note =new Note();
            $note->src_id =$lead->id;
            $note->assignby_id =$lead->assinee_id?:$lead->addedby_id;
            $note->description =$r->description;
            $note->addedby_id =Auth::id();
            $note->type =1; //Lead Note
            $note->save();

            Session()->flash('success','Note Are added successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);

        }

        if($action=='note-update'){
            $note =$lead->notes()->find($r->note_id);
            if(!$note){
                Session()->flash('error','This note Are Not Found');
                return redirect()->back();
            }
            $check = $r->validate([
                'description' => 'nullable|max:500',
            ]);

            $note->description =$r->description;
            $note->editedby_id =Auth::id();
            $note->save();

            Session()->flash('success','Note Are updated successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);

        }

        if($action=='call-deletenote'){
            $note =$lead->notes()->find($r->note_id);
            if(!$note){
                Session()->flash('error','This note Are Not Found');
                return redirect()->back();
            }
            $note->delete();
            Session()->flash('success','Note Are Deleted successfully done!');
            return redirect()->route('admin.leadsAction',['view',$lead->id]);
        }

        if($action=='update'){

            $check = $r->validate([
                'factory_name' => 'nullable|max:100',
                'owner_name' => 'required|max:100',
                // 'concern' => 'required|max:100',
                'owner_designation' => 'nullable|max:100',
                'owner_mobile' => 'required|max:100',
                'owner_email' => 'nullable|email|max:100',
                'company_address' => 'nullable|max:200',
                // 'source' => 'required|max:100',
                'key_parson_name' => 'nullable|max:100',
                'key_parson_designation' => 'nullable|max:100',
                'key_parson_mobile' => 'nullable|max:100',
                'key_parson_whatsapp_mobile' => 'nullable|max:100',
                'key_parson_email' => 'nullable|max:100',
                // 'company_address' => 'required',
                'google_map' => 'nullable',
                'partner_name' => 'nullable|max:100',
                'partner_designation' => 'nullable|max:100',
                'partner_details' => 'nullable',
                'manager_name' => 'nullable|max:100',
                'manager_designation' => 'nullable|max:100',
                'manager_details' => 'nullable',
                'pm_name' => 'nullable|max:100',
                'pm_designation' => 'nullable|max:100',
                'pm_details' => 'nullable',
                'operator_name' => 'nullable|max:100',
                'operator_details' => 'nullable',
                'operator2_name' => 'nullable|max:100',
                'operator2_details' => 'nullable',
                'engineer_name' => 'nullable|max:100',
                'engineer_designation' => 'nullable|max:100',
                'engineer_details' => 'nullable',
                'customer_status' => 'nullable|max:100',
                'company_category' => 'nullable|max:100',
                'company_status' => 'nullable|max:100',
                'machine_quantity' => 'nullable|numeric',
                'number_of_employee' => 'nullable|numeric',
                'services*' => 'nullable|numeric',
                'priority' => 'nullable|max:20',
                'assignee' => 'required|numeric',
                'note' => 'nullable|max:2000',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'requirement' => 'nullable',
                'remarks' => 'nullable',
                'next_visit_day' => 'nullable|date',
                'created_at' => 'required|date',
            ]);

            $hasLead =Lead::where('status',['New','Contacted','Interested','Follow-up Sheduled','Meeting Done','Proposal Sent'])
                        ->where('id','<>',$lead->id)
                        ->where(function($q)use($r){
                            $q->where('name',$r->owner_name)->orWhere('mobile',$r->owner_mobile);
                        })
                        ->first();
            // if($hasLead){
            //     $name =$hasLead->assineeUser?$hasLead->assineeUser->name:'Not Found';
            //     $text ='Your information Lead already Running, Owner name: '.$r->owner_name.', Mobile No: '.$r->owner_mobile.', assinee by '.$name;
            //     Session()->flash('error',$text);
            //     return redirect()->back()->withInput();
            // }

            $nextDate =$r->next_visit_day?Carbon::parse($r->next_visit_day . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();
            $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();

            $lead->factory_name=$r->factory_name;
            $lead->concern=$r->concern;
            $lead->name=$r->owner_name;
            $lead->mobile=$r->owner_mobile;
            $lead->email=$r->owner_email;
            $lead->designation=$r->owner_designation;
            $lead->address=$r->company_address;
            $lead->division=$r->division;
            $lead->district=$r->district;
            $lead->city=$r->city;
            $lead->source=$r->source;
            $lead->key_parson_name=$r->key_parson_name;
            $lead->key_parson_designation=$r->key_parson_designation;
            $lead->key_parson_mobile=$r->key_parson_mobile;
            $lead->key_parson_whatsapp_mobile=$r->key_parson_whatsapp_mobile;
            $lead->key_parson_email=$r->key_parson_email;
            $lead->google_map=$r->google_map;
            $lead->customer_status=$r->customer_status;
            $lead->partner_name=$r->partner_name;
            $lead->partner_designation=$r->partner_designation;
            $lead->partner_details=$r->partner_details;
            $lead->manager_name=$r->manager_name;
            $lead->manager_designation=$r->manager_designation;
            $lead->manager_details=$r->manager_details;
            $lead->pm_name=$r->pm_name;
            $lead->pm_designation=$r->pm_designation;
            $lead->pm_details=$r->pm_details;
            $lead->operator_name=$r->operator_name;
            $lead->operator_details=$r->operator_details;
            $lead->operator2_name=$r->operator2_name;
            $lead->operator2_details=$r->operator2_details;
            $lead->engineer_name=$r->engineer_name;
            $lead->engineer_designation=$r->engineer_designation;
            $lead->engineer_details=$r->engineer_details;
            $lead->company_category=$r->company_category;
            $lead->company_status=$r->company_status;
            $lead->machine_quantity=$r->machine_quantity;
            $lead->number_of_employee=$r->number_of_employee;
            $lead->services_id=$r->services;
            $lead->priority=$r->priority;
            $lead->assinee_id=$r->assignee;
            $lead->requirement=$r->requirement;
            $lead->notes=$r->remarks;

            if (!$createDate->isSameDay($lead->created_at)) {
                $lead->created_at = $createDate;
            }

            if($r->next_visit_day){
                if (!$nextDate->isSameDay($lead->next_visit_day)){
                    $lead->next_visit_day = $nextDate;
                }
            }else{
                $lead->next_visit_day = null;
            }

            $lead->status=$r->status?:'new';
            $lead->save();

            return redirect()->route('admin.leadsAction',['view',$lead->id]);
        }


        if($action=='add-person' || $action=='remove-person' || $action=='update-person'){

            $type =$r->type?:0;

            if($action=='add-person'){
                $data =new LeadPerson();
                $data->lead_id =$lead->id;
                $data->save();
            }

            if($action=='remove-person'){
                $data =$lead->persons()->where('type',$type)->find($r->person_id);
                if($data){
                    $data->delete();
                }
            }

            if($action=='update-person'){
                $data =$lead->persons()->where('type',$type)->find($r->person_id);
                if($data && in_array($r->column, ['name', 'designation','mobile','email','description'])){
                    $data[$r->column]=$r->key_value;
                    $data->save();
                }
            }

            $view =View(adminTheme().'leads.includes.personList',compact('lead','type'))->render();

            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);

        }

        if($action=='delete'){


            $lead->visits()->delete();
            $lead->tasks()->delete();
            $lead->notes()->delete();
            $lead->persons()->delete();
            $lead->attachmentFiles()->delete();
            $lead->meetings()->delete();
            $lead->delete();
            Session()->flash('success','Lead Are Deleted successfully done!');
            return back();
        }

        $users =User::latest()->where('admin',true)->get();

        $services =Post::latest()->where('type',3)->where('status','active')->where(function($q)use($r){
                if($r->search){
                    $q->where('name','like','%'.$r->search.'%');
                }
                })->select(['id','name'])->get();


        return view(adminTheme().'leads.leadsEdit',compact('lead','users','services','action'));

    }



    //Leads Function Start

    //tasks Function Start
    public function tasks(Request $r){
        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['tasks']['all']);
        $tasks =Task::latest()
                ->where(function($q) use($r,$allPer) {
                      if($r->search){
                          $q->where('name','LIKE','%'.$r->search.'%');
                      }
                      if($r->status){
                        $q->where('status',$r->status);
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

                          $q->whereDate('due_date','>=',$from)->whereDate('due_date','<=',$to);
                      }

                        // Check Permission
                        if($allPer){
                         $q->where('addedby_id',auth::id());
                        }

                })
                ->paginate(25);

        $users =User::latest()->where('admin',true)->get();
        $companies =Company::latest()->where('status','active')->get();

        //Total Count Results
        $totals = DB::table('tasks')
        ->selectRaw('count(*) as total')
        ->where(function($q) use($allPer) {
              if($allPer){
                 $q->where('addedby_id',auth::id());
                }
          })
        ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
        ->selectRaw("count(case when status = 'in progress' then 1 end) as progress")
        ->selectRaw("count(case when status = 'review' then 1 end) as review")
        ->selectRaw("count(case when status = 'completed' then 1 end) as completed")
        ->selectRaw("count(case when status = 'on hold' then 1 end) as hold")
        ->selectRaw("count(case when status = 'canceled' then 1 end) as canceled")
        ->first();

        return view(adminTheme().'tasks.tasksList',compact('tasks','totals','users','companies'));
    }


    public function tasksAction(Request $r,$action,$id=null){
        if($action=='create'){

            $check = $r->validate([
                'name' => 'required|max:100',
                'assignee' => 'required|numeric',
                'company' => 'nullable|numeric',
                'priority' => 'required|max:20',
                'due_date' => 'required|date',
                'description' => 'nullable|max:2000',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $task =new Task();
            $task->name =$r->name;
            $task->assignby_id =$r->assignee;
            $task->src_id =$r->company;
            $task->priority =$r->priority;
            $task->due_date =$r->due_date?:Carbon::now();
            $task->description =$r->description;
            $task->addedby_id =Auth::id();
            $task->status ='pending';
            $task->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$task->id;
             $srcType  =10;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Task Are Successfully Create Done!');
            return redirect()->back();

        }

        $task =Task::find($id);
        if(!$task){
            Session()->flash('error','This Task Are Not Found');
            return redirect()->route('admin.tasks');
        }

        if($action=='update'){
            $check = $r->validate([
                'name' => 'required|max:100',
                'assignee' => 'required|numeric',
                'company' => 'nullable|numeric',
                'priority' => 'required|max:20',
                'due_date' => 'required|date',
                'assinee_date' => 'required|date',
                'status' => 'required|max:20',
                'description' => 'nullable|max:2000',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $task->name =$r->name;
            $task->assignby_id =$r->assignee;
            $task->src_id =$r->company;
            $task->priority =$r->priority;
            $task->due_date =$r->due_date?:Carbon::now();
            $task->created_at =$r->assinee_date?:Carbon::now();
            $task->status =$r->status?:'pending';
            $task->description =$r->description;
            $task->addedby_id =Auth::id();
            $task->save();

            ///////Image Upload Start////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$task->id;
             $srcType  =10;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Task Are Successfully Updated Done!');
            return redirect()->back();

        }

        if($action=='delete'){

            //Task Media File Delete
            $medies =Media::where('src_type',10)->where('src_id',$task->id)->get();
            foreach ($medies as  $media){
                if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                }
                $media->delete();
            }

            $task->delete();

            Session()->flash('success','Task Are Successfully Deleted!');
            return redirect()->back();
        }

        return back();

    }



    //tasks Function Start

    //Meatings Function Start
    public function meetings(Request $r){
        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['meetings']['all']);
        $meetings=Meeting::latest()
                    ->where(function($q) use($r,$allPer) {
                          if($r->search){
                              $q->where('name','LIKE','%'.$r->search.'%');
                          }
                          if($r->status){
                            $q->where('status',$r->status);
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

                          // Check Permission
                        if($allPer){
                         $q->where('addedby_id',auth::id());
                        }


                      })
                    ->paginate(25);

        $users =User::latest()->where('admin',true)->get();
        $companies =Company::latest()->where('status','active')->get();

        //Total Count Results
        $totals = DB::table('meetings')
        ->where(function($q) use($allPer) {
              if($allPer){
                 $q->where('addedby_id',auth::id());
                }
          })
        ->selectRaw('count(*) as total')
        ->selectRaw("count(case when status = 'Scheduled' then 1 end) as scheduled")
        ->selectRaw("count(case when status = 'In progress' then 1 end) as progress")
        ->selectRaw("count(case when status = 'Completed' then 1 end) as completed")
        ->selectRaw("count(case when status = 'Canceled' then 1 end) as canceled")
        ->selectRaw("count(case when status = 'Rescheduled' then 1 end) as rescheduled")
        ->first();

        return view(adminTheme().'meetings.meetingsList',compact('meetings','totals','users','companies'));
    }

    public function meetingsAction(Request $r,$action,$id=null){

        if($action=='create'){

            $check = $r->validate([
                'company*' => 'required|numeric',
                'host' => 'required|numeric',
                'name' => 'required|max:100',
                'date_time' => 'required|date',
                'location' => 'required|max:100',
                'meeting_type' => 'required|max:50',
                'status' => 'required|max:20',
                'description' => 'nullable|max:2000',
            ]);

            $meeting =new Meeting();
            $meeting->participants_id =json_encode($r->company);
            $meeting->host_id =$r->host;
            $meeting->name =$r->name;
            $meeting->created_at =$r->date_time?:Carbon::now();
            $meeting->location =$r->location;
            $meeting->meeting_type =$r->meeting_type;
            $meeting->status =$r->status;
            $meeting->description =$r->description;
            $meeting->addedby_id =Auth::id();
            $meeting->save();


            Session()->flash('success','Meeting Are Successfully Create Done!');
            return redirect()->back();

        }

        $meeting =Meeting::find($id);
        if(!$meeting){
            Session()->flash('error','This Meeting Are Not Found');
            return redirect()->route('admin.meetings');
        }

        if($action=='update'){
            $check = $r->validate([
                'company*' => 'required|numeric',
                'host' => 'required|numeric',
                'name' => 'required|max:100',
                'date_time' => 'required|date',
                'location' => 'required|max:100',
                'meeting_type' => 'required|max:50',
                'status' => 'required|max:20',
                'description' => 'nullable|max:2000',
            ]);

            $meeting->participants_id =json_encode($r->company);
            $meeting->host_id =$r->host;
            $meeting->name =$r->name;
            $meeting->created_at =$r->date_time?:Carbon::now();
            $meeting->location =$r->location;
            $meeting->meeting_type =$r->meeting_type;
            $meeting->status =$r->status;
            $meeting->description =$r->description;
            $meeting->editedby_id =Auth::id();
            $meeting->save();

            Session()->flash('success','Meeting Are Successfully Updated Done!');
            return redirect()->back();

        }

        if($action=='delete'){

            $meeting->delete();

            Session()->flash('success','Meeting Are Successfully Deleted!');
            return redirect()->back();
        }


        return back();
    }

    //Meatings Function Start

    //Visit Function Start
    public function visits(Request $r){
        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['visits']['all']);

        $visits=Visit::latest()
                    ->where(function($q) use($r,$allPer) {

                            if ($r->search) {
                                $q->where(function ($query) use ($r) {
                                    $query->whereHas('company', function ($qq) use ($r) {
                                        $qq->where('factory_name', 'LIKE', '%' . $r->search . '%');
                                    })
                                    ->orWhereHas('lead', function ($qq) use ($r) {
                                        $qq->where('name', 'LIKE', '%' . $r->search . '%');
                                    });
                                });
                            }



                            if($r->status){
                                $q->where('status',$r->status);
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

                        // Check Permission
                        if($allPer){
                         $q->where('addedby_id',auth::id());
                        }
                      })
                    ->paginate(25);

        $users =User::latest()->where('admin',true)->get();
        $companies =Company::latest()->where('status','active')->get();

        //Total Count Results
        $totals = DB::table('visits')
        ->where(function($q) use($allPer) {
              if($allPer){
                 $q->where('addedby_id',auth::id());
                }
          })
        ->selectRaw('count(*) as total')
        ->selectRaw("count(case when status = 'Not Potential' then 1 end) as nonPotential")
        ->selectRaw("count(case when status = 'Potential' then 1 end) as potential")
        ->selectRaw("count(case when status = 'Very Potential' then 1 end) as veryPotential")
        ->first();

        return view(adminTheme().'visits.visitsList',compact('visits','totals','users','companies'));
    }

    public function visitsAction(Request $r,$action,$id=null){

        if($action=='create'){

            $check = $r->validate([
                'company' => 'required|numeric',
                'assignee' => 'required|numeric',
                'visit_date' => 'required|date|max:100',
                'location' => 'required|max:100',
                'description' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $visit =new Visit();
            $visit->src_id =$r->company;
            $visit->assignby_id =$r->assignee;
            $visit->visit_date =$r->visit_date?:Carbon::now();
            $visit->description =$r->description;
            $visit->location =$r->location;
            $visit->addedby_id =Auth::id();
            $visit->status =$r->status?:'Scheduled';
            $visit->type =0;
            $visit->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$visit->id;
             $srcType  =11;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Visit Are added successfully done!');
            return redirect()->back();

        }

        $visit =Visit::find($id);
        if(!$visit){
            Session()->flash('error','This Visit Are Not Found');
            return redirect()->route('admin.meetings');
        }

        if($action=='update'){

            $check = $r->validate([
                'company' => 'required|numeric',
                'assignee' => 'required|numeric',
                'visit_date' => 'required|date|max:100',
                'location' => 'required|max:100',
                'description' => 'nullable|max:500',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);


            $visit->src_id =$r->company;
            $visit->assignby_id =$r->assignee;
            $visit->visit_date =$r->visit_date?:Carbon::now();
            $visit->description =$r->description;
            $visit->location =$r->location;
            $visit->status =$r->status?:'Scheduled';
            $visit->editedby_id =Auth::id();
            $visit->save();

            ///////Image UploadStart////////////
            if($r->hasFile('attachment')){
             $file =$r->attachment;
             $src  =$visit->id;
             $srcType  =11;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            ///////Image Upload End////////////

            Session()->flash('success','Visit Are Successfully Updated Done!');
            return redirect()->back();

        }

        if($action=='delete'){
            //Task Media File Delete
            $medies =Media::where('src_type',11)->where('src_id',$visit->id)->get();
            foreach ($medies as  $media){
                if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                }
                $media->delete();
            }
            $visit->delete();

            Session()->flash('success','Visit Are Successfully Deleted!');
            return redirect()->back();
        }


        return back();
    }

    //Visit Function Start


    public function themeSetting(Request $r){
      return view(adminTheme().'theme-setting.themeSetting');
    }



    // Staff Management Function Start

    public function staffAdmin(Request $r){

      //Filter Actions Start
      if($r->action){
        if($r->checkid){

        $datas=User::latest()->whereIn('status',[0,1])->where('staff',true)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

            if($r->action==1){
              $data->status=1;
              $data->save();
            }elseif($r->action==2){
              $data->status=0;
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){

              //User Media File Delete
              $data->staff=false;
              $data->addedby_at=null;
              $data->permission_id=null;
              $data->addedby_id=null;
              $data->save();

            }

        }

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }

        return redirect()->back();
      }

      //Filter Action End


      $users =User::latest()->whereIn('status',[0,1])->where('staff',true)
        ->where(function($q) use($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
              $q->orWhere('email','LIKE','%'.$r->search.'%');
              $q->orWhere('mobile','LIKE','%'.$r->search.'%');
          }

          if($r->role){
             $q->where('permission_id',$r->role);
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

              $q->whereDate('addedby_at','>=',$from)->whereDate('addedby_at','<=',$to);
          }

      })
      ->select(['id','permission_id','name', 'designation_id', 'email','mobile','addedby_at','addedby_id','status'])
      ->paginate(12)->appends([
        'search'=>$r->search,
        'startDate'=>$r->startDate,
        'endDate'=>$r->endDate,
      ]);

      //Total Count Results
      $totals = DB::table('users')->whereIn('status',[0,1])->where('staff',true)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 1 then 1 end) as active")
      ->selectRaw("count(case when status = 0 then 1 end) as inactive")
      ->first();

      $roles =Permission::latest()->where('status','active')->get();

      return view(adminTheme().'users.staff.users',compact('users','totals','roles'));
    }

    public function staffAdminAction (Request $r,$action,$id=null){

      //Add Admin User Start
      if($action=='create' && $r->isMethod('post')){

        if(filter_var($r->username, FILTER_VALIDATE_EMAIL)){
          $hasUser =User::latest()->whereIn('status',[0,1])->where('email',$r->username)->first();
        }else{
          $hasUser =User::latest()->whereIn('status',[0,1])->where('mobile',$r->username)->first();
        }

        if(!$hasUser){
            Session()->flash('error','This User Are Not Register');
            return redirect()->route('admin.staffAdmin');
        }

        if($hasUser->staff){
            Session()->flash('error','This User Are already Staff Authorize');
            return redirect()->route('admin.staffAdmin');
        }

        $hasUser->staff=true;
        $hasUser->permission_id=1;
        $hasUser->addedby_at=Carbon::now();
        $hasUser->addedby_id=Auth::id();
        $hasUser->save();

        Session()->flash('success','User Are Successfully Staff Authorize Done!');
        return redirect()->route('admin.staffAdminAction',['edit',$hasUser->id]);

      }
      //Add Admin User End


      $user=User::whereIn('status',[0,1])->where('staff',true)->find($id);

      if(!$user){
        Session()->flash('error','This Staff User Are Not Found');
        return redirect()->route('admin.staffAdmin');
      }

        //Update User Profile Start
        if($action=='update' && $r->isMethod('post')){

            $check = $r->validate([
                 'name' => 'required|max:100|unique:users,name,'.$user->id,
                 'email' => 'required|max:100|unique:users,email,'.$user->id,
                 'mobile' => 'nullable|max:20|unique:users,mobile,'.$user->id,
                 'gender' => 'nullable|max:10',
                 'address' => 'nullable|max:191',
                 'division' => 'nullable|numeric',
                 'district' => 'nullable|max:191',
                 'city' => 'nullable|max:191',
                 'postal_code' => 'nullable|max:20',
                 'role' => 'nullable|numeric',
                 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

             ]);

           $user->name =$r->name;
           $user->mobile =$r->mobile;
           $user->email =$r->email;
           $user->gender =$r->gender;
           $user->address_line1 =$r->address;
           $user->division =$r->division;
           $user->district =$r->district;
           $user->city =$r->city;
           $user->postal_code =$r->postal_code;
           $user->permission_id =$r->role;

           ///////Image UploadStart////////////
           if($r->hasFile('image')){
             $file =$r->image;
             $src  =$user->id;
             $srcType  =6;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
           }
           ///////Image Upload End////////////

           $user->status=$r->status?true:false;
           $user->save();

           Session()->flash('success','Your Updated Are Successfully Done!');
           return redirect()->route('admin.staffAdminAction',['edit',$user->id]);
        }
        //Update User Profile End

        //Update User Password Start
        if($action=='change-password' && $r->isMethod('post')){

          $validator = Validator::make($r->all(), [
              'old_password' => 'required|string|min:8',
              'password' => 'required|string|min:8|confirmed|different:old_password',
          ]);

          if($validator->fails()){
              return redirect()->route('admin.staffAdminAction',['edit',$user->id])->withErrors($validator)->withInput();
          }

          if(Hash::check($r->old_password, $user->password)){
            $user->password_show=$r->password;
            $user->password=Hash::make($r->password);
            $user->update();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->route('admin.staffAdminAction',['edit',$user->id]);
          }else{
            Session()->flash('error','Current Password Are Not Match');
            return redirect()->route('admin.staffAdminAction',['edit',$user->id]);
          }
        }
        //Update User Password End

        //Delete User End
        if($action=='delete'){
          $user->staff=false;
          $user->addedby_at=null;
          $user->permission_id=null;
          $user->addedby_id=null;
          $user->save();

          Session()->flash('success','Staff User Are Removed Successfully Done');
          return redirect()->route('admin.staffAdmin');
        }
        //Delete User End
        $roles =Permission::latest()->where('status','active')->get();

        if($action == 'view'){
            return view(adminTheme().'users.staff.viewUser',compact('user','roles', 'action'));
        }

        return view(adminTheme().'users.staff.editUser',compact('user','roles'));

    }

    // User Management Function Start

    public function usersAdmin(Request $r){

      //Filter Actions Start
      if($r->action){
        if($r->checkid){

        $datas=User::latest()->whereIn('status',[0,1])->where('admin',true)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

            if($r->action==1){
              $data->status=1;
              $data->save();
            }elseif($r->action==2){
              $data->status=0;
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){

              //User Media File Delete
              $data->admin=false;
              $data->addedby_at=null;
              $data->permission_id=null;
              $data->addedby_id=null;
              $data->save();

            }

        }

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }

        return redirect()->back();
      }

      //Filter Action End


      $users =User::latest()->whereIn('status',[0,1])->where('admin',true)
        ->where('permission_id',1)
        ->where(function($q) use($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
              $q->orWhere('email','LIKE','%'.$r->search.'%');
              $q->orWhere('mobile','LIKE','%'.$r->search.'%');
          }

          if($r->role){
             $q->where('permission_id',$r->role);
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

              $q->whereDate('addedby_at','>=',$from)->whereDate('addedby_at','<=',$to);
          }

      })
      ->select(['id','permission_id','name', 'designation_id', 'email','mobile','addedby_at','addedby_id','status'])
      ->paginate(12)->appends([
        'search'=>$r->search,
        'startDate'=>$r->startDate,
        'endDate'=>$r->endDate,
      ]);

      //Total Count Results
      $totals = DB::table('users')->whereIn('status',[0,1])->where('admin',true)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 1 then 1 end) as active")
      ->selectRaw("count(case when status = 0 then 1 end) as inactive")
      ->first();

      $roles =Permission::latest()->where('status','active')->get();

      return view(adminTheme().'users.admins.users',compact('users','totals','roles'));
    }

    public function usersAdminAction (Request $r,$action,$id=null){
      //Add Admin User Start
      if($action=='create' && $r->isMethod('post')){

        if(filter_var($r->username, FILTER_VALIDATE_EMAIL)){
          $hasUser =User::latest()->whereIn('status',[0,1])->where('email',$r->username)->first();
        }else{
          $hasUser =User::latest()->whereIn('status',[0,1])->where('mobile',$r->username)->first();
        }

        if(!$hasUser){
            Session()->flash('error','This User Are Not Register');
            return redirect()->route('admin.usersAdmin');
        }

        if($hasUser->admin){
            Session()->flash('error','This User Are already Admin Authorize');
            return redirect()->route('admin.usersAdmin');
        }

        $hasUser->admin=true;
        $hasUser->permission_id=1;
        $hasUser->addedby_at=Carbon::now();
        $hasUser->addedby_id=Auth::id();
        $hasUser->save();

        Session()->flash('success','User Are Successfully Admin Authorize Done!');
        return redirect()->route('admin.usersAdminAction',['edit',$hasUser->id]);

      }
      //Add Admin User End


      $user=User::whereIn('status',[0,1])->where('admin',true)->find($id);

      if(!$user){
        Session()->flash('error','This Admin User Are Not Found');
        return redirect()->route('admin.usersAdmin');
      }

        //Update User Profile Start
        if($action=='update' && $r->isMethod('post')){

            $check = $r->validate([
                 'name' => 'required|max:100|unique:users,name,'.$user->id,
                 'email' => 'required|max:100|unique:users,email,'.$user->id,
                 'mobile' => 'nullable|max:20|unique:users,mobile,'.$user->id,
                 'gender' => 'nullable|max:10',
                 'address' => 'nullable|max:191',
                 'division' => 'nullable|numeric',
                 'district' => 'nullable|max:191',
                 'city' => 'nullable|max:191',
                 'postal_code' => 'nullable|max:20',
                 'role' => 'nullable|numeric',
                 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

             ]);

           $user->name =$r->name;
           $user->mobile =$r->mobile;
           $user->email =$r->email;
           $user->gender =$r->gender;
           $user->address_line1 =$r->address;
           $user->division =$r->division;
           $user->district =$r->district;
           $user->city =$r->city;
           $user->postal_code =$r->postal_code;
           $user->permission_id =$r->role;

           ///////Image UploadStart////////////
           if($r->hasFile('image')){
             $file =$r->image;
             $src  =$user->id;
             $srcType  =6;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
           }
           ///////Image Upload End////////////

           $user->status=$r->status?true:false;
           $user->save();

           Session()->flash('success','Your Updated Are Successfully Done!');
           return redirect()->route('admin.usersAdminAction',['edit',$user->id]);
        }
        //Update User Profile End

        //Update User Password Start
        if($action=='change-password' && $r->isMethod('post')){

          $validator = Validator::make($r->all(), [
              'old_password' => 'required|string|min:8',
              'password' => 'required|string|min:8|confirmed|different:old_password',
          ]);

          if($validator->fails()){
              return redirect()->route('admin.usersAdminAction',['edit',$user->id])->withErrors($validator)->withInput();
          }

          if(Hash::check($r->old_password, $user->password)){
            $user->password_show=$r->password;
            $user->password=Hash::make($r->password);
            $user->update();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->route('admin.usersAdminAction',['edit',$user->id]);
          }else{
            Session()->flash('error','Current Password Are Not Match');
            return redirect()->route('admin.usersAdminAction',['edit',$user->id]);
          }
        }
        //Update User Password End

        //Delete User End
        if($action=='delete'){
          $user->admin=false;
          $user->addedby_at=null;
          $user->permission_id=null;
          $user->addedby_id=null;
          $user->save();

          Session()->flash('success','Admin User Are Removed Successfully Done');
          return redirect()->route('admin.usersAdmin');
        }
        //Delete User End
        $roles =Permission::latest()->where('status','active')->get();


        if($action == 'view'){
            return view(adminTheme().'users.admins.viewUser',compact('user','roles', 'action'));
        }

        return view(adminTheme().'users.admins.editUser',compact('user','roles'));

    }



    public function usersCustomer(Request $r){

      //Filter Actions Start
      if($r->action){
        if($r->checkid){
        $datas=User::latest()->whereIn('status',[0,1])->whereIn('id',$r->checkid)->get();

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

      $users =User::latest()->whereIn('status',[0,1])
      ->where(function($q) use($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
              $q->orWhere('email','LIKE','%'.$r->search.'%');
              $q->orWhere('mobile','LIKE','%'.$r->search.'%');
          }
          if($r->status){
            $q->where('status',$r->status=='inactive'?0:1);
          }


          if($r->role_id){
              $q->where('permission_id',$r->role_id);
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
      ->select(['id','permission_id','name','email','designation_id','mobile','created_at','addedby_id','status'])
        ->paginate(25)->appends([
          'search'=>$r->search,
          'status'=>$r->status,
          'startDate'=>$r->startDate,
          'endDate'=>$r->endDate,
        ]);

      //Total Count Results
      $totals = DB::table('users')->whereIn('status',[0,1])
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 1 then 1 end) as active")
      ->selectRaw("count(case when status = 0 then 1 end) as inactive")
      ->first();

      $roles =Permission::latest()->where('status','active')->get();
      return view(adminTheme().'users.customers.users',compact('users','totals','roles'));
    }


    public function usersCustomerAction(Request $r,$action,$id=null){

      //Add New User Start
      if($action=='create' && $r->isMethod('post')){

        $user =User::where('email',$r->email)->first();
        if(!$user){
          $password=Str::random(8);
          $user =new User();
          $user->name =$r->name;
          $user->email =$r->email;
          $user->password_show=$password;
          $user->password=Hash::make($password);
          $user->save();
        }

        return redirect()->route('admin.usersCustomerAction',['edit',$user->id]);
      }
      //Add New User End


      $user=User::whereIn('status',[0,1])->find($id);
      if(!$user){
        Session()->flash('error','This User Are Not Found');
        return redirect()->route('admin.usersCustomer');
      }

         if($action == 'location'){
            if($r->ajax()){
                $lat = null;
                $lng = null;
                $time = null;

                if($user->lastLocation){
                    $lat = $user->lastLocation->latitude ?? null;
                    $lng = $user->lastLocation->longitude ?? null;
                    $time = $user->lastLocation->updated_at->format('d M y, h:i A');
                }

                return response()->json([
                    'latitude' => $lat,
                    'longitude'=> $lng,
                    'time'=> $time,
                ]);
            }

            return view('admin.user_location', compact('user', 'action'));
        }

      if($action=='edit'){
        $departments =Attribute::latest()->where('type',3)->where('status','<>','temp')->get();
        $designations =Attribute::latest()->where('type',2)->where('status','<>','temp')->get();

        $roles =Permission::latest()->where('status','active')->get();

        return view(adminTheme().'users.customers.editUser',compact('user','departments','designations','roles'));
      }

      if($action=='salary-details'){
        $salaries =$user->salaries()->latest()->paginate(12);
        return view(adminTheme().'users.customers.salaryDetails',compact('user','salaries'));
      }

      if($action=='loan-details'){
        $loans =$user->loans()->latest()->paginate(12);
        return view(adminTheme().'users.customers.loanDetails',compact('user','loans'));
      }

      //Update User Profile Start
      if($action=='update' && $r->isMethod('post')){

            $check = $r->validate([
                'name' => 'required|max:100',
                'email' => 'required|max:100|unique:users,email,'.$user->id,
                'mobile' => 'nullable|max:20|unique:users,mobile,'.$user->id,
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|max:10',
                'marital_status' => 'nullable|max:20',
                'present_address' => 'nullable|max:200',
                'address' => 'nullable|max:191',
                'division' => 'nullable|numeric',
                'district' => 'nullable|numeric',
                'city' => 'nullable|numeric',
                'designation' => 'nullable|numeric',
                'department' => 'nullable|numeric',
                'employee_id' => 'nullable|max:100',
                'profile' => 'nullable|max:1000',
                'salary_type' => 'required|max:100',
                'salary_amount' => 'nullable|numeric',
                'employment_status' => 'nullable|max:100',
                'exited_at' => 'nullable|date|max:50',
                'created_at' => 'nullable|date|max:50',
                'role' => 'nullable|numeric',
                'postal_code' => 'nullable|max:20',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user->name =$r->name;
            $user->mobile =$r->mobile;
            $user->email =$r->email;
            $user->gender =$r->gender;
            $user->marital_status =$r->marital_status;
            $user->dob =$r->date_of_birth;
            $user->address_line1 =$r->address;
            $user->address_line2 =$r->present_address;
            $user->division =$r->division;
            $user->district =$r->district;
            $user->city =$r->city;
            $user->postal_code =$r->postal_code;
            $user->designation_id =$r->designation;
            $user->department_id =$r->department;
            $user->employee_id =$r->employee_id;
            $user->salary_amount =$r->salary_amount?:0;
            $user->profile =$r->profile;
            $user->salary_type =$r->salary_type?:'Monthly';
            $user->employment_status =$r->employment_status?:'Monthly';
            $user->created_at =$r->created_at?:Carbon::now();
            $user->exited_at =$r->exited_at;

              if($r->password){
                $user->password_show=$r->password;
                $user->password=Hash::make($r->password);
              }

            if($user->id!=Auth::id() && Auth::user()->permission_id==1){

                if($r->role){
                    $user->admin=true;
                    $user->permission_id=$r->role;
                    $user->addedby_at=Carbon::now();
                    $user->addedby_id=Auth::id();
                }else{
                  $user->admin=false;
                  $user->addedby_at=null;
                  $user->permission_id=null;
                  $user->addedby_id=null;
                  $user->save();
                }
            }
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

          $user->login_status=$r->login_status?true:false;
          $user->status=$r->status?true:false;
          $user->save();

          Session()->flash('success','Your Updated Are Successfully Done!');
          return redirect()->back();

        }
        //Update User Profile End

        if($action=='user-document'){

            if($r->file_action=='addfile'){
                $media =new Media();
                $media->src_id=$user->id;
                $media->src_type=6;
                $media->use_Of_file=3;
                $media->addedby_id=Auth::id();
                $media->save();
            }

            if($r->file_action=='removeData'){
                $file =$user->galleryFiles()->find($r->file_id);
                if($file){
                    if(File::exists($file->file_url)){
                        File::delete($file->file_url);
                    }
                    $file->delete();
                }
            }

            if($r->file_action=='removeFile'){
                $file =$user->galleryFiles()->find($r->file_id);
                if($file){
                    if(File::exists($file->file_url)){
                        File::delete($file->file_url);
                    }
                    $file->file_url=null;
                    $file->alt_text=null;
                    $file->file_rename=null;
                    $file->file_size=null;
                    $file->file_path=null;
                    $file->save();
                }
            }

            if($r->file_action=='updateTitle'){
                $file =$user->galleryFiles()->find($r->file_id);
                if($file){
                   $file->file_name =$r->title;
                   $file->save();

                }
            }

            if($r->file_action=='updateFile'){
                $fileData =$user->galleryFiles()->find($r->file_id);
                if($fileData){
                    if(File::exists($fileData->file_url)){
                        File::delete($fileData->file_url);
                    }

                    $file =$r->file;

                    $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
                    $fullname = basename($file->getClientOriginalName());
                    $ext =$file->getClientOriginalExtension();
                    $size =$file->getSize();

                    $year =carbon::now()->format('Y');
                    $month =carbon::now()->format('M');
                    $folder = $month.'_'.$year;

                    $img =time().'.'.uniqid().'.'.$file->getClientOriginalExtension();
                    $path ="medies/".$folder;
                    $fullpath ="public/medies/".$folder.'/'.$img;
                    $fileData->alt_text=Str::limit($name,250);
                    $fileData->file_rename=Str::limit($img,100);
                    $fileData->file_size=$size;
                    if($ext=='png' || $ext=='jpeg' || $ext=='svg' || $ext=='gif' || $ext=='jpg' || $ext=='webp'){
                      $fileData->file_type=1;
                      }elseif($ext=='pdf'){
                      $fileData->file_type=2;
                      }elseif($ext=='docx'){
                      $fileData->file_type=3;
                      }elseif($ext=='zip' || $ext=='rar'){
                      $fileData->file_type=4;
                      }elseif($ext=='mp4' || $ext=='webm' || $ext=='mov' || $ext=='wmv'){
                      $fileData->file_type=5;
                      }elseif($ext=='mp3'){
                      $fileData->file_type=6;
                    }
                    $file->move(public_path($path), $img);
                    $fileData->file_url =$fullpath;
                    $fileData->file_path =$path;
                    $fileData->save();
                }
            }

            $view =View(adminTheme().'users.customers.includes.userFiles',compact('user'))->render();

            return Response()->json([
              'success' => true,
              'view' => $view,
            ]);

        }


        //Update User Password Change Start
        if($action=='change-password' && $r->isMethod('post')){

          $validator = Validator::make($r->all(), [
              'old_password' => 'required|string|min:8',
              'password' => 'required|string|min:8|confirmed|different:old_password',
          ]);

          if($validator->fails()){
              return redirect()->back()->withErrors($validator)->withInput();
          }

          if(Hash::check($r->old_password, $user->password)){
            $user->password_show=$r->password;
            $user->password=Hash::make($r->password);
            $user->update();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();
          }else{
          Session()->flash('error','Current Password Are Not Match');
          return redirect()->back();
          }

        }
        //Update User Password Change End

        //Delete User Start
        if($action=='delete'){

          $userFiles =Media::latest()->where('src_type',6)->where('src_id',$user->id)->get();
          foreach ($userFiles as $media){
                $file0 = $media->file_url;
                if(str_starts_with($file0, 'public/')){
                    $file0 = substr($file0, 7);
                }
                if(File::exists($file0)){
                  File::delete($file0);
                }
                $media->delete();
          }
          $user->delete();
          Session()->flash('success','User Are Deleted Successfully Deleted!');
          return redirect()->back();
        }
        //Delete User End



        $startDate=$r->startDate?Carbon::parse($r->startDate):Carbon::now()->startOfMonth();
        $endDate=$r->endDate?Carbon::parse($r->endDate):Carbon::now();


        return view(adminTheme().'users.customers.viewUser',compact('user','action','startDate','endDate'));
    }

    public function subscribes(Request $r){

    // Filter Action Start
      if($r->action){
        if($r->checkid){

        PostExtra::latest()->where('type',1)->whereIn('id',$r->checkid)->delete();

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }

        return redirect()->back();
      }

      //Filter Action End

    $subscribes =PostExtra::where('type',1)
    ->where(function($q) use ($r){

      if($r->search){
            $q->where('name','LIKE','%'.$r->search.'%');
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
    ->select(['id','name','created_at'])
    ->paginate(50)->appends([
      'search'=>$r->search,
      'startDate'=>$r->startDate,
      'endDate'=>$r->endDate,
    ]);

    return view(adminTheme().'users.subscribes.subscribeAll',compact('subscribes'));
  }


    public function userRoles(Request $r){

    $roles =Permission::latest()
    ->where('status','active')
    ->where(function($q) use($r) {

      if($r->search){
          $q->where('name','LIKE','%'.$r->search.'%');
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
  ->select(['id','name','created_at','addedby_id','status'])
    ->paginate(25)->appends([
      'search'=>$r->search,
      'startDate'=>$r->startDate,
      'endDate'=>$r->endDate,
    ]);

  return view(adminTheme().'users.roles.userRoles',compact('roles'));
}


  public function userRoleAction(Request $r,$action,$id=null){

    if($action=='create'){
        $role  =Permission::where('addedby_id',Auth::id())->where('status','temp')->first();
        if(!$role){
          $role = new Permission();
          $role->status='temp';
          $role->addedby_id=Auth::id();
        }
        $role->created_at=Carbon::now();
        $role->save();

        return redirect()->route('admin.userRoleAction',['edit',$role->id]);
    }

    $role=Permission::find($id);
    if(!$role){
      Session()->flash('error','This Role Are Not Found');
      return redirect()->route('admin.userRoles');
    }

    if($action=='update'){

      //Role Update
      $check = $r->validate([
          'name' => 'required|max:100',
      ]);

      if($role->id==1){
        $role->name =$r->name;
        $role->permission =$r->permission;
      }else{
        $role->name =$r->name;
        $role->permission =$r->permission;
      }
      $role->status ='active';
      $role->save();

      Session()->flash('success','Role Updated Are Successfully Done!');
      return redirect()->back();
    }

    if($action=='delete'){
      //Role Delete
      $role->delete();

      Session()->flash('success','Role Deleted Are Successfully Done!');
      return redirect()->route('admin.userRoles');

    }

    return view(adminTheme().'users.roles.userRoleEdit',compact('role'));

  }

  // User Management Function End

  public function reports(Request $r){
        $startDate=$r->startDate?Carbon::parse($r->startDate):Carbon::now();
        $endDate=$r->endDate?Carbon::parse($r->endDate):Carbon::now();


        $leads = Lead::latest()->where('status','<>','temp')
                ->where(function($q)use($r){
                    if($r->concern){
                        $q->where('concern',$r->concern);
                    }
                    if($r->employee_id){
                        $q->where('assinee_id',$r->employee_id);
                    }
                })
                ->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)
                ->get();

        $customers = Company::latest()->where('status','<>','temp')
                ->where(function($q)use($r){
                    if($r->concern){
                        $q->where('concern',$r->concern);
                    }
                    if($r->employee_id){
                        $q->where('addedby_id',$r->employee_id);
                    }
                })
                ->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)
                ->get();

        $meetings = Meeting::latest()->where('status','<>','temp')
                    ->where(function($q)use($r){
                        if($r->employee_id){
                            $q->where('host_id',$r->employee_id);
                        }
                    })
                    ->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)
                    ->get();

        $visits = Visit::latest()->where('status','<>','temp')
                    ->where(function($q)use($r){
                        if($r->employee_id){
                            $q->where('assignby_id',$r->employee_id);
                        }
                    })
                    ->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)
                    ->get();

        $sales =Order::latest()->where('order_type','sale_invoices')->where('order_status','confirmed')
                ->where(function($q)use($r){
                    if($r->employee_id){
                        $q->where('addedby_id',$r->employee_id);
                    }
                })
                ->whereDate('created_at','>=',$startDate)->whereDate('created_at','<=',$endDate)
                ->get();
        $summeryReport =[
            'Leads'=>$leads->count(),
            'Companies'=>$customers->count(),
            'Meeting'=>$meetings->count(),
            'Visits'=>$visits->count(),
            'Sales'=>$sales->sum('grand_total'),
            'SalesDue'=>$sales->sum('due_amount'),
            'SalesPaid'=>$sales->sum('paid_amount'),
        ];
      return view(adminTheme().'reports.summeryReports',compact('startDate','endDate','summeryReport','leads','customers','meetings','visits','sales'));
  }


  // Setting Function Start
  public function setting($type){

    $general =General::first();
    if($type=='general'){
      return view(adminTheme().'setting.general',compact('general','type'));
    }else if($type=='mail'){
      return view(adminTheme().'setting.mail',compact('general','type'));
    }else if($type=='sms'){
      return view(adminTheme().'setting.sms',compact('general','type'));
    }else if($type=='social'){
      return view(adminTheme().'setting.social',compact('general','type'));
    }else if($type=='document'){
      return view(adminTheme().'setting.document',compact('general','type'));
    }else if($type=='support'){
      return view(adminTheme().'setting.support',compact('general','type'));
    }else if($type=='logo'){

      if(File::exists($general->logo)){
            File::delete($general->logo);
      }
      $general->logo=null;
      $general->save();

      Session()->flash('success','Logo Deleted Are Successfully Done!');
      return redirect()->back();
    }else if($type=='favicon'){
       if(File::exists($general->favicon)){
            File::delete($general->favicon);
      }
      $general->favicon=null;
      $general->save();

      Session()->flash('success','Logo Deleted Are Successfully Done!');
      return redirect()->back();
    }else if($type=='signature'){
       if(File::exists($general->signature)){
            File::delete($general->signature);
      }
      $general->signature=null;
      $general->save();

      Session()->flash('success','Banner Deleted Are Successfully Done!');
      return redirect()->back();
    }else if($type=='cache-clear'){

      //return view(adminTheme().'setting.cacheDatabase',compact('general','type'));

      Artisan::call('cache:clear');
      Artisan::call('config:clear');
      Artisan::call('config:cache');
      Artisan::call('view:clear');
      Artisan::call('route:clear');
      Artisan::call('clear-compiled');

      Session()->flash('success','Cache Clear Are Successfully Done!');

      return redirect(url('/ecom9/admin/dashboard'));

    }else{
      return redirect()->route('admin.setting','general','type');
    }

  }


  public function settingUpdate(Request $r,$type){


    $general =General::first();

    if($type=='general'){

        $check = $r->validate([
            'title' => 'nullable|max:100',
            'subtitle' => 'nullable|max:200',
            'mobile' => 'nullable|max:100',
            'mobile2' => 'nullable|max:100',
            'email' => 'nullable|max:100',
            'email2' => 'nullable|max:100',
            'currency' => 'nullable|max:10',
            'website' => 'nullable|max:100',
            'meta_author' => 'nullable|max:100',
            'meta_title' => 'nullable|max:200',
            'meta_description' => 'nullable|max:200',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $general->title=$r->title;
        $general->subtitle=$r->subtitle;
        $general->mobile=$r->mobile;
        $general->mobile2=$r->mobile2;
        $general->email=$r->email;
        $general->email2=$r->email2;
        $general->address_one=$r->address_one;
        $general->address_two=$r->address_two;
        $general->currency=$r->currency;
        $general->website=$r->website;
        $general->meta_author=$r->meta_author;
        $general->meta_title=$r->meta_title;
        $general->meta_keyword=$r->meta_keyword;
        $general->meta_description=$r->meta_description;
        $general->script_head=$r->script_head;
        $general->script_body=$r->script_body;
        $general->custom_css=$r->custom_css;
        $general->custom_js=$r->custom_js;
        $general->copyright_text=$r->footer_text;
        $general->pi_terms_condition=$r->pi_terms_condition;


        ///////Image UploadStart////////////

        if($r->hasFile('logo')){

          $file=$r->logo;
          if(File::exists($general->logo)){
                File::delete($general->logo);
          }

          $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
          $fullName = basename($file->getClientOriginalName());
          $ext =$file->getClientOriginalExtension();
          $size =$file->getSize();

          $year =carbon::now()->format('Y');
          $month =carbon::now()->format('M');
          $folder = $month.'_'.$year;

          $img =time().'.'.uniqid().'.'.$file->getClientOriginalExtension();
          $path ="medies/".$folder;
          $fullPath ="medies/".$folder.'/'.$img;

          $file->move(public_path($path), $img);
          $general->logo =$fullPath;

      }

         ///////Image UploadStart////////////

        if($r->hasFile('favicon')){

            $file=$r->favicon;

            if(File::exists($general->favicon)){
                  File::delete($general->favicon);
            }

            $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $fullName = basename($file->getClientOriginalName());
            $ext =$file->getClientOriginalExtension();
            $size =$file->getSize();

            $year =carbon::now()->format('Y');
            $month =carbon::now()->format('M');
            $folder = $month.'_'.$year;

            $img =time().'.'.uniqid().'.'.$file->getClientOriginalExtension();
            $path ="medies/".$folder;
            $fullPath ="medies/".$folder.'/'.$img;

            $file->move(public_path($path), $img);
            $general->favicon =$fullPath;

        }

        if($r->hasFile('signature')){

            $file=$r->signature;

            if(File::exists($general->signature)){
                  File::delete($general->signature);
            }

            $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $fullName = basename($file->getClientOriginalName());
            $ext =$file->getClientOriginalExtension();
            $size =$file->getSize();

            $year =carbon::now()->format('Y');
            $month =carbon::now()->format('M');
            $folder = $month.'_'.$year;

            $img =time().'.'.uniqid().'.'.$file->getClientOriginalExtension();
            $path ="medies/".$folder;
            $fullPath ="medies/".$folder.'/'.$img;

            $file->move(public_path($path), $img);
            $general->signature =$fullPath;

        }
        $general->commingsoon_mode=$r->commingsoon_mode?true:false;
        $general->save();

        Session()->flash('success','General Updated Are Successfully Done!');

    }


    if($type=='mail'){

      $check = $r->validate([
            'mail_from_address' => 'nullable|max:100',
            'mail_from_name' => 'nullable|max:100',
            'mail_driver' => 'nullable|max:100',
            'mail_host' => 'nullable|max:100',
            'mail_port' => 'nullable|max:100',
            'mail_encryption' => 'nullable|max:100',
            'mail_username' => 'nullable|max:100',
            'mail_password' => 'nullable|max:100',
            'admin_mails' => 'nullable|max:1000',
        ]);

      $general->mail_from_address=$r->mail_from_address;
      $general->mail_from_name=$r->mail_from_name;
      $general->mail_driver=$r->mail_driver;
      $general->mail_host=$r->mail_host;
      $general->mail_port=$r->mail_port;
      $general->mail_encryption=$r->mail_encryption;
      $general->mail_username=$r->mail_username;
      $general->mail_password=$r->mail_password;
      $general->admin_mails=$r->admin_mails;
      $general->mail_status=$r->mail_status?true:false;
      $general->register_mail_user=$r->register_mail_user?true:false;
      $general->register_mail_author=$r->register_mail_author?true:false;
      $general->forget_password_mail_user=$r->forget_password_mail_user?true:false;
      $general->register_verify_mail_user=$r->register_verify_mail_user?true:false;
      $general->save();

      Session()->flash('success','Mail Updated Are Successfully Done!');

    }

    if($type=='sms'){

      $check = $r->validate([
            'sms_type' => 'nullable|max:50',
            'sms_senderid' => 'nullable|max:50',
            'sms_url_nonmasking' => 'nullable|max:200',
            'sms_url_masking' => 'nullable|max:200',
            'sms_username' => 'nullable|max:50',
            'sms_password' => 'nullable|max:50',
            'admin_numbers' => 'nullable|max:1000',
      ]);

      $general->sms_type=$r->sms_type;
      $general->sms_senderid=$r->sms_senderid;
      $general->sms_url_nonmasking=$r->sms_url_nonmasking;
      $general->sms_url_masking=$r->sms_url_masking;
      $general->sms_username=$r->sms_username;
      $general->sms_password=$r->sms_password;
      $general->admin_numbers=$r->admin_numbers;
      $general->sms_status=$r->sms_status?true:false;
      $general->register_sms_user=$r->register_sms_user?true:false;
      $general->register_sms_author=$r->register_sms_author?true:false;
      $general->forget_password_sms_user=$r->forget_password_sms_user?true:false;
      $general->register_verify_sms_user=$r->register_verify_sms_user?true:false;
      $general->save();

      Session()->flash('success','SMS Updated Are Successfully Done!');

    }

    if($type=='social'){


      $check = $r->validate([
            'facebook_link' => 'nullable|max:200',
            'twitter_link' => 'nullable|max:200',
            'instagram_link' => 'nullable|max:200',
            'linkedin_link' => 'nullable|max:200',
            'pinterest_link' => 'nullable|max:200',
            'youtube_link' => 'nullable|max:200',
            'fb_app_id' => 'nullable|max:100',
            'fb_app_secret' => 'nullable|max:100',
            'fb_app_redirect_url' => 'nullable|max:200',
            'google_client_id' => 'nullable|max:100',
            'google_client_secret' => 'nullable|max:100',
            'google_client_redirect_url' => 'nullable|max:200',
            'tw_app_id' => 'nullable|max:100',
            'tw_app_secret' => 'nullable|max:100',
            'tw_app_redirect_url' => 'nullable|max:200',
        ]);

        $general->facebook_link=$r->facebook_link;
        $general->twitter_link=$r->twitter_link;
        $general->instagram_link=$r->instagram_link;
        $general->linkedin_link=$r->linkedin_link;
        $general->pinterest_link=$r->pinterest_link;
        $general->youtube_link=$r->youtube_link;
        $general->fb_app_id=$r->fb_app_id;
        $general->fb_app_secret=$r->fb_app_secret;
        $general->fb_app_redirect_url=$r->fb_app_redirect_url;
        $general->google_client_id=$r->google_client_id;
        $general->google_client_secret=$r->google_client_secret;
        $general->google_client_redirect_url=$r->google_client_redirect_url;
        $general->tw_app_id=$r->tw_app_id;
        $general->tw_app_secret=$r->tw_app_secret;
        $general->tw_app_redirect_url=$r->tw_app_redirect_url;
        $general->save();

        Session()->flash('success','Advance Updated Are Successfully Done!');

    }


    return redirect()->route('admin.setting',$type);


  }

  // Setting Function End



}
