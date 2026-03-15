<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('admin.employee_portal.dashboard', compact('todayAttendance', 'presentDays', 'absentDays', 'pendingLeaves', 'notices'));
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

        return view('admin.employee_portal.daily_attendance', compact('attendance', 'date'));
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

        return view('admin.employee_portal.online_attendance');
    }

    /**
     * Personal Information View
     */
    public function myProfile()
    {
        $user = Auth::user();
        return view('admin.employee_portal.profile', compact('user'));
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

        return view('admin.employee_portal.monthly_attendance', compact('attendances', 'leaves', 'month', 'year'));
    }
}
