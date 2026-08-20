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

  // hr_factory(), hr_factory_name(), and hr_employee_salary() now live in the
  // mestiaque/hr package (src/helpers.php) — single source of truth, loaded
  // automatically via composer 'files' autoload. Do not redefine them here.

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

if (!function_exists('clientPublicIp')) {
    function clientPublicIp(?\Illuminate\Http\Request $request = null): ?string
    {
        $request = $request ?: request();
        $candidates = [];

        foreach (['CF-Connecting-IP', 'True-Client-IP', 'X-Real-IP', 'X-Forwarded-For'] as $header) {
            $value = $request->headers->get($header);
            if (!$value) {
                continue;
            }

            foreach (explode(',', $value) as $ip) {
                $candidates[] = trim($ip);
            }
        }

        $candidates[] = $request->ip();

        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }

        return $request->ip();
    }
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
// function welcomeTheme(){
//   $theme=general()->theme.'.';
//   if(isMobileDevice()){
//     $theme=general()->theme.'.';
//   }
//   return $theme;
// }

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

  [$fileableType, $useCase] = \App\Models\File::resolveFileable((int) $srcType, (int) $fileUse);

  if($fileStatus){
      $existing = \App\Models\File::where('fileable_type',$fileableType)->where('fileable_id',$src)->where('use_case',$useCase)->first();
  }else{
      $existing = null;
  }

  if($existing){
      // Remove the previously uploaded physical file before replacing it.
      \Illuminate\Support\Facades\Storage::disk($existing->disk ?: 'public')->delete(array_filter([
          $existing->file_path,
          $existing->file_path_sm,
          $existing->file_path_md,
          $existing->file_path_lg,
      ]));
      $record = $existing;
  }else{
      $record = new \App\Models\File();
      $record->file_name = (string) Str::uuid();
  }

  $originalName = basename($file->getClientOriginalName());
  $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
  $ext = $file->getClientOriginalExtension();
  $size = $file->getSize();

  $storedName = Str::uuid().'.'.$ext;
  $relativeDir = \App\Models\File::storageFolderFor($fileableType, $useCase);

  $storedPath = $file->storeAs($relativeDir, $storedName, 'public');

  $record->fileable_type = $fileableType;
  $record->fileable_id = $src;
  $record->use_case = $useCase;
  $record->original_name = Str::limit($originalName, 250, '');
  $record->alt_text = Str::limit($name, 250, '');
  $record->file_path = $storedPath;
  $record->file_full_path = asset('storage/'.$storedPath);
  $record->disk = 'public';
  $record->extension = $ext;
  $record->size = $size;
  $record->file_type = $file->getMimeType();
  $record->addedby_id = $author;
  $record->save();

  return $record;

}

if (!function_exists('deleteUserFiles')) {
  function deleteUserFiles($userId)
  {
    // Legacy media-table rows (historical, pre-files-table uploads)
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

    // Current files-table rows
    $files = \App\Models\File::where('fileable_type', \App\Models\User::class)->where('fileable_id', $userId)->get();

    foreach ($files as $file) {
      \Illuminate\Support\Facades\Storage::disk($file->disk ?: 'public')->delete(array_filter([
        $file->file_path,
        $file->file_path_sm,
        $file->file_path_md,
        $file->file_path_lg,
      ]));

      $file->delete();
    }

    return true;
  }
}

if (!function_exists('recordFileColumn')) {
  /**
   * Mirror a file that a model stores directly on one of its own columns
   * (e.g. users.signature, users.photo, general.signature) into the central
   * files table, in addition to the column value itself. Never touches the
   * column — call this right after you set/save it.
   *
   * $relativePath must be the path already stored on the column, with any
   * 'storage/' prefix stripped (i.e. relative to the given $disk).
   */
  function recordFileColumn($fileableType, $fileableId, string $useCase, ?string $relativePath, $uploadedFile = null, ?int $author = null, string $disk = 'public')
  {
    if (!$relativePath) {
      return null;
    }

    $record = \App\Models\File::firstOrNew([
      'fileable_type' => $fileableType,
      'fileable_id' => $fileableId,
      'use_case' => $useCase,
    ]);

    if ($record->exists && $record->file_path && $record->file_path !== $relativePath) {
      \Illuminate\Support\Facades\Storage::disk($record->disk ?: $disk)->delete($record->file_path);
    }

    if (!$record->exists) {
      $record->file_name = (string) \Illuminate\Support\Str::uuid();
    }

    $record->disk = $disk;
    $record->file_path = $relativePath;
    $record->file_full_path = $disk === 'public' ? asset('storage/'.$relativePath) : asset($relativePath);

    if ($uploadedFile) {
      $record->original_name = $uploadedFile->getClientOriginalName();
      $record->extension = $uploadedFile->getClientOriginalExtension();
      $record->size = $uploadedFile->getSize();
      $record->file_type = $uploadedFile->getMimeType();
    }

    $record->addedby_id = $author;
    $record->save();

    return $record;
  }
}

if (!function_exists('friendlyModelName')) {
  /**
   * Turns a logged model's FQCN (e.g. ME\AccSfl\Models\AcExpenseIou) into a
   * human name (e.g. "Expense IOU") for the Data Change Log, without needing
   * a hand-maintained label per model across every module in the app.
   */
  function friendlyModelName(?string $modelClass): string
  {
    if (!$modelClass) {
      return 'Unknown';
    }

    $base = class_basename($modelClass);

    // Strip the module's class-name prefix (Ac, Hr, Inv, Prod, Trc, Comm, ...).
    foreach (['Ac', 'Hr', 'Inv', 'Prod', 'Trc', 'Comm', 'Mer'] as $prefix) {
      if (str_starts_with($base, $prefix) && strlen($base) > strlen($prefix) && ctype_upper($base[strlen($prefix)])) {
        $base = substr($base, strlen($prefix));
        break;
      }
    }

    $spaced = trim((string) preg_replace('/(?<!^)(?=[A-Z])/', ' ', $base));
    $words = explode(' ', $spaced);

    $acronyms = [
      'id' => 'ID', 'lc' => 'LC', 'ud' => 'UD', 'btma' => 'BTMA', 'qc' => 'QC',
      'iou' => 'IOU', 'grn' => 'GRN', 'api' => 'API', 'fg' => 'FG', 'po' => 'PO',
      'b2b' => 'B2B', 'nid' => 'NID', 'uom' => 'UOM',
    ];

    foreach ($words as &$word) {
      $lower = strtolower($word);
      if (isset($acronyms[$lower])) {
        $word = $acronyms[$lower];
      }
    }

    return implode(' ', $words) ?: $base;
  }
}

if (!function_exists('friendlyFieldName')) {
  /**
   * Turns a raw DB column name (e.g. nid_number, employee_id) into a
   * human label (e.g. "NID Number", "Employee ID") for the Data Change Log.
   */
  function friendlyFieldName(string $key): string
  {
    $acronyms = [
      'id' => 'ID', 'nid' => 'NID', 'bn' => 'Bangla', 'uom' => 'UOM', 'qty' => 'Quantity',
      'ip' => 'IP', 'url' => 'URL', 'lc' => 'LC', 'po' => 'PO', 'ud' => 'UD', 'qc' => 'QC',
      'btma' => 'BTMA', 'grn' => 'GRN', 'iou' => 'IOU', 'fg' => 'FG', 'api' => 'API',
      'etin' => 'ETIN',
    ];

    $words = explode('_', $key);

    foreach ($words as &$word) {
      $lower = strtolower($word);
      $word = $acronyms[$lower] ?? ucfirst($word);
    }

    return implode(' ', $words);
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
        if (!is_array($permissions)) {
          $permissions = [];
        }

        // Developer role (999) auto-inherits whatever a Super Admin checked in
        // Developer Permissions — those modules/actions are hidden from the Role
        // Update checklist entirely, so this is the only way to grant them.
        if ((int) Auth::user()->permission_id === 999) {
          $general = \App\Models\General::first();
          if ($general) {
            $permissions = array_replace_recursive($permissions, $general->developerGrantedPermissions());
          }
        }

        if (empty($permissions)) return false;

        // Support dot notation directly: hasChildPermission('dev.all')
        $module = trim($module);
        if ($permissionKey !== null) {
          $permissionKey = trim($permissionKey);
        }

        if ($permissionKey === null && str_contains($module, '.')) {
          [$module, $permissionKey] = array_pad(explode('.', $module, 2), 2, null);
          $module = trim((string) $module);
          $permissionKey = $permissionKey !== null ? trim((string) $permissionKey) : null;
        }

        $isAllowedValue = static function ($value): bool {
          return in_array($value, ['on', '1', 1, true, 'true'], true);
        };

        if (!array_key_exists($module, $permissions)) return false;

        $modulePermission = $permissions[$module];

        // Scalar permission style: {"dev":"on"}
        if (!is_array($modulePermission)) {
          return $permissionKey === null ? $isAllowedValue($modulePermission) : false;
        }

        // Specific key check: hasChildPermission('dev', 'all') or hasChildPermission('dev.all')
        if ($permissionKey !== null) {
          if (array_key_exists($permissionKey, $modulePermission)) {
            return $isAllowedValue($modulePermission[$permissionKey]);
          }

          // Fallback: if specific key missing but module-level all is granted.
          if (array_key_exists('all', $modulePermission) && $isAllowedValue($modulePermission['all'])) {
            return true;
          }

          return false;
        }

        // Module-level check: when 'all' exists, use it as authoritative module permission.
        if (array_key_exists('all', $modulePermission)) {
          return $isAllowedValue($modulePermission['all']);
        }

        // Legacy shape support: {'dev': {'dev': 'on'}}
        if (array_key_exists($module, $modulePermission)) {
          return $isAllowedValue($modulePermission[$module]);
        }

        // Fallback for modules without 'all': allow if any child permission is granted.
        foreach ($modulePermission as $value) {
          if ($isAllowedValue($value)) return true;
        }

        return false;
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

    $holiday = \ME\Hr\Models\HrHoliday::where('status', 'active')
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

    $leave = \ME\Hr\Models\HrEmployeeLeave::where('employee_id', $userId)
      ->whereDate('start_date', '<=', $date)
      ->whereDate('end_date', '>=', $date)
      ->first();

    $attendance = \ME\Hr\Models\HrAttendance::where('user_id', $userId)
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


if (!function_exists('bn_date')) {
    function bn_date($date, $format = 'd/m/Y')
    {
        if (!$date) return null;

        // English → Bangla digits
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];

        // Full + Short month map
        $months = [
            'January' => 'জানুয়ারি', 'Jan' => 'জানু',
            'February' => 'ফেব্রুয়ারি', 'Feb' => 'ফেব্রু',
            'March' => 'মার্চ', 'Mar' => 'মার্চ',
            'April' => 'এপ্রিল', 'Apr' => 'এপ্রিল',
            'May' => 'মে',
            'June' => 'জুন', 'Jun' => 'জুন',
            'July' => 'জুলাই', 'Jul' => 'জুলাই',
            'August' => 'আগস্ট', 'Aug' => 'আগস্ট',
            'September' => 'সেপ্টেম্বর', 'Sep' => 'সেপ্টেম্বর',
            'October' => 'অক্টোবর', 'Oct' => 'অক্টোবর',
            'November' => 'নভেম্বর', 'Nov' => 'নভেম্বর',
            'December' => 'ডিসেম্বর', 'Dec' => 'ডিসেম্বর',
        ];

        $formatted = \Carbon\Carbon::parse($date)->format($format);

        // Month replace (if format contains month text)
        $formatted = str_replace(array_keys($months), array_values($months), $formatted);

        // Digit replace
        return str_replace($en, $bn, $formatted);
    }
}

if (!function_exists('bn_time')) {
    function bn_time($time, $short = true, $withSeconds = false)
    {
        if (!$time) return null;

        try {
            $dt = \Carbon\Carbon::parse($time);
        } catch (\Exception $e) {
            return $time;
        }

        // Format
        $format = $withSeconds ? 'h:i:s' : 'h:i';
        $formattedTime = $dt->format($format);

        // Convert to Bangla digits
        $bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        $formattedTime = str_replace(range(0, 9), $bnDigits, $formattedTime);

        // AM / PM in Bangla
        if ($short) {
            $period = $dt->format('A') === 'AM' ? 'পূঃ' : 'অঃ';
        } else {
            $period = $dt->format('A') === 'AM' ? 'পূর্বাহ্ণ' : 'অপরাহ্ণ';
        }

        return "{$formattedTime} {$period}";
    }
}


if(!function_exists('att_status')) {
    function att_status($status, $lang = 'en')
    {
        $map = [
            'absent' => ['en' => 'A', 'bn' => 'অনুপস্থিত'],
            'leave' => ['en' => 'L', 'bn' => 'ছুটি'],
            'weekend' => ['en' => 'W', 'bn' => 'সাপ্তাহিক ছুটি'],
            'holiday' => ['en' => 'H', 'bn' => 'সরকারি ছুটি'],
            'late and early exit' => ['en' => 'LEO', 'bn' => 'বিলম্ব ও আগে বের'],
            'punch missing' => ['en' => 'PM', 'bn' => 'পাঞ্চ মিসিং'],
            'early exit' => ['en' => 'EO', 'bn' => 'আগে বের'],
            'late and punch missing' => ['en' => 'LPM', 'bn' => 'বিলম্ব ও পাঞ্চ মিসিং'],
            'late' => ['en' => 'LATE', 'bn' => 'বিলম্ব'],
            'present' => ['en' => 'P', 'bn' => 'উপস্থিত'],
        ];

        $key = strtolower(trim($status));

        return $map[$key][$lang] ?? $status;
    }
}
