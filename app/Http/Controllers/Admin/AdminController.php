<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attribute;
use App\Models\Company;
use App\Models\CompanyMachinery;
use App\Models\CompanyPerson;
use App\Models\Country;
use App\Models\Expense;
use App\Models\ExpenseIou;
use App\Models\General;
use App\Models\Lead;
use App\Models\LeadPerson;
use App\Models\Media;
use App\Models\Meeting;
use App\Models\Note;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use App\Models\Permission;
use App\Models\Post;
use App\Models\PostAttribute;
use App\Models\PostExtra;
use App\Models\Review;
use App\Models\Service;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLocation;
use App\Models\Visit;
use App\Services\UserService;
use Artisan;
use Carbon\Carbon;
use DB;
use Hash;
use ME\Hr\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Image;
use Pdf;
use Redirect,Response;
use Str;
use Validator;

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
            'total_order' => OrderDetail::where('status','<>','temp')->count(),
            'total_order_confirmed' => OrderDetail::where('status','confirmed')->count(),
            'total_order_pending' => OrderDetail::where('status','pending')->count(),
            'total_order_cancelled' => OrderDetail::where('status','cancelled')->count(),
            'total_staff' => User::where('status',1)->where('staff',true)->count(),
            'total_staff_present' => User::where('status',1)->where('staff',true)->count(),
            'total_staff_absent' => 0,
            'total_staff_worked' => 0,
            'total_sale' => 0,
            'total_order_amount' => 0,
            'total_expenses' => Expense::sum('amount'),
            'total_IOU' => ExpenseIou::where('status','pending')->sum('amount'),
        ];

        $userActivity = $this->getUserActivityReport(new Request());
        return view('admin.dashboard',compact('reports', 'userActivity'));
    }

    public function getUserActivityReport(Request $request)
    {
        $this->tollaranceTime = 5 ;
        $start = Carbon::now()->subDays(30);
        $end = Carbon::now();
        $nMinutesAgo = Carbon::now()->subMinutes($this->tollaranceTime);
        $limit = $request->input('limit', 10); // default 10
        $all = $request->input('all', false); // if true, use paginate

        // 1. Login logs in last 30 days
        $loginLogsByUser = ActivityLog::where('event', 'login')
            ->whereBetween('created_at', [$start, $end])
            ->select('user_id', 'created_at')
            ->whereHas('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        $userIds = $loginLogsByUser->keys();

        if ($userIds->isEmpty()) {
            return collect(); // no users
        }

        // 2. Last active logs for all users
        $lastActive = ActivityLog::whereIn('user_id', $userIds)
            ->where('event', 'user_active')
            ->whereHas('user')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        // 3. Recent activity and logout in last 10 minutes
        $recentActiveLogs = ActivityLog::whereIn('user_id', $userIds)
            ->where('event', 'user_active')
            ->whereHas('user')
            ->where('created_at', '>=', $nMinutesAgo)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        $recentLogoutLogs = ActivityLog::whereIn('user_id', $userIds)
            ->where('event', 'logout')
            ->whereHas('user')
            ->where('created_at', '>=', $nMinutesAgo)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id');

        // 4. Determine active users
        $activeUserIds = [];
        foreach ($recentActiveLogs as $userId => $logs) {
            $lastActiveTime = $logs->first()->created_at;
            $lastLogoutTime = $recentLogoutLogs->get($userId)?->first()?->created_at ?? null;

            if (!$lastLogoutTime || $lastActiveTime->gt($lastLogoutTime)) {
                $activeUserIds[] = $userId;
            }
        }

        // 5. Get user info
        $users = User::whereIn('id', $userIds)
            ->select('id', 'name', 'mobile')
            ->get()
            ->keyBy('id');

        // 6. Build report
        $userActivity = collect($userIds)->map(function ($userId) use ($users, $loginLogsByUser, $lastActive, $activeUserIds) {
            $user = $users[$userId];

            $lastLogin = $loginLogsByUser->get($userId)?->first()?->created_at ?? null;
            $lastActiveAt = $lastActive->get($userId)?->first()?->created_at ?? null;

            return [
                'name' => $user->name,
                'mobile' => $user->mobile,
                'login_at' => $lastLogin ? $lastLogin->format('d.m.Y h:i A') : null,
                'last_active_at' => $lastActiveAt ? $lastActiveAt->format('d.m.Y h:i A') : null,
                'active_status' => in_array($userId, $activeUserIds),
                'last_active_ago' => $lastActiveAt ? $lastActiveAt->copy()->addMinutes($this->tollaranceTime)->diffForHumans() : null,
            ];
        });

        // 7. Sort: active first, then inactive; both by last_active_at desc
        // $userActivity = $userActivity->sort(function ($a, $b) {
        //     if ($a['active_status'] && !$b['active_status']) return -1;
        //     if (!$a['active_status'] && $b['active_status']) return 1;

        //     $aTime = $a['last_active_at'] ? Carbon::createFromFormat('d.m.Y h:i A', $a['last_active_at']) : Carbon::minValue();
        //     $bTime = $b['last_active_at'] ? Carbon::createFromFormat('d.m.Y h:i A', $b['last_active_at']) : Carbon::minValue();

        //     return $bTime->timestamp <=> $aTime->timestamp;
        // })->values();

        $userActivity = $userActivity->sort(function ($a, $b) {
            if ($a['active_status'] && !$b['active_status']) return -1;
            if (!$a['active_status'] && $b['active_status']) return 1;

            $aTime = $a['last_active_at']
                ? Carbon::createFromFormat('d.m.Y h:i A', $a['last_active_at'])
                : Carbon::createFromTimestamp(0);

            $bTime = $b['last_active_at']
                ? Carbon::createFromFormat('d.m.Y h:i A', $b['last_active_at'])
                : Carbon::createFromTimestamp(0);

            return $bTime->timestamp <=> $aTime->timestamp;
        })->values();

        // 8. Handle pagination or limit
        if ($all) {
            // Use Laravel LengthAwarePaginator
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = $limit;
            $currentItems = $userActivity->slice(($page - 1) * $perPage, $perPage)->values();
            return new LengthAwarePaginator($currentItems, $userActivity->count(), $perPage, $page, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
            ]);
        } else {
            // Just return limited results
            return $userActivity->take($limit);
        }
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

    public function expenses(Request $r){

        $this->from = $from =$r->startDate ?? Carbon::now()->format('Y-m-d');
        $this->to = $to =$r->endDate ?? Carbon::now()->format('Y-m-d');

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

        // base query for filters
        $query = Expense::where('status', '<>', 'temp')
                        ->where(function($q) use ($r) {
                            if($r->search){
                                $search = ltrim($r->search, '0');
                                $q->where('id', 'LIKE', '%' . $search . '%');
                            }
                            if($r->status){
                                $q->where('status', $r->status);
                            }
                            if($r->expense_type){
                                $q->where('category_id', $r->expense_type);
                            }
                            if($r->account_id){
                                $q->where('account_id', $r->account_id);
                            }
                            $q->whereDate('created_at', '>=', $this->from)
                            ->whereDate('created_at', '<=', $this->to);
                        });

        // Pagination er age total sum ber kora (Current Filter onujayi)
        $totalFilteredAmount = $query->sum('amount');

        // Ekhon pagination kora
        $expenses = $query->latest()
                            ->paginate(25)
                            ->appends([
                                'search' => $r->search,
                                'status' => $r->status,
                                'startDate' => $r->startDate,
                                'endDate' => $r->endDate,
                                'expense_type' => $r->expense_type,
                                'account_id' => $r->account_id,
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
                'filtered_expenses' => numberFormat(
                    Expense::where('status', '<>', 'temp')
                        ->whereDate('created_at', '>=', $this->from)
                        ->whereDate('created_at', '<=', $this->to)
                        ->sum('amount'),
                    2
                ),
                'filtered_total' => numberFormat($totalFilteredAmount, 2),
            ];

        $expenseTypes =Attribute::where('type',5)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $paymentMethods =Attribute::where('type',9)->where('status','active')->orderBy('name')->select(['id','name','amount'])->get();
        $accountMethods =Attribute::where('type',10)->where('status','active')
                                    ->where('addedby_id',Auth::id())->orderBy('name')
                                    ->select(['id','name','amount'])->get();
        $branches =Attribute::where('type',0)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $filterAccounts = Attribute::where('type',10)->where('status','active')->orderBy('name')->select(['id','name'])->get();

        $lastAudit = Expense::whereNotNull('audit_at')->latest()->first();

        return view(adminTheme().'expenses.expensesAll',compact('expenses','report','expenseTypes','paymentMethods','accountMethods','branches', 'to', 'from', 'lastAudit', 'filterAccounts'));
    }


    public function expensesAction(Request $r,$action,$id=null){
        if($action == 'audit'){
            $auditIds = $r->audit_data;
            $expensesToAudit = Expense::whereIn('id', $auditIds)->whereNull('audit_at')->get();
            foreach ($expensesToAudit as $expense) {
                $expense->audit_at = now();
                $expense->audit_by = auth()->id();
                $expense->save();
            }
            Session()->flash('success','You have successfully audited the report');
            return redirect()->back();
        }

        //Add Service  Start
        if($action=='create'){

            $check = $r->validate([
                'expense_type' => 'required|numeric',
                'payment' => 'required|numeric',
                'account' => 'required|numeric',
                'branch_id' => 'required|numeric',
                'amount' => 'required|numeric',
                'company_name' => 'required|max:100',
                'receiver_name' => 'required|max:100',
                'receiver_mobile' => 'nullable|max:100',
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
            $expense->company_name=$r->company_name;
            $expense->receiver_name=$r->receiver_name;
            $expense->receiver_mobile=$r->receiver_mobile;
            $expense->status ='active';
            $expense->addedby_id =Auth::id();
            $expense->created_at = $createDate;
            $expense->save();

            $method->amount -=$expense->amount;
            $method->save();

            $transection =new Transaction();
            $transection->type=5;
            $transection->src_id=$expense->id;
            $transection->payment_method_id=$expense->method_id;
            $transection->account_id=$expense->account_id;
            $transection->amount=$expense->amount;
            $transection->status ='success';
            $transection->addedby_id =Auth::id();
            $transection->created_at =$expense->created_at;
            $transection->balance =$method->amount;
            $transection->transection_id =$expense->created_at->format('ymd') . random_int(1000, 9999);
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

        // if($action=='update'){

        //     $check = $r->validate([
        //         'expense_type' => 'required|numeric',
        //         'payment' => 'required|numeric',
        //         'branch_id' => 'required|numeric',
        //         'company_name' => 'required|max:100',
        //         'receiver_name' => 'required|max:100',
        //         'received_mobile' => 'nullable|max:100',
        //         // 'title' => 'required|max:100',
        //         'created_at' => 'nullable|date',
        //         'attachment' => 'nullable||file|max:25600',
        //     ]);
        //     $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();


        //     $expense->category_id=$r->expense_type;
        //     $expense->method_id=$r->payment;
        //     $expense->branch_id=$r->branch_id;
        //     $expense->title=$r->title;
        //     $expense->description=$r->description;
        //     $expense->company_name=$r->company_name;
        //     $expense->receiver_name=$r->receiver_name;
        //     $expense->receiver_mobile=$r->receiver_mobile;
        //     $expense->status =$r->status?'active':'inactive';
        //     $expense->editedby_id =Auth::id();
        //     if (!$createDate->isSameDay($expense->created_at)) {
        //         $expense->created_at = $createDate;
        //     }
        //     $expense->save();
        //     if($transection = $expense->transection){
        //         $transection->payment_method_id =$expense->method_id;
        //         $transection->created_at =$expense->created_at;
        //         $transection->save();
        //     }

        //     ///////Image Upload End////////////
        //     if($r->hasFile('attachment')){
        //       $file =$r->attachment;
        //       $src  =$expense->id;
        //       $srcType  =8;
        //       $fileUse  =1;
        //       uploadFile($file,$src,$srcType,$fileUse);
        //     }
        //     ///////Image Upload End////////////

        //     Session()->flash('success','Your Are Successfully Added');
        //     return redirect()->back();

        // }

        if($action=='update'){

            $check = $r->validate([
                'expense_type' => 'required|numeric',
                'payment' => 'required|numeric',
                'branch_id' => 'required|numeric',
                'company_name' => 'required|max:100',
                'receiver_name' => 'required|max:100',
                'receiver_mobile' => 'nullable|max:100',
                // 'title' => 'required|max:100',
                'amount' => 'required|numeric', // <-- amount validation
                'created_at' => 'nullable|date',
                'attachment' => 'nullable|file|max:25600',
            ]);

            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $oldAmount = $expense->amount; // পুরানো amount
            $newAmount = $r->amount;
            $diff = $newAmount - $oldAmount; // পরিবর্তনের পরিমাণ

            $method = Attribute::find($expense->account_id);
            if(!$method){
                Session()->flash('error','Account method not found');
                return redirect()->back();
            }

            // Check if account balance is sufficient (only if increasing expense)
            if($diff > 0 && $diff > $method->amount){
                Session()->flash('error','Account Balance Not Sufficient');
                return redirect()->back();
            }

            // Update expense fields
            $expense->category_id = $r->expense_type;
            $expense->method_id = $r->payment;
            $expense->branch_id = $r->branch_id;
            $expense->title = $r->title;
            $expense->description = $r->description;
            $expense->company_name = $r->company_name;
            $expense->receiver_name = $r->receiver_name;
            $expense->receiver_mobile = $r->receiver_mobile;
            $expense->amount = $newAmount; // নতুন amount
            $expense->status = $r->status ? 'active' : 'inactive';
            $expense->editedby_id = Auth::id();
            if (!$createDate->isSameDay($expense->created_at)) {
                $expense->created_at = $createDate;
            }
            $expense->save();

            // Update account balance
            $method->amount -= $diff; // পুরানো থেকে নতুন অনুযায়ী adjust
            $method->save();

            // Update related transaction
            if($transection = $expense->transection){
                $transection->amount = $expense->amount;
                $transection->balance = $method->amount;
                $transection->payment_method_id = $expense->method_id;
                $transection->created_at = $expense->created_at;
                $transection->save();
            }

            // Handle attachment
            if($r->hasFile('attachment')){
                $file = $r->attachment;
                $src = $expense->id;
                $srcType = 8;
                $fileUse = 1;
                uploadFile($file, $src, $srcType, $fileUse);
            }

            Session()->flash('success','Expense successfully updated');
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

    public function expensesIOU(Request $r){
        // Filter Action Start
            if($r->action){

                if($r->checkid){

                    $datas=ExpenseIou::latest()->whereIn('id',$r->checkid)->get();
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

        $expenseIou =ExpenseIou::whereNotIn('status', ['temp', 'completed'])
                    ->where(function($q) use ($r) {

                              if($r->search){

                                  $q->where('employee_id','LIKE','%'.$r->search.'%')
                                    ->orWhere('company_name','LIKE','%'.$r->search.'%')
                                    ->orWhere('receiver_name','LIKE','%'.$r->search.'%')
                                    ->orWhere(function($qq)use($r){
                                        $qq->whereHas('employee',function($qqq)use($r){
                                            $qqq->where('name','LIKE','%'.$r->search.'%');
                                        });
                                    });
                              }

                              if($r->status){
                                 $q->where('status',$r->status);
                              }

                                        if($r->account_id){
                                            $q->where('account_id',$r->account_id);
                              }

                            if ($r->quick_filter) {

                                // All → no filtering
                                if ($r->quick_filter === 'all') {
                                    // Do nothing, show all records
                                }
                                // Today
                                else if ($r->quick_filter === 'today') {
                                    $q->whereDate('created_at', Carbon::today());
                                }
                                // Yesterday
                                else if ($r->quick_filter === 'yesterday') {
                                    $q->whereDate('created_at', Carbon::yesterday());
                                }
                                // Over 2 Days
                                else if ($r->quick_filter === 'over_2_days') {
                                    $q->whereDate('created_at', '<=', Carbon::now()->subDays(2));
                                }
                                // Over 7 Days
                                else if ($r->quick_filter === 'over_7_days') {
                                    $q->whereDate('created_at', '<=', Carbon::now()->subDays(7));
                                }
                                // Over 1 Month
                                else if ($r->quick_filter === 'over_1_month') {
                                    $q->whereDate('created_at', '<=', Carbon::now()->subMonth());
                                }
                                // Optional: Invalid filter
                                else {
                                    // Do nothing or handle invalid filter
                                }

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
                        // ->orderBy('id','desc')
                        ->latest()
                        ->get();
        $paymentMethods =Attribute::where('type',9)->where('status','active')->orderBy('name')->select(['id','name','amount'])->get();
        $accountMethods =Attribute::where('type',10)->where('status','active')->where('addedby_id',Auth::id())->orderBy('name')->select(['id','name','amount'])->get();
        $branches =Attribute::where('type',0)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $users =User::where('status',1)->orderBy('name')->select(['id','name'])->get();
        $filterAccounts = Attribute::where('type',10)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $report=[
            'today_expenses'=>numberFormat(
                    ExpenseIou::where('status', '<>', 'temp')
                        ->whereDate('created_at', Carbon::today())
                        ->sum('amount'),
                    2
                ),
            'monthly_expenses'=>numberFormat(
                    ExpenseIou::where('status', '<>', 'temp')
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount'),
                    2
                )
        ];

        return view(adminTheme().'expenses.expensesIOU',compact('expenseIou','branches','users','accountMethods','paymentMethods','report','filterAccounts'));
    }

    public function expensesIOUAction(Request $r,$action,$id=null){

        if($action=='create'){
            $check = $r->validate([
                'employee_id' => 'nullable|max:100',
                'payment' => 'required|numeric',
                'account' => 'required|numeric',
                'branch_id' => 'required|numeric',
                'amount' => 'required|numeric',
                'company_name' => 'nullable|max:100',
                'receiver_name' => 'nullable|max:100',
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

            $expense =new ExpenseIou();
            $expense->employee_id=$r->employee_id;
            $expense->user_id=$expense->employeeUser?$expense->employeeUser->id:null;
            $expense->method_id=$r->payment;
            $expense->account_id=$method->id;
            $expense->branch_id=$r->branch_id;
            $expense->company_name=$r->company_name;
            $expense->receiver_name=$r->receiver_name;
            $expense->amount=$r->amount;
            $expense->description=$r->description;
            $expense->status ='pending';
            $expense->addedby_id =Auth::id();
            $expense->created_at = $createDate;
            $expense->save();

            $method->amount -=$expense->amount;
            $method->save();

            $transection =new Transaction();
            $transection->type=7;
            $transection->src_id=$expense->id;
            $transection->billing_name=$expense->receiver_name;
            $transection->payment_method_id=$expense->method_id;
            $transection->account_id=$expense->account_id;
            $transection->amount=$expense->amount;
            $transection->status ='success';
            $transection->addedby_id =Auth::id();
            $transection->created_at =$expense->created_at;
            $transection->balance =$method->amount;
            $transection->transection_id =$expense->created_at->format('ymd') . random_int(1000, 9999);
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

            return $r;

        }

        if($action=='search-employee'){

            $employee = User::where('status',1)->whereNotNull('employee_id')->where('employee_id', $r->search)->first();
            if ($employee) {
                return response()->json([
                    'status' => true,
                    'name'   => $employee->name
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'name'   => null
                ]);
            }

        }

        $expense =ExpenseIou::find($id);
        if(!$expense){
            Session()->flash('error','This I.O.U Are Not Found');
            return redirect()->route('admin.expensesIOU');
        }

        if($action=='update'){

            $check = $r->validate([
                'employee_id' => 'nullable|max:100',
                'payment' => 'required|numeric',
                'branch_id' => 'required|numeric',
                'amount' => 'required|numeric',
                'company_name' => 'nullable|max:100',
                'receiver_name' => 'nullable|max:100',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable|file|max:25600',
            ]);

            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $expense->employee_id=$r->employee_id;
            $expense->user_id=$expense->employeeUser?$expense->employeeUser->id:null;
            $expense->method_id = $r->payment;
            $expense->branch_id = $r->branch_id;
            $expense->amount = $r->amount ?: 0;
            $expense->description = $r->description;
            $expense->company_name = $r->company_name;
            $expense->receiver_name = $r->receiver_name;

            $prevStatus = $expense->status; // Store previous status
            $expense->status = $r->status ? 'completed' : 'pending';
            $expense->editedby_id = Auth::id();

            if (!$createDate->isSameDay($expense->created_at)) {
                $expense->created_at = $createDate;
            }

            $expense->save();

            // Handle transaction updates
            if($transection = $expense->transection){
                $amount = $expense->amount;

                // If status changed to completed, refund the account
                if($prevStatus != 'completed' && $expense->status == 'completed'){
                    if($method = $expense->account){
                        $method->amount += $amount; // refund
                        $method->save();
                    }
                    $transection->status = 'Refund';
                    $transection->amount = $amount;
                    $transection->balance = $method->amount ?? 0;
                    $transection->created_at = $expense->created_at;
                    $transection->save();
                } else {
                    // Normal update for amount changes
                    if($transection->amount > $amount){
                        $needAmount = $transection->amount - $amount;
                        $transection->amount -= $needAmount;

                        if($method = $expense->account){
                            $method->amount += $needAmount;
                            $method->save();
                        }

                    } elseif($transection->amount < $amount){
                        $needAmount = $amount - $transection->amount;
                        $transection->amount += $needAmount;
                        if($method = $expense->account){
                            $method->amount -= $needAmount;
                            $method->save();
                        }
                    }

                    $transection->created_at = $expense->created_at;
                    $transection->save();
                }
            }

            ///////Image Upload////////////
            if($r->hasFile('attachment')){
                $file = $r->attachment;
                $src = $expense->id;
                $srcType = 10;
                $fileUse = 1;
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


        return back();

    }

    public function completedIou(Request $r)
    {
        $completedIou =ExpenseIou::where('status', 'completed')
                    ->where(function($q) use ($r) {
                              if($r->search){

                                  $q->where('employee_id','LIKE','%'.$r->search.'%')
                                    ->orWhere('company_name','LIKE','%'.$r->search.'%')
                                    ->orWhere('receiver_name','LIKE','%'.$r->search.'%')
                                    ->orWhere(function($qq)use($r){
                                        $qq->whereHas('employee',function($qqq)use($r){
                                            $qqq->where('name','LIKE','%'.$r->search.'%');
                                        });
                                    });
                              }

                            if($r->status){
                                $q->where('status',$r->status);
                            }

                            if($r->account_id){
                                $q->where('account_id',$r->account_id);
                            }

                            if($r->startDate || $r->endDate) {
                                $from = $r->startDate ? Carbon::parse($r->startDate)->startOfDay() : Carbon::minValue();
                                $to   = $r->endDate ? Carbon::parse($r->endDate)->endOfDay() : Carbon::now()->endOfDay();

                                $q->whereBetween('updated_at', [$from, $to]);
                            }

                        })
                        ->orderBy('updated_at','desc')
                        ->paginate(50);

        $filterAccounts = Attribute::where('type',10)->where('status','active')->orderBy('name')->select(['id','name'])->get();

        return view(adminTheme().'expenses.completedIOU', compact('completedIou', 'filterAccounts'));
    }



    public function expenseIOUReports(Request $r){

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

        $expenses = ExpenseIou::latest()->whereNotIn('status', ['temp', 'completed'])
            ->where(function($q) use ($r) {

                if($r->search){
                    $q->where('member_id',$r->search);
                }

                if($r->branch_id){
                    $q->where('branch_id',$r->branch_id);
                }

                if ($r->employee) {
                    $q->where(function($sub) use ($r) {
                        // Search by direct employee_id column
                        $sub->where('employee_id', 'like', '%' . $r->employee . '%')
                            // OR search by name in the employeeUser relationship
                            ->orWhereHas('employeeUser', function($query) use ($r) {
                                $query->where('name', 'like', '%' . $r->employee . '%');
                            });
                    });
                }

                if ($r->account_id) {
                    $q->where('account_id', $r->account_id);
                }
            })
            ->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)
            ->get();


        $users =User::where('status',1)->orderBy('name')->select(['id','name'])->get();
        $filterAccounts = Attribute::where('type',10)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $branches =Attribute::where('type',0)->where('status','active')->orderBy('name')->select(['id','name'])->get();

        return view(adminTheme().'expenses.expenseIOUReports',compact('expenses','users','from','to','branches','filterAccounts'));
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

                if($r->account_id){
                    $q->where('account_id',$r->account_id);
                }
            })
            ->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)
            ->get();


        $expenseTypes =Attribute::where('type',5)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $filterAccounts = Attribute::where('type',10)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $branches =Attribute::where('type',0)->where('status','active')->orderBy('name')->select(['id','name'])->get();
        $supplierBill = Transaction::where('type', 3)->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to)->sum('amount');

        if($r->summery){

            return view(adminTheme().'expenses.expenseSummeryReports',compact('expenses','expenseTypes','from','to','branches', 'supplierBill', 'filterAccounts'));
        }

        return view(adminTheme().'expenses.expenseReports',compact('expenses','expenseTypes','from','to','branches', 'supplierBill', 'filterAccounts'));
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
                                $q->where('account_id',$r->account);
                            }

                            if($r->payment){
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
                        ->orderBy('id', 'desc')
                        ->paginate(10);
        $paymentMethods =Attribute::latest()->where('type',9)->where('status','active')->select(['id','name'])->get();
        $accountMethods =Attribute::latest()->where('type',10)->where('status','active')->select(['id','name'])->get();
        return view(adminTheme().'accounts.deposits',compact('transections','paymentMethods','accountMethods'));
    }

    public function depositsAction(Request $r,$action,$id=null){
        //Add Service  Start
        if($action == 'create') {

            $check = $r->validate([
                'account' => 'required|numeric',
                'amount' => 'required|numeric',
                'received_method' => 'nullable|max:100',
                'received_from' => 'nullable|max:100',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable|file|max:25600',
            ]);

            $account = Attribute::where('type', 10)->where('status', 'active')->find($r->account);
            if (!$account) {
                Session()->flash('error','Account method not found');
                return redirect()->back();
            }

            $createDate = $r->created_at
                ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s'))
                : Carbon::now();

            $deposit = new Transaction();
            $deposit->type = 1;
            $deposit->payment_method = $r->received_method;
            $deposit->billing_name = $r->received_from;
            $deposit->account_id = $account->id;
            $deposit->payment_method_id = $r->payment;
            $deposit->amount = $r->amount;
            $deposit->billing_note = $r->description;
            $deposit->billing_reason = $r->bank_name;
            $deposit->status = 'pending'; // <-- status pending on creation
            $deposit->addedby_id = Auth::id();
            $deposit->created_at = $createDate;
            $deposit->save();

            $deposit->balance = 0; // account not updated yet
            $deposit->transection_id = $deposit->created_at->format('ymd') . $deposit->id;
            $deposit->save();

            // Image upload
            if ($r->hasFile('attachment')) {
                $file = $r->attachment;
                $src = $deposit->id;
                $srcType = 9;
                $fileUse = 1;
                uploadFile($file, $src, $srcType, $fileUse);
            }

            Session()->flash('success','Deposit successfully created and pending approval');
            return redirect()->back();
        }

        // Approve deposit
        $deposit = Transaction::where('type', 1)->find($id);
        if (!$deposit) {
            Session()->flash('error','This deposit not found');
            return redirect()->route('admin.deposits');
        }

        if ($action == 'approve') {
            $deposit->status = 'success';
            $deposit->save();

            $account = Attribute::find($deposit->account_id);
            if ($account) {
                $account->amount += $deposit->amount; // Add amount to account
                $account->save();

                $deposit->balance = $account->amount;
                $deposit->save();
            }

            Session()->flash('success','Deposit approved and account updated successfully');
            return redirect()->back();
        }



        if($action=='update'){
            $check = $r->validate([
                // 'payment' => 'required|numeric',
                'received_method' => 'nullable|max:100',
                'received_from' => 'nullable|max:100',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);
            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $deposit->payment_method=$r->received_method;
            $deposit->billing_name=$r->received_from;
            // $deposit->payment_method_id=$r->payment;
            $deposit->billing_note=$r->description;
            $deposit->billing_reason=$r->bank_name;
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
                $datas=Transaction::where('type',6)->whereIn('id',$r->checkid)->get();
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
                'bank_name' => 'nullable|max:100',
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
            $withdrawal->billing_reason=$r->bank_name;
            $withdrawal->status ='success';
            $withdrawal->addedby_id =Auth::id();
            $withdrawal->created_at = $createDate;
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
                'bank_name' => 'nullable|max:100',
                'created_at' => 'nullable|date',
                'attachment' => 'nullable||file|max:25600',
            ]);
            $createDate = $r->created_at ? Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')) : Carbon::now();

            $withdrawal->src_id=$r->payment;
            $withdrawal->payment_method_id=$r->payment;
            $withdrawal->billing_note=$r->description;
            $withdrawal->billing_reason=$r->bank_name;
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
        $accounts = Attribute::latest()->where('type',10)->where('status','<>','temp')
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

        // ✅ ADD CALCULATED BALANCE FOR EACH ACCOUNT
        $accounts->getCollection()->transform(function ($account) {
            $currentBalance = Transaction::where('account_id', $account->id)
                ->where('status','success')
                ->selectRaw("
                    SUM(
                        CASE
                            WHEN type IN (0,1) THEN amount
                            WHEN type IN (3,4,5,6,7) THEN -amount
                            ELSE 0
                        END
                    ) as balance
                ")
                ->value('balance') ?? 0;

            $account->current_balance = $currentBalance;
            return $account;
        });

        $adminUsers = User::where('admin',true)->orWhere('status',1)->orderBy('name')->select(['id','name','mobile'])->get();

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
        // dd($id);
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
            $method->addedby_id =$r->account_owner;
            $method->created_at =$r->created_at?:Carbon::now();
            $method->save();

            Session()->flash('success','Your Are Successfully Updated');
            return redirect()->back();

        }

        // Update Department Action End

        if($action == 'daily-account-summaryX')
        {
            $fromDate = $r->from_date ? Carbon::parse($r->from_date)->startOfDay() : Carbon::now()->startOfDay();

            // 1️⃣ Cash in Hand (opening balance)
            // "form date er ager din" er balance
            $openingBalance = Transaction::where('status', 'success')
                                ->where('account_id', $method->id)
                                ->where('created_at', '<', $fromDate)
                                ->sum('amount'); // sum of all deposits/fund transfers before this date

            // 2️⃣ Fund Transfer (from form date)
            $fundTransfer = Transaction::where('type', 1) // deposit/fund transfer
                                ->where('status', 'success')
                                ->where('account_id', $method->id)
                                ->whereBetween('created_at', [$fromDate, $fromDate->copy()->endOfDay()])
                                ->sum('amount');

            // 3️⃣ Adjust IOU (Completed IOU)
            $adjustIou = ExpenseIou::where('status','completed')
                            ->where('account_id', $method->id)
                            ->whereBetween('created_at', [$fromDate, $fromDate->copy()->endOfDay()])
                            ->sum('amount');

            // 4️⃣ TOTAL
            $total = $openingBalance + $fundTransfer + $adjustIou;

            // 5️⃣ Total Expense
            $totalExpense = Expense::where('status','active')
                                ->where('account_id', $method->id)
                                ->whereBetween('created_at', [$fromDate, $fromDate->copy()->endOfDay()])
                                ->sum('amount');

            // 6️⃣ Now Balance
            $nowBalance = $total - $totalExpense;
            // dd($nowBalance);

            return view(adminTheme().'accounts.daily-account-summary', compact(
                'fromDate',
                'openingBalance',
                'fundTransfer',
                'adjustIou',
                'total',
                'totalExpense',
                'nowBalance',
                'method'
            ));
        }

        if($action == 'daily-account-summary')
        {
            $fromDate = $r->from_date ? Carbon::parse($r->from_date)->startOfDay() : Carbon::now()->startOfDay();

            // 1️⃣ Opening balance (সব transactions এর sum before this date)
            $openingBalance = Transaction::where('status', 'success')
                                        ->where('account_id', $method->id)
                                        ->where('created_at', '<', $fromDate)
                                        ->selectRaw("
                                            SUM(
                                                CASE
                                                    WHEN type IN (0,1) THEN amount
                                                    WHEN type IN (3,4,5,6,7) THEN -amount
                                                    ELSE 0
                                                END
                                            ) as balance
                                        ")
                                        ->value('balance') ?? 0;

            // 2️⃣ Today এর transactions
            $transections = Transaction::where('status', 'success')
                                        ->where('account_id', $method->id)
                                        ->whereBetween('created_at', [$fromDate, $fromDate->copy()->endOfDay()])
                                        ->whereIn('type', [0,1,3,4,5,6,7])
                                        ->get();

            // 3️⃣ Calculate totals
            $creditTotal = 0;  // Type 0,1
            $debitTotal = 0;   // Type 3,4,5,6,7

            foreach($transections as $t) {
                if(in_array($t->type, [0,1])) {
                    $creditTotal += $t->amount;
                } else {
                    $debitTotal += $t->amount;
                }
            }

            // 4️⃣ Final calculation
            $total = $openingBalance + $creditTotal;
            $nowBalance = $total - $debitTotal;

            return view(adminTheme().'accounts.daily-account-summary', compact(
                'fromDate',
                'openingBalance',
                'creditTotal',
                'debitTotal',
                'total',
                'nowBalance',
                'method'
            ));
        }

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

        $openingBalance = Transaction::where('account_id', $method->id)
                            ->whereDate('created_at', '<', $from)
                            ->where('status','success')
                            ->selectRaw("
                                SUM(
                                    CASE
                                        WHEN type IN (0,1) THEN amount
                                        WHEN type IN (3,4,5,6,7) THEN -amount
                                        ELSE 0
                                    END
                                ) as balance
                            ")
                            ->value('balance') ?? 0;

        //$transections =Transaction::latest()->whereDate('created_at','>=',$from)->where('payment_method_id',$method->id)->whereDate('created_at','<=',$to)->whereIn('type',[0,1,2,3,4])->get();
        $transections = Transaction::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            // ->where('payment_method_id', $method->id)
            ->where('account_id', $method->id)
            ->where('status','success')
            //->where('type',1)
            ->whereIn('type', [0,1,3,4,5,6,7])
            ->orderBy('created_at')
            ->get();

            $balance = $openingBalance;
            $transections->map(function ($t) use (&$balance) {
                if (in_array($t->type, [0,1])) {
                    $balance += $t->amount;
                } else {
                    $balance -= $t->amount;
                }
                $t->running_balance = $balance;
                return $t;
            });
            $availableBalance = $balance;

        return view(adminTheme().'accounts.accountsMethodsView',compact('method','openingBalance','availableBalance','transections','from','to'));


    }

    public function accountsStatement(Request $r) {
        $user = Auth::user();

        // accounts fetch
        $accounts = Attribute::with('user')
            ->where('type', 10)
            ->where('status', 'active')
            ->orderBy('name')
            ->select(['id', 'name', 'amount'])
            ->get();

        $firstAccount = $accounts->first();
        $accountId = $r->account_id ?? $firstAccount?->id;

        $from = $r->startDate ? Carbon::parse($r->startDate) : Carbon::now()->subDays(30);
        $to = $r->endDate ? Carbon::parse($r->endDate) : Carbon::now();

        $method = Attribute::with('user')->where('type', 10)->find($accountId);

        $openingBalance = 0;
        $debetTotal = 0;
        $creditTotal = 0;
        $transections = collect();

        if ($method) {
            // Opening Balance
            $openingBalance = Transaction::where('account_id', $method->id)
                ->where('status', 'success')
                ->whereDate('created_at', '<', $from)
                ->selectRaw("
                    SUM(
                        CASE
                            WHEN type IN (0,1) THEN amount
                            WHEN type IN (3,4,5,6,7) THEN -amount
                            ELSE 0
                        END
                    ) as balance
                ")->value('balance') ?? 0;

            // (A) Normal transactions (created_at)
            $normalTransQuery = Transaction::where('account_id', $method->id)
                ->where('status', 'success')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->whereIn('type', [0,1,3,4,5,6,7]);
            $normalTrans = $normalTransQuery->get();

            // (B) IOU refund transactions (updated_at)
            $refundTransQuery = Transaction::where('account_id', $method->id)
                ->where('type', 7)
                ->where('status', 'refund')
                ->whereDate('updated_at', '>=', $from)
                ->whereDate('updated_at', '<=', $to);
            $refundTrans = $refundTransQuery->get();

            // Merge & unique
            $transactions = $normalTrans->merge($refundTrans)->unique('id');

            // Sort
            $transactions = $transactions->sortBy(function($t) {
                return ($t->type == 7 && strtolower($t->status) == 'refund')
                    ? ($t->updated_at ?? $t->created_at)
                    : $t->created_at;
            })->values();

            $runningBalance = $openingBalance;

            foreach ($transactions as $tran) {
                // Reference
                switch($tran->type) {
                    case 0: $reference = 'Sales'; break;
                    case 1: $reference = 'Deposit'; break;
                    case 3: $reference = 'Creditor Bill'; break;
                    case 4: $reference = 'Transfer Balance'; break;
                    case 5: $reference = 'Expense'; break;
                    case 6: $reference = 'Withdrawal'; break;
                    case 7: $reference = ($tran->status === 'refund' || strtolower($tran->status) === 'refund') ? 'I.O.U' : 'I.O.U'; break;
                    default: $reference = 'Unknown'; break;
                }

                // -------- Particulars Generator (maximum info) ----------
                $particulars = '';
                if ($tran->type == 0) { // Sales
                    $particulars = "Customer: " . ($tran->sale->name ?? 'N/A');
                } elseif ($tran->type == 1) { // Deposit
                    $particulars = "Deposit";
                } elseif ($tran->type == 3) { // Creditor Bill
                    $particulars = "Invoice: " . ($tran->purchase->order_no ?? ($tran->transection_id ?? 'N/A'));
                    if ($tran->billing_name)   $particulars .= " | Name: {$tran->billing_name}";
                    if ($tran->payment_method) $particulars .= " | Method: {$tran->payment_method}";
                    if ($tran->billing_note)   $particulars .= " | Note: {$tran->billing_note}";
                } elseif ($tran->type == 4) { // Transfer
                    $particulars = "Transfer";
                } elseif ($tran->type == 5) { // Expense
                    if ($tran->expense) {
                        $particulars = "Company: {$tran->expense->company_name} | Receiver: {$tran->expense->receiver_name}";
                        if ($tran->expense->description) $particulars .= " | Desc: {$tran->expense->description}";
                    } else {
                        $particulars = "Expense";
                    }
                } elseif ($tran->type == 6) { // Withdrawal
                    $particulars = "Withdraw";
                } elseif ($tran->type == 7) { // IOU
                    if ($tran->expenseIou) {
                        $particulars = "Company: {$tran->expenseIou->company_name} | Receiver: {$tran->expenseIou->receiver_name}";
                        if ($tran->expenseIou->description) $particulars .= " | Desc: {$tran->expenseIou->description}";
                    } else {
                        $particulars = "I.O.U";
                    }
                } else {
                    $particulars = "Txn#{$tran->transection_id}";
                }
                // সকল টাইপেই, যদি এগুলো থাকে তাহলে যোগ করে দাও (except 3, কারণ ওখানে উপরে ডিটেইল হয়েছে)
                $extras = [];
                if ($tran->transection_id && $tran->type != 3) $extras[] = "Txn#: {$tran->transection_id}";
                if ($tran->billing_name && $tran->type != 3)   $extras[] = "Name: {$tran->billing_name}";
                if ($tran->payment_method && $tran->type != 3) $extras[] = "Method: {$tran->payment_method}";
                if ($tran->billing_note && $tran->type != 3)   $extras[] = "Note: {$tran->billing_note}";
                if (!empty($extras)) $particulars .= ' | ' . implode(' | ', $extras);

                // -------- IOU refund: 2 Rows Setup ---------
                if ($tran->type == 7 && strtolower($tran->status) == 'refund') {
                    // (A) refund credit row
                    $creditTran = clone $tran;
                    $creditTran->transaction_direction = 'credit';
                    $creditTran->reference = $reference;
                    $creditTran->particulars = $particulars." (I.O.U Refund)";
                    $creditTran->created_at = $tran->created_at;
                    $creditTran->running_balance = $runningBalance + $tran->amount;
                    $creditTotal += $tran->amount;
                    $runningBalance = $creditTran->running_balance;
                    $transections->push($creditTran);

                    // (B) IOU Adjustment row
                    $debitTran = clone $tran;
                    $debitTran->transaction_direction = 'debit';
                    $debitTran->reference = 'I.O.U Adjustment';
                    $debitTran->particulars = $particulars." (I.O.U Adjustment)";
                    $debitTran->created_at = $tran->updated_at;
                    $debitTran->running_balance = $runningBalance - $tran->amount;
                    $debetTotal += $tran->amount;
                    $runningBalance = $debitTran->running_balance;
                    $transections->push($debitTran);
                }
                //--------- Regular (Debit/Credit) Row ---------
                else {
                    if (in_array($tran->type, [0,1])) {
                        $tran->transaction_direction = 'credit';
                        $creditTotal += $tran->amount;
                        $tran->running_balance = $runningBalance + $tran->amount;
                        $runningBalance = $tran->running_balance;
                    } else {
                        $tran->transaction_direction = 'debit';
                        $debetTotal += $tran->amount;
                        $tran->running_balance = $runningBalance - $tran->amount;
                        $runningBalance = $tran->running_balance;
                    }
                    $tran->reference = $reference;
                    $tran->particulars = $particulars;
                    $transections->push($tran);
                }
            }

            // Final sort by created_at for best order (refund adjustment included)
            $transections = $transections->sortBy(function($item) {
                return $item->created_at instanceof \Carbon\Carbon ? $item->created_at->timestamp : strtotime($item->created_at);
            })->values();
        }

        return view(
            adminTheme().'accounts.accountsStatement',
            compact(
                'accounts',
                'method',
                'openingBalance',
                'transections',
                'from',
                'to',
                'debetTotal',
                'creditTotal'
            )
        );
    }

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

    //Branch Function

    public function branchs(Request $r){


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

              $medias =Media::latest()->where('src_type',0)->where('src_id',$data->id)->get();
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

    //Branch Function

    public function floorLines(Request $r){

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

      $lines=Attribute::where('type',4)->where('status','<>','temp')
        ->where(function($q) use ($r) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status);
          }

      })
      ->orderBy('slug')
      ->select(['id','name','slug','type','description','capacity','created_at','addedby_id','status'])
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      //Total Count Results
      $total = DB::table('attributes')->where('status','<>','temp')
      ->where('type',4)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'floor-lines.index',compact('lines','total'));

    }

    public function floorLinesAction(Request $r,$action,$id=null){
      // Add Department Action Start
      if($action=='create'){

        $check = $r->validate([
            'floor' => 'required|max:100',
            'line' => 'required|max:100',
            'capacity' => 'nullable|numeric',
        ]);

        $line =Attribute::where('type',4)->where('slug',$r->line)->first();
        if($line){
            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();
        }
        $line =new Attribute();
        $line->name=$r->floor;
        $line->slug=$r->line;
        $line->capacity=$r->capacity?:0;
        $line->type =4;
        $line->status ='active';
        $line->addedby_id =Auth::id();
        $line->save();

        Session()->flash('success','Your Are Successfully Added');
        return redirect()->back();

      }

      // Add Department Action End


      $line =Attribute::where('type',4)->find($id);
      if(!$line){
        Session()->flash('error','This Branch Are Not Found');
        return redirect()->route('admin.branchs');
      }

      // Update Department Action Start
      if($action=='update'){

        $check = $r->validate([
            'floor' => 'required|max:100',
            'line' => 'required|max:100',
            'created_at' => 'required|date',
            'capacity' => 'nullable|numeric',
        ]);
        $hasLine =Attribute::where('type',4)->where('id','<>',$line->id)->where('slug',$r->line)->first();
        if($hasLine){
            Session()->flash('error','Floor name already have');
            return redirect()->back();
        }
        $line->name=$r->floor;
        $line->slug=$r->line;
        $line->capacity=$r->capacity?:0;

        $createDate =$r->created_at?Carbon::parse($r->created_at . ' ' . Carbon::now()->format('H:i:s')):Carbon::now();
        if(!$line->created_at->isSameDay($createDate)){
        $line->created_at =$createDate;
        }
        $line->status =$r->status?'active':'inactive';
        $line->fetured =$r->fetured?1:0;
        $line->editedby_id =Auth::id();
        $line->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update Department Action End


      // Delete Department Action Start
      if($action=='delete'){
        $line->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.floorLines');

      }
      // Delete Department Action End
      return redirect()->back();

    }


    public function themeSetting(Request $r){
      return view(adminTheme().'theme-setting.themeSetting');
    }



    // Staff Management Function Start
    /* =====================================================
     | Staff Actions
     ===================================================== */


    public function staffAdmin(Request $r)
    {
        /* ================= Bulk Actions ================= */
        if ($r->action) {
            if (!$r->checkid) {
                Session()->flash('info','Please select at least one item');
                return redirect()->back();
            }

            $users = User::withTrashed()->filterByType('staff')->whereIn('id',$r->checkid)->get();

            foreach ($users as $user) {
                switch ($r->action) {
                    case 1: $user->status = 1; $user->save(); break;
                    case 2: $user->status = 0; $user->save(); break;
                    case 3: $user->fetured = true; $user->save(); break;
                    case 4: $user->fetured = false; $user->save(); break;
                    case 5: // Soft Delete
                        if (!$user->trashed()) {
                            $user->deleted_at = Carbon::now();
                            $user->deleted_by = Auth::id();
                            $user->save();
                        }
                        break;
                    case 6: if ($user->trashed()) $user->restore(); break;
                    case 7: // Force Delete
                        if ($user->trashed()) {
                            deleteUserFiles($user->id);
                            $user->forceDelete();
                        }
                        break;
                }
            }

            Session()->flash('success','Bulk action completed successfully!');
            return redirect()->back();
        }
        /* ================= Bulk Actions End ================= */


        /* ================= Staff List ================= */
        $users = User::latest()
            ->whereIn('status',[0,1])
            ->filterByType('staff')
            ->where(function ($q) use ($r) {
                if ($r->search) {
                    $q->where('name','LIKE',"%{$r->search}%")
                    ->orWhere('mobile','LIKE',"%{$r->search}%")
                    ->orWhere('email','LIKE',"%{$r->search}%");
                }
            })
            ->paginate(12)
            ->appends($r->all());

        $totals = User::withTrashed()->filterByType('staff')
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status=1 then 1 end) as active")
            ->selectRaw("count(case when status=0 then 1 end) as inactive")
            ->selectRaw("count(case when deleted_at IS NOT NULL then 1 end) as deleted")
            ->first();

                    /* ================= Trash View ================= */
        if ($r->view == 'deleted') {
            $users = User::onlyTrashed()->filterByType('staff')->latest()->paginate(12);
            return view(adminTheme().'users.staff.users_deleted', compact('users','totals'));
        }

        return view(adminTheme().'users.staff.users', compact('users','totals'));
    }

    public function staffAdminAction(Request $r, $action, $id = null)
    {
        /* ================= CREATE STAFF ================= */
        if ($action == 'create' && $r->isMethod('post')) {
            $r->validate([
                'name'   => 'required|string|max:255',
                'mobile' => 'required|digits:11|regex:/^0[0-9]{10}$/',
                'email'  => 'nullable|email|max:255',
            ]);

            // Active check
            $activeQuery = User::where('mobile',$r->mobile);
            if ($r->email) $activeQuery->orWhere('email',$r->email);

            if ($activeQuery->exists()) {
                Session()->flash('info','Email or Mobile already exists');
                return redirect()->back();
            }

            // Trash check
            $trashQuery = User::onlyTrashed()->where('mobile',$r->mobile);
            if ($r->email) $trashQuery->orWhere('email',$r->email);

            if ($trashQuery->exists()) {
                Session()->flash('info','This email or mobile exists in trash');
                return redirect()->back();
            }

            $password = Str::random(8);
            $user = new User();
            $user->name = $r->name;
            $user->mobile = $r->mobile;
            $user->email = $r->email ?? null;
            $user->password_show = $password;
            $user->password = Hash::make($password);
            $user->setTypes('staff');
            $user->addedby_id = Auth::id();
            $user->addedby_at = Carbon::now();
            $user->save();

            Session()->flash('success','Staff created successfully!');
            return redirect()->route('admin.staffAdminAction',['edit',$user->id]);
        }
        /* ================= CREATE END ================= */

        /* ================= RESTORE ================= */
        if ($action == 'restore') {
            $user = User::onlyTrashed()->filterByType('staff')->find($id);
            if (!$user) {
                Session()->flash('error','Staff not found in trash');
                return redirect()->route('admin.staffAdmin',['view'=>'deleted']);
            }
            $user->restore();
            Session()->flash('success','Staff restored successfully!');
            return redirect()->route('admin.staffAdmin');
        }

        /* ================= FORCE DELETE ================= */
        if ($action == 'force-delete') {
            $user = User::onlyTrashed()->filterByType('staff')->find($id);
            if (!$user) {
                Session()->flash('error','Staff not found in trash');
                return redirect()->route('admin.staffAdmin',['view'=>'deleted']);
            }
            deleteUserFiles($user->id);
            $user->forceDelete();
            Session()->flash('success','Staff permanently deleted!');
            return redirect()->route('admin.staffAdmin',['view'=>'deleted']);
        }

        /* ================= FIND STAFF ================= */
        $user = User::filterByType('staff')->find($id);
        if (!$user) {
            Session()->flash('error','Staff user not found');
            return redirect()->route('admin.staffAdmin');
        }

        /* ================= UPDATE ================= */
        if ($action == 'update' && $r->isMethod('post')) {
            $r->validate([
                'name'   => 'required|max:100|unique:users,name,'.$user->id,
                'email'  => 'nullable|unique:users,email,'.$user->id,
                'mobile' => 'nullable|unique:users,mobile,'.$user->id,
            ]);

            $user->name = $r->name;
            $user->email = $r->email;
            $user->mobile = $r->mobile;
            $user->status = $r->status ? 1 : 0;
            $user->setTypes('staff');

            if ($r->hasFile('image')) {
                uploadFile($r->image,$user->id,6,1,Auth::id());
            }

            $user->save();
            Session()->flash('success','Staff updated successfully!');
            return redirect()->back();
        }

        /* ================= PASSWORD ================= */
        if ($action == 'change-password' && $r->isMethod('post')) {
            $r->validate([
                'old_password' => 'required|min:8',
                'password' => 'required|min:8|confirmed|different:old_password',
            ]);

            if (!Hash::check($r->old_password,$user->password)) {
                Session()->flash('error','Old password not match');
                return redirect()->back();
            }

            $user->password_show = $r->password;
            $user->password = Hash::make($r->password);
            $user->save();

            Session()->flash('success','Password updated successfully!');
            return redirect()->back();
        }

        /* ================= SOFT DELETE ================= */
        if ($action == 'delete') {
            $user->deleted_at = Carbon::now();
            $user->deleted_by = Auth::id();
            $user->save();
            Session()->flash('success','Staff deleted successfully!');
            return redirect()->route('admin.staffAdmin');
        }

        if ($action == 'view') {
            return view(adminTheme().'users.staff.viewUser', compact('user','action'));
        }
        $roles = Permission::where('status','active')->get();

        return view(adminTheme().'users.staff.editUser', compact('user', 'roles'));
    }


    // merchandisers Management Function Start
    // Merchandisers List + Bulk Actions
    public function merchandisers(Request $r)
    {
        // Bulk Actions
        if ($r->action) {
            if ($r->checkid) {
                $datas = User::latest()->filterByType('merchandiser')
                    ->whereIn('status', [0, 1])
                    ->whereIn('id', $r->checkid)
                    ->get();

                foreach ($datas as $data) {
                    switch ($r->action) {
                        case 1:
                            $data->status = 1;
                            $data->save();
                            break;
                        case 2:
                            $data->status = 0;
                            $data->save();
                            break;
                        case 3:
                            $data->fetured = true;
                            $data->save();
                            break;
                        case 4:
                            $data->fetured = false;
                            $data->save();
                            break;
                        case 5:
                            // Soft Delete
                            $data->merchandiser = false;
                            $data->addedby_at = null;
                            $data->permission_id = null;
                            $data->addedby_id = null;
                            $data->save();
                            $data->delete(); // soft delete
                            break;
                    }
                }

                Session()->flash('success', 'Action Successfully Completed!');
            } else {
                Session()->flash('info', 'Please select at least one user');
            }
            return redirect()->back();
        }

        // Filter & Search
        $usersQuery = User::latest()->filterByType('merchandiser')
            ->whereIn('status', [0, 1])
            ->where('merchandiser', true)
            ->where(function ($q) use ($r) {
                if ($r->search) {
                    $q->where('name', 'LIKE', '%' . $r->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $r->search . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $r->search . '%');
                }
                if ($r->role) {
                    $q->where('permission_id', $r->role);
                }
                if ($r->startDate || $r->endDate) {
                    $from = $r->startDate ?? Carbon::now()->format('Y-m-d');
                    $to = $r->endDate ?? Carbon::now()->format('Y-m-d');
                    $q->whereDate('addedby_at', '>=', $from)
                    ->whereDate('addedby_at', '<=', $to);
                }
            });

        // Show soft-deleted if requested
        if ($r->view == 'deleted') {
            $usersQuery->onlyTrashed()->with('deletedBy');
        }

        $users = $usersQuery->select([
                'id','permission_id','name','email','mobile','addedby_at','addedby_id','status','created_at','employee_id', 'deleted_at', 'deleted_by'
            ])
            ->paginate(12)
            ->appends($r->all());

        $totals = User::withTrashed()->filterByType('merchandiser')
            ->whereIn('status', [0,1])
        ->selectRaw('count(*) as total')
        ->selectRaw("count(case when status = 1 then 1 end) as active")
        ->selectRaw("count(case when status = 0 then 1 end) as inactive")
        ->selectRaw("count(case when deleted_at IS NOT NULL then 1 end) as deleted")
        ->first();

        $roles = Permission::latest()->where('status','active')->get();

        if ($r->view == 'deleted') {
            return view(adminTheme().'users.merchandisers.users_deleted', compact('users','totals','roles'));
        }

            return view(adminTheme().'users.merchandisers.users', compact('users','totals','roles'));
    }

    // Merchandisers Action (Create, Edit, Update, Password, Delete, Restore, Force Delete)
    public function merchandisersAction(Request $r, $action, $id=null)
    {
        // CREATE MERCHANDISER
        if ($action == 'create' && $r->isMethod('post')) {
            $r->validate([
                'name'   => 'required|string|max:255',
                'mobile' => 'required|digits:11|regex:/^0[0-9]{10}$/',
                'email'  => 'nullable|email|max:255',
            ], [
                'name.required'   => 'Name is required.',
                'mobile.required' => 'Mobile is required.',
                'mobile.digits'   => 'Mobile must be 11 digits.',
                'mobile.regex'    => 'Mobile must start with 0.',
                'email.email'     => 'Enter valid email.'
            ]);

            $existsUser = User::where(function ($q) use ($r) {

                    if (!empty($r->email)) {
                        $q->where('email', $r->email);
                    }

                    if (!empty($r->mobile)) {
                        $q->orWhere('mobile', $r->mobile);
                    }

                })->first();

            if ($existsUser) {
                if($r->has('api')){
                    return response()->json([
                        'success'          => false,
                        'msg'              => "his email or mobile alrady used.",
                        'merchant_created' => false,
                    ]);
                }
                Session()->flash('error','This email or mobile alrady used.');
                return redirect()->back();
            }

            if(!$existsUser){
                $password = Str::random(8);
                $user                = new User();
                $user->name          = $r->name;
                $user->mobile        = $r->mobile;
                $user->email         = $r->email ?? null;
                $user->password_show = $password;
                $user->password      = Hash::make($password);

                  // Default flags
                $user->setTypes('merchandiser');

                $user->addedby_id = Auth::id();
                $user->addedby_at = Carbon::now();
                $user->save();
            }

            if($r->has('api')){
                return response()->json([
                    'success'          => true,
                    'msg'              => "Merchant registered successfully",
                    'merchant_created' => true,
                    'id'               => $user->id,
                    'name'             => $user->name,
                ]);
            }

            Session()->flash('success','Merchandiser created successfully!');
            return redirect()->route('admin.merchandisersAction',['edit',$user->id]);
        }

        // Fetch User (active merchandiser)
        $user = User::whereIn('status',[0,1])->where('merchandiser',true)->find($id);

        // Handle Restore & Force Delete (soft-deleted users)
        if ($action == 'restore') {
            $user = User::onlyTrashed()->find($id);
            if(!$user){
                Session()->flash('error','User not found or already restored!');
                return redirect()->back();
            }
            $user->restore();
            Session()->flash('success','User restored successfully!');
            return redirect()->back();
        }

        if ($action == 'force-delete') {
            $user = User::onlyTrashed()->find($id);
            // TODO: Delete media if needed
            $user->forceDelete();
            Session()->flash('success','User permanently deleted!');
            return redirect()->back();
        }

        if(!$user){
            Session()->flash('error','User not found.');
            return redirect()->route('admin.merchandisers');
        }

        // UPDATE MERCHANDISER
        if ($action == 'update' && $r->isMethod('post')) {
            $r->validate([
                'name'        => 'required|max:100|unique:users,name,'.$user->id,
                'email'       => 'nullable|max:100|unique:users,email,'.$user->id,
                'mobile'      => 'required|max:20|unique:users,mobile,'.$user->id,
                'gender'      => 'nullable|max:10',
                'address'     => 'nullable|max:191',
                'division'    => 'nullable|numeric',
                'district'    => 'nullable|max:191',
                'city'        => 'nullable|max:191',
                'postal_code' => 'nullable|max:20',
                'role'        => 'nullable|numeric',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user->name          = $r->name;
            $user->mobile        = $r->mobile;
            $user->email         = $r->email;
            $user->gender        = $r->gender;
            $user->address_line1 = $r->address;
            $user->division      = $r->division;
            $user->district      = $r->district;
            $user->city          = $r->city;
            $user->postal_code   = $r->postal_code;
            $user->permission_id = $r->role;
            $user->setTypes('merchandiser');


            if($r->hasFile('image')){
                $file = $r->image;
                $src  = $user->id;
                $srcType  = 6;
                $fileUse  = 1;
                $author=Auth::id();
                uploadFile($file,$src,$srcType,$fileUse,$author);
            }

            $user->status = $r->status?true:false;
            $user->save();

            Session()->flash('success','User updated successfully!');
            return redirect()->route('admin.merchandisersAction',['edit',$user->id]);
        }

        // CHANGE PASSWORD
        if($action=='change-password' && $r->isMethod('post')){
            $validator = Validator::make($r->all(), [
                'old_password'=>'required|string|min:8',
                'password'=>'required|string|min:8|confirmed|different:old_password',
            ]);
            if($validator->fails()){
                return redirect()->route('admin.merchandisersAction',['edit',$user->id])->withErrors($validator)->withInput();
            }
            if(Hash::check($r->old_password,$user->password)){
                $user->password_show=$r->password;
                $user->password=Hash::make($r->password);
                $user->update();
                Session()->flash('success','Password changed successfully!');
            }else{
                Session()->flash('error','Current password does not match!');
            }
            return redirect()->route('admin.merchandisersAction',['edit',$user->id]);
        }

        // SOFT DELETE
        if($action=='delete'){
            $user->deleted_at = now();
            $user->deleted_by = Auth::id();
            $user->save();
            $user->delete();
            Session()->flash('success','Merchandiser soft-deleted successfully!');
            return redirect()->route('admin.merchandisers');
        }

        $roles = Permission::latest()->where('status','active')->get();
        return view(adminTheme().'users.merchandisers.editUser',compact('user','roles'));
    }


    // User Management Function Start
    /* =========================================================
     | Admin User Actions
     ========================================================= */
    public function usersAdmin(Request $r)
    {
        /* ================= Bulk Actions ================= */
        if ($r->action) {

            if (!$r->checkid) {
                Session()->flash('info', 'Please select at least one item');
                return redirect()->back();
            }

            $users = User::whereIn('id', $r->checkid)
                ->filterByType('admin')
                ->withTrashed()
                ->get();

            foreach ($users as $user) {

                switch ($r->action) {

                    case 1: // Active
                        $user->status = 1;
                        $user->save();
                        break;

                    case 2: // Inactive
                        $user->status = 0;
                        $user->save();
                        break;

                    case 3: // Featured
                        $user->fetured = true;
                        $user->save();
                        break;

                    case 4: // Unfeatured
                        $user->fetured = false;
                        $user->save();
                        break;

                    case 5: // Soft Delete
                        if (!$user->trashed()) {
                            $user->deleted_at = Carbon::now();
                            $user->deleted_by = Auth::id();
                            $user->save();
                        }
                        break;

                    case 6: // Restore
                        if ($user->trashed()) {
                            $user->restore();
                        }
                        break;

                    case 7: // Force Delete
                        if ($user->trashed()) {
                            deleteUserFiles($user->id);
                            $user->forceDelete();
                        }
                        break;
                }
            }

            Session()->flash('success', 'Bulk action completed successfully!');
            return redirect()->back();
        }
        /* ================= Bulk Actions End ================= */

        /* ================= Admin List ================= */
        $users = User::latest()
            ->whereIn('status', [0,1])
            ->filterByType('admin')
            ->where(function ($q) use ($r) {

                if ($r->search) {
                    $q->where('name','LIKE',"%{$r->search}%")
                      ->orWhere('mobile','LIKE',"%{$r->search}%")
                      ->orWhere('email','LIKE',"%{$r->search}%");
                }

                if ($r->role) {
                    $q->where('permission_id',$r->role);
                }

                if ($r->startDate || $r->endDate) {

                    $from = $r->startDate ?? Carbon::now()->format('Y-m-d');
                    $to   = $r->endDate   ?? Carbon::now()->format('Y-m-d');

                    $q->whereDate('addedby_at','>=',$from)
                      ->whereDate('addedby_at','<=',$to);
                }
            })
            ->paginate(12)
            ->appends($r->all());


        $totals = User::withTrashed()->filterByType('admin')
            ->whereIn('status',[0,1])
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status=1 then 1 end) as active")
            ->selectRaw("count(case when status=0 then 1 end) as inactive")
            ->selectRaw("count(case when deleted_at IS NOT NULL then 1 end) as deleted")
            ->first();

        $roles = Permission::where('status','active')->get();

        if ($r->view == 'deleted') {

            $users = User::onlyTrashed()
                ->filterByType('admin')
                ->latest()
                ->paginate(12)->appends($r->all());;

            return view(adminTheme().'users.admins.users_deleted', compact('users','totals','roles'));
        }

        return view(adminTheme().'users.admins.users', compact('users','totals','roles'));
    }

    public function usersAdminAction(Request $r, $action, $id = null)
    {
        /* ================= CREATE ADMIN ================= */
        if ($action == 'create' && $r->isMethod('post')) {

            $r->validate([
                'name'   => 'required|string|max:255',
                'mobile' => 'required|digits:11|regex:/^0[0-9]{10}$/',
                'email'  => 'nullable|email|max:255',
            ]);

            // Active check
            $activeQuery = User::where('mobile',$r->mobile);
            if ($r->email) {
                $activeQuery->orWhere('email',$r->email);
            }

            if ($activeQuery->exists()) {
                Session()->flash('error','Email or Mobile already exists');
                return redirect()->back();
            }

            // Trash check
            $trashQuery = User::onlyTrashed()->where('mobile',$r->mobile);
            if ($r->email) {
                $trashQuery->orWhere('email',$r->email);
            }

            if ($trashQuery->exists()) {
                Session()->flash('error','This email or mobile exists in trash');
                return redirect()->back();
            }

            $password = Str::random(8);

            $user = new User();
            $user->name          = $r->name;
            $user->mobile        = $r->mobile;
            $user->email         = $r->email ?? null;
            $user->password_show = $password;
            $user->password      = Hash::make($password);
            $user->permission_id = 1;
            $user->addedby_at    = Carbon::now();
            $user->addedby_id    = Auth::id();

            $user->setTypes('admin');
            $user->save();

            Session()->flash('success','Admin created successfully!');
            return redirect()->route('admin.usersAdminAction',['edit',$user->id]);
        }
        /* ================= CREATE END ================= */


        /* ================= RESTORE ================= */
        if ($action == 'restore') {

            $user = User::onlyTrashed()->find($id);

            if (!$user) {
                Session()->flash('error','Admin not found in trash');
                return redirect()->route('admin.usersAdmin',['view'=>'deleted']);
            }

            $user->restore();
            Session()->flash('success','Admin restored successfully!');
            return redirect()->route('admin.usersAdmin');
        }


        /* ================= FORCE DELETE ================= */
        if ($action == 'force-delete') {

            $user = User::onlyTrashed()->find($id);

            if (!$user) {
                Session()->flash('error','Admin not found in trash');
                return redirect()->route('admin.usersAdmin',['view'=>'deleted']);
            }

            deleteUserFiles($user->id);
            $user->forceDelete();

            Session()->flash('success','Admin permanently deleted!');
            return redirect()->route('admin.usersAdmin',['view'=>'deleted']);
        }


        /* ================= FIND USER ================= */
        $user = User::filterByType('admin')->find($id);

        if (!$user) {
            Session()->flash('error','Admin user not found');
            return redirect()->route('admin.usersAdmin');
        }


        /* ================= UPDATE ================= */
        if ($action == 'update' && $r->isMethod('post')) {

            $r->validate([
                'name'   => 'required|max:100|unique:users,name,'.$user->id,
                'email'  => 'nullable|unique:users,email,'.$user->id,
                'mobile' => 'nullable|unique:users,mobile,'.$user->id,
            ]);

            $user->name = $r->name;
            $user->email = $r->email;
            $user->mobile = $r->mobile;
            $user->status = $r->status ? 1 : 0;
            $user->permission_id = $r->role;

            $user->setTypes('admin');

            if ($r->hasFile('image')) {
                uploadFile($r->image,$user->id,6,1,Auth::id());
            }

            $user->save();

            Session()->flash('success','Admin updated successfully!');
            return redirect()->back();
        }


        /* ================= PASSWORD ================= */
        if ($action == 'change-password' && $r->isMethod('post')) {

            $r->validate([
                'old_password' => 'required|min:8',
                'password' => 'required|min:8|confirmed|different:old_password',
            ]);

            if (!Hash::check($r->old_password,$user->password)) {
                Session()->flash('error','Old password not match');
                return redirect()->back();
            }

            $user->password_show = $r->password;
            $user->password = Hash::make($r->password);
            $user->save();

            Session()->flash('success','Password updated successfully!');
            return redirect()->back();
        }


        /* ================= SOFT DELETE ================= */
        if ($action == 'delete') {

            $user->deleted_at = Carbon::now();
            $user->deleted_by = Auth::id();
            $user->save();

            Session()->flash('success','Admin deleted successfully!');
            return redirect()->route('admin.usersAdmin');
        }


        $roles = Permission::where('status','active')->get();

        if ($action == 'view') {
            return view(adminTheme().'users.admins.viewUser', compact('user','roles','action'));
        }

        return view(adminTheme().'users.admins.editUser', compact('user','roles'));
    }


    // Users Customer List + Bulk Actions
    public function usersCustomer(Request $r)
    {
        $departments = Attribute::latest()->filterBy('department')->where('status','<>','temp')->get(['id', 'name']);
        $designations = Attribute::latest()->filterBy('designation')->where('status','<>','temp')->get(['id', 'name']);
        $divisions = Attribute::latest()->filterBy('divisions')->where('status','<>','temp')->get(['id', 'name']);
        $sections = Attribute::latest()->filterBy('sections')->where('status','<>','temp')->get(['id', 'name']);
        $empTypes = Attribute::latest()->filterBy('employee_type')->where('status','<>','temp')->get(['id', 'name']);
        $shifts = Shift::latest()->get(['id', 'name_of_shift']);

        $departmentsMap = $departments->pluck('name', 'id');
        $designationsMap = $designations->pluck('name', 'id');
        $divisionsMap = $divisions->pluck('name', 'id');
        $sectionsMap = $sections->pluck('name', 'id');
        $empTypesMap = $empTypes->pluck('name', 'id');
        $shiftsMap = $shifts->pluck('name_of_shift', 'id');

        // Bulk Actions
        if ($r->action) {
            if ($r->checkid) {
                $datas = User::latest()
                    ->filterByType('customer')
                    ->whereIn('id', $r->checkid)
                    ->get();

                foreach ($datas as $data) {
                    switch ($r->action) {
                        case 1: // Activate
                            $data->status = 1;
                            $data->save();
                            break;
                        case 2: // Deactivate
                            $data->status = 0;
                            $data->save();
                            break;
                        case 5: // Soft Delete
                            // Delete user media
                            $userFiles = Media::where('src_type', 6)->where('src_id', $data->id)->get();
                            foreach ($userFiles as $media) {
                                if (File::exists($media->file_url)) {
                                    File::delete($media->file_url);
                                }
                                $media->delete();
                            }
                            $data->deleted_by = auth()->id();
                            $data->save();
                            $data->delete();
                            break;
                    }
                }

                Session()->flash('success', 'Action Successfully Completed!');
            } else {
                Session()->flash('info', 'Please select at least one user.');
            }

            return redirect()->back();
        }

        // Query Users
        $usersQuery = User::latest()->filterByType('customer')
            ->with(['permission', 'designation', 'department'])
            ->where(function ($q) use ($r) {
                if ($r->search) {
                    $q->where('name', 'LIKE', '%' . $r->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $r->search . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $r->search . '%')
                    ->orWhere('employee_id', 'LIKE', '%' . $r->search . '%');
                }
                if ($r->status) {
                    $q->where('status', $r->status == 'inactive' ? 0 : 1);
                }
                if ($r->role_id) {
                    $q->where('permission_id', $r->role_id);
                }
                if ($r->division_id) {
                    $q->where('division', $r->division_id);
                }
                if ($r->designation_id) {
                    $q->where('designation_id', $r->designation_id);
                }
                if ($r->department_id) {
                    $q->where('department_id', $r->department_id);
                }
                if ($r->section_id) {
                    $q->where('section_id', $r->section_id);
                }
                if ($r->shift_id) {
                    $q->where('shift_id', $r->shift_id);
                }
                if ($r->employee_type) {
                    $q->where('employee_type', $r->employee_type);
                }
                if ($r->startDate || $r->endDate) {
                    $from = $r->startDate ?? Carbon::now()->format('Y-m-d');
                    $to = $r->endDate ?? Carbon::now()->format('Y-m-d');
                    $q->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
                }
                if ($r->joining_start || $r->joining_end) {
                    $from = $r->joining_start ?? Carbon::now()->format('Y-m-d');
                    $to = $r->joining_end ?? Carbon::now()->format('Y-m-d');
                    $q->whereDate('joining_date', '>=', $from)
                    ->whereDate('joining_date', '<=', $to);
                }
            });

        // Show soft-deleted users if requested
        if ($r->view == 'deleted') {
            $usersQuery->onlyTrashed()->with('deletedBy');
        } else {
            $usersQuery->whereIn('status', [0, 1]);
        }

        $users = $usersQuery->select([
            'id','permission_id','name','email','employee_id','designation_id','department_id','section_id','shift_id','employee_type','division','joining_date','mobile','created_at','addedby_id','status','deleted_by','deleted_at'
        ])
        ->paginate(25)
        ->appends($r->all());
        // dd($users);

        $totals = User::withTrashed()->filterByType('customer')
            ->whereIn('status', [0,1])
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 1 then 1 end) as active")
            ->selectRaw("count(case when status = 0 then 1 end) as inactive")
            ->selectRaw("count(case when deleted_at IS NOT NULL then 1 end) as deleted")
            ->first();

        $roles = Permission::latest()->where('status','active')->get();

        if ($r->view == 'deleted') {
            return view(adminTheme() . 'users.customers.users_deleted', compact('users', 'totals', 'roles'));
        } else {
            return view(adminTheme() . 'users.customers.users', compact(
                'users',
                'totals',
                'roles',
                'departments',
                'designations',
                'divisions',
                'sections',
                'empTypes',
                'shifts',
                'departmentsMap',
                'designationsMap',
                'divisionsMap',
                'sectionsMap',
                'empTypesMap',
                'shiftsMap'
            ));
        }
    }


    // Users Customer Action (Create, Edit, Update, Password, Soft Delete, Restore, Force Delete)
    public function usersCustomerAction(Request $r, $action, $id = null)
    {
        try{

            $authId = auth()->id();
            // RESTORE SOFT DELETED USER
            if ($action == 'restore') {
                $user = User::onlyTrashed()->find($id);
                if (!$user) {
                    Session()->flash('error', 'User not found or already restored!');
                    return redirect()->back();
                }
                $user->restore();
                Session()->flash('success', 'User restored successfully!');
                return redirect()->back();
            }

            // FORCE DELETE USER
            if ($action == 'force-delete') {
                if ($id == $authId) {
                    Session()->flash('error', 'You cannot force delete yourself!');
                    return redirect()->back();
                }
                $user = User::onlyTrashed()->find($id);
                if ($user) {
                    $userFiles = Media::where('src_type', 6)->where('src_id', $user->id)->get();
                    foreach ($userFiles as $media) {
                        if (File::exists($media->file_url)) {
                            File::delete($media->file_url);
                        }
                        $media->delete();
                    }
                    $user->forceDelete();
                }
                Session()->flash('success', 'User permanently deleted!');
                return redirect()->back();
            }

            // CREATE USER
            if ($action == 'create' && $r->isMethod('post')) {

                $r->validate([
                    'name' => 'required|max:100',
                    'email' => [
                        'nullable',
                        'email',
                        'max:100',
                        Rule::unique('users', 'email')->whereNull('deleted_at'),
                        'required_without:mobile',
                    ],
                    'mobile' => [
                        'nullable',
                        'max:20',
                        Rule::unique('users', 'mobile')->whereNull('deleted_at'),
                        'required_without:email',
                    ],
                ]);

                $password = Str::random(8);
                $user = new User();
                $user->name          = $r->name;
                $user->mobile        = $r->mobile;
                $user->email         = $r->email;
                $user->password_show = $password;
                $user->password      = Hash::make($password);
                $user->status        = 1;

                $user->setTypes('customer'); // if you already have setTypes method
                $user->save();

                Session()->flash('success', 'User created successfully!');
                return redirect()->route('admin.usersCustomer');
            }


            $user = $action === 'employee-create'
                ? new User()
                : User::whereIn('status', [0,1])->find($id);

            if (!$user && !in_array($action, ['employee-create'])) {
                Session()->flash('error', 'User not found.');
                return redirect()->route('admin.usersCustomer');
            }



            // UPDATE USER PROFILE
            if ($action == 'update' && $r->isMethod('post')) {
                try {
                    // Minimal customer edit form sends only a few profile fields.
                    $isSimpleEdit = !$r->hasAny([
                        'designation_id', 'department_id', 'division_id', 'section_id',
                        'line_number', 'shift_id', 'employee_type', 'grade_lavel',
                        'gross_salary', 'basic_salary', 'house_rent', 'medical_allowance',
                        'transport_allowance', 'food_allowance', 'father_name', 'mother_name',
                        'present_village', 'permanent_village'
                    ]);

                    if ($isSimpleEdit) {
                        $r->validate([
                            'name' => 'required|max:100',
                            'bn_name' => 'nullable|max:100',
                            'employee_id' => [
                                'nullable',
                                'max:50',
                                Rule::unique('users', 'employee_id')->ignore($user->id)->whereNull('deleted_at'),
                            ],
                            'email' => [
                                'nullable',
                                'email',
                                'max:100',
                                Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at'),
                                'required_without:mobile',
                            ],
                            'mobile' => [
                                'nullable',
                                'max:20',
                                Rule::unique('users', 'mobile')->ignore($user->id)->whereNull('deleted_at'),
                                'required_without:email',
                            ],
                            'password' => 'nullable|min:6|max:100',
                            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,zip,rar,txt',
                        ]);

                        $user->employee_id = $r->employee_id;
                        $user->name = $r->name;
                        $user->bn_name = $r->bn_name;
                        $user->email = $r->email;
                        $user->mobile = $r->mobile;

                        if ($r->filled('password')) {
                            $user->password_show = $r->password;
                            $user->password = Hash::make($r->password);
                        }

                        $user->settypes('customer');
                        $user->save();

                        if ($r->hasFile('image')) {
                            uploadFile($r->image, $user->id, 6, 1, Auth::id());
                        }
                    } else {
                        $userService = app(UserService::class);
                        $r->validate($userService->getUpdateValidationRules($user->id));
                        $userService->update($r, $user);

                        if ($r->filled('password')) {
                            $user->password_show = $r->password;
                            $user->password = Hash::make($r->password);
                        }

                        $user->settypes('customer');
                        $user->save();
                    }

                    if ($r->hasFile('file')) {
                        $file = $r->file('file');
                        $ext = strtolower($file->getClientOriginalExtension());
                        $size = $file->getSize();
                        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $folder = now()->format('M_Y');
                        $imgName = time() . '.' . uniqid() . '.' . $ext;
                        $path = 'medies/' . $folder;
                        $fullPath = $path . '/' . $imgName;

                        if (!File::isDirectory(public_path($path))) {
                            File::makeDirectory(public_path($path), 0755, true);
                        }

                        $fileType = match ($ext) {
                            'png', 'jpeg', 'jpg', 'gif', 'svg', 'webp' => 1,
                            'pdf' => 2,
                            'doc', 'docx' => 3,
                            'zip', 'rar' => 4,
                            'mp4', 'webm', 'mov', 'wmv' => 5,
                            'mp3' => 6,
                            default => 0,
                        };

                        Media::create([
                            'src_id' => $user->id,
                            'src_type' => 6,
                            'use_Of_file' => 3,
                            'addedby_id' => Auth::id(),
                            'file_name' => Str::limit($name, 250),
                            'alt_text' => Str::limit($name, 250),
                            'file_rename' => $imgName,
                            'file_size' => $size,
                            'file_type' => $fileType,
                            'file_url' => $fullPath,
                            'file_path' => $path,
                        ]);

                        $file->move(public_path($path), $imgName);
                    }

                    Session()->flash('success', 'Update Successful!');
                    return redirect()->back();

                } catch (ValidationException $e) {
                    throw $e;
                } catch (\Exception $e) {

                    return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
                }
            }

            // PASSWORD CHANGE
            if ($action == 'change-password' && $r->isMethod('post')) {
                $validator = Validator::make($r->all(), [
                    'old_password' => 'required|string|min:8',
                    'password' => 'required|string|min:8|confirmed|different:old_password',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (Hash::check($r->old_password, $user->password)) {
                    $user->password_show = $r->password;
                    $user->password = Hash::make($r->password);
                    $user->save();
                    Session()->flash('success', 'Password changed successfully!');
                } else {
                    Session()->flash('error', 'Current password does not match!');
                }
                return redirect()->back();
            }

            // SOFT DELETE
            if ($action == 'delete') {

                if ($user->id == $authId) {
                    Session()->flash('error', 'You cannot delete your own account!');
                    return redirect()->back();
                }
                $user->deleted_by = auth()->id();
                $user->save();
                $user->delete();
                Session()->flash('success', 'User soft deleted successfully!');
                return redirect()->back();
            }

            
            // ROLE ASSIGN
            if ($action == 'role' && $r->isMethod('post')) {
                if (!$user) {
                    Session()->flash('error', 'User not found.');
                    return redirect()->back();
                }

                $r->validate([
                    'role' => 'nullable|exists:permissions,id',
                ]);

                $user->permission_id = $r->role ?: null;
                $user->save();

                Session()->flash('success', 'Role updated successfully!');
                return redirect()->back();
            }

            if($action=='edit' || $action == 'employee-create'){
                $departments   = Attribute::latest()->filterBy('department')->where('status','<>','temp')->get();
                $designations  = Attribute::latest()->filterBy('designation')->where('status','<>','temp')->get();
                $divisions     = Attribute::latest()->filterBy('divisions')->where('status', '<>', 'temp')->get();
                $grades        = Attribute::latest()->filterBy('grades')->where('status', '<>', 'temp')->get();
                $lines         = Attribute::latest()->filterBy('line_number')->where('status', '<>', 'temp')->get();
                $sections      = Attribute::latest()->filterBy('sections')->where('status', '<>', 'temp')->get();
                $emp_types     = Attribute::latest()->where('type', 16)->where('status', '<>', 'temp')->get();
                $shifts        = Shift::latest()->get();

                $roles =Permission::latest()->where('status','active')->get();

                return view(adminTheme().'users.customers.editUser', compact('user','departments','designations','divisions','grades','lines','sections','shifts','roles', 'emp_types'));

            }

            if ($action == 'print') {
                return view(adminTheme().'users.customers.printUser', compact('user'));
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
                        $fullPath = "".$path.'/'.$imgName;

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

            // Return view
            $startDate = $r->startDate ? Carbon::parse($r->startDate) : Carbon::now()->startOfMonth();
            $endDate = $r->endDate ? Carbon::parse($r->endDate) : Carbon::now();

            $selectedGrade = null;
            if (!empty($user->grade_lavel)) {
                $selectedGrade = Attribute::find($user->grade_lavel);
            }

            return view(adminTheme().'users.customers.viewUser',compact('user','action','startDate','endDate', 'selectedGrade'));
        }catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // Users Customer List + Bulk Actions
    public function usersCustomerXXXXXXXXX(Request $r)
    {
        $departments = Attribute::latest()->filterBy('department')->where('status','<>','temp')->get(['id', 'name']);
        $designations = Attribute::latest()->filterBy('designation')->where('status','<>','temp')->get(['id', 'name']);
        $divisions = Attribute::latest()->filterBy('divisions')->where('status','<>','temp')->get(['id', 'name']);
        $sections = Attribute::latest()->filterBy('sections')->where('status','<>','temp')->get(['id', 'name']);
        $empTypes = Attribute::latest()->filterBy('employee_type')->where('status','<>','temp')->get(['id', 'name']);
        $shifts = Shift::latest()->get(['id', 'name_of_shift']);

        $departmentsMap = $departments->pluck('name', 'id');
        $designationsMap = $designations->pluck('name', 'id');
        $divisionsMap = $divisions->pluck('name', 'id');
        $sectionsMap = $sections->pluck('name', 'id');
        $empTypesMap = $empTypes->pluck('name', 'id');
        $shiftsMap = $shifts->pluck('name_of_shift', 'id');

        // Bulk Actions
        if ($r->action) {
            if ($r->checkid) {
                $datas = User::latest()
                    ->filterByType('customer')
                    ->whereIn('id', $r->checkid)
                    ->get();

                foreach ($datas as $data) {
                    switch ($r->action) {
                        case 1: // Activate
                            $data->status = 1;
                            $data->save();
                            break;
                        case 2: // Deactivate
                            $data->status = 0;
                            $data->save();
                            break;
                        case 5: // Soft Delete
                            // Delete user media
                            $userFiles = Media::where('src_type', 6)->where('src_id', $data->id)->get();
                            foreach ($userFiles as $media) {
                                if (File::exists($media->file_url)) {
                                    File::delete($media->file_url);
                                }
                                $media->delete();
                            }
                            $data->deleted_by = auth()->id();
                            $data->save();
                            $data->delete();
                            break;
                    }
                }

                Session()->flash('success', 'Action Successfully Completed!');
            } else {
                Session()->flash('info', 'Please select at least one user.');
            }

            return redirect()->back();
        }

        // Query Users
        $usersQuery = User::latest()->filterByType('customer')
            ->with(['permission', 'designation', 'department'])
            ->where(function ($q) use ($r) {
                if ($r->search) {
                    $q->where('name', 'LIKE', '%' . $r->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $r->search . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $r->search . '%')
                    ->orWhere('employee_id', 'LIKE', '%' . $r->search . '%');
                }
                if ($r->status) {
                    $q->where('status', $r->status == 'inactive' ? 0 : 1);
                }
                if ($r->role_id) {
                    $q->where('permission_id', $r->role_id);
                }
                if ($r->division_id) {
                    $q->where('division', $r->division_id);
                }
                if ($r->designation_id) {
                    $q->where('designation_id', $r->designation_id);
                }
                if ($r->department_id) {
                    $q->where('department_id', $r->department_id);
                }
                if ($r->section_id) {
                    $q->where('section_id', $r->section_id);
                }
                if ($r->shift_id) {
                    $q->where('shift_id', $r->shift_id);
                }
                if ($r->employee_type) {
                    $q->where('employee_type', $r->employee_type);
                }
                if ($r->startDate || $r->endDate) {
                    $from = $r->startDate ?? Carbon::now()->format('Y-m-d');
                    $to = $r->endDate ?? Carbon::now()->format('Y-m-d');
                    $q->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
                }
                if ($r->joining_start || $r->joining_end) {
                    $from = $r->joining_start ?? Carbon::now()->format('Y-m-d');
                    $to = $r->joining_end ?? Carbon::now()->format('Y-m-d');
                    $q->whereDate('joining_date', '>=', $from)
                    ->whereDate('joining_date', '<=', $to);
                }
            });

        // Show soft-deleted users if requested
        if ($r->view == 'deleted') {
            $usersQuery->onlyTrashed()->with('deletedBy');
        } else {
            $usersQuery->whereIn('status', [0, 1]);
        }

        $users = $usersQuery->select([
            'id','permission_id','name','email','employee_id','designation_id','department_id','section_id','shift_id','employee_type','division','joining_date','mobile','created_at','addedby_id','status','deleted_by','deleted_at'
        ])
        ->paginate(25)
        ->appends($r->all());
        // dd($users);

        $totals = User::withTrashed()->filterByType('customer')
            ->whereIn('status', [0,1])
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 1 then 1 end) as active")
            ->selectRaw("count(case when status = 0 then 1 end) as inactive")
            ->selectRaw("count(case when deleted_at IS NOT NULL then 1 end) as deleted")
            ->first();

        $roles = Permission::latest()->where('status','active')->get();

        if ($r->view == 'deleted') {
            return view(adminTheme() . 'users.customers.users_deleted', compact('users', 'totals', 'roles'));
        } else {
            return view(adminTheme() . 'users.customers.users', compact(
                'users',
                'totals',
                'roles',
                'departments',
                'designations',
                'divisions',
                'sections',
                'empTypes',
                'shifts',
                'departmentsMap',
                'designationsMap',
                'divisionsMap',
                'sectionsMap',
                'empTypesMap',
                'shiftsMap'
            ));
        }
    }


    // Users Customer Action (Create, Edit, Update, Password, Soft Delete, Restore, Force Delete)
    public function usersCustomerActionXXXXXXXXXXXX(Request $r, $action, $id = null)
    {
        try{

            $authId = auth()->id();
            // RESTORE SOFT DELETED USER
            if ($action == 'restore') {
                $user = User::onlyTrashed()->find($id);
                if (!$user) {
                    Session()->flash('error', 'User not found or already restored!');
                    return redirect()->back();
                }
                $user->restore();
                Session()->flash('success', 'User restored successfully!');
                return redirect()->back();
            }

            // FORCE DELETE USER
            if ($action == 'force-delete') {
                if ($id == $authId) {
                    Session()->flash('error', 'You cannot force delete yourself!');
                    return redirect()->back();
                }
                $user = User::onlyTrashed()->find($id);
                if ($user) {
                    $userFiles = Media::where('src_type', 6)->where('src_id', $user->id)->get();
                    foreach ($userFiles as $media) {
                        if (File::exists($media->file_url)) {
                            File::delete($media->file_url);
                        }
                        $media->delete();
                    }
                    $user->forceDelete();
                }
                Session()->flash('success', 'User permanently deleted!');
                return redirect()->back();
            }

            // CREATE USER
            if ($action == 'create' && $r->isMethod('post')) {

                $r->validate([
                    'name' => 'required|max:100',
                    'email' => [
                        'nullable',
                        'email',
                        'max:100',
                        Rule::unique('users', 'email')->whereNull('deleted_at'),
                        'required_without:mobile',
                    ],
                    'mobile' => [
                        'nullable',
                        'max:20',
                        Rule::unique('users', 'mobile')->whereNull('deleted_at'),
                        'required_without:email',
                    ],
                ]);

                $password = Str::random(8);
                $user = new User();
                $user->name          = $r->name;
                $user->mobile        = $r->mobile;
                $user->email         = $r->email;
                $user->password_show = $password;
                $user->password      = Hash::make($password);
                $user->status        = 1;

                $user->setTypes('customer'); // if you already have setTypes method
                $user->save();

                Session()->flash('success', 'User created successfully!');
                return redirect()->route('admin.usersCustomer');
            }


            $user = $action === 'employee-create'
                ? new User()
                : User::whereIn('status', [0,1])->find($id);

            if (!$user && !in_array($action, ['employee-create'])) {
                Session()->flash('error', 'User not found.');
                return redirect()->route('admin.usersCustomer');
            }

            if ($action == 'employee-create' && $r->isMethod('post')) {
                try {
                    $userService = app(UserService::class);
                    $rules = $userService->getUpdateValidationRules();
                    $rules['name'] = 'required|max:100';
                    $rules['employee_id'] = 'required|max:50|unique:users,employee_id';
                    $rules['mobile'] = 'nullable|max:20|unique:users,mobile';
                    $rules['password'] = 'nullable|min:6|max:100';

                    $r->validate($rules);

                    $userService->update($r, $user);
                    $user->setTypes('customer');
                    $user->save();

                    Session()->flash('success', 'Employee created successfully!');
                    return redirect()->route('admin.usersCustomerAction', ['view', $user->id]);
                } catch (ValidationException $e) {
                    throw $e;
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
                }
            }

            // UPDATE USER PROFILE
            if ($action == 'update' && $r->isMethod('post')) {
                try {
                    $isSimpleEdit = !$r->filled('employee_id') && !$r->filled('designation_id') && !$r->filled('department_id');

                    if ($isSimpleEdit) {
                        $r->validate([
                            'name' => 'required|max:100',
                            'employee_id' => 'nullable|max:50|unique:users,employee_id,' . $user->id . ',id,deleted_at,NULL',
                            'bn_name' => 'nullable|max:100',
                            'email' => [
                                'nullable',
                                'email',
                                'max:100',
                                Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at'),
                                'required_without:mobile',
                            ],
                            'mobile' => [
                                'nullable',
                                'max:20',
                                Rule::unique('users', 'mobile')->ignore($user->id)->whereNull('deleted_at'),
                                'required_without:email',
                            ],
                            'password' => 'nullable|min:6|max:100',
                            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,zip,rar,txt',
                        ]);

                        $user->name = $r->name;
                        $user->employee_id = $r->employee_id;
                        $user->bn_name = $r->bn_name;
                        $user->email = $r->email;
                        $user->mobile = $r->mobile;

                        if ($r->filled('password')) {
                            $user->password_show = $r->password;
                            $user->password = Hash::make($r->password);
                        }

                        if ($r->hasFile('image')) {
                            $this->uploadFile($r, $user, 'image', '', 6, '', array('image'));
                        }

                        $user->settypes('customer');
                        $user->save();

                        if ($r->hasFile('file')) {
                            Media::create([
                                'user_id' => Auth::id(),
                                'model_name' => 'User',
                                'model_id' => $user->id,
                                'src_type' => 6,
                                'use_of_file' => 3,
                                'file_path' => $r->file->store('medies/' . date('m_Y')),
                                'file_name' => Str::slug($user->name) . '_' . time() . '.' . $r->file->extension(),
                            ]);
                        }
                    } else {
                        $userService = app(UserService::class);
                        $r->validate($userService->getUpdateValidationRules($user->id));
                        $userService->update($r, $user);

                        if ($r->filled('password')) {
                            $user->password_show = $r->password;
                            $user->password = Hash::make($r->password);
                        }

                        $user->settypes('customer');
                        $user->save();
                    }

                    if ($r->hasFile('file')) {
                        $file = $r->file('file');
                        $ext = strtolower($file->getClientOriginalExtension());
                        $size = $file->getSize();
                        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $folder = now()->format('M_Y');
                        $imgName = time() . '.' . uniqid() . '.' . $ext;
                        $path = 'medies/' . $folder;
                        $fullPath = $path . '/' . $imgName;

                        if (!File::isDirectory(public_path($path))) {
                            File::makeDirectory(public_path($path), 0755, true);
                        }

                        $fileType = match ($ext) {
                            'png', 'jpeg', 'jpg', 'gif', 'svg', 'webp' => 1,
                            'pdf' => 2,
                            'doc', 'docx' => 3,
                            'zip', 'rar' => 4,
                            'mp4', 'webm', 'mov', 'wmv' => 5,
                            'mp3' => 6,
                            default => 0,
                        };

                        Media::create([
                            'src_id' => $user->id,
                            'src_type' => 6,
                            'use_Of_file' => 3,
                            'addedby_id' => Auth::id(),
                            'file_name' => Str::limit($name, 250),
                            'alt_text' => Str::limit($name, 250),
                            'file_rename' => $imgName,
                            'file_size' => $size,
                            'file_type' => $fileType,
                            'file_url' => $fullPath,
                            'file_path' => $path,
                        ]);

                        $file->move(public_path($path), $imgName);
                    }

                    Session()->flash('success', 'Update Successful!');
                    return redirect()->back();

                } catch (ValidationException $e) {
                    throw $e;
                } catch (\Exception $e) {

                    return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
                }
            }

            // PASSWORD CHANGE
            if ($action == 'change-password' && $r->isMethod('post')) {
                $validator = Validator::make($r->all(), [
                    'old_password' => 'required|string|min:8',
                    'password' => 'required|string|min:8|confirmed|different:old_password',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if (Hash::check($r->old_password, $user->password)) {
                    $user->password_show = $r->password;
                    $user->password = Hash::make($r->password);
                    $user->save();
                    Session()->flash('success', 'Password changed successfully!');
                } else {
                    Session()->flash('error', 'Current password does not match!');
                }
                return redirect()->back();
            }

            // SOFT DELETE
            if ($action == 'delete') {

                if ($user->id == $authId) {
                    Session()->flash('error', 'You cannot delete your own account!');
                    return redirect()->back();
                }
                $user->deleted_by = auth()->id();
                $user->save();
                $user->delete();
                Session()->flash('success', 'User soft deleted successfully!');
                return redirect()->back();
            }

            // ROLE ASSIGN
            if ($action == 'role' && $r->isMethod('post')) {
                if (!$user) {
                    Session()->flash('error', 'User not found.');
                    return redirect()->back();
                }

                $r->validate([
                    'role' => 'nullable|exists:permissions,id',
                ]);

                $user->permission_id = $r->role ?: null;
                $user->save();

                Session()->flash('success', 'Role updated successfully!');
                return redirect()->back();
            }

            if($action=='edit' || $action == 'employee-create'){
                $departments   = Attribute::latest()->filterBy('department')->where('status','<>','temp')->get();
                $designations  = Attribute::latest()->filterBy('designation')->where('status','<>','temp')->get();
                $divisions     = Attribute::latest()->filterBy('divisions')->where('status', '<>', 'temp')->get();
                $grades        = Attribute::latest()->filterBy('grades')->where('status', '<>', 'temp')->get();
                $lines         = Attribute::latest()->filterBy('line_number')->where('status', '<>', 'temp')->get();
                $sections      = Attribute::latest()->filterBy('sections')->where('status', '<>', 'temp')->get();
                $emp_types     = Attribute::latest()->where('type', 16)->where('status', '<>', 'temp')->get();
                $shifts        = Shift::latest()->get();

                $roles =Permission::latest()->where('status','active')->get();

                return view(adminTheme().'users.customers.editUser', compact('user','departments','designations','divisions','grades','lines','sections','shifts','roles', 'emp_types'));

            }

            if ($action == 'print') {
                return view(adminTheme().'users.customers.printUser', compact('user'));
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
                        $fullPath = "".$path.'/'.$imgName;

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

            // Return view
            $startDate = $r->startDate ? Carbon::parse($r->startDate) : Carbon::now()->startOfMonth();
            $endDate = $r->endDate ? Carbon::parse($r->endDate) : Carbon::now();

            $selectedGrade = null;
            if (!empty($user->grade_lavel)) {
                $selectedGrade = Attribute::find($user->grade_lavel);
            }

            return view(adminTheme().'users.customers.viewUser',compact('user','action','startDate','endDate', 'selectedGrade'));
        }catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
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
                'currency_decimal' => 'nullable|numeric',
                'currency_position' => 'nullable|numeric',
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
            $general->currency_decimal=$r->currency_decimal;
            $general->currency_position=$r->currency_position;
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
        return redirect()->route('admin.employeeType');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['clients']['all']);
      if($allPer && $employeeType->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.employeeType');
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
        return redirect()->route('admin.employeeType');

      }
      // Delete EmployeeType Action End
      return redirect()->back();

    }


    // divisions
    public function divisions(Request $r){
        if($r->action){
            if($r->checkid){

            $datas=Attribute::latest()->where('type', 27)->whereIn('id',$r->checkid)->get();

            foreach($datas as $data){

                if($r->action==1){
                $data->status='active';
                $data->save();
                }elseif($r->action==2){
                $data->status='inactive';
                $data->save();
                }elseif($r->action==5){

                $medias =Media::latest()->where('src_type',27)->where('src_id',$data->id)->get();
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


        $divisions=Attribute::latest()->where('type',27)->where('status','<>','temp')
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
        ->where('type',27)
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

            $division =Attribute::where('type',27)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$division){
            $division =new Attribute();
            }

            $division->name=$r->name;
            $division->description=$r->description;
            $division->type =27;
            $division->status ='active';
            $division->addedby_id =Auth::id();
            $division->save();

            $slug =Str::slug($r->name);
            if($slug==null){
            $division->slug=$division->id;
            }else{
            if(Attribute::where('type',27)->where('slug',$slug)->whereNotIn('id',[$division->id])->count() >0){
            $division->slug=$slug.'-'.$division->id;
            }else{
            $division->slug=$slug;
            }
            }
            $division->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }


        $division =Attribute::where('type',27)->find($id);
        if(!$division){
            Session()->flash('error','This Division Are Not Found');
            return redirect()->route('admin.divisions');
        }

        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
        if($allPer && $division->addedby_id!=Auth::id()){
            Session()->flash('error','You are unauthorized Try!!');
            return redirect()->route('admin.divisions');
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
                $srcType  =27;
                $fileUse  =2;
                $author=Auth::id();
                uploadFile($file,$src,$srcType,$fileUse,$author);

                }

                $slug =Str::slug($r->name);
                if($slug==null){
                $division->slug=$division->id;
                }else{
                if(Attribute::where('type',27)->where('slug',$slug)->whereNotIn('id',[$division->id])->count() >0){
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
            $medias =Media::latest()->where('src_type',27)->where('src_id',$division->id)->get();
                foreach($medias as $media){
                if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                }
                $media->delete();
                }

                $division->delete();

                Session()->flash('success','Your Are Successfully Done');
                return redirect()->route('admin.divisions');
        }

        return redirect()->back();

    }
    // end divisions

    // grades
    public function grades(Request $r){
        if($r->action){
            if($r->checkid){

            $datas=Attribute::latest()->filterBy('grades')->whereIn('id',$r->checkid)->get();

            foreach($datas as $data){

                if($r->action==1){
                $data->status='active';
                $data->save();
                }elseif($r->action==2){
                $data->status='inactive';
                $data->save();
                }elseif($r->action==5){

                $medias =Media::latest()->where('src_type',18)->where('src_id',$data->id)->get();
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


        $grades=Attribute::latest()->filterBy('grades')->where('status','<>','temp')
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
        ->where('type',28)
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

            $grade =Attribute::where('type',28)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$grade){
            $grade =new Attribute();
            }

            $grade->name=$r->name;
            $grade->description=json_encode($r->json);
            $grade->type =28;
            $grade->status ='active';
            $grade->addedby_id =Auth::id();
            $grade->save();

            $slug =Str::slug($r->name);
            if($slug==null){
            $grade->slug=$grade->id;
            }else{
            if(Attribute::where('type',28)->where('slug',$slug)->whereNotIn('id',[$grade->id])->count() >0){
            $grade->slug=$slug.'-'.$grade->id;
            }else{
            $grade->slug=$slug;
            }
            }
            $grade->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

        }


        $grade =Attribute::where('type',28)->find($id);
        if(!$grade){
            Session()->flash('error','This Grade Are Not Found');
            return redirect()->route('admin.grades');
        }

        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
        if($allPer && $grade->addedby_id!=Auth::id()){
            Session()->flash('error','You are unauthorized Try!!');
            return redirect()->route('admin.grades');
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
                if(Attribute::where('type',28)->where('slug',$slug)->whereNotIn('id',[$grade->id])->count() >0){
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
            $medias =Media::latest()->where('src_type',18)->where('src_id',$grade->id)->get();
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

            $datas=Attribute::latest()->where('type',29)->whereIn('id',$r->checkid)->get();

            foreach($datas as $data){

                if($r->action==1){
                  $data->status='active';
                  $data->save();
                }elseif($r->action==2){
                  $data->status='inactive';
                  $data->save();
                }elseif($r->action==5){

                  $medias =Media::latest()->where('src_type',29)->where('src_id',$data->id)->get();
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


          $sections=Attribute::latest()->where('type',29)->where('status','<>','temp')
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
          ->where('type',29)
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

            $section =Attribute::where('type',29)->where('status','temp')->where('addedby_id',Auth::id())->first();
            if(!$section){
              $section =new Attribute();
            }

            $section->name=$r->name;
            $section->description=$r->description;
            $section->type =29;
            $section->status ='active';
            $section->addedby_id =Auth::id();
            $section->save();

             $slug =Str::slug($r->name);
             if($slug==null){
              $section->slug=$section->id;
             }else{
              if(Attribute::where('type',29)->where('slug',$slug)->whereNotIn('id',[$section->id])->count() >0){
              $section->slug=$slug.'-'.$section->id;
              }else{
              $section->slug=$slug;
              }
            }
            $section->save();

            Session()->flash('success','Your Are Successfully Added');
            return redirect()->back();

          }

          $section =Attribute::where('type',29)->find($id);
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
                  if(Attribute::where('type',29)->where('slug',$slug)->whereNotIn('id',[$section->id])->count() >0){
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
        return redirect()->route('hr-center.masters.index', 'shifts');
    }

    // Function to handle the form creation and updating logic
    public function shiftsAction(Request $r, $action, $id = null)
    {
        if ($action === 'form') {
            if ($id) {
                return redirect()->route('hr-center.masters.edit', ['entity' => 'shifts', 'id' => $id]);
            }

            return redirect()->route('hr-center.masters.create', 'shifts');
        }

        return redirect()->route('hr-center.masters.index', 'shifts');
    }



}
