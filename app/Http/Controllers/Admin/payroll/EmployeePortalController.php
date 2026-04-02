<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Media;
use App\Models\payroll\Attendance;
use App\Models\payroll\Leave;
use App\Models\payroll\Notice;
use App\Models\payroll\Shift;
use App\Models\payroll\UserLocation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class EmployeePortalController extends Controller
{
    /**
     * Employee Portal Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        // Get this month's attendance summary
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthlyAttendance = Attendance::where('user_id', $user->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->get();

        $presentDays = $monthlyAttendance->where('in_time', '!=', null)->count();
        $absentDays = Carbon::now()->daysInMonth - $presentDays;

        // Get pending leave applications
        $pendingLeaves = Leave::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Get active notices for employee dashboard
        $notices = Notice::where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->orderBy('priority', 'desc')
            ->orderBy('notice_date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.payroll.employee_portal.dashboard', compact('todayAttendance', 'presentDays', 'absentDays', 'pendingLeaves', 'notices'));
    }

    /**
     * Daily Attendance (Mark Attendance)
     */
    public function dailyAttendance(Request $request)
    {
        $user = Auth::user();
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        // Check if already marked
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $date)
            ->first();

        if ($request->isMethod('post')) {
            // Mark attendance
            if (!$attendance) {
                Attendance::create([
                    'user_id' => $user->id,
                    'in_time' => Carbon::now()->format('H:i:s'),
                    'date' => $date,
                    'created_at' => Carbon::now()
                ]);
                return redirect()->back()->with('success', 'Attendance marked successfully!');
            } else {
                return redirect()->back()->with('error', 'Attendance already marked!');
            }
        }

        return view('admin.payroll.employee_portal.daily_attendance', compact('attendance', 'date'));
    }

    /**
     * Online Attendance (With Google Map Location)
     */
    public function onlineAttendance(Request $request)
    {
        $user = Auth::user();

        if ($request->isMethod('post')) {
            $request->validate([
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            // Store location with attendance
            Attendance::create([
                'user_id' => $user->id,
                'in_time' => Carbon::now()->format('H:i:s'),
                'out_time' => Carbon::now()->format('H:i:s'),
                'date' => Carbon::today()->format('Y-m-d'),
                'location_lat' => $request->latitude,
                'location_long' => $request->longitude,
                'created_at' => Carbon::now()
            ]);

            return redirect()->back()->with('success', 'Online attendance marked with location!');
        }

        return view('admin.payroll.employee_portal.online_attendance');
    }

    /**
     * Personal Information View
     */
    public function myProfile()
    {
        $user = Auth::user();
        return view('admin.payroll.employee_portal.profile', compact('user'));
    }

    /**
     * View Monthly Attendance
     */
    public function monthlyAttendance(Request $request)
    {
        $user = Auth::user();
        $month = $request->month ?? Carbon::now()->format('m');
        $year = $request->year ?? Carbon::now()->format('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();

        // Get leaves for the month
        $leaves = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->get();

        return view('admin.payroll.employee_portal.monthly_attendance', compact('attendances', 'leaves', 'month', 'year'));
    }

    public function myLocationUpdate(Request $r){

        if($r->ajax()){
            $user =Auth::user();

            $data =UserLocation::where('user_id',$user->id)->first();
            if(!$data){
                $data =new UserLocation();
                $data->user_id =$user->id;
            }
            $data->latitude =$r->lat;
            $data->longitude =$r->lng;
            $data->visit_url =$r->visit_url;
            $data->save();
            $user->latitude =$data->latitude;
            $user->longitude =$data->longitude;
            $user->save();
            return Response()->json([
                  'success' => true
              ]);
        }

        return redirect()->route('admin.dashboard');
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


    //Employee Type Function
    public function employeeType(Request $r){


      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Attribute::latest()->where('type',16)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

            if($r->action==1){
              $data->status='active';
              $data->save();
            }elseif($r->action==2){
              $data->status='inactive';
              $data->save();
            }elseif($r->action==5){

              $medias =Media::latest()->where('src_type',16)->where('src_id',$data->id)->get();
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

      $employeeTypes=Attribute::latest()->where('type',16)->where('status','<>','temp')
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
      ->where('type',16)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'payroll.employee-types.employeeTypesAll',compact('employeeTypes','totals'));

    }

    public function employeeTypeAction(Request $r,$action,$id=null){
      // Add EmployeeType Action Start
      if($action=='create'){

        $check = $r->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:1000',
        ]);

        $employeeType =Attribute::where('type',16)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$employeeType){
          $employeeType =new Attribute();
        }
        $employeeType->name=$r->name;
        $employeeType->description=$r->description;
        $employeeType->type =16;
        $employeeType->status ='active';
        $employeeType->addedby_id =Auth::id();
        $employeeType->save();

        $slug =Str::slug($r->name);
         if($slug==null){
          $employeeType->slug=$employeeType->id;
         }else{
          if(Attribute::where('type',16)->where('slug',$slug)->whereNotIn('id',[$employeeType->id])->count() >0){
          $employeeType->slug=$slug.'-'.$employeeType->id;
          }else{
          $employeeType->slug=$slug;
          }
        }
        $employeeType->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }

      // Add EmployeeType Action End


      $employeeType =Attribute::where('type',16)->find($id);
      if(!$employeeType){
        Session()->flash('error','This Employee Type Are Not Found');
        return redirect()->route('admin.employeeTypes');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['clients']['all']);
      if($allPer && $employeeType->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.employeeTypes');
      }

      // Update EmployeeType Action Start
      if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:191',
            'seo_title' => 'nullable|max:200',
            'seo_desc' => 'nullable|max:250',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $employeeType->name=$r->name;
        $employeeType->short_description=$r->short_description;
        $employeeType->description=$r->description;
        $employeeType->seo_title=$r->seo_title;
        $employeeType->short_description=$r->short_description;
        $employeeType->seo_keyword=$r->seo_keyword;

        ///////Image UploadStart////////////

        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$employeeType->id;
          $srcType  =3;
          $fileUse  =1;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }

        ///////Image Upload End////////////

        ///////Banner Upload End////////////

        if($r->hasFile('banner')){

          $file =$r->banner;
          $src  =$employeeType->id;
          $srcType  =3;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);

        }

        ///////Banner Upload End////////////

        $slug =Str::slug($r->name);
         if($slug==null){
          $employeeType->slug=$employeeType->id;
         }else{
          if(Attribute::where('type',16)->where('slug',$slug)->whereNotIn('id',[$employeeType->id])->count() >0){
          $employeeType->slug=$slug.'-'.$employeeType->id;
          }else{
          $employeeType->slug=$slug;
          }
        }

        $employeeType->status =$r->status?'active':'inactive';
        $employeeType->fetured =$r->fetured?1:0;
        $employeeType->editedby_id =Auth::id();
        $employeeType->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update EmployeeType Action End


      // Delete EmployeeType Action Start
      if($action=='delete'){
        $medias =Media::latest()->where('src_type',16)->where('src_id',$employeeType->id)->get();
        foreach($medias as $media){
          if(File::exists($media->file_url)){
            File::delete($media->file_url);
          }
          $media->delete();
        }

        $employeeType->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.employeeTypes');

      }
      // Delete EmployeeType Action End
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

      return view(adminTheme().'payroll.designations.designationsAll',compact('designations','totals'));

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


    // divisions
    public function divisions(Request $r){

          if($r->action){
            if($r->checkid){

            $datas=Attribute::latest()->where('type',11)->whereIn('id',$r->checkid)->get();

            foreach($datas as $data){

                if($r->action==1){
                  $data->status='active';
                  $data->save();
                }elseif($r->action==2){
                  $data->status='inactive';
                  $data->save();
                }elseif($r->action==5){

                  $medias =Media::latest()->where('src_type',11)->where('src_id',$data->id)->get();
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


          $divisions=Attribute::latest()->where('type',11)->where('status','<>','temp')
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


          $totals = DB::table('attributes')
          ->where('type',11)
          ->selectRaw('count(*) as total')
          ->selectRaw("count(case when status = 'active' then 1 end) as active")
          ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
          ->first();

          return view(adminTheme().'payroll.divisions.divisionsAll',compact('divisions','totals'));

    }

    public function divisionsAction(Request $r,$action,$id=null){

          if($action=='create'){
            $check = $r->validate([
                'name' => 'required|max:100',
                'description' => 'nullable|max:1000',
            ]);

            $division =Attribute::where('type',11)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$division){
              $division =new Attribute();
            }

            $division->name=$r->name;
            $division->description=$r->description;
            $division->type =11;
            $division->status ='active';
            $division->addedby_id =Auth::id();
            $division->save();

             $slug =Str::slug($r->name);
             if($slug==null){
              $division->slug=$division->id;
             }else{
              if(Attribute::where('type',11)->where('slug',$slug)->whereNotIn('id',[$division->id])->count() >0){
              $division->slug=$slug.'-'.$division->id;
              }else{
              $division->slug=$slug;
              }
            }
            $division->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

          }


          $division =Attribute::where('type',11)->find($id);
          if(!$division){
            Session()->flash('error','This Division Are Not Found');
            return redirect()->route('admin.admin.divisions');
          }

          $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
          if($allPer && $division->addedby_id!=Auth::id()){
            Session()->flash('error','You are unauthorized Try!!');
            return redirect()->route('admin.admin.divisions');
          }


          if($action=='update'){

              $check = $r->validate([
                  'name' => 'required|max:191',
                  'seo_title' => 'nullable|max:200',
                  'seo_desc' => 'nullable|max:250',
                  'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                  'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
              ]);

              $division->name=$r->name;
              $division->short_description=$r->short_description;
              $division->description=$r->description;
              $division->seo_title=$r->seo_title;
              $division->short_description=$r->short_description;
              $division->seo_keyword=$r->seo_keyword;

                if($r->hasFile('image')){
                  $file =$r->image;
                  $src  =$division->id;
                  $srcType  =11;
                  $fileUse  =1;
                  $author=Auth::id();
                  uploadFile($file,$src,$srcType,$fileUse,$author);
                }


                if($r->hasFile('banner')){

                  $file =$r->banner;
                  $src  =$division->id;
                  $srcType  =11;
                  $fileUse  =2;
                  $author=Auth::id();
                  uploadFile($file,$src,$srcType,$fileUse,$author);

                }

                 $slug =Str::slug($r->name);
                 if($slug==null){
                  $division->slug=$division->id;
                 }else{
                  if(Attribute::where('type',11)->where('slug',$slug)->whereNotIn('id',[$division->id])->count() >0){
                  $division->slug=$slug.'-'.$division->id;
                  }else{
                  $division->slug=$slug;
                  }
                }
                $division->status =$r->status?'active':'inactive';
                $division->fetured =$r->fetured?1:0;
                $division->editedby_id =Auth::id();
                $division->save();

                Session()->flash('success','Your Are Successfully Done');
                return redirect()->back();

          }


          if($action=='delete'){
              $medias =Media::latest()->where('src_type',11)->where('src_id',$division->id)->get();
                foreach($medias as $media){
                  if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                  }
                  $media->delete();
                }

                $division->delete();

                Session()->flash('success','Your Are Successfully Done');
                return redirect()->route('admin.admin.divisions');
          }

          return redirect()->back();

    }
    // end divisions

    // grades
    public function grades(Request $r){

          if($r->action){
            if($r->checkid){

            $datas=Attribute::latest()->where('type',12)->whereIn('id',$r->checkid)->get();

            foreach($datas as $data){

                if($r->action==1){
                  $data->status='active';
                  $data->save();
                }elseif($r->action==2){
                  $data->status='inactive';
                  $data->save();
                }elseif($r->action==5){

                  $medias =Media::latest()->where('src_type',12)->where('src_id',$data->id)->get();
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


          $grades=Attribute::latest()->where('type',12)->where('status','<>','temp')
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


          $totals = DB::table('attributes')
          ->where('type',12)
          ->selectRaw('count(*) as total')
          ->selectRaw("count(case when status = 'active' then 1 end) as active")
          ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
          ->first();

          return view(adminTheme().'payroll.grades.gradesAll',compact('grades','totals'));

    }

    public function gradesAction(Request $r,$action,$id=null){

          if($action=='create'){
            $check = $r->validate([
                'name' => 'required|max:100',
                'json' => 'nullable',
            ]);

            $grade =Attribute::where('type',12)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$grade){
              $grade =new Attribute();
            }

            $grade->name=$r->name;
            $grade->description=json_encode($r->json);
            $grade->type =12;
            $grade->status ='active';
            $grade->addedby_id =Auth::id();
            $grade->save();

             $slug =Str::slug($r->name);
             if($slug==null){
              $grade->slug=$grade->id;
             }else{
              if(Attribute::where('type',12)->where('slug',$slug)->whereNotIn('id',[$grade->id])->count() >0){
              $grade->slug=$slug.'-'.$grade->id;
              }else{
              $grade->slug=$slug;
              }
            }
            $grade->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

          }


          $grade =Attribute::where('type',12)->find($id);
          if(!$grade){
            Session()->flash('error','This Grade Are Not Found');
            return redirect()->route('admin.admin.grades');
          }

          $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
          if($allPer && $grade->addedby_id!=Auth::id()){
            Session()->flash('error','You are unauthorized Try!!');
            return redirect()->route('admin.admin.grades');
          }


          if($action=='update'){

              $check = $r->validate([
                  'name' => 'required|max:191',
              ]);

              $grade->name=$r->name;
              $grade->description=json_encode($r->json);

              $slug =Str::slug($r->name);
              if($slug==null){
                $grade->slug=$grade->id;
              }else{
                if(Attribute::where('type',12)->where('slug',$slug)->whereNotIn('id',[$grade->id])->count() >0){
                $grade->slug=$slug.'-'.$grade->id;
                }else{
                $grade->slug=$slug;
                }
              }

              $grade->status =$r->status?'active':'inactive';
              $grade->editedby_id =Auth::id();
              $grade->save();

              Session()->flash('success','Your Are Successfully Done');
              return redirect()->back();

          }


          if($action=='delete'){
              $medias =Media::latest()->where('src_type',12)->where('src_id',$grade->id)->get();
                foreach($medias as $media){
                  if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                  }
                  $media->delete();
                }

                $grade->delete();

                Session()->flash('success','Your Are Successfully Done');
                return redirect()->route('admin.admin.grades');
          }

          return redirect()->back();

    }
    // end grades


    // sections
    public function sections(Request $r){

          if($r->action){
            if($r->checkid){

            $datas=Attribute::latest()->where('type',14)->whereIn('id',$r->checkid)->get();

            foreach($datas as $data){

                if($r->action==1){
                  $data->status='active';
                  $data->save();
                }elseif($r->action==2){
                  $data->status='inactive';
                  $data->save();
                }elseif($r->action==5){

                  $medias =Media::latest()->where('src_type',14)->where('src_id',$data->id)->get();
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


          $sections=Attribute::latest()->where('type',14)->where('status','<>','temp')
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

          $totals = DB::table('attributes')
          ->where('type',14)
          ->selectRaw('count(*) as total')
          ->selectRaw("count(case when status = 'active' then 1 end) as active")
          ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
          ->first();

          return view(adminTheme().'payroll.sections.sectionsAll',compact('sections','totals'));

    }

    public function sectionsAction(Request $r,$action,$id=null){

          if($action=='create'){
            $check = $r->validate([
                'name' => 'required|max:100',
                'description' => 'nullable|max:1000',
            ]);

            $section =Attribute::where('type',14)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$section){
              $section =new Attribute();
            }

            $section->name=$r->name;
            $section->description=$r->description;
            $section->type =14;
            $section->status ='active';
            $section->addedby_id =Auth::id();
            $section->save();

             $slug =Str::slug($r->name);
             if($slug==null){
              $section->slug=$section->id;
             }else{
              if(Attribute::where('type',14)->where('slug',$slug)->whereNotIn('id',[$section->id])->count() >0){
              $section->slug=$slug.'-'.$section->id;
              }else{
              $section->slug=$slug;
              }
            }
            $section->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

          }

          $section =Attribute::where('type',14)->find($id);
          if(!$section){
            Session()->flash('error','Not Found');
            return redirect()->route('admin.sections');
          }

          $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
          if($allPer && $section->addedby_id!=Auth::id()){
            Session()->flash('error','Unauthorized');
            return redirect()->route('admin.sections');
          }

          if($action=='update'){

              $check = $r->validate([
                  'name' => 'required|max:191',
                  'seo_title' => 'nullable|max:200',
                  'seo_desc' => 'nullable|max:250',
                  'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                  'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
              ]);

              $section->name=$r->name;
              $section->short_description=$r->short_description;
              $section->description=$r->description;
              $section->seo_title=$r->seo_title;
              $section->short_description=$r->short_description;
              $section->seo_keyword=$r->seo_keyword;


                if($r->hasFile('image')){
                  uploadFile($file,$section->id,14,1,Auth::id());
                }

                if($r->hasFile('banner')){
                  uploadFile($file,$section->id,14,2,Auth::id());
                }


                 $slug =Str::slug($r->name);
                 if($slug==null){
                  $section->slug=$section->id;
                 }else{
                  if(Attribute::where('type',14)->where('slug',$slug)->whereNotIn('id',[$section->id])->count() >0){
                  $section->slug=$slug.'-'.$section->id;
                  }else{
                  $section->slug=$slug;
                  }
                }

                $section->status =$r->status?'active':'inactive';
                $section->fetured =$r->fetured?1:0;
                $section->editedby_id =Auth::id();
                $section->save();

                Session()->flash('success','Your Are Successfully Done');
                return redirect()->back();

          }

          if($action=='delete'){
              $medias =Media::latest()->where('src_type',14)->where('src_id',$section->id)->get();
                foreach($medias as $media){
                  if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                  }
                  $media->delete();
                }

                $section->delete();

                Session()->flash('success','Your Are Successfully Done');
                return redirect()->route('admin.sections');
          }

          return redirect()->back();
    }
    // end sections

    // shifts
    public function shifts(Request $r)
    {
        // Bulk Actions
        if ($r->action && $r->checkid) {
            $shifts = Shift::whereIn('id', $r->checkid)->get();

            foreach ($shifts as $shift) {
                switch ($r->action) {
                    case 1: // Activate
                        $shift->status = 'active';
                        $shift->save();
                        break;
                    case 2: // Deactivate
                        $shift->status = 'inactive';
                        $shift->save();
                        break;
                    case 5: // Delete
                        // Delete associated media
                        $medias = Media::where('src_type', 15)->where('src_id', $shift->id)->get();
                        foreach ($medias as $media) {
                            if (File::exists($media->file_url)) {
                                File::delete($media->file_url);
                            }
                            $media->delete();
                        }
                        $shift->delete();
                        break;
                }
            }

            Session::flash('success', 'Action Successfully Completed!');
            return redirect()->back();
        } elseif ($r->action) {
            Session::flash('info', 'Please select at least one shift.');
            return redirect()->back();
        }

        // Filters
        $shifts = Shift::latest()
            ->when($r->search, function($q) use ($r) {
                $q->where('name_of_shift', 'LIKE', '%' . $r->search . '%');
            })
            ->when($r->status, function($q) use ($r) {
                $q->where('status', $r->status);
            })
            ->paginate(25)
            ->appends($r->only(['search','status']));

        // Totals
        $totals = Shift::selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 'active' then 1 end) as active")
            ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
            ->first();

        return view(adminTheme().'payroll.shifts.shiftsAll', compact('shifts', 'totals'));
    }


    // Function to handle the form creation and updating logic
    public function shiftsAction(Request $r, $action, $id = null)
    {
        try {
            // Show form
            if ($action == 'form') {
                $shift = $id ? Shift::find($id) : null;
                if ($id && !$shift) {
                    Session::flash('error', 'Shift not found.');
                    return redirect()->route('admin.shifts');
                }
                return view(adminTheme().'payroll.shifts.create_edit', compact('shift'));
            }

            // Validation
            $validatedData = $r->validate([
                'name_of_shift' => 'required|string|max:100',
                'name_of_shift_bn' => 'nullable|string|max:100',
                'shift_starting_time' => 'required',
                'red_marking_on' => 'required',
                'shift_closing_time' => 'required',
                'shift_closing_time_next_day' => 'nullable|boolean',
                'over_time_allowed_up_to' => 'required',
                'over_time_allowed_up_to_next_day' => 'nullable|boolean',
                'over_time_1_allowed_up_to' => 'required',
                'over_time_1_allowed_up_to_next_day' => 'nullable|boolean',
                'card_accept_from' => 'required',
                'card_accept_to' => 'required',
                'card_accept_to_next_day' => 'nullable|boolean',
                'meal_option' => 'nullable|string',
                'tiffin_allowance' => 'nullable|numeric',
                'no_lunch_hour_holiday' => 'nullable|boolean',
                'dinner_allowance' => 'nullable|boolean',
                'dinner_count_option' => 'nullable|string',
                'double_shift' => 'nullable|boolean',
                'weekly_overtime_allowed' => 'nullable',
                'weekly_ot_sat' => 'nullable',
                'weekly_ot_sun' => 'nullable',
                'weekly_ot_mon' => 'nullable',
                'weekly_ot_tue' => 'nullable',
                'weekly_ot_wed' => 'nullable',
                'weekly_ot_thu' => 'nullable',
            ]);

            // Fix boolean fields properly
            $booleanFields = [
                'shift_closing_time_next_day',
                'over_time_allowed_up_to_next_day',
                'over_time_1_allowed_up_to_next_day',
                'card_accept_to_next_day',
                'no_lunch_hour_holiday',
                'dinner_allowance',
                'double_shift',
            ];

            foreach ($booleanFields as $field) {
                $validatedData[$field] = $r->boolean($field);
            }

            if ($action == 'store') {
                $shift = new Shift($validatedData);
                $shift->addedby_id = Auth::id();
                $shift->status = 'active';
                $shift->save();

                Session::flash('success', 'Shift created successfully!');
                return redirect()->route('admin.shifts');
            }

            if ($action == 'update') {
                $shift = Shift::find($id);
                if (!$shift) {
                    Session::flash('error', 'Shift not found.');
                    return redirect()->route('admin.shifts');
                }

                // Optional: Permission check
                $userPerm = json_decode(Auth::user()->permission->permission ?? '{}', true);
                $allPer = empty($userPerm['brands']['all']);
                if ($allPer && $shift->addedby_id != Auth::id()) {
                    Session::flash('error', 'Unauthorized');
                    return redirect()->route('admin.shifts');
                }

                $shift->update($validatedData);
                $shift->editedby_id = Auth::id();
                $shift->save();

                Session::flash('success', 'Shift updated successfully!');
                return redirect()->route('admin.shifts');
            }

            if ($action == 'delete') {
                $shift = Shift::find($id);
                if (!$shift) {
                    Session::flash('error', 'Shift not found.');
                    return redirect()->route('admin.shifts');
                }

                // Delete related media
                $medias = Media::where('src_type', 15)->where('src_id', $shift->id)->get();
                foreach ($medias as $media) {
                    if (File::exists($media->file_url)) {
                        File::delete($media->file_url);
                    }
                    $media->delete();
                }

                $shift->delete();
                Session::flash('success', 'Shift deleted successfully.');
                return redirect()->route('admin.shifts');
            }

            return redirect()->back();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laravel validation exception
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log the error and show friendly message
            \Log::error('Shift Action Error: '.$e->getMessage(), [
                'action' => $action,
                'id' => $id,
                'user_id' => Auth::id(),
                'request' => $r->all(),
            ]);

            Session::flash('error', 'Something went wrong! Please try again.');
            return redirect()->back()->withInput();
        }
    }

}
