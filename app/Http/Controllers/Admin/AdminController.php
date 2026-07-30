<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attribute;
use App\Models\General;
use App\Models\Media;
use App\Models\Permission;
use App\Models\User;
use App\Services\UserService;
use Artisan;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Image;
use Str;
use Validator;

class AdminController extends Controller
{
    public function resizeImage($loadImage, $type, $w, $h)
    {

        $img = $type.$loadImage->file_rename;
        $fullpath = 'public/'.$loadImage->file_path.'/'.$img;
        $path = public_path($loadImage->file_path.'/');

        $image = Image::make($loadImage->file_url);
        $image->fit($w, $h);
        $image->save($path.$img);

        if ($type == 'sm') {
            $loadImage->file_url_sm = $fullpath;
            $loadImage->save();
        } elseif ($type == 'md') {
            $loadImage->file_url_md = $fullpath;
            $loadImage->save();
        } elseif ($type == 'lg') {
            $loadImage->file_url_lg = $fullpath;
            $loadImage->save();
        }

        return true;
    }

    public function dashboard(Request $request)
    {
        $userActivity = $this->getUserActivityReport(new Request);

        $totalRoles = Permission::count();
        $totalUsers = User::count();
        $activeUsers = User::where('status', 1)->count();
        $inactiveUsers = User::where('status', 0)->count();
        $todayLogins = ActivityLog::
            where('user_type', User::class)
            ->whereDate('created_at', today())
            ->distinct('user_id')
            ->count('user_id');

        $chartData = collect();
        $dailyLogins = ActivityLog::
            where('user_type', User::class)
            ->whereDate('created_at', '>=', Carbon::now()->subDays(29))
            ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT user_id) as active')
            ->groupBy('date')
            ->pluck('active', 'date');

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $activeCount = $dailyLogins->get($dateStr, 0);
            $chartData->push([
                'date' => $date->format('d M'),
                'active' => $activeCount,
                'inactive' => max(0, $totalUsers - $activeCount),
            ]);
        }

        return view('admin.dashboard', compact('userActivity', 'totalRoles', 'totalUsers', 'activeUsers', 'inactiveUsers', 'todayLogins', 'chartData'));
    }

    public function getUserActivityReport(Request $request)
    {
        $this->tollaranceTime = 5;
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

            if (! $lastLogoutTime || $lastActiveTime->gt($lastLogoutTime)) {
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
            if ($a['active_status'] && ! $b['active_status']) {
                return -1;
            }
            if (! $a['active_status'] && $b['active_status']) {
                return 1;
            }

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

    public function myProfile(Request $r)
    {
        $user = Auth::user();

        $toleranceMinutes = 5;
        $activeCutoff = now()->subMinutes($toleranceMinutes);

        $loginLogsQuery = ActivityLog::with('user')
            ->where('event', 'login')
            ->where('user_type', User::class)
            ->where('user_id', $user->id);

        $loginLogs = $loginLogsQuery->latest()->take(5)->get();

        $latestActiveLog = ActivityLog::where('event', 'user_active')
            ->where('user_type', User::class)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $latestLogoutLog = ActivityLog::where('event', 'logout')
            ->where('user_type', User::class)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $loginLogs->transform(function ($log) use ($latestActiveLog, $latestLogoutLog, $activeCutoff) {
            $log->login_data = is_array($log->data) ? $log->data : (json_decode($log->data, true) ?: []);
            $log->last_active_log = $latestActiveLog;
            $log->last_logout_log = $latestLogoutLog;
            $log->is_active_now = $latestActiveLog
                && $latestActiveLog->created_at->gte($activeCutoff)
                && (! $latestLogoutLog || $latestActiveLog->created_at->gt($latestLogoutLog->created_at));

            return $log;
        });

        return view(adminTheme().'users.myProfile', compact('user', 'loginLogs'));
    }

    public function loginHistory(Request $request)
    {
        $toleranceMinutes = 5;
        $perPage = (int) $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100]) ? $perPage : 25;
        $activeCutoff = now()->subMinutes($toleranceMinutes);

        $baseQuery = ActivityLog::where('event', 'login')
            ->where('user_type', User::class)
            ->whereHas('user');

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $userIds = User::where(function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            })->pluck('id');

            $baseQuery->whereIn('user_id', $userIds);
        }

        $latestLoginSub = ActivityLog::select(DB::raw('MAX(id) as max_id'))
            ->where('event', 'login')
            ->where('user_type', User::class)
            ->whereIn('user_id', function ($q) use ($baseQuery) {
                $q->select('user_id')->from($baseQuery);
            })
            ->groupBy('user_id');

        $loginLogsQuery = ActivityLog::with('user')
            ->whereIn('id', function ($query) use ($latestLoginSub) {
                $query->select('max_id')->from($latestLoginSub);
            })
            ->orderByDesc('created_at');

        $allLoginLogs = $loginLogsQuery->get();
        $allUserIds = $allLoginLogs->pluck('user_id')->filter()->unique()->values();

        $recentActiveLogs = ActivityLog::where('event', 'user_active')
            ->where('user_type', User::class)
            ->where('created_at', '>=', $activeCutoff)
            ->whereIn('user_id', $allUserIds)
            ->latest()
            ->get()
            ->groupBy('user_id');

        $recentLogoutLogs = ActivityLog::where('event', 'logout')
            ->where('user_type', User::class)
            ->where('created_at', '>=', $activeCutoff)
            ->whereIn('user_id', $allUserIds)
            ->latest()
            ->get()
            ->groupBy('user_id');

        $activeUserIds = $recentActiveLogs->filter(function ($logs, $userId) use ($recentLogoutLogs) {
            $lastActive = $logs->first();
            $lastLogout = $recentLogoutLogs->get($userId)?->first();

            return ! $lastLogout || $lastActive->created_at->gt($lastLogout->created_at);
        })->keys()->values();

        if ($request->status === 'active') {
            $allLoginLogs = $allLoginLogs->filter(fn ($log) => $activeUserIds->contains($log->user_id))->values();
        } elseif ($request->status === 'inactive') {
            $allLoginLogs = $allLoginLogs->filter(fn ($log) => ! $activeUserIds->contains($log->user_id))->values();
        }

        $page = $request->input('page', 1);
        $loginLogs = new LengthAwarePaginator(
            $allLoginLogs->forPage($page, $perPage),
            $allLoginLogs->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $pageUserIds = $loginLogs->getCollection()->pluck('user_id')->filter()->unique()->values();

        $latestActiveLogs = ActivityLog::where('event', 'user_active')
            ->where('user_type', User::class)
            ->whereIn('user_id', $pageUserIds)
            ->latest()
            ->get()
            ->groupBy('user_id')
            ->map(fn ($logs) => $logs->first());

        $latestLogoutLogs = ActivityLog::where('event', 'logout')
            ->where('user_type', User::class)
            ->whereIn('user_id', $pageUserIds)
            ->latest()
            ->get()
            ->groupBy('user_id')
            ->map(fn ($logs) => $logs->first());

        $loginLogs->getCollection()->transform(function ($log) use ($latestActiveLogs, $latestLogoutLogs, $activeCutoff) {
            $lastActive = $latestActiveLogs->get($log->user_id);
            $lastLogout = $latestLogoutLogs->get($log->user_id);

            $log->login_data = is_array($log->data) ? $log->data : (json_decode($log->data, true) ?: []);
            $log->last_active_log = $lastActive;
            $log->last_logout_log = $lastLogout;
            $log->is_active_now = $lastActive
                && $lastActive->created_at->gte($activeCutoff)
                && (! $lastLogout || $lastActive->created_at->gt($lastLogout->created_at));

            return $log;
        });

        $todayLoginCount = ActivityLog::where('event', 'login')
            ->where('user_type', User::class)
            ->whereDate('created_at', today())
            ->count();

        $trackedUserCount = ActivityLog::where('event', 'login')
            ->where('user_type', User::class)
            ->distinct('user_id')
            ->count('user_id');

        $activeUserCount = $activeUserIds->count();

        return view(adminTheme().'users.loginHistory', [
            'loginLogs' => $loginLogs,
            'todayLoginCount' => $todayLoginCount,
            'trackedUserCount' => $trackedUserCount,
            'activeUserCount' => $activeUserCount,
            'toleranceMinutes' => $toleranceMinutes,
        ]);
    }

    public function userLoginHistory(User $user)
    {
        $toleranceMinutes = 5;
        $activeCutoff = now()->subMinutes($toleranceMinutes);

        $loginLogsQuery = ActivityLog::with('user')
            ->where('event', 'login')
            ->where('user_type', User::class)
            ->where('user_id', $user->id)
            ->latest();

        $loginLogs = $loginLogsQuery->paginate(25)->withQueryString();

        $pageUserIds = $loginLogs->getCollection()->pluck('user_id')->filter()->unique()->values();

        $latestActiveLogs = ActivityLog::where('event', 'user_active')
            ->where('user_type', User::class)
            ->whereIn('user_id', $pageUserIds)
            ->latest()
            ->get()
            ->groupBy('user_id')
            ->map(fn ($logs) => $logs->first());

        $latestLogoutLogs = ActivityLog::where('event', 'logout')
            ->where('user_type', User::class)
            ->whereIn('user_id', $pageUserIds)
            ->latest()
            ->get()
            ->groupBy('user_id')
            ->map(fn ($logs) => $logs->first());

        $loginLogs->getCollection()->transform(function ($log) use ($latestActiveLogs, $latestLogoutLogs, $activeCutoff) {
            $lastActive = $latestActiveLogs->get($log->user_id);
            $lastLogout = $latestLogoutLogs->get($log->user_id);

            $log->login_data = is_array($log->data) ? $log->data : (json_decode($log->data, true) ?: []);
            $log->last_active_log = $lastActive;
            $log->last_logout_log = $lastLogout;
            $log->is_active_now = $lastActive
                && $lastActive->created_at->gte($activeCutoff)
                && (! $lastLogout || $lastActive->created_at->gt($lastLogout->created_at));

            return $log;
        });

        return view(adminTheme().'users.userLoginHistory', [
            'loginLogs' => $loginLogs,
            'user' => $user,
            'toleranceMinutes' => $toleranceMinutes,
        ]);
    }

    public function editProfile(Request $r)
    {

        $user = Auth::user();
        if ($r->isMethod('post')) {
            if ($r->actionType == 'profile') {
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

                $user->name = $r->name;
                $user->mobile = $r->mobile;
                $user->email = $r->email;
                $user->gender = $r->gender;
                $user->address_line1 = $r->address;
                $user->division = $r->division;
                $user->district = $r->district;
                $user->city = $r->city;
                $user->postal_code = $r->postal_code;
                // /////Image UploadStart////////////
                if ($r->hasFile('image')) {
                    $file = $r->image;
                    $src = $user->id;
                    $srcType = 6;
                    $fileUse = 1;
                    $author = Auth::id();

                    $loadImage = uploadFile($file, $src, $srcType, $fileUse, $author);
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
                // /////Image Upload End////////////
                $user->save();

                Session()->flash('success', 'Your Updated Are Successfully Done!');

            }
            if ($r->actionType == 'change-password') {

                $check = $r->validate([
                    'old_password' => 'required|string|min:8',
                    'password' => 'required|string|min:8|confirmed|different:old_password',
                ]);

                if (Hash::check($r->old_password, $user->password)) {
                    $user->password_show = $r->password;
                    $user->password = Hash::make($r->password);
                    $user->update();
                    Session()->flash('success', 'Your Are Successfully Done');
                } else {
                    Session()->flash('error', 'Current Password Are Not Match');
                }
            }

            return back();
        }

        return view(adminTheme().'users.editProfile', compact('user'));

    }

    public function usersAdmin(Request $r)
    {
        /* ================= Bulk Actions ================= */
        if ($r->action) {

            if (! $r->checkid) {
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
                        if (! $user->trashed()) {
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
            ->whereIn('status', [0, 1])
            ->filterByType('admin')
            ->with(['permission', 'addedBy'])
            ->where(function ($q) use ($r) {

                if ($r->search) {
                    $q->where('name', 'LIKE', "%{$r->search}%")
                        ->orWhere('mobile', 'LIKE', "%{$r->search}%")
                        ->orWhere('email', 'LIKE', "%{$r->search}%")
                        ->orWhere('employee_id', 'LIKE', "%{$r->search}%");
                }

                if ($r->role) {
                    $q->where('permission_id', $r->role);
                }

                if ($r->status) {
                    $q->where('status', $r->status == 'inactive' ? 0 : 1);
                }

                if ($r->startDate || $r->endDate) {
                    $from = $r->startDate ?? Carbon::now()->format('Y-m-d');
                    $to = $r->endDate ?? Carbon::now()->format('Y-m-d');
                    $q->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                }
            })
            ->paginate(25)
            ->appends($r->all());

        $totals = User::withTrashed()->filterByType('admin')
            ->whereIn('status', [0, 1])
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when status=1 then 1 end) as active')
            ->selectRaw('count(case when status=0 then 1 end) as inactive')
            ->selectRaw('count(case when deleted_at IS NOT NULL then 1 end) as deleted')
            ->first();

        $roles = Permission::where('status', 'active')->get();

        if ($r->view == 'deleted') {

            $users = User::onlyTrashed()
                ->filterByType('admin')
                ->latest()
                ->paginate(12)->appends($r->all());

            return view(adminTheme().'users.admins.users_deleted', compact('users', 'totals', 'roles'));
        }

        return view(adminTheme().'users.admins.users', compact('users', 'totals', 'roles'));
    }

    public function usersAdminAction(Request $r, $action, $id = null)
    {
        /* ================= CREATE ADMIN ================= */
        if ($action == 'create' && $r->isMethod('post')) {

            $r->validate([
                'name' => 'required|string|max:255',
                'mobile' => ['nullable', 'digits:11', 'regex:/^0[0-9]{10}$/', 'required_without:email',
                    Rule::unique('users', 'mobile')->whereNull('deleted_at')],
                'email' => ['nullable', 'email', 'max:255', 'required_without:mobile',
                    Rule::unique('users', 'email')->whereNull('deleted_at')],
            ]);

            // Trash check
            $trashQuery = User::onlyTrashed()
                ->where(function ($q) use ($r) {
                    if ($r->mobile) {
                        $q->orWhere('mobile', $r->mobile);
                    }
                    if ($r->email) {
                        $q->orWhere('email', $r->email);
                    }
                });

            if ($trashQuery->exists()) {
                Session()->flash('error', 'This email or mobile exists in trash');

                return redirect()->back();
            }

            $password = Str::random(8);

            $user = new User;
            $user->name = $r->name;
            $user->mobile = $r->mobile;
            $user->email = $r->email ?? null;
            $user->password_show = $password;
            $user->password = Hash::make($password);
            $user->permission_id = 1;
            $user->addedby_at = Carbon::now();
            $user->addedby_id = Auth::id();

            $user->setTypes('admin');
            $user->save();

            Session()->flash('success', 'Admin created successfully!');

            return redirect()->route('admin.usersAdminAction', ['edit', $user->id]);
        }
        /* ================= CREATE END ================= */

        /* ================= RESTORE ================= */
        if ($action == 'restore') {

            $user = User::onlyTrashed()->find($id);

            if (! $user) {
                Session()->flash('error', 'Admin not found in trash');

                return redirect()->route('admin.usersAdmin', ['view' => 'deleted']);
            }

            $user->restore();
            Session()->flash('success', 'Admin restored successfully!');

            return redirect()->route('admin.usersAdmin');
        }

        /* ================= FORCE DELETE ================= */
        if ($action == 'force-delete') {

            $user = User::onlyTrashed()->find($id);

            if (! $user) {
                Session()->flash('error', 'Admin not found in trash');

                return redirect()->route('admin.usersAdmin', ['view' => 'deleted']);
            }

            deleteUserFiles($user->id);
            $user->forceDelete();

            Session()->flash('success', 'Admin permanently deleted!');

            return redirect()->route('admin.usersAdmin', ['view' => 'deleted']);
        }

        /* ================= FIND USER ================= */
        $user = User::filterByType('admin')->find($id);

        if (! $user) {
            Session()->flash('error', 'Admin user not found');

            return redirect()->route('admin.usersAdmin');
        }

        /* ================= UPDATE ================= */
        if ($action == 'update' && $r->isMethod('post')) {

            $r->validate([
                'name' => 'required|max:100|unique:users,name,'.$user->id,
                'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at')],
                'mobile' => ['nullable', 'max:20', Rule::unique('users', 'mobile')->ignore($user->id)->whereNull('deleted_at')],
                'employee_id' => ['nullable', 'max:50', Rule::unique('users', 'employee_id')->ignore($user->id)->whereNull('deleted_at')],
                'status' => 'nullable|in:0,1',
                'role' => 'nullable|exists:permissions,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            $user->name = $r->name;
            $user->email = $r->email;
            $user->mobile = $r->mobile;
            $user->employee_id = $r->employee_id;
            $user->status = $r->has('status') ? (int) $r->status : $user->status;
            $user->permission_id = $r->role ?: null;

            $user->setTypes('admin');

            if ($r->hasFile('image')) {
                uploadFile($r->image, $user->id, 6, 1, Auth::id());
            }

            $user->save();

            Session()->flash('success', 'Admin updated successfully!');

            return redirect()->back();
        }

        /* ================= PASSWORD ================= */
        if ($action == 'change-password' && $r->isMethod('post')) {

            $r->validate([
                'old_password' => 'required|min:8',
                'password' => 'required|min:8|confirmed|different:old_password',
            ]);

            if (! Hash::check($r->old_password, $user->password)) {
                Session()->flash('error', 'Old password not match');

                return redirect()->back();
            }

            $user->password_show = $r->password;
            $user->password = Hash::make($r->password);
            $user->save();

            Session()->flash('success', 'Password updated successfully!');

            return redirect()->back();
        }

        /* ================= SOFT DELETE ================= */
        if ($action == 'delete') {

            $user->deleted_at = Carbon::now();
            $user->deleted_by = Auth::id();
            $user->save();

            Session()->flash('success', 'Admin deleted successfully!');

            return redirect()->route('admin.usersAdmin');
        }

        $roles = Permission::where('status', 'active')->get();

        if ($action == 'view') {
            return view(adminTheme().'users.admins.viewUser', compact('user', 'roles', 'action'));
        }

        return view(adminTheme().'users.admins.editUser', compact('user', 'roles'));
    }

    public function usersCustomer(Request $r)
    {
        $departments = Attribute::latest()->filterBy('department')->where('status', '<>', 'temp')->get(['id', 'name']);
        $designations = Attribute::latest()->filterBy('designation')->where('status', '<>', 'temp')->get(['id', 'name']);
        $divisions = Attribute::latest()->filterBy('divisions')->where('status', '<>', 'temp')->get(['id', 'name']);
        $sections = Attribute::latest()->filterBy('sections')->where('status', '<>', 'temp')->get(['id', 'name']);
        $empTypes = Attribute::latest()->filterBy('employee_type')->where('status', '<>', 'temp')->get(['id', 'name']);

        $departmentsMap = $departments->pluck('name', 'id');
        $designationsMap = $designations->pluck('name', 'id');
        $divisionsMap = $divisions->pluck('name', 'id');
        $sectionsMap = $sections->pluck('name', 'id');
        $empTypesMap = $empTypes->pluck('name', 'id');

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
            ->with(['permission', 'addedBy'])
            ->where(function ($q) use ($r) {
                if ($r->search) {
                    $q->where('name', 'LIKE', '%'.$r->search.'%')
                        ->orWhere('email', 'LIKE', '%'.$r->search.'%')
                        ->orWhere('mobile', 'LIKE', '%'.$r->search.'%')
                        ->orWhere('employee_id', 'LIKE', '%'.$r->search.'%');
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
            'id', 'permission_id', 'name', 'email', 'employee_id', 'designation_id', 'department_id', 'section_id', 'employee_type', 'division', 'joining_date', 'mobile', 'created_at', 'addedby_id', 'status', 'deleted_by', 'deleted_at',
        ])
            ->paginate(25)
            ->appends($r->all());
        // dd($users);

        $totals = User::withTrashed()->filterByType('customer')
            ->whereIn('status', [0, 1])
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when status = 1 then 1 end) as active')
            ->selectRaw('count(case when status = 0 then 1 end) as inactive')
            ->selectRaw('count(case when deleted_at IS NOT NULL then 1 end) as deleted')
            ->first();

        $roles = Permission::latest()->where('status', 'active')->get();

        if ($r->view == 'deleted') {
            return view(adminTheme().'users.customers.users_deleted', compact('users', 'totals', 'roles'));
        } else {
            return view(adminTheme().'users.customers.users', compact(
                'users',
                'totals',
                'roles',
                'departments',
                'designations',
                'divisions',
                'sections',
                'empTypes',
                'departmentsMap',
                'designationsMap',
                'divisionsMap',
                'sectionsMap',
                'empTypesMap',
            ));
        }
    }

    public function usersCustomerAction(Request $r, $action, $id = null)
    {
        try {

            $authId = auth()->id();
            // RESTORE SOFT DELETED USER
            if ($action == 'restore') {
                $user = User::onlyTrashed()->find($id);
                if (! $user) {
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
                $user = new User;
                $user->name = $r->name;
                $user->mobile = $r->mobile;
                $user->email = $r->email;
                $user->password_show = $password;
                $user->password = Hash::make($password);
                $user->status = 1;

                $user->setTypes('customer'); // if you already have setTypes method
                $user->save();

                Session()->flash('success', 'User created successfully!');

                return redirect()->route('admin.usersCustomer');
            }

            $user = $action === 'employee-create'
                ? new User
                : User::whereIn('status', [0, 1])->find($id);

            if (! $user && ! in_array($action, ['employee-create'])) {
                Session()->flash('error', 'User not found.');

                return redirect()->route('admin.usersCustomer');
            }

            // UPDATE USER PROFILE
            if ($action == 'update' && $r->isMethod('post')) {
                try {
                    // Minimal customer edit form sends only a few profile fields.
                    $isSimpleEdit = ! $r->hasAny([
                        'designation_id', 'department_id', 'division_id', 'section_id',
                        'line_number', 'employee_type', 'grade_lavel',
                        'gross_salary', 'basic_salary', 'house_rent', 'medical_allowance',
                        'transport_allowance', 'food_allowance', 'father_name', 'mother_name',
                        'present_village', 'permanent_village',
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
                            'status' => 'nullable|in:0,1',
                            'role' => 'nullable|exists:permissions,id',
                            'password' => 'nullable|min:6|max:100',
                            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,zip,rar,txt',
                        ]);

                        $user->employee_id = $r->employee_id;
                        $user->name = $r->name;
                        $user->bn_name = $r->bn_name;
                        $user->email = $r->email;
                        $user->mobile = $r->mobile;
                        $user->status = $r->has('status') ? (int) $r->status : $user->status;
                        $user->permission_id = $r->has('role') ? ($r->role ?: null) : $user->permission_id;

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
                        $imgName = time().'.'.uniqid().'.'.$ext;
                        $path = 'medies/'.$folder;
                        $fullPath = $path.'/'.$imgName;

                        if (! File::isDirectory(public_path($path))) {
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
                if (! $user) {
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

            if ($action == 'edit' || $action == 'employee-create') {
                $departments = Attribute::latest()->filterBy('department')->where('status', '<>', 'temp')->get();
                $designations = Attribute::latest()->filterBy('designation')->where('status', '<>', 'temp')->get();
                $divisions = Attribute::latest()->filterBy('divisions')->where('status', '<>', 'temp')->get();
                $grades = Attribute::latest()->filterBy('grades')->where('status', '<>', 'temp')->get();
                $lines = Attribute::latest()->filterBy('line_number')->where('status', '<>', 'temp')->get();
                $sections = Attribute::latest()->filterBy('sections')->where('status', '<>', 'temp')->get();
                $emp_types = Attribute::latest()->where('type', 16)->where('status', '<>', 'temp')->get();

                $roles = Permission::latest()->where('status', 'active')->get();

                return view(adminTheme().'users.customers.editUser', compact('user', 'departments', 'designations', 'divisions', 'grades', 'lines', 'sections', 'roles', 'emp_types'));

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
                    if ($file && File::exists($file->file_url)) {
                        File::delete($file->file_url);
                    }

                    if ($fileAction == 'removeData') {
                        $file?->delete();
                    }
                    if ($fileAction == 'removeFile') {
                        $file?->update([
                            'file_url' => null, 'file_path' => null, 'alt_text' => null, 'file_rename' => null, 'file_size' => null,
                        ]);
                    }
                }

                if ($fileAction == 'updateTitle' && $fileId) {
                    $file = $user->galleryFiles()->find($fileId);
                    if ($file) {
                        $file->update(['file_name' => $r->title]);
                    }
                }

                if ($fileAction == 'updateFile' && $fileId && $r->hasFile('file')) {
                    $fileData = $user->galleryFiles()->find($fileId);
                    if ($fileData) {
                        if (File::exists($fileData->file_url)) {
                            File::delete($fileData->file_url);
                        }

                        $file = $r->file;
                        $ext = $file->getClientOriginalExtension();
                        $size = $file->getSize();
                        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $folder = now()->format('M_Y');
                        $imgName = time().'.'.uniqid().'.'.$ext;
                        $path = 'medies/'.$folder;
                        $fullPath = ''.$path.'/'.$imgName;

                        $fileData->update([
                            'alt_text' => Str::limit($name, 250),
                            'file_rename' => $imgName,
                            'file_size' => $size,
                            'file_type' => match (strtolower($ext)) {
                                'png','jpeg','jpg','gif','svg','webp' => 1,
                                'pdf' => 2,
                                'docx' => 3,
                                'zip','rar' => 4,
                                'mp4','webm','mov','wmv' => 5,
                                'mp3' => 6,
                                default => 0
                            },
                            'file_url' => $fullPath,
                            'file_path' => $path,
                        ]);

                        $file->move(public_path($path), $imgName);
                    }
                }
                $view = view(adminTheme().'users.customers.includes.userFiles', compact('user'))->render();

                return response()->json(['success' => true, 'view' => $view]);
            }

            // Return view
            $startDate = $r->startDate ? Carbon::parse($r->startDate) : Carbon::now()->startOfMonth();
            $endDate = $r->endDate ? Carbon::parse($r->endDate) : Carbon::now();

            $selectedGrade = null;
            if (! empty($user->grade_lavel)) {
                $selectedGrade = Attribute::find($user->grade_lavel);
            }

            return view(adminTheme().'users.customers.viewUser', compact('user', 'action', 'startDate', 'endDate', 'selectedGrade'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function userRoles(Request $r)
    {

        $roles = Permission::latest()
            ->where('status', 'active')
            ->where(function ($q) use ($r) {

                if ($r->search) {
                    $q->where('name', 'LIKE', '%'.$r->search.'%');
                }

                if ($r->startDate || $r->endDate) {
                    if ($r->startDate) {
                        $from = $r->startDate;
                    } else {
                        $from = Carbon::now()->format('Y-m-d');
                    }

                    if ($r->endDate) {
                        $to = $r->endDate;
                    } else {
                        $to = Carbon::now()->format('Y-m-d');
                    }

                    $q->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);

                }

            })
            ->select(['id', 'name', 'created_at', 'addedby_id', 'status'])
            ->paginate(25)->appends([
                'search' => $r->search,
                'startDate' => $r->startDate,
                'endDate' => $r->endDate,
            ]);

        return view(adminTheme().'users.roles.userRoles', compact('roles'));
    }

    public function userRoleAction(Request $r, $action, $id = null)
    {
        if ($action == 'create') {
            $role = Permission::where('addedby_id', Auth::id())->where('status', 'temp')->first();
            if (! $role) {
                $role = new Permission;
                $role->status = 'temp';
                $role->addedby_id = Auth::id();
            }
            $role->created_at = Carbon::now();
            $role->save();

            return redirect()->route('admin.userRoleAction', ['edit', $role->id]);
        }

        $role = Permission::find($id);
        if (! $role) {
            Session()->flash('error', 'This Role Are Not Found');

            return redirect()->route('admin.userRoles');
        }

        if ($action == 'update') {
            // Role Update
            $check = $r->validate([
                'name' => 'required|max:100',
            ]);

            if ($role->id == 1) {
                $role->name = $r->name;
                $role->permission = $r->permission;
            } else {
                $role->name = $r->name;
                $role->permission = $r->permission;
            }
            $role->status = 'active';
            $role->save();

            Session()->flash('success', 'Role Updated Are Successfully Done!');

            return redirect()->back();
        }

        if ($action == 'delete') {
            // Role Delete
            $role->delete();

            Session()->flash('success', 'Role Deleted Are Successfully Done!');

            return redirect()->route('admin.userRoles');

        }

        return view(adminTheme().'users.roles.userRoleEdit', compact('role'));

    }

    public function setting($type)
    {

        $general = General::first();
        if ($type == 'general') {
            return view(adminTheme().'setting.general', compact('general', 'type'));
        } elseif ($type == 'mail') {
            return view(adminTheme().'setting.mail', compact('general', 'type'));
        } elseif ($type == 'sms') {
            return view(adminTheme().'setting.sms', compact('general', 'type'));
        } elseif ($type == 'social') {
            return view(adminTheme().'setting.social', compact('general', 'type'));
        } elseif ($type == 'document') {
            return view(adminTheme().'setting.document', compact('general', 'type'));
        } elseif ($type == 'support') {
            return view(adminTheme().'setting.support', compact('general', 'type'));
        } elseif ($type == 'logo') {

            if (File::exists($general->logo)) {
                File::delete($general->logo);
            }
            $general->logo = null;
            $general->save();

            Session()->flash('success', 'Logo Deleted Are Successfully Done!');

            return redirect()->back();
        } elseif ($type == 'favicon') {
            if (File::exists($general->favicon)) {
                File::delete($general->favicon);
            }
            $general->favicon = null;
            $general->save();

            Session()->flash('success', 'Logo Deleted Are Successfully Done!');

            return redirect()->back();
        } elseif ($type == 'signature') {
            if (File::exists($general->signature)) {
                File::delete($general->signature);
            }
            $general->signature = null;
            $general->save();

            Session()->flash('success', 'Banner Deleted Are Successfully Done!');

            return redirect()->back();
        } elseif ($type == 'cache-clear') {

            // return view(adminTheme().'setting.cacheDatabase',compact('general','type'));

            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('clear-compiled');

            Session()->flash('success', 'Cache Clear Are Successfully Done!');

            return redirect(url('/ecom9/admin/dashboard'));

        } else {
            return redirect()->route('admin.setting', 'general', 'type');
        }

    }

    public function settingUpdate(Request $r, $type)
    {

        $general = General::first();

        if ($type == 'general') {

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

            $general->title = $r->title;
            $general->subtitle = $r->subtitle;
            $general->mobile = $r->mobile;
            $general->mobile2 = $r->mobile2;
            $general->email = $r->email;
            $general->email2 = $r->email2;
            $general->address_one = $r->address_one;
            $general->address_two = $r->address_two;
            $general->currency = $r->currency;
            $general->currency_decimal = $r->currency_decimal;
            $general->currency_position = $r->currency_position;
            $general->website = $r->website;
            $general->meta_author = $r->meta_author;
            $general->meta_title = $r->meta_title;
            $general->meta_keyword = $r->meta_keyword;
            $general->meta_description = $r->meta_description;
            $general->script_head = $r->script_head;
            $general->script_body = $r->script_body;
            $general->custom_css = $r->custom_css;
            $general->custom_js = $r->custom_js;
            $general->copyright_text = $r->footer_text;
            $general->pi_terms_condition = $r->pi_terms_condition;

            // /////Image UploadStart////////////

            if ($r->hasFile('logo')) {

                $file = $r->logo;
                if (File::exists($general->logo)) {
                    File::delete($general->logo);
                }

                $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
                $fullName = basename($file->getClientOriginalName());
                $ext = $file->getClientOriginalExtension();
                $size = $file->getSize();

                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('M');
                $folder = $month.'_'.$year;

                $img = time().'.'.uniqid().'.'.$file->getClientOriginalExtension();
                $path = 'medies/'.$folder;
                $fullPath = 'medies/'.$folder.'/'.$img;

                $file->move(public_path($path), $img);
                $general->logo = $fullPath;

            }

            // /////Image UploadStart////////////

            if ($r->hasFile('favicon')) {

                $file = $r->favicon;

                if (File::exists($general->favicon)) {
                    File::delete($general->favicon);
                }

                $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
                $fullName = basename($file->getClientOriginalName());
                $ext = $file->getClientOriginalExtension();
                $size = $file->getSize();

                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('M');
                $folder = $month.'_'.$year;

                $img = time().'.'.uniqid().'.'.$file->getClientOriginalExtension();
                $path = 'medies/'.$folder;
                $fullPath = 'medies/'.$folder.'/'.$img;

                $file->move(public_path($path), $img);
                $general->favicon = $fullPath;

            }

            if ($r->hasFile('signature')) {

                $file = $r->signature;

                if (File::exists($general->signature)) {
                    File::delete($general->signature);
                }

                $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
                $fullName = basename($file->getClientOriginalName());
                $ext = $file->getClientOriginalExtension();
                $size = $file->getSize();

                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('M');
                $folder = $month.'_'.$year;

                $img = time().'.'.uniqid().'.'.$file->getClientOriginalExtension();
                $path = 'medies/'.$folder;
                $fullPath = 'medies/'.$folder.'/'.$img;

                $file->move(public_path($path), $img);
                $general->signature = $fullPath;

            }
            $general->commingsoon_mode = $r->commingsoon_mode ? true : false;
            $general->save();

            Session()->flash('success', 'General Updated Are Successfully Done!');

        }

        if ($type == 'mail') {

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

            $general->mail_from_address = $r->mail_from_address;
            $general->mail_from_name = $r->mail_from_name;
            $general->mail_driver = $r->mail_driver;
            $general->mail_host = $r->mail_host;
            $general->mail_port = $r->mail_port;
            $general->mail_encryption = $r->mail_encryption;
            $general->mail_username = $r->mail_username;
            $general->mail_password = $r->mail_password;
            $general->admin_mails = $r->admin_mails;
            $general->mail_status = $r->mail_status ? true : false;
            $general->register_mail_user = $r->register_mail_user ? true : false;
            $general->register_mail_author = $r->register_mail_author ? true : false;
            $general->forget_password_mail_user = $r->forget_password_mail_user ? true : false;
            $general->register_verify_mail_user = $r->register_verify_mail_user ? true : false;
            $general->save();

            Session()->flash('success', 'Mail Updated Are Successfully Done!');

        }

        if ($type == 'sms') {

            $check = $r->validate([
                'sms_type' => 'nullable|max:50',
                'sms_senderid' => 'nullable|max:50',
                'sms_url_nonmasking' => 'nullable|max:200',
                'sms_url_masking' => 'nullable|max:200',
                'sms_username' => 'nullable|max:50',
                'sms_password' => 'nullable|max:50',
                'admin_numbers' => 'nullable|max:1000',
            ]);

            $general->sms_type = $r->sms_type;
            $general->sms_senderid = $r->sms_senderid;
            $general->sms_url_nonmasking = $r->sms_url_nonmasking;
            $general->sms_url_masking = $r->sms_url_masking;
            $general->sms_username = $r->sms_username;
            $general->sms_password = $r->sms_password;
            $general->admin_numbers = $r->admin_numbers;
            $general->sms_status = $r->sms_status ? true : false;
            $general->register_sms_user = $r->register_sms_user ? true : false;
            $general->register_sms_author = $r->register_sms_author ? true : false;
            $general->forget_password_sms_user = $r->forget_password_sms_user ? true : false;
            $general->register_verify_sms_user = $r->register_verify_sms_user ? true : false;
            $general->save();

            Session()->flash('success', 'SMS Updated Are Successfully Done!');

        }

        if ($type == 'social') {

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

            $general->facebook_link = $r->facebook_link;
            $general->twitter_link = $r->twitter_link;
            $general->instagram_link = $r->instagram_link;
            $general->linkedin_link = $r->linkedin_link;
            $general->pinterest_link = $r->pinterest_link;
            $general->youtube_link = $r->youtube_link;
            $general->fb_app_id = $r->fb_app_id;
            $general->fb_app_secret = $r->fb_app_secret;
            $general->fb_app_redirect_url = $r->fb_app_redirect_url;
            $general->google_client_id = $r->google_client_id;
            $general->google_client_secret = $r->google_client_secret;
            $general->google_client_redirect_url = $r->google_client_redirect_url;
            $general->tw_app_id = $r->tw_app_id;
            $general->tw_app_secret = $r->tw_app_secret;
            $general->tw_app_redirect_url = $r->tw_app_redirect_url;
            $general->save();

            Session()->flash('success', 'Advance Updated Are Successfully Done!');

        }

        return redirect()->route('admin.setting', $type);

    }
}
