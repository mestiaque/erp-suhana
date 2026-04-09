<?php

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Media;
use App\Models\Country;
use App\Models\General;
use App\Models\Attribute;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


function random_color($seed = 0) {
    $colors = [
        '#3498db', '#e74c3c', '#9b59b6', '#f1c40f',
        '#1abc9c', '#e67e22', '#34495e', '#16a085',
        '#c0392b', '#8e44ad', '#27ae60', '#d35400',
        '#2980b9', '#2c3e50', '#f39c12', '#00b894'
    ];
    return $colors[$seed % count($colors)];
}

    function general() {
        // Skip DB query if running in console (artisan commands)
        if (app()->runningInConsole()) {
            return null; // or return default object if you want
        }

        return General::first();
    }

function websiteTitle($title=null){

  $hasTitle =$title?' - ':'';
  $text =general()->title;
  $text1=general()->subtitle;
  $text2=$text&&$text1?' - ':'';
  return $title.$hasTitle.$text.$text2.$text1;
}

function isMobileDevice() {
  $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
  return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i"
, $userAgent) === 1;
}

// function adminTheme(){
//   $theme=general()->adminTheme.'.';
//   if(isMobileDevice()){
//     $theme=general()->adminTheme.'.';
//   }
//   return $theme;
// }

function adminTheme() {
    // মূল theme name
    $theme = general()->adminTheme;

    // Mobile device হলে আলাদা theme ব্যবহার করতে চাইলে uncomment করতে পারো
    // if (isMobileDevice()) {
    //     $theme = general()->mobileTheme ?? $theme;
    // }

    $viewPath = resource_path('views/' . $theme);

    // Linux case-sensitive: যদি folder না থাকে, strtolower try করি
    if (!file_exists($viewPath) && file_exists(resource_path('views/' . strtolower($theme)))) {
        $theme = strtolower($theme);
    }

    // শেষে dot দিয়ে return (Laravel view path convention)
    return $theme . '.';
}


function welcomeTheme(){
  $theme=general()->theme.'.';
  if(isMobileDevice()){
    $theme=general()->theme.'.';
  }
  return $theme;
}

function serial($serial){
    $data =$serial.'th';
    if($serial==1){
        $data =$serial.'st';
    }elseif($serial==2){
        $data =$serial.'nd';
    }elseif($serial==3){
        $data =$serial.'rd';
    }
    return $data;
}

function assetLink(){
  return 'public/'.general()->theme;
}

function assetLinkAdmin(){
  return 'public/'.general()->adminTheme;
}

function geoData($type=null,$parent=null,$id=null){

  $data =Country::orderBy('name')->select(['id','type','parent_id','name']);
  if($type){
    $data =$data->where('type',$type);
  }

  if($parent){
    $data =$data->where('parent_id',$parent);
  }

  if($id){
    $data =$data->find($id);
  }else{
    $data =$data->get();
  }

  return $data;
}

function bn2enNumber ($number){
  $search_array= array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
  $replace_array= array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  $en_number = str_replace($search_array, $replace_array, $number);

  return $en_number;
}

function en2bnNumber ($number){
  $search_array= array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  $replace_array= array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
  $en_number = str_replace($search_array, $replace_array, $number);

  return $en_number;
}

function en2bnMonth($month){
  $search_array= array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October","November","December","Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct","Nov","Dec","01", "02", "03", "04", "05", "06", "07", "08", "09", "10","11","12");
  $replace_array= array("জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগষ্ট", "সেপ্টেম্বর", "অক্টোবর","নভেম্বর","ডিসেম্বর","জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগষ্ট", "সেপ্টেম্বর", "অক্টোবর","নভেম্বর","ডিসেম্বর","জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগষ্ট", "সেপ্টেম্বর", "অক্টোবর","নভেম্বর","ডিসেম্বর");
  $en_number = str_replace($search_array, $replace_array, $month);
  return $en_number;
}

function priceFormat($amount=0){
  $formatAmount ='';
  $formatAmount = number_format($amount,general()->currency_decimal);
  return $formatAmount;
}

function numberFormat($amount=0,$type=0,$currency=null){
  $formatAmount =$amount;
  $currency = $currency??general()->currency;
  if($type==1){
    $formatAmount =rtrim(rtrim(number_format($amount, general()->currency_decimal, '.', ''), '0'), '.');
  }elseif($type==2){
    $amountFormet = number_format($amount,general()->currency_decimal);
    if(general()->currency_position==0){
      $formatAmount = $currency.' '.$amountFormet;
    }else{
      $formatAmount = $amountFormet.' '.$currency;
    }
  }elseif($type==3){
    $amountFormet = rtrim(rtrim(number_format($amount, general()->currency_decimal, '.', ''), '0'), '.');
    if(general()->currency_position==0){
      $formatAmount = $currency.' '.$amountFormet;
    }else{
      $formatAmount = $amountFormet.' '.$currency;
    }
  }else{
    $formatAmount = number_format($amount,general()->currency_decimal);
  }
  return $formatAmount;
}

function priceFullFormat($amount=0){
  $formatAmount ='';
  $amountFormet = number_format($amount,general()->currency_decimal);
  if(general()->currency_position==0){
    $formatAmount = general()->currency.' '.$amountFormet;
  }else{
     $formatAmount = $amountFormet.' '.general()->currency;
  }
  return $formatAmount;
}

function sendMail($toEmail,$toName,$subject,$datas,$template,$attachments=null){
  try {
    Mail::send($template,compact('datas'), function ($message) use ($toEmail,$toName,$subject,$attachments) {
        $message->from(general()->mail_from_address, general()->mail_from_name);
        $message->to($toEmail,$toName);
        //To bb mail
        //$message->cc($ccRecipients);
        //To Replay diffrent mail
        //$message->replyTo('replyto@example.com', 'Reply To Name');

        $message->subject($subject);

        if($attachments){
            // Attachments
            foreach ($attachments as $attachment) {
                $message->attach($attachment['path'], [
                    'as' => $attachment['name'],
                    'mime' => $attachment['mime'],
                ]);
            }
        }

    });
      return true;
  } catch (Exception $ex) {
      // Debug via $ex->getMessage();
      return false;
  }

}

function sendSMS($to,$msg){
  $userId = general()->sms_username;
  $pass = general()->sms_password;
  $masking = general()->sms_senderid;
  if(general()->sms_type=='Non Masking'){
  $url =general()->sms_url_nonmasking;
  return "{$url}?username={$userId}&password={$pass}&number={$to}&message={$msg}";
  }else{
  $url =general()->sms_url_masking;
  return "{$url}?username={$userId}&password={$pass}&number={$to}&message={$msg}&senderid={$masking}";
  }
}

function slider($location=null){
  return Attribute::latest()->where('type',1)->where('status','active')->where('location',$location)->first();
}

function menu($location=null){
  return Attribute::latest()->where('type',8)->where('status','active')->where('location',$location)->first();
}

function page($id=null){
  return Post::where('type',0)->find($id);
}

function pageTemplate($template=null){
  return Post::where('type',0)->where('template',$template)->first();
}

function uploadFile($file,$src,$srcType,$fileUse,$author=null,$fileStatus=true){


  if($fileStatus){
      $media = Media::where('src_type',$srcType)->where('use_Of_file',$fileUse)->where('src_id',$src)->first();
  }else{
      $media = null;
  }

  if(!$media){
    $media =new Media();
    }else{

        $file0 = $media->file_url;
        if(str_starts_with($file0, 'public/')){
            $file0 = substr($file0, 7);
        }
        if(File::exists($file0)){
          File::delete($file0);
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
    }

    $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
    $fullname = basename($file->getClientOriginalName());
    $ext =$file->getClientOriginalExtension();
    $size =$file->getSize();

    $year =carbon::now()->format('Y');
    $month =carbon::now()->format('M');
    $folder = $month.'_'.$year;

    $img =time().'.'.uniqid().'.'.$file->getClientOriginalExtension();
    $path ="medies/".$folder;
    $fullpath ="medies/".$folder.'/'.$img;
    $media->src_type=$srcType;
    $media->use_Of_file=$fileUse;
    $media->src_id=$src;
    $media->file_name=Str::limit($fullname,250);
    $media->alt_text=Str::limit($name,250);
    $media->file_rename=Str::limit($img,100);
    $media->file_size=$size;
    if($ext=='png' || $ext=='jpeg' || $ext=='svg' || $ext=='gif' || $ext=='jpg' || $ext=='webp'){
      $media->file_type=1;
      }elseif($ext=='pdf'){
      $media->file_type=2;
      }elseif($ext=='docx'){
      $media->file_type=3;
      }elseif($ext=='zip' || $ext=='rar'){
      $media->file_type=4;
      }elseif($ext=='mp4' || $ext=='webm' || $ext=='mov' || $ext=='wmv'){
      $media->file_type=5;
      }elseif($ext=='mp3'){
      $media->file_type=6;
    }
    $file->move(public_path($path), $img);
    $media->file_url =$fullpath;
    $media->file_path =$path;
    $media->addedby_id=$author;
    $media->save();

    return $media;

}

if (!function_exists('deleteUserFiles')) {
  function deleteUserFiles($userId)
  {
    $medias = Media::where('src_type', 6)->where('src_id', $userId)->get();

    foreach ($medias as $media) {
      $paths = [
        $media->file_url,
        $media->file_url_sm,
        $media->file_url_md,
        $media->file_url_lg,
      ];

      foreach ($paths as $path) {
        if (!$path) {
          continue;
        }

        $normalizedPath = str_starts_with($path, 'public/') ? substr($path, 7) : $path;

        if (File::exists($normalizedPath)) {
          File::delete($normalizedPath);
        }
      }

      $media->delete();
    }

    return true;
  }
}



if (!function_exists('hasParentPermission')) {
    function hasParentPermission(string $parent): bool
    {
        if (!Auth::check()) return false;

        $roles = Auth::user()->permission;
        if (!$roles) return false;

        $permissions = json_decode($roles->permission, true);

        // Check if parent exists and is 'on', '1' or true
        return isset($permissions[$parent]) && in_array($permissions[$parent], ['on', '1', true]);
    }
}

if (!function_exists('hasChildPermission')) {
    function hasChildPermission(string $module, string $permissionKey = null): bool
    {
        if (!Auth::check()) return false;

        $roles = Auth::user()->permission;
        if (!$roles) return false;

        $permissions = json_decode($roles->permission, true);

        // If $permissionKey is null, check if module exists with any 'on' permission
        if ($permissionKey === null) {
            if (!isset($permissions[$module])) return false;

            foreach ($permissions[$module] as $value) {
                if (in_array($value, ['on', '1', true])) return true;
            }
            return false;
        }

        // Check specific child permission
        return isset($permissions[$module][$permissionKey]) && in_array($permissions[$module][$permissionKey], ['on', '1', true]);
    }
}

if (! function_exists('can')) {
    function can($ability, $arguments = []) {
    return Auth::check() && Gate::allows($ability, $arguments);
    }
}

/**
 * Get attendance status for a user on a specific date.
 */
if (!function_exists('getAttendanceStatus')) {
  function getAttendanceStatus($userId, $date) {
    $date = Carbon::parse($date)->format('Y-m-d');
    $dayOfWeek = Carbon::parse($date)->dayOfWeek;

    $result = [
      'status' => 'A',
      'status_text' => 'Absent',
      'is_holiday' => false,
      'is_weekly_off' => false,
      'is_leave' => false,
      'is_present' => false,
      'in_time' => null,
      'out_time' => null,
      'work_hours' => 0,
      'holiday_info' => null,
      'leave_info' => null,
    ];

    $offdaySetting = Attribute::where('type', 21)->where('status', 'active')->first();
    $offdayNumber = $offdaySetting
      ? array_search($offdaySetting->name, ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])
      : 5;

    $holiday = \ME\Hr\Models\Holiday::where('status', 'active')
      ->whereDate('from_date', '<=', $date)
      ->whereDate('to_date', '>=', $date)
      ->first();

    if ($holiday) {
      $result['status'] = 'H';
      $result['status_text'] = 'Holiday';
      $result['is_holiday'] = true;
      $result['holiday_info'] = $holiday;
      return $result;
    }

    if ($dayOfWeek == $offdayNumber) {
      $result['status'] = 'WO';
      $result['status_text'] = 'Weekly Off';
      $result['is_weekly_off'] = true;
      return $result;
    }

    $leave = \ME\Hr\Models\Leave::where('user_id', $userId)
      ->where('status', 'approved')
      ->whereDate('start_date', '<=', $date)
      ->whereDate('end_date', '>=', $date)
      ->first();

    $attendance = \ME\Hr\Models\Attendance::where('user_id', $userId)
      ->whereDate('date', $date)
      ->first();

    if ($leave) {
      $result['status'] = 'L';
      $result['status_text'] = 'Leave';
      $result['is_leave'] = true;
      $result['leave_info'] = $leave;
      return $result;
    }

    if ($attendance) {
      $result['status'] = ($attendance->status === 'late') ? 'LT' : 'P';
      $result['status_text'] = ($attendance->status === 'late') ? 'Late' : 'Present';
      $result['is_present'] = true;
      $result['in_time'] = $attendance->in_time;
      $result['out_time'] = $attendance->out_time;
      $result['work_hours'] = isset($attendance->in_minutes)
        ? round(((float) $attendance->in_minutes) / 60, 2)
        : ($attendance->work_hour ?? 0);
    }

    return $result;
  }
}

/**
 * Get monthly attendance summary for a user.
 */
if (!function_exists('getMonthlyAttendanceSummary')) {
  function getMonthlyAttendanceSummary($userId, $year, $month, $upToToday = true) {
    $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

    $today = Carbon::today();
    if ($upToToday && $endDate->gt($today)) {
      $endDate = $today;
    }

    $summary = [
      'present' => 0,
      'late' => 0,
      'absent' => 0,
      'leave' => 0,
      'holiday' => 0,
      'weekly_off' => 0,
      'total_work_hours' => 0,
      'days_counted' => 0,
    ];

    if ($startDate->gt($today)) {
      return $summary;
    }

    $period = CarbonPeriod::create($startDate, $endDate);

    foreach ($period as $date) {
      $summary['days_counted']++;
      $status = getAttendanceStatus($userId, $date);

      switch ($status['status']) {
        case 'P':
          $summary['present']++;
          $summary['total_work_hours'] += $status['work_hours'];
          break;
        case 'LT':
          $summary['late']++;
          $summary['total_work_hours'] += $status['work_hours'];
          break;
        case 'L':
          $summary['leave']++;
          break;
        case 'H':
          $summary['holiday']++;
          break;
        case 'WO':
          $summary['weekly_off']++;
          break;
        case 'A':
          $summary['absent']++;
          break;
      }
    }

    return $summary;
  }
}


