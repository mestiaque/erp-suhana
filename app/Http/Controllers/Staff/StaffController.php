<?php

namespace App\Http\Controllers\Staff;

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

class StaffController extends Controller
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
        return view('staff.dashboard');
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


}
