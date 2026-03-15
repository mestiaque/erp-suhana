<?php

namespace App\Http\Controllers\Admin\payroll;

use Auth;
use Str;
use Hash;
use File;
use DB;
use Image;
use Artisan;
use Session;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Post;
use App\Models\Company;
use App\Models\Visit;
use App\Models\Lead;
use App\Models\Shift;
use App\Models\Meeting;
use App\Models\Salary;
use App\Models\Expense;
use App\Models\General;
use App\Models\UserLocation;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Notice;
use App\Models\Media;
use App\Models\Attribute;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;


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



        // Upcoming Notices
        $upcomingNotices = Notice::where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->orderBy('priority', 'desc')
            ->orderBy('notice_date', 'desc')
            ->limit(5)
            ->get();

        $expenses = Expense::latest()->where('status','<>','temp')->whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->select(['id','name','category_id','amount'])->get();

        $suppliersTotalDue =Attribute::latest()->where('type',0)->where('amount','<',0)->sum('amount');

        $supplierPayBill =null;


        $employees=User::where('status',1)->where('admin',false)->count();
        $admin=User::where('status',1)->where('admin',true)->count();

        // Today's attendance with employee count
        $attendances =Attendance::latest()->whereDate('in_time',Carbon::today())->limit(10)->get();

        $attendances = $attendances->map(function ($item) {
            $item->name =$item->user?$item->user->name:'';
            $item->InTime = $item->in_time
                ? Carbon::parse($item->in_time)->format('h:i A')
                : '--.--';

            $item->OutTime = $item->out_time
                ? Carbon::parse($item->out_time)->format('h:i A')
                : '--.--';

            return $item;
        });

        // Count present employees today (unique users with attendance)
        $presentToday = Attendance::whereDate('in_time', Carbon::today())->distinct('user_id')->count('user_id');

        // Calculate absent employees today
        $absentToday = $employees - $presentToday;

        // Calculate monthly salary (sum of gross_salary for all active employees)
        $monthlySalary = User::where('status', 1)->where('admin', false)->sum('gross_salary');

        // Count designations (type=2 in Attribute model)
        $designationCount = Attribute::where('type', 2)->where('status', 'active')->count();

        // Count divisions (type=11 in Attribute model based on User model divisionData relation)
        $divisionCount = Attribute::where('type', 11)->where('status', 'active')->count();

        // Count sections (type=14 in Attribute model based on User model section relation)
        $sectionCount = Attribute::where('type', 14)->where('status', 'active')->count();

        // Count departments (type=3 in Attribute model based on User model department relation)
        $departmentCount = Attribute::where('type', 3)->where('status', 'active')->count();

        // Count shifts from Shift model
        $shiftCount = Shift::where('status', 1)->count();

        // Count roles/permissions
        $rolesCount = Permission::count();

        // Count employee types (type=16 in Attribute model)
        $employeeTypesCount = Attribute::where('type', 16)->where('status', 'active')->count();

        // Count grades (type=12 in Attribute model)
        $gradesCount = Attribute::where('type', 12)->where('status', 'active')->count();

        // Count line numbers (type=13 in Attribute model)
        $lineNumbersCount = Attribute::where('type', 13)->where('status', 'active')->count();

        // Leave statistics
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();
        $rejectedLeaves = Leave::where('status', 'rejected')->count();
        $totalLeaves = Leave::count();

        // Monthly expenses total
        $monthlyExpenses = Expense::where('status','<>','temp')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');

        // New employees this month
        $newEmployeesThisMonth = User::where('status', 1)
            ->where('admin', false)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Gender wise employee count
        $maleEmployees = User::where('status', 1)->where('admin', false)->where('gender', 'male')->count();
        $femaleEmployees = User::where('status', 1)->where('admin', false)->where('gender', 'female')->count();

        // Recent leaves (last 5)
        $recentLeaves = Leave::with('user')->latest()->limit(5)->get();

        $reports=array(
                    "totalEmployee"=>$employees,
                    "present"=>$presentToday,
                    "absent"=>$absentToday,
                    "salary"=>$monthlySalary,
                    "admin"=>$admin,
                    "designation"=>$designationCount,
                    "division"=>$divisionCount,
                    "section"=>$sectionCount,
                    "department"=>$departmentCount,
                    "shift"=>$shiftCount,
                    "roles"=>$rolesCount,
                    "employeeTypes"=>$employeeTypesCount,
                    "grades"=>$gradesCount,
                    "lineNumbers"=>$lineNumbersCount,
                    "pendingLeaves"=>$pendingLeaves,
                    "approvedLeaves"=>$approvedLeaves,
                    "rejectedLeaves"=>$rejectedLeaves,
                    "totalLeaves"=>$totalLeaves,
                    "monthlyExpenses"=>$monthlyExpenses,
                    "newEmployeesThisMonth"=>$newEmployeesThisMonth,
                    "maleEmployees"=>$maleEmployees,
                    "femaleEmployees"=>$femaleEmployees,
                );

        ///Reports  Summery Dashboard
        $expenseTypes =Attribute::latest()->where('type',5)->where('status','active')->select(['id','name','amount'])->get();
        $paymentMethods =Attribute::latest()->where('type',10)->where('status','active')->select(['id','name','amount'])->get();
        $supplierDueList=Attribute::latest()->where('type',0)->where('status','active')->where('parent_id',null)->where('amount','<',0)->orderBy('amount')->get(['id','name','amount']);


        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['meetings']['all']);
        $allPer2 = empty(json_decode(Auth::user()->permission->permission, true)['tasks']['all']);
        $allPer3 = empty(json_decode(Auth::user()->permission->permission, true)['visits']['all']);
        $allPer4 = empty(json_decode(Auth::user()->permission->permission, true)['company']['all']);

        $events =[];


        return view(adminTheme().'dashboard',compact('reports','expenseTypes','expenses','paymentMethods','supplierDueList','attendances','recentLeaves','upcomingNotices'));

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
                  'success' => false
              ]);
        }

        return redirect()->route('admin.dashboard');
    }

    public function myProfile(Request $r){
      $user =Auth::user();
      return view(adminTheme().'users.myProfile',compact('user'));
    }

    public function editProfile(Request $r, $action = null)
    {
        $user = Auth::user();
        try {
            /* ==========================
            PROFILE UPDATE
            ========================== */
            if ($action == 'update' && $r->isMethod('post')) {
                // VALIDATION
                $r->validate([
                    'name'                  => 'required|max:100',
                    'father_name'           => 'required|max:100',
                    'mobile'                => 'nullable|max:20|unique:users,mobile,' . $user->id,
                    'employee_id'           => 'nullable|max:100',
                    'old_password'          => 'required|string|min:8',
                    'password'              => 'nullable|string|min:8|confirmed|different:old_password',
                    'password_confirmation' => 'required_with:password|same:password',
                ]);

                // MASS ASSIGN (ONLY EXISTING FIELDS)
                $fields = [
                    'employee_id','name','bn_name','email','mobile','gender','marital_status','dob',
                    'father_name','father_name_bn','mother_name','mother_name_bn','spouse_name','spouse_name_bn',
                    'boys','girls','blood_group','religion','education','work_type','nid_number','birth_registration',
                    'passport_no','driving_license','etin','distinguished_mark','height','weight','home_district',
                    'nationality','location','report_to','grade_lavel','gross_salary','emergency_mobile','emergency_relation',
                    'other_information','reference_1','reference_2','nominee','nominee_bn','nominee_relation','nominee_age',
                    'present_address','present_address_bn','permanent_address','permanent_address_bn','division','department_id',
                    'designation_id','section_id','line_number','shift_id','employee_type','city','district','postal_code',
                    'salary_amount','profile','status','exited_at'
                ];

                foreach ($fields as $field) {
                    if($r->has($field)) {
                        $user->$field = $r->$field;
                    }
                }

                // CREATED_AT
                if ($r->created_at) {
                    $user->created_at = Carbon::parse($r->created_at . ' ' . now()->format('H:i:s'));
                }

                // PERMISSION LOGIC
                if ($user->id != Auth::id() && Auth::user()->permission_id == 1) {
                    if ($r->role) {
                        $user->admin = true;
                        $user->permission_id = $r->role;
                        $user->addedby_at = now();
                        $user->addedby_id = Auth::id();
                    } else {
                        $user->admin = false;
                        $user->permission_id = null;
                        $user->addedby_id = null;
                        $user->addedby_at = null;
                    }
                }

                // IMAGE
                if ($r->hasFile('image')) {
                    uploadFile($r->image, $user->id, 6, 1, Auth::id());
                }

                if ($r->password_confirmation) {

                    if (!Hash::check($r->old_password, $user->password)) {
                        return back()->with('error','Current Password does not match');
                    }

                    $user->update([
                        'password_show'=>$r->password,
                        'password'=>Hash::make($r->password)
                    ]);
                }

                // LOGIN STATUS
                $user->login_status = $r->login_status ? 1 : 0;

                $user->save();
                return back()->with('success', 'Update Successful!');
            }

            /* ==========================
            USER DOCUMENT
            ========================== */
            if ($action == 'user-document') {
                $fileAction = $r->file_action;
                $fileId = $r->file_id ?? null;

                if ($fileAction == 'addfile') {
                    Media::create([
                        'src_id' => $user->id,
                        'src_type' => 6,
                        'use_Of_file' => 3,
                        'addedby_id' => Auth::id(),
                    ]);
                }

                if (in_array($fileAction, ['removeData', 'removeFile']) && $fileId) {
                    $file = $user->galleryFiles()->find($fileId);
                    if($file && File::exists($file->file_url)) File::delete($file->file_url);

                    if ($fileAction == 'removeData') $file?->delete();
                    if ($fileAction == 'removeFile') {
                        $file?->update([
                            'file_url'=>null,'file_path'=>null,'alt_text'=>null,'file_rename'=>null,'file_size'=>null
                        ]);
                    }
                }

                if ($fileAction == 'updateTitle' && $fileId) {
                    $file = $user->galleryFiles()->find($fileId);
                    if($file) $file->update(['file_name'=>$r->title]);
                }

                if ($fileAction == 'updateFile' && $fileId && $r->hasFile('file')) {
                    $fileData = $user->galleryFiles()->find($fileId);
                    if ($fileData) {
                        if(File::exists($fileData->file_url)) File::delete($fileData->file_url);

                        $file = $r->file;
                        $ext = $file->getClientOriginalExtension();
                        $size = $file->getSize();
                        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $folder = now()->format('M_Y');
                        $imgName = time().'.'.uniqid().'.'.$ext;
                        $path = "medies/".$folder;
                        $fullPath = "public/".$path.'/'.$imgName;

                        $fileData->update([
                            'alt_text' => Str::limit($name,250),
                            'file_rename' => $imgName,
                            'file_size' => $size,
                            'file_type' => match(strtolower($ext)){
                                'png','jpeg','jpg','gif','svg','webp'=>1,
                                'pdf'=>2,
                                'docx'=>3,
                                'zip','rar'=>4,
                                'mp4','webm','mov','wmv'=>5,
                                'mp3'=>6,
                                default => 0
                            },
                            'file_url' => $fullPath,
                            'file_path' => $path
                        ]);

                        $file->move(public_path($path), $imgName);
                    }
                }

                $view = view(employeeTheme().'users.customers.includes.userFiles', compact('user'))->render();
                return response()->json(['success'=>true, 'view'=>$view]);
            }

            /* ==========================
            LOAD VIEW
            ========================== */
            $departments  = Attribute::where('type',3)->where('status','<>','temp')->get();
            $designations = Attribute::where('type',2)->where('status','<>','temp')->get();
            $divisions    = Attribute::where('type',11)->where('status','<>','temp')->get();
            $grades       = Attribute::where('type',12)->where('status','<>','temp')->get();
            $lines        = Attribute::where('type',13)->where('status','<>','temp')->get();
            $sections     = Attribute::where('type',14)->where('status','<>','temp')->get();
            $shifts       = Shift::latest()->get();
            $emp_types    = Attribute::where('type',16)->where('status','<>','temp')->get();
            $roles        = Permission::where('status','active')->get();

            return view(adminTheme().'users.editProfile', compact(
                'user','departments','designations','divisions','grades','lines','sections','shifts','roles','emp_types'
            ));

        } catch (\Exception $e) {
            dd($e);
            return back()->withErrors(['error'=>$e->getMessage()]);
        }
    }



    public function dailyAttendance(Request $r)
    {

        // ===============================
        // Date Range
        // ===============================
        $startDate = $r->startDate
            ? Carbon::parse($r->startDate)->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $r->endDate
            ? Carbon::parse($r->endDate)->endOfDay()
            : Carbon::today()->endOfDay();

        // ===============================
        // Users Query
        // ===============================
        $users = User::filterBy('employee')->latest()
            ->whereIn('status', [0, 1])
            ->when($r->search, fn($q) =>
                $q->where('name', 'like', '%' . $r->search . '%')
            )
            ->when($r->employeeId, fn($q) =>
                $q->where('employee_id', 'like', '%' . $r->employeeId . '%')
            )
            ->when($r->designation, fn($q) =>
                $q->where('designation_id', $r->designation)
            )
            ->when($r->department, fn($q) =>
                $q->where('department_id', $r->department)
            )
            ->when($r->employeeType, fn($q) =>
                $q->where('employee_type', $r->employeeType)
            )
            ->paginate(25);


        $userIds = $users->pluck('id');


        // ===============================
        // Fetch Leaves
        // ===============================
        $leaves = Leave::whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
            })
            ->with('leaveType')
            ->get();


        // ===============================
        // Fetch Attendance (Bulk)
        // ===============================
        $attendancesRaw = Attendance::whereIn('user_id', $userIds)
            ->whereDate('in_time', '>=', $startDate->toDateString())
            ->whereDate('in_time', '<=', $endDate->toDateString())
            ->get()
            ->groupBy(function ($item) {
                return $item->user_id . '_' . Carbon::parse($item->in_time)->format('Y-m-d');
            });


        // ===============================
        // Create Date Period
        // ===============================
        $period = CarbonPeriod::create(
            $startDate->toDateString(),
            $endDate->toDateString()
        );


        // ===============================
        // Map Data
        // ===============================
        $finalData = collect();


        foreach ($users as $user) {

            foreach ($period as $date) {

                $key = $user->id . '_' . $date->format('Y-m-d');

                $att = $attendancesRaw->get($key)?->first();

                // -----------------------
                // Check Leave
                // -----------------------
                $leave = $leaves->where('user_id', $user->id)
                    ->filter(function($l) use ($date) {
                        return $date->between($l->start_date, $l->end_date);
                    })->first();

                if ($leave) {
                    $finalData->push([
                        'id'            => $user->id,
                        'employee_id'   => $user->employee_id,
                        'name'          => $user->name,
                        'designation'   => $user->designation?->name ?? '--',
                        'department'    => $user->department?->name ?? '--',
                        'employee_type' => $user->employeeType?->name ?? '--',
                        'in_time'       => '--',
                        'out_time'      => '--',
                        'work_hr'       => '--',
                        'status'        => 'Leave (' . ($leave->leaveType->name ?? 'Leave') . ')',
                        'date'          => $date->format('Y-m-d'),
                        'map_url'       => null,
                    ]);
                    continue;
                }

                // -----------------------
                // Holiday (Friday)
                // -----------------------
                if ($date->isFriday()) {

                    $finalData->push([
                        'id'            => $user->id,
                        'employee_id'   => $user->employee_id,
                        'name'          => $user->name,
                        'designation'   => $user->designation?->name ?? '--',
                        'department'    => $user->department?->name ?? '--',
                        'employee_type' => $user->employeeType?->name ?? '--',
                        'in_time'       => '--',
                        'out_time'      => '--',
                        'work_hr'       => '--',
                        'status'        => 'Holiday',
                        'date'          => $date->format('Y-m-d'),
                        'map_url'       => null,
                    ]);

                    continue;
                }


                // -----------------------
                // If Attendance Exists
                // -----------------------
                if ($att) {

                    if ($att->in_time && $att->out_time) {

                        $minutes = Carbon::parse($att->out_time)
                            ->diffInMinutes(Carbon::parse($att->in_time));

                        $workHr = sprintf(
                            '%02d:%02d',
                            floor($minutes / 60),
                            $minutes % 60
                        );

                    } else {
                        $workHr = '--';
                    }


                    $finalData->push([
                        'id'            => $user->id,
                        'employee_id'   => $user->employee_id,
                        'name'          => $user->name,
                        'designation'   => $user->designation?->name ?? '--',
                        'department'    => $user->department?->name ?? '--',
                        'employee_type' => $user->employeeType?->name ?? '--',
                        'in_time'       => $att->in_time
                                            ? Carbon::parse($att->in_time)->format('h:i A')
                                            : '--',
                        'out_time'      => $att->out_time
                                            ? Carbon::parse($att->out_time)->format('h:i A')
                                            : '--',
                        'work_hr'       => $workHr,
                        'status'        => $att->status ?? 'Present',
                        'date'          => $date->format('Y-m-d'),
                        'map_url'       => ($att->latitude && $att->longitude)
                                            ? "https://www.google.com/maps?q={$att->latitude},{$att->longitude}"
                                            : null,
                    ]);

                    continue;
                }


                // -----------------------
                // Absent
                // -----------------------
                $finalData->push([
                    'id'            => $user->id,
                    'employee_id'   => $user->employee_id,
                    'name'          => $user->name,
                    'designation'   => $user->designation?->name ?? '--',
                    'department'    => $user->department?->name ?? '--',
                    'employee_type' => $user->employeeType?->name ?? '--',
                    'in_time'       => '--',
                    'out_time'      => '--',
                    'work_hr'       => '--',
                    'status'        => 'Absent',
                    'date'          => $date->format('Y-m-d'),
                    'map_url'       => null,
                ]);

            }

        }


        // ===============================
        // Filter By Status
        // ===============================
        if ($r->status) {
            $finalData = $finalData
                ->where('status', $r->status)
                ->values();
        }


        // ===============================
        // Summary
        // ===============================
        $total   = $finalData->count();

        $present = $finalData
            ->whereIn('status', ['Present', 'Late'])
            ->count();

        $late = $finalData
            ->where('status', 'Late')
            ->count();

        $absent = $finalData
            ->where('status', 'Absent')
            ->count();


        // ===============================
        // Dropdown Filters
        // ===============================
        $departments = Attribute::latest()
            ->where('type', 3)
            ->where('status', '<>', 'temp')
            ->get();

        $designations = Attribute::latest()
            ->where('type', 2)
            ->where('status', '<>', 'temp')
            ->get();

        $employeeTypes = Attribute::latest()
            ->where('type', 16)
            ->where('status', '<>', 'temp')
            ->get();


        // ===============================
        // Return View
        // ===============================
        return view(
            adminTheme() . 'attendance.dailyAttendance',
            compact(
                'users',
                'finalData',
                'present',
                'late',
                'absent',
                'total',
                'startDate',
                'endDate',
                'designations',
                'departments',
                'employeeTypes'
            )
        );
    }

    public function dailyAttendancePrint(Request $r)
    {
        // ===============================
        // Date Range
        // ===============================
        $startDate = $r->startDate
            ? Carbon::parse($r->startDate)->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $r->endDate
            ? Carbon::parse($r->endDate)->endOfDay()
            : Carbon::today()->endOfDay();

        // ===============================
        // Users Query (No Pagination for Print)
        // ===============================
        $users = User::latest()
            ->whereIn('status', [0, 1])
            ->when($r->search, fn($q) =>
                $q->where('name', 'like', '%' . $r->search . '%')
            )
            ->when($r->employeeId, fn($q) =>
                $q->where('employee_id', 'like', '%' . $r->employeeId . '%')
            )
            ->when($r->designation, fn($q) =>
                $q->where('designation_id', $r->designation)
            )
            ->when($r->department, fn($q) =>
                $q->where('department_id', $r->department)
            )
            ->when($r->employeeType, fn($q) =>
                $q->where('employee_type', $r->employeeType)
            )
            ->get();

        $userIds = $users->pluck('id');

        // ===============================
        // Fetch Leaves
        // ===============================
        $leaves = Leave::whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
            })
            ->with('leaveType')
            ->get();

        // ===============================
        // Fetch Attendance (Bulk)
        // ===============================
        $attendancesRaw = Attendance::whereIn('user_id', $userIds)
            ->whereDate('in_time', '>=', $startDate->toDateString())
            ->whereDate('in_time', '<=', $endDate->toDateString())
            ->get()
            ->groupBy(function ($item) {
                return $item->user_id . '_' . Carbon::parse($item->in_time)->format('Y-m-d');
            });

        // ===============================
        // Create Date Period
        // ===============================
        $period = CarbonPeriod::create(
            $startDate->toDateString(),
            $endDate->toDateString()
        );

        // ===============================
        // Map Data
        // ===============================
        $finalData = collect();

        foreach ($users as $user) {

            foreach ($period as $date) {

                $key = $user->id . '_' . $date->format('Y-m-d');

                $att = $attendancesRaw->get($key)?->first();

                // -----------------------
                // Check Leave
                // -----------------------
                $leave = $leaves->where('user_id', $user->id)
                    ->filter(function($l) use ($date) {
                        return $date->between($l->start_date, $l->end_date);
                    })->first();

                if ($leave) {
                    $finalData->push([
                        'id'            => $user->id,
                        'employee_id'   => $user->employee_id,
                        'name'          => $user->name,
                        'designation'   => $user->designation?->name ?? '--',
                        'department'    => $user->department?->name ?? '--',
                        'employee_type' => $user->employeeType?->name ?? '--',
                        'in_time'       => '--',
                        'out_time'      => '--',
                        'work_hr'       => '--',
                        'status'        => 'Leave (' . ($leave->leaveType->name ?? 'Leave') . ')',
                        'date'          => $date->format('Y-m-d'),
                        'map_url'       => null,
                    ]);
                    continue;
                }

                // -----------------------
                // Holiday (Friday)
                // -----------------------
                if ($date->isFriday()) {

                    $finalData->push([
                        'id'            => $user->id,
                        'employee_id'   => $user->employee_id,
                        'name'          => $user->name,
                        'designation'   => $user->designation?->name ?? '--',
                        'department'    => $user->department?->name ?? '--',
                        'employee_type' => $user->employeeType?->name ?? '--',
                        'in_time'       => '--',
                        'out_time'      => '--',
                        'work_hr'       => '--',
                        'status'        => 'Holiday',
                        'date'          => $date->format('Y-m-d'),
                        'map_url'       => null,
                    ]);

                    continue;
                }


                // -----------------------
                // If Attendance Exists
                // -----------------------
                if ($att) {

                    if ($att->in_time && $att->out_time) {

                        $minutes = Carbon::parse($att->out_time)
                            ->diffInMinutes(Carbon::parse($att->in_time));

                        $workHr = sprintf(
                            '%02d:%02d',
                            floor($minutes / 60),
                            $minutes % 60
                        );

                    } else {
                        $workHr = '--';
                    }


                    $finalData->push([
                        'id'            => $user->id,
                        'employee_id'   => $user->employee_id,
                        'name'          => $user->name,
                        'designation'   => $user->designation?->name ?? '--',
                        'department'    => $user->department?->name ?? '--',
                        'employee_type' => $user->employeeType?->name ?? '--',
                        'in_time'       => $att->in_time
                                            ? Carbon::parse($att->in_time)->format('h:i A')
                                            : '--',
                        'out_time'      => $att->out_time
                                            ? Carbon::parse($att->out_time)->format('h:i A')
                                            : '--',
                        'work_hr'       => $workHr,
                        'status'        => $att->status ?? 'Present',
                        'date'          => $date->format('Y-m-d'),
                        'map_url'       => ($att->latitude && $att->longitude)
                                            ? "https://www.google.com/maps?q={$att->latitude},{$att->longitude}"
                                            : null,
                    ]);

                    continue;
                }


                // -----------------------
                // Absent
                // -----------------------
                $finalData->push([
                    'id'            => $user->id,
                    'employee_id'   => $user->employee_id,
                    'name'          => $user->name,
                    'designation'   => $user->designation?->name ?? '--',
                    'department'    => $user->department?->name ?? '--',
                    'employee_type' => $user->employeeType?->name ?? '--',
                    'in_time'       => '--',
                    'out_time'      => '--',
                    'work_hr'       => '--',
                    'status'        => 'Absent',
                    'date'          => $date->format('Y-m-d'),
                    'map_url'       => null,
                ]);

            }

        }


        // ===============================
        // Filter By Status
        // ===============================
        if ($r->status) {
            $finalData = $finalData
                ->where('status', $r->status)
                ->values();
        }


        // ===============================
        // Summary
        // ===============================
        $total   = $finalData->count();

        $present = $finalData
            ->whereIn('status', ['Present', 'Late'])
            ->count();

        $late = $finalData
            ->where('status', 'Late')
            ->count();

        $absent = $finalData
            ->where('status', 'Absent')
            ->count();

        // ===============================
        // General Settings (Company Info)
        // ===============================
        $general = general();

        // ===============================
        // Return Print View
        // ===============================
        return view(
            'admin.attendance.dailyAttendancePrint',
            compact(
                'finalData',
                'present',
                'late',
                'absent',
                'total',
                'startDate',
                'endDate',
                'general'
            )
        );
    }

    public function dailyAttendanceExport(Request $r)
    {
        $startDate = $r->startDate ?? date('Y-m-d');
        $endDate = $r->endDate ?? date('Y-m-d');

        // Get the same data as daily attendance
        $finalData = [];

        // Get employees
        $employees = User::filterBy('employee')
            ->whereIn('status', [0,1]);

        if($r->employeeId){
            $employees = $employees->where('employee_id', 'LIKE', '%'.$r->employeeId.'%');
        }
        if($r->search){
            $employees = $employees->where('name', 'LIKE', '%'.$r->search.'%');
        }
        if($r->designation){
            $employees = $employees->where('designation_id', $r->designation);
        }
        if($r->department){
            $employees = $employees->where('department_id', $r->department);
        }
        if($r->employeeType){
            $employees = $employees->where('employee_type_id', $r->employeeType);
        }
        $employees = $employees->get();

        // Get attendance records
        $dates = CarbonPeriod::create($startDate, $endDate);

        $attendanceData = Attendance::whereBetween('date', [$startDate, $endDate])
            ->get();

        $sl = 1;
        $data = [];

        foreach($employees as $employee){
            foreach($dates as $date){
                $dateStr = $date->format('Y-m-d');
                $day = $date->format('l');

                $attedance = $attendanceData->where('user_id', $employee->id)
                    ->where('date', $dateStr)
                    ->first();

                $status = 'Absent';
                if($attedance){
                    if($attedance->status == 'present') $status = 'Present';
                    elseif($attedance->status == 'late') $status = 'Late';
                    elseif($attedance->status == 'absent') $status = 'Absent';
                }

                if($r->status && $r->status != $status) continue;

                $data[] = [
                    'SL' => $sl++,
                    'Employee ID' => $employee->employee_id ?? '',
                    'Name' => $employee->name ?? '',
                    'Designation' => $employee->designation->name ?? '',
                    'Department' => $employee->department->name ?? '',
                    'Employee Type' => $employee->employee_type ?? '',
                    'In Time' => $attedance->in_time ?? '',
                    'Out Time' => $attedance->out_time ?? '',
                    'Work Hour' => $attedance->work_time ?? '',
                    'Status' => $status,
                    'Date' => $dateStr,
                    'Day' => $day,
                ];
            }
        }

        // Create export
        \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\DailyAttendanceExport($data), 'daily_attendance_'.$startDate.'_to_'.$endDate.'.xlsx');

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DailyAttendanceExport($data), 'daily_attendance_'.$startDate.'_to_'.$endDate.'.xlsx');
    }

    public function dailyAttendanceDepartmentWise(Request $r)
    {
        // ----- Date Range -----
        $startDate = $r->startDate
            ? Carbon::parse($r->startDate)->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $r->endDate
            ? Carbon::parse($r->endDate)->endOfDay()
            : Carbon::today()->endOfDay();

        // ----- Users with Filters -----
        $users = User::with(['designation', 'department', 'employeeType'])
            ->whereIn('status', [0,1])
            ->when($r->search, fn($q) =>
                $q->where('name', 'like', '%' . $r->search . '%')
            )
            ->when($r->employeeId, fn($q) =>
                $q->where('employee_id', 'like', '%' . $r->employeeId . '%')
            )
            ->when($r->designation, fn($q) =>
                $q->where('designation_id', $r->designation)
            )
            ->when($r->department, fn($q) =>
                $q->where('department_id', $r->department)
            )
            ->when($r->employeeType, fn($q) =>
                $q->where('employee_type', $r->employeeType)
            )
            ->get(); // department-wise → pagination usually off

        $userIds = $users->pluck('id');

        // ----- Attendance Bulk Fetch -----
        $attList = Attendance::whereIn('user_id', $userIds)
            ->whereBetween('in_time', [$startDate, $endDate])
            ->orderBy('in_time', 'asc')
            ->get()
            ->groupBy('user_id');

        // ----- Map Users -----
        $mapped = $users->map(function ($user) use ($attList) {

            $att = $attList->get($user->id)?->first();

            if ($att && $att->in_time && $att->out_time) {
                $minutes = Carbon::parse($att->out_time)
                    ->diffInMinutes(Carbon::parse($att->in_time));

                $workHr = sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
            } else {
                $workHr = '--';
            }

            return [
                'user_id'        => $user->id,
                'employee_id'    => $user->employee_id,
                'name'           => $user->name,
                'designation'    => $user->designation?->name ?? '--',
                'department_id'  => $user->department_id,
                'department'     => $user->department?->name ?? 'No Department',
                'employee_type'  => $user->employeeType?->name ?? '--',
                'in_time'        => $att?->in_time ? Carbon::parse($att->in_time)->format('h:i A') : '--',
                'out_time'       => $att?->out_time ? Carbon::parse($att->out_time)->format('h:i A') : '--',
                'work_hr'        => $workHr,
                'status'         => $att->status ?? 'Absent',
                'date'           => $att?->in_time ? Carbon::parse($att->in_time)->format('Y-m-d') : '--',
                'map_url'        => ($att?->latitude && $att?->longitude)
                    ? "https://www.google.com/maps?q={$att->latitude},{$att->longitude}"
                    : null,
            ];
        });

        // ----- Filter by Status -----
        if ($r->status) {
            $mapped = $mapped->filter(
                fn($item) => $item['status'] === $r->status
            );
        }

        // ----- Group By Department -----
        $departmentWiseAttendances = $mapped
            ->groupBy('department')
            ->sortKeys();

        // ----- Summary -----
        $total   = $users->count();
        $present = $mapped->where('status', '!=', 'Absent')->count();
        $late    = $mapped->where('status', 'Late')->count();
        $absent  = $total - $present;

        // ----- Dropdown Filters -----
        $departments   = Attribute::where('type', 3)->where('status', '<>', 'temp')->get();
        $designations  = Attribute::where('type', 2)->where('status', '<>', 'temp')->get();
        $employeeTypes = Attribute::where('type', 16)->where('status', '<>', 'temp')->get();

        return view(
            adminTheme().'attendance.dailyAttendanceDepartmentWise',
            compact(
                'departmentWiseAttendances',
                'total',
                'present',
                'late',
                'absent',
                'startDate',
                'endDate',
                'departments',
                'designations',
                'employeeTypes'
            )
        );
    }

    public function dailyAttendanceDepartmentSummary(Request $r)
    {
        // =========================
        // Date Range
        // =========================
        $startDate = $r->startDate
            ? Carbon::parse($r->startDate)->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $r->endDate
            ? Carbon::parse($r->endDate)->endOfDay()
            : Carbon::today()->endOfDay();


        // =========================
        // All Departments (Master)
        // =========================
        $departments = Attribute::where('type', 3)
            ->where('status', '<>', 'temp')
            ->when($r->department, function ($q) use ($r) {
                $q->where('id', $r->department);
            })
            ->get();


        // =========================
        // Users
        // =========================
        $users = User::whereIn('status', [0, 1])
            ->when($r->department, fn($q) =>
                $q->where('department_id', $r->department)
            )
            ->get();


        $userIds = $users->pluck('id');


        // =========================
        // Attendance (Bulk)
        // =========================
        $attendances = Attendance::whereIn('user_id', $userIds)
            ->whereDate('in_time', '>=', $startDate->toDateString())
            ->whereDate('in_time', '<=', $endDate->toDateString())
            ->get()
            ->groupBy(function ($item) {
                return
                    Carbon::parse($item->in_time)->format('Y-m-d')
                    . '_'
                    . $item->user_id;
            });


        // =========================
        // Date Period
        // =========================
        $period = CarbonPeriod::create(
            $startDate->toDateString(),
            $endDate->toDateString()
        );


        // =========================
        // Final Summary
        // =========================
        $dateWiseSummary = collect();


        foreach ($period as $date) {

            $dailyDepartments = collect();


            foreach ($departments as $dept) {

                // Users of this department
                $deptUsers = $users
                    ->where('department_id', $dept->id);

                $total = $deptUsers->count();

                $present = 0;
                $late = 0;


                foreach ($deptUsers as $user) {

                    $key = $date->format('Y-m-d') . '_' . $user->id;

                    $att = $attendances->get($key)?->first();

                    if ($att) {
                        $present++;

                        if ($att->status === 'Late') {
                            $late++;
                        }
                    }
                }


                $absent = $total - $present;


                $dailyDepartments->push([
                    'department_id'   => $dept->id,
                    'department_name' => $dept->name,
                    'total'           => $total,
                    'present'         => $present,
                    'late'            => $late,
                    'absent'          => $absent,
                ]);
            }


            $dateWiseSummary->push([
                'date'        => $date->format('Y-m-d'),
                'readable'    => $date->format('d M, Y'),
                'departments' => $dailyDepartments
            ]);
        }


        // =========================
        // Return View
        // =========================
        return view(
            adminTheme() . 'attendance.dailyAttendanceDepartmentSummary',
            compact(
                'dateWiseSummary',
                'departments',
                'startDate',
                'endDate'
            )
        );
    }

    /**
     * Live Location Tracking - Track employee locations in real-time
     */
    public function liveLocationTracking(Request $request)
    {
        $employee_id = $request->employee_id;
        $department_id = $request->department_id;

        // Get employees with their last location
        $query = User::where('status', 1)
            ->where('customer', 1)
            ->where('employee_status', 'active')
            ->with(['department', 'lastLocation']);

        if ($employee_id) {
            $query->where('id', $employee_id);
        }

        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        $employees = $query->orderBy('name')->get();

        // Get recent attendance with location for today
        $today = Carbon::today()->format('Y-m-d');
        $attendancesWithLocation = Attendance::where('date', $today)
            ->whereNotNull('location_lat')
            ->whereNotNull('location_long')
            ->where('location_lat', '!=', '')
            ->where('location_long', '!=', '')
            ->get()
            ->groupBy('user_id');

        // Get all employees with location data
        $employeesWithLocation = UserLocation::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->get()
            ->groupBy('user_id');

        // Build employee location data
        $locationData = [];
        foreach ($employees as $employee) {
            $lastLocation = $employee->lastLocation;

            // Try to get from today's attendance first
            $todayAttendance = $attendancesWithLocation->get($employee->id)?->first();

            // Then try from UserLocation
            $userLocation = $employeesWithLocation->get($employee->id)?->first();

            $location = null;
            $locationTime = null;
            $locationSource = null;

            if ($todayAttendance && $todayAttendance->location_lat && $todayAttendance->location_long) {
                $location = [
                    'lat' => $todayAttendance->location_lat,
                    'lng' => $todayAttendance->location_long,
                ];
                $locationTime = $todayAttendance->in_time;
                $locationSource = 'Attendance';
            } elseif ($userLocation && $userLocation->latitude && $userLocation->longitude) {
                $location = [
                    'lat' => $userLocation->latitude,
                    'lng' => $userLocation->longitude,
                ];
                $locationTime = $userLocation->updated_at;
                $locationSource = 'Mobile App';
            }

            $locationData[] = [
                'employee' => $employee,
                'location' => $location,
                'location_time' => $locationTime,
                'location_source' => $locationSource,
                'has_location' => $location !== null,
            ];
        }

        // Filter employees with location
        $employeesWithLocationOnly = array_filter($locationData, function($item) {
            return $item['has_location'];
        });

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $allEmployees = User::where('status', 1)
            ->where('customer', 1)
            ->where('employee_status', 'active')
            ->orderBy('name')
            ->get();

        return view(adminTheme() . 'attendance.live_location_tracking', compact(
            'locationData',
            'employeesWithLocationOnly',
            'departments',
            'allEmployees',
            'employee_id',
            'department_id'
        ));
    }



    public function dailyAttendanceAction(Request $r,$action,$id=null){

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

          return redirect()->route('admin.dailyAttendanceAction',['edit',$invoice->id]);
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

    public function gradeWiseSalaryReport(Request $r)
    {
        // ----- Date Range -----
        $startDate = $r->startDate
            ? Carbon::parse($r->startDate)->startOfDay()
            : Carbon::today()->startOfMonth();

        $endDate = $r->endDate
            ? Carbon::parse($r->endDate)->endOfDay()
            : Carbon::today()->endOfDay();

        // ----- Users Query with Filters -----
        $users = User::latest()
            ->when($r->search, fn($q) => $q->where('name','like','%'.$r->search.'%'))
            ->when($r->grade, fn($q) => $q->where('grade_lavel', $r->grade))
            ->when($r->designation, fn($q) => $q->where('designation_id', $r->designation))
            ->when($r->department, fn($q) => $q->where('department_id', $r->department))
            ->when($r->employeeType, fn($q) => $q->where('employee_type_id', $r->employeeType))
            ->paginate(50);

        $userIds = $users->pluck('id');

        // ----- Fetch Salary Records -----
        $salaryRecords = Salary::whereIn('user_id', $userIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        // ----- Map Users to Salary Data -----
        $salaryData = $users->map(function($user) use ($salaryRecords) {

            $grade = Attribute::where('type',12)->find($user->grade_lavel);
            $gradeJson = $grade ? json_decode($grade->description, true) : [];

            $salaryPaid = $salaryRecords->get($user->id);
            $totalPaid = $salaryPaid ? $salaryPaid->sum('net_salary_amount') : 0;
            $lastPaid = $salaryPaid ? $salaryPaid->first()?->created_at : null;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'grade' => $grade?->name ?? '--',
                'designation' => $user->designation?->name ?? '--',
                'department' => $user->department?->name ?? '--',
                'employee_type' => $user->employeeType?->name ?? '--',
                'basic' => $gradeJson['basic_salary'] ?? 0,
                'house_rent' => $gradeJson['house_rent'] ?? 0,
                'medical' => $gradeJson['medical_allowance'] ?? 0,
                'transport' => $gradeJson['transport_allowance'] ?? 0,
                'food' => $gradeJson['food_allowance'] ?? 0,
                'attendance_bonus' => $gradeJson['attendance_bonus'] ?? 0,
                'other_allowance' => $gradeJson['other_allowance'] ?? 0,
                'stamp_charge' => $gradeJson['stamp_charge'] ?? 0,
                'computed_salary' => ($gradeJson['basic_salary'] ?? 0) + ($gradeJson['house_rent'] ?? 0) + ($gradeJson['medical_allowance'] ?? 0) + ($gradeJson['transport_allowance'] ?? 0) + ($gradeJson['food_allowance'] ?? 0) + ($gradeJson['attendance_bonus'] ?? 0) + ($gradeJson['other_allowance'] ?? 0) + ($gradeJson['stamp_charge'] ?? 0),
                'total_paid' => $totalPaid,
                'last_paid' => $lastPaid ? Carbon::parse($lastPaid)->format('Y-m-d') : '--',
            ];
        });

        // ----- Summary -----
        $totalEmployees = $users->total();
        $totalSalary = $salaryData->sum('computed_salary');
        $totalPaid = $salaryData->sum('total_paid');

        // ----- Filters for dropdowns -----
        $grades = Attribute::latest()->where('type',12)->where('status','<>','temp')->get();
        $departments = Attribute::latest()->where('type',3)->where('status','<>','temp')->get();
        $designations = Attribute::latest()->where('type',2)->where('status','<>','temp')->get();
        $employeeTypes = Attribute::latest()->where('type',4)->where('status','active')->get();

        return view(
            adminTheme().'salary.gradeWiseSalaryReport',
            compact(
                'users',
                'salaryData',
                'totalEmployees',
                'totalSalary',
                'totalPaid',
                'grades',
                'departments',
                'designations',
                'employeeTypes',
                'startDate',
                'endDate'
            )
        );
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

      return view(adminTheme().'employee-types.employeeTypesAll',compact('employeeTypes','totals'));

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

          return view(adminTheme().'divisions.divisionsAll',compact('divisions','totals'));

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

          return view(adminTheme().'grades.gradesAll',compact('grades','totals'));

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

    // line_numbers
    public function line_numbers(Request $r){

          if($r->action){
            if($r->checkid){

            $datas=Attribute::latest()->where('type',13)->whereIn('id',$r->checkid)->get();

            foreach($datas as $data){

                if($r->action==1){
                  $data->status='active';
                  $data->save();
                }elseif($r->action==2){
                  $data->status='inactive';
                  $data->save();
                }elseif($r->action==5){

                  $medias =Media::latest()->where('src_type',13)->where('src_id',$data->id)->get();
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


          $line_numbers=Attribute::latest()->where('type',13)->where('status','<>','temp')
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
          ->where('type',13)
          ->selectRaw('count(*) as total')
          ->selectRaw("count(case when status = 'active' then 1 end) as active")
          ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
          ->first();

          return view(adminTheme().'lineNumbers.lineNumbersAll',compact('line_numbers','totals'));

    }

    public function line_numbersAction(Request $r,$action,$id=null){

          if($action=='create'){
            $check = $r->validate([
                'name' => 'required|max:100',
                'description' => 'nullable|max:1000',
            ]);

            $line =Attribute::where('type',13)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$line){
              $line =new Attribute();
            }

            $line->name=$r->name;
            $line->description=$r->description;
            $line->type =13;
            $line->status ='active';
            $line->addedby_id =Auth::id();
            $line->save();

             $slug =Str::slug($r->name);
             if($slug==null){
              $line->slug=$line->id;
             }else{
              if(Attribute::where('type',13)->where('slug',$slug)->whereNotIn('id',[$line->id])->count() >0){
              $line->slug=$slug.'-'.$line->id;
              }else{
              $line->slug=$slug;
              }
            }
            $line->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

          }

          $line =Attribute::where('type',13)->find($id);
          if(!$line){
            Session()->flash('error','Not Found');
            return redirect()->route('admin.line_numbers');
          }

          $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
          if($allPer && $line->addedby_id!=Auth::id()){
            Session()->flash('error','Unauthorized');
            return redirect()->route('admin.line_numbers');
          }

          if($action=='update'){

              $check = $r->validate([
                  'name' => 'required|max:191',
                  'seo_title' => 'nullable|max:200',
                  'seo_desc' => 'nullable|max:250',
                  'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                  'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
              ]);

              $line->name=$r->name;
              $line->short_description=$r->short_description;
              $line->description=$r->description;
              $line->seo_title=$r->seo_title;
              $line->short_description=$r->short_description;
              $line->seo_keyword=$r->seo_keyword;


                if($r->hasFile('image')){
                  $file =$r->image;
                  uploadFile($file,$line->id,13,1,Auth::id());
                }

                if($r->hasFile('banner')){
                  $file =$r->banner;
                  uploadFile($file,$line->id,13,2,Auth::id());
                }


                 $slug =Str::slug($r->name);
                 if($slug==null){
                  $line->slug=$line->id;
                 }else{
                  if(Attribute::where('type',13)->where('slug',$slug)->whereNotIn('id',[$line->id])->count() >0){
                  $line->slug=$slug.'-'.$line->id;
                  }else{
                  $line->slug=$slug;
                  }
                }

                $line->status =$r->status?'active':'inactive';
                $line->fetured =$r->fetured?1:0;
                $line->editedby_id =Auth::id();
                $line->save();

                Session()->flash('success','Your Are Successfully Done');
                return redirect()->back();

          }

          if($action=='delete'){
              $medias =Media::latest()->where('src_type',13)->where('src_id',$line->id)->get();
                foreach($medias as $media){
                  if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                  }
                  $media->delete();
                }

                $line->delete();

                Session()->flash('success','Your Are Successfully Done');
                return redirect()->route('admin.line_numbers');
          }

          return redirect()->back();
    }
    // end line_numbers

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

          return view(adminTheme().'sections.sectionsAll',compact('sections','totals'));

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

        return view(adminTheme().'shifts.shiftsAll', compact('shifts', 'totals'));
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
                return view(adminTheme().'shifts.create_edit', compact('shift'));
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



    public function themeSetting(Request $r){
      return view(adminTheme().'theme-setting.themeSetting');
    }



    // User Management Function Start

    public function usersAdmin(Request $r){

      //Filter Actions Start
      if($r->action){
        if($r->checkid){

        $datas=User::latest()->whereIn('status',[0,1])->where('admin',true)->whereIn('id',$r->checkid)->filterBy('employee')->get();

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
        // ->where('permission_id',1)
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
      ->select(['id','permission_id','name','email','mobile','addedby_at','addedby_id','status', 'employee_id', 'password_show'])
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
        $r->validate([
            'name' => 'required|max:100',
            'email' => 'required|max:50|unique:users,email',
        ]);

        // Generate random password
        $password = User::generatePassword();

        $user = User::create([
            'name' => $r->name,
            'email' => $r->email,
            'password' => bcrypt($password),
            'password_show' => $password,
            'admin' => true,
            'customer' => $r->is_employee ? true : false,
            'status' => 1,
            'addedby_id' => Auth::id(),
            'addedby_at' => Carbon::now(),
        ]);

        Session()->flash('success', 'Admin created successfully!');
        return redirect()->route('admin.usersAdmin');

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
                 'name' => 'required|max:100',
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

        return view(adminTheme().'users.admins.editUser',compact('user','roles'));

    }


    public function usersCustomer(Request $r)
    {
        // =============================
        // BULK ACTIONS
        // =============================
        if ($r->action) {

            if (!$r->checkid) {
                Session()->flash('info', 'Please select at least one user.');
                return redirect()->back();
            }

            $usersToAction = User::whereIn('id', $r->checkid)
                                 ->whereIn('status', [0,1])
                                 ->get();

            foreach ($usersToAction as $user) {
                if ($r->action == 1) {
                    $user->update(['status' => 1]);
                } elseif ($r->action == 2) {
                    $user->update(['status' => 0]);
                } elseif ($r->action == 5) {
                    // delete media files
                    $mediaFiles = Media::where('src_type', 6)->where('src_id', $user->id)->get();
                    foreach ($mediaFiles as $media) {
                        if(File::exists($media->file_url)) File::delete($media->file_url);
                        $media->delete();
                    }
                    $user->delete();
                }
            }

            Session()->flash('success','Action completed successfully!');
            return redirect()->back();
        }

        // =============================
        // MAIN QUERY
        // =============================
        $users = User::filterBy('employee')->latest()
            ->whereIn('status', [0,1])
            ->with(['designation', 'department', 'section', 'line'])
            ->where(function($q) use($r) {

                // --- SEARCH ---
                if($r->search){
                    $q->where(function($q2) use($r) {
                        $q2->where('name', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('email', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('employee_id', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('mobile', 'LIKE', '%'.$r->search.'%');
                    });
                }

                // --- FILTERS USING IF/ELSE ---
                if($r->designation_id){
                    $q->where('designation_id', $r->designation_id);
                } else {
                    if($r->department_id){
                        $q->where('department_id', $r->department_id);
                    } else {
                        if($r->section_id){
                            $q->where('section_id', $r->section_id);
                        } else {
                            if($r->line_number){
                                $q->where('line_number', $r->line_number);
                            }
                        }
                    }
                }

                // --- Joining Date ---
                if($r->startDate || $r->endDate){
                    $from = $r->startDate ?: Carbon::now()->format('Y-m-d');
                    $to   = $r->endDate ?: Carbon::now()->format('Y-m-d');
                    $q->whereDate('created_at','>=',$from)
                      ->whereDate('created_at','<=',$to);
                }

                // --- Salary ---
                if($r->min_salary && $r->max_salary){
                    $q->whereBetween('salary_amount', [$r->min_salary, $r->max_salary]);
                } elseif($r->min_salary){
                    $q->where('salary_amount','>=',$r->min_salary);
                } elseif($r->max_salary){
                    $q->where('salary_amount','<=',$r->max_salary);
                }

                // --- Status ---
                if($r->status){
                    if($r->status === 'inactive'){
                        $q->where('status', 0);
                    } else {
                        $q->where('status', 1);
                    }
                }

            })
            ->select([
                'id',
                'name',
                'email',
                'mobile',
                'designation_id',
                'department_id',
                'section_id',
                'line_number',
                'salary_amount',
                'gross_salary',
                'created_at',
                'permission_id',
                'status',
                'employee_id',
                'password_show',
            ])
            ->paginate(25)
            ->appends($r->all());

        // =============================
        // TOTAL COUNTS
        // =============================
        $totals = DB::table('users')
            ->whereIn('status',[0,1])
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 1 then 1 end) as active")
            ->selectRaw("count(case when status = 0 then 1 end) as inactive")
            ->first();

        // ROLES
        $roles = Permission::latest()->where('status','active')->get();

        return view(adminTheme().'users.customers.users', compact('users','totals','roles'));
    }

    public function usersCustomerPrint(Request $r)
    {
        // Get users with same filters as main list
        $users = User::filterBy('employee')->latest()
            ->whereIn('status', [0,1])
            ->with(['designation', 'department', 'section', 'line'])
            ->where(function($q) use($r) {
                if($r->search){
                    $q->where(function($q2) use($r) {
                        $q2->where('name', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('email', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('employee_id', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('mobile', 'LIKE', '%'.$r->search.'%');
                    });
                }
                if($r->designation_id){
                    $q->where('designation_id', $r->designation_id);
                } elseif($r->department_id){
                    $q->where('department_id', $r->department_id);
                } elseif($r->section_id){
                    $q->where('section_id', $r->section_id);
                } elseif($r->line_number){
                    $q->where('line_number', $r->line_number);
                }
                if($r->startDate || $r->endDate){
                    $from = $r->startDate ?: Carbon::now()->format('Y-m-d');
                    $to   = $r->endDate ?: Carbon::now()->format('Y-m-d');
                    $q->whereDate('created_at','>=',$from)
                      ->whereDate('created_at','<=',$to);
                }
                if($r->status){
                    if($r->status === 'inactive'){
                        $q->where('status', 0);
                    } else {
                        $q->where('status', 1);
                    }
                }
                if($r->role_id){
                    $q->where('role_id', $r->role_id);
                }
            })
            ->get();

        return view('admin.users.customers.print', compact('users'));
    }

    public function usersCustomerExport(Request $r)
    {
        // Get users with same filters as main list
        $users = User::filterBy('employee')->latest()
            ->whereIn('status', [0,1])
            ->with(['designation', 'department', 'section', 'line'])
            ->where(function($q) use($r) {
                if($r->search){
                    $q->where(function($q2) use($r) {
                        $q2->where('name', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('email', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('employee_id', 'LIKE', '%'.$r->search.'%')
                           ->orWhere('mobile', 'LIKE', '%'.$r->search.'%');
                    });
                }
                if($r->designation_id){
                    $q->where('designation_id', $r->designation_id);
                } elseif($r->department_id){
                    $q->where('department_id', $r->department_id);
                } elseif($r->section_id){
                    $q->where('section_id', $r->section_id);
                } elseif($r->line_number){
                    $q->where('line_number', $r->line_number);
                }
                if($r->startDate || $r->endDate){
                    $from = $r->startDate ?: Carbon::now()->format('Y-m-d');
                    $to   = $r->endDate ?: Carbon::now()->format('Y-m-d');
                    $q->whereDate('created_at','>=',$from)
                      ->whereDate('created_at','<=',$to);
                }
                if($r->status){
                    if($r->status === 'inactive'){
                        $q->where('status', 0);
                    } else {
                        $q->where('status', 1);
                    }
                }
                if($r->role_id){
                    $q->where('role_id', $r->role_id);
                }
            })
            ->get();

        // Export to Excel
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UsersExport($users), 'employees_'.date('Y-m-d').'.xlsx');
    }

    public function usersCustomerAction(Request $r,$action,$id=null){
        try{
            //Add New User Start
            if($action=='create' && $r->isMethod('post')){

                $r->validate([
                    'name' => 'required|max:100',
                    'employee_id' => 'required|max:50|unique:users,employee_id',
                ]);

                // Generate random password
                $password = User::generatePassword();

                $user = User::create([
                    'name' => $r->name,
                    'employee_id' => $r->employee_id,
                    'password' => bcrypt($password),
                    'password_show' => $password,
                    'admin' => false,
                    'customer' => true,
                    'status' => 1,
                    'addedby_id' => Auth::id(),
                    'addedby_at' => Carbon::now(),
                ]);

                Session()->flash('success', 'Employee created successfully! Login Employee ID: ' . $r->employee_id . ', Password: ' . $password);
                return redirect()->route('admin.usersCustomer');
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
                $departments   = Attribute::latest()->where('type',3)->where('status','<>','temp')->get();
                $designations  = Attribute::latest()->where('type',2)->where('status','<>','temp')->get();
                $divisions     = Attribute::latest()->where('type', 11)->where('status', '<>', 'temp')->get();
                $grades        = Attribute::latest()->where('type', 12)->where('status', '<>', 'temp')->get();
                $line_numbers  = Attribute::latest()->where('type', 13)->where('status', '<>', 'temp')->get();
                $sections      = Attribute::latest()->where('type', 14)->where('status', '<>', 'temp')->get();
                $shifts        = Shift::latest()->get();
                $emp_types     = Attribute::latest()->where('type', 16)->where('status', '<>', 'temp')->get();

                $roles =Permission::latest()->where('status','active')->get();

                return view(adminTheme().'users.customers.editUser', compact('user','departments','designations','divisions','grades','line_numbers','sections','shifts','roles', 'emp_types'));

            }

            if($action=='salary-details'){
                $salaries =$user->salaries()->latest()->paginate(12);
                return view(adminTheme().'users.customers.salaryDetails',compact('user','salaries'));
            }

            if($action=='loan-details'){
                $loans =$user->loans()->latest()->paginate(12);
                return view(adminTheme().'users.customers.loanDetails',compact('user','loans'));
            }


            if ($action == 'update' && $r->isMethod('post')) {
                try {

                    // VALIDATION - ONLY FIELDS THAT ACTUALLY EXIST IN FORM
                    $r->validate([
                        'name'        => 'required|max:100',
                        'father_name' => 'required|max:100',
                        'mobile'      => 'nullable|max:20|unique:users,mobile,' . $user->id,
                        'employee_id' => 'nullable|max:100',
                    ]);

                    // MASS UPDATE – MAP ALL FIELDS
                    $user->employee_id = $r->employee_id;
                    $user->name = $r->name;
                    $user->bn_name = $r->bn_name;
                    $user->email = $r->email;
                    $user->mobile = $r->mobile;

                    $user->gender = $r->gender;
                    $user->marital_status = $r->marital_status;
                    $user->dob = $r->date_of_birth;

                    $user->father_name = $r->father_name;
                    $user->father_name_bn = $r->father_name_bn;
                    $user->mother_name = $r->mother_name;
                    $user->mother_name_bn = $r->mother_name_bn;
                    $user->spouse_name = $r->spouse_name;
                    $user->spouse_name_bn = $r->spouse_name_bn;

                    $user->boys = $r->boys;
                    $user->girls = $r->girls;

                    $user->blood_group = $r->blood_group;
                    $user->religion = $r->religion;
                    $user->education = $r->education;
                    $user->work_type = $r->work_type;

                    $user->nid_number = $r->nid_number;
                    $user->birth_registration = $r->birth_registration;
                    $user->passport_no = $r->passport_no;
                    $user->driving_license = $r->driving_license;
                    $user->etin = $r->etin;

                    $user->distinguished_mark = $r->distinguished_mark;
                    $user->height = $r->height;
                    $user->weight = $r->weight;

                    $user->home_district = $r->home_district;
                    $user->nationality = $r->nationality;
                    $user->location = $r->location;
                    $user->report_to = $r->report_to;
                    $user->grade_lavel = $r->grade_lavel;
                    $user->gross_salary = $r->gross_salary ?? 0;

                    // NEW EMPLOYEE FIELDS - Salary Breakdown
                    $user->basic_salary = $r->basic_salary ?? 0;
                    $user->house_rent = $r->house_rent ?? 0;
                    $user->medical_allowance = $r->medical_allowance ?? 0;
                    $user->transport_allowance = $r->transport_allowance ?? 0;
                    $user->food_allowance = $r->food_allowance ?? 0;
                    $user->conveyance_allowance = $r->conveyance_allowance ?? 0;
                    $user->provident_fund = $r->provident_fund ?? 0;

                    // NEW EMPLOYEE FIELDS - Employment Dates
                    $user->joining_date = $r->joining_date;
                    $user->confirmation_date = $r->confirmation_date;
                    $user->retirement_date = $r->retirement_date;
                    $user->employee_status = $r->employee_status ?? 'active';

                    // NEW EMPLOYEE FIELDS - Photo & Signature
                    if ($r->hasFile('photo')) {
                        $photoPath = $r->file('photo')->store('employees/photos', 'public');
                        $user->photo = 'storage/' . $photoPath;
                    }
                    if ($r->hasFile('signature')) {
                        $signaturePath = $r->file('signature')->store('employees/signatures', 'public');
                        $user->signature = 'storage/' . $signaturePath;
                    }


                    $user->emergency_mobile = $r->emergency_mobile;
                    $user->emergency_relation = $r->emergency_relation;

                    $user->other_information = $r->other_information;
                    $user->reference_1 = $r->reference_1;
                    $user->reference_2 = $r->reference_2;

                    $user->nominee = $r->nominee;
                    $user->nominee_bn = $r->nominee_bn;
                    $user->nominee_relation = $r->nominee_relation;
                    $user->nominee_age = $r->nominee_age;

                    $user->present_address = $r->present_address;
                    $user->present_address_bn = $r->present_address_bn;
                    $user->permanent_address = $r->permanent_address;
                    $user->permanent_address_bn = $r->permanent_address_bn;

                    // FIXED FIELD MAPS
                    $user->division = $r->division_id;
                    $user->department_id = $r->department_id;
                    $user->designation_id = $r->designation_id;
                    $user->section_id = $r->section_id;
                    $user->line_number = $r->line_number;
                    $user->shift_id = $r->shift_id;
                    $user->employee_type = $r->employee_type;


                    $user->city = $r->city;
                    $user->district = $r->district;
                    $user->postal_code = $r->postal_code;

                    $user->salary_amount = $r->salary_amount ?: 0;
                    $user->profile = $r->profile;
                    $user->status = 1;


                    // CREATED DATE
                    if ($r->created_at) {
                        $user->created_at = Carbon::parse($r->created_at . ' ' . now()->format('H:i:s'));
                    }

                    $user->exited_at = $r->exited_at;


                    // PASSWORD UPDATE
                    if ($r->password) {
                        $user->password_show = $r->password;
                        $user->password = Hash::make($r->password);
                    }


                    // PERMISSION LOGIC
                    if ($user->id != Auth::id() && Auth::user()->permission_id == 1) {
                        if ($r->role) {
                            $user->admin = true;
                            $user->permission_id = $r->role;
                            $user->addedby_at = now();
                            $user->addedby_id = Auth::id();
                        } else {
                            $user->admin = false;
                            $user->permission_id = null;
                            $user->addedby_id = null;
                            $user->addedby_at = null;
                        }
                    }


                    // IMAGE
                    if ($r->hasFile('image')) {
                        uploadFile($r->image, $user->id, 6, 1, Auth::id());
                    }


                    // STATUS LOGIC
                    $user->login_status = $r->login_status ? 1 : 0;
                    // $user->status = $r->status ? 1 : 0;


                    $user->save();

                    Session()->flash('success', 'Update Successful!');
                    return redirect()->back();

                } catch (\Exception $e) {

                    return redirect()->back()->withErrors(['error' => $e->getMessage()]);
                }
            }

            if($action=='idcard'){
                return view(adminTheme().'users.customers.id_card_bn',compact('user'));
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

            if ($action == 'user-document') {
                $fileAction = $r->file_action;
                $fileId = $r->file_id ?? null;

                if ($fileAction == 'addfile') {
                    Media::create([
                        'src_id' => $user->id,
                        'src_type' => 6,
                        'use_Of_file' => 3,
                        'addedby_id' => Auth::id(),
                    ]);
                }

                if (in_array($fileAction, ['removeData', 'removeFile']) && $fileId) {
                    $file = $user->galleryFiles()->find($fileId);
                    if($file && File::exists($file->file_url)) File::delete($file->file_url);

                    if ($fileAction == 'removeData') $file?->delete();
                    if ($fileAction == 'removeFile') {
                        $file?->update([
                            'file_url'=>null,'file_path'=>null,'alt_text'=>null,'file_rename'=>null,'file_size'=>null
                        ]);
                    }
                }

                if ($fileAction == 'updateTitle' && $fileId) {
                    $file = $user->galleryFiles()->find($fileId);
                    if($file) $file->update(['file_name'=>$r->title]);
                }

                if ($fileAction == 'updateFile' && $fileId && $r->hasFile('file')) {
                    $fileData = $user->galleryFiles()->find($fileId);
                    if ($fileData) {
                        if(File::exists($fileData->file_url)) File::delete($fileData->file_url);

                        $file = $r->file;
                        $ext = $file->getClientOriginalExtension();
                        $size = $file->getSize();
                        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $folder = now()->format('M_Y');
                        $imgName = time().'.'.uniqid().'.'.$ext;
                        $path = "medies/".$folder;
                        $fullPath = "public/".$path.'/'.$imgName;

                        $fileData->update([
                            'alt_text' => Str::limit($name,250),
                            'file_rename' => $imgName,
                            'file_size' => $size,
                            'file_type' => match(strtolower($ext)){
                                'png','jpeg','jpg','gif','svg','webp'=>1,
                                'pdf'=>2,
                                'docx'=>3,
                                'zip','rar'=>4,
                                'mp4','webm','mov','wmv'=>5,
                                'mp3'=>6,
                                default => 0
                            },
                            'file_url' => $fullPath,
                            'file_path' => $path
                        ]);

                        $file->move(public_path($path), $imgName);
                    }
                }
                $view = view(adminTheme().'users.customers.includes.userFiles', compact('user'))->render();
                return response()->json(['success'=>true, 'view'=>$view]);
            }

            $startDate=$r->startDate?Carbon::parse($r->startDate):Carbon::now()->startOfMonth();
            $endDate=$r->endDate?Carbon::parse($r->endDate):Carbon::now();

            return view(adminTheme().'users.customers.viewUser',compact('user','action','startDate','endDate'));
        }catch(\Exception $e){
            dd($e);
            Session()->flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }

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

    public function reports(Request $r,$action=null){
            $startDate=$r->startDate?Carbon::parse($r->startDate):Carbon::now();
            $endDate=$r->endDate?Carbon::parse($r->endDate):Carbon::now();

            if($action=='products'){
                $datas =Post::latest()->where('type',3)->where('status','active')->get();

                return view(adminTheme().'reports.productReports',compact('startDate','endDate','action','datas'));
            }

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
