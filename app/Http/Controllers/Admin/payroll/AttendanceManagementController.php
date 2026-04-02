<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\payroll\Attendance;
use App\Models\payroll\AttendanceMachineLog;
use App\Models\payroll\Holiday;
use App\Models\payroll\Leave;
use App\Models\payroll\Roaster;
use App\Models\payroll\Salary;
use App\Models\payroll\Shift;
use App\Models\payroll\UserLocation;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Faker\Provider\pt_PT\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceManagementController extends Controller
{
    /**
     * Check if given time is within card accept window
     */
    private function withinCardAcceptWindow(?Shift $shift, Carbon $time): bool
    {
        if (!$shift || !$shift->card_accept_from || !$shift->card_accept_to) return true;

        $from = Carbon::parse($time->toDateString().' '.$shift->card_accept_from, 'Asia/Dhaka');
        $to   = Carbon::parse($time->toDateString().' '.$shift->card_accept_to, 'Asia/Dhaka');
        if ($shift->card_accept_to_next_day) $to->addDay();

        return $time->betweenIncluded($from, $to);
    }

    /**
     * Get shift start datetime
     */
    private function shiftStartDateTime(?Shift $shift, Carbon $time): ?Carbon
    {
        if (!$shift || !$shift->shift_starting_time) return null;
        return Carbon::parse($time->toDateString().' '.$shift->shift_starting_time, 'Asia/Dhaka');
    }

    /**
     * Get shift end datetime
     */
    private function shiftEndDateTime(?Shift $shift, Carbon $time): ?Carbon
    {
        if (!$shift || !$shift->shift_closing_time) return null;

        $end = Carbon::parse($time->toDateString().' '.$shift->shift_closing_time, 'Asia/Dhaka');
        if ($shift->shift_closing_time_next_day) $end->addDay();
        return $end;
    }

    /**
     * Get overtime end datetime
     */
    private function overtimeEndDateTime(?Shift $shift, Carbon $time): ?Carbon
    {
        if (!$shift) return null;

        $candidates = [];

        if ($shift->over_time_allowed_up_to) {
            $t = Carbon::parse($time->toDateString().' '.$shift->over_time_allowed_up_to, 'Asia/Dhaka');
            if ($shift->over_time_allowed_up_to_next_day) $t->addDay();
            $candidates[] = $t;
        }

        if ($shift->over_time_1_allowed_up_to) {
            $t = Carbon::parse($time->toDateString().' '.$shift->over_time_1_allowed_up_to, 'Asia/Dhaka');
            if ($shift->over_time_1_allowed_up_to_next_day) $t->addDay();
            $candidates[] = $t;
        }

        if (empty($candidates)) return null;

        return collect($candidates)->sort()->last();
    }

    /**
     * Check if weekly overtime is allowed for the given day
     */
    private function isWeeklyOvertimeAllowed(?Shift $shift, Carbon $time): bool
    {
        if (!$shift || !$shift->weekly_overtime_allowed) return false;

        $map = [
            6 => 'weekly_ot_sat',
            0 => 'weekly_ot_sun',
            1 => 'weekly_ot_mon',
            2 => 'weekly_ot_tue',
            3 => 'weekly_ot_wed',
            4 => 'weekly_ot_thu',
        ];

        $key = $map[$time->dayOfWeek] ?? null;
        if (!$key) return true;

        return (bool) $shift->{$key};
    }

    /**
     * Apply shift logic to attendance
     */
    private function applyShiftLogic(Attendance $attendance, ?Shift $shift): void
    {
        if (!$attendance->in_time || !$shift) {
            $attendance->status = 'Present';
            $attendance->in_minutes = 0;
            $attendance->overtime_minutes = 0;
            return;
        }

        // Calculate status based on shift start time
        $shiftStart = $this->shiftStartDateTime($shift, $attendance->in_time);
        if ($shiftStart) {
            $attendance->status = $attendance->in_time->greaterThan($shiftStart) ? 'Late' : 'Present';
        } else {
            $attendance->status = 'Present';
        }

        // Calculate in_minutes and overtime_minutes if out_time exists
        if ($attendance->in_time && $attendance->out_time) {
            $attendance->in_minutes = $attendance->in_time->diffInMinutes($attendance->out_time);

            $shiftEnd = $this->shiftEndDateTime($shift, $attendance->in_time);
            $otEnd    = $this->overtimeEndDateTime($shift, $attendance->in_time);

            if ($shiftEnd && $this->isWeeklyOvertimeAllowed($shift, $attendance->in_time)) {
                $cap = $otEnd ?? $attendance->out_time;
                $out = $attendance->out_time->lt($cap) ? $attendance->out_time : $cap;

                $attendance->overtime_minutes = $out->greaterThan($shiftEnd)
                    ? $shiftEnd->diffInMinutes($out)
                    : 0;
            } else {
                $attendance->overtime_minutes = 0;
            }
        } else {
            $attendance->in_minutes = 0;
            $attendance->overtime_minutes = 0;
        }
    }

    /**
     * Roaster Management - List
     */
    public function roasterIndex(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');
        $department_id = $request->department_id;

        $query = Roaster::with(['user.department', 'user.designation', 'shift'])
            ->where('roster_date', $date);

        if ($department_id) {
            $query->whereHas('user', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }
        if ($request->employee_id) {
            $query->where('user_id', $request->employee_id);
        }

        $roasters = $query->get();

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $shifts = Shift::where('status', 'active')->get();
        $employees = User::where('employee_status', 'active')->filterByType('employee')->get();

        return view(adminTheme().'payroll.attendance.roaster_index', compact('roasters', 'departments', 'shifts', 'employees', 'date'));
    }

    /**
     * Roaster Management - Create
     */
    public function roasterCreate(Request $request)
    {
        $employees = User::where('employee_status', 'active')
            ->with(['department', 'designation'])
            ->filterByType('employee')
            ->get();

        $shifts = Shift::where('status', 'active')->get();

        return view(adminTheme().'payroll.attendance.roaster_create', compact('employees', 'shifts'));
    }

    /**
     * Roaster Management - Store
     */
    public function roasterStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'employee_ids' => 'required|array',
                'roster_date' => 'required|date',
                'shift_id' => 'required|exists:shifts,id',
            ]);

            $shift = Shift::findOrFail($request->shift_id);

            foreach ($request->employee_ids as $employeeId) {
                Roaster::updateOrCreate(
                    [
                        'user_id' => $employeeId,
                        'roster_date' => $request->roster_date,
                    ],
                    [
                        'shift_id' => $request->shift_id,
                        'in_time' => $shift->in_time,
                        'out_time' => $shift->out_time,
                        'day_type' => $request->day_type ?? 'working',
                        'remarks' => $request->remarks,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.attendance.roaster.index')->with('success', 'Roaster created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->with('error', 'Error creating roaster: ' . $e->getMessage());
        }
    }

    /**
     * Roaster Management - Bulk Update
     */
    public function roasterBulkUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'user_ids' => 'required|array',
                'shift_id' => 'required|exists:shifts,id',
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $shift = Shift::findOrFail($request->shift_id);
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                foreach ($request->user_ids as $userId) {
                    Roaster::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'roster_date' => $currentDate->format('Y-m-d'),
                        ],
                        [
                            'shift_id' => $request->shift_id,
                            'in_time' => $shift->in_time,
                            'out_time' => $shift->out_time,
                            'day_type' => 'working',
                        ]
                    );
                }
                $currentDate->addDay();
            }

            DB::commit();
            return back()->with('success', 'Roasters updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return back()->with('error', 'Error updating roasters: ' . $e->getMessage());
        }
    }

    /**
     * Roaster Management - Update
     */
    public function roasterUpdate(Request $request, $id)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'roster_date' => 'required|date',
        ]);

        try {
            $roaster = Roaster::findOrFail($id);
            $shift = Shift::findOrFail($request->shift_id);

            $roaster->update([
                'shift_id' => $request->shift_id,
                'in_time' => $shift->in_time,
                'out_time' => $shift->out_time,
                'roster_date' => $request->roster_date,
            ]);

            return back()->with('success', 'Roaster updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating roaster: ' . $e->getMessage());
        }
    }

    /**
     * Roaster Management - Destroy
     */
    public function roasterDestroy($id)
    {
        try {
            $roaster = Roaster::findOrFail($id);
            $roaster->delete();

            return back()->with('success', 'Roaster deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting roaster: ' . $e->getMessage());
        }
    }

    /**
     * Process Attendance from Machine Data
     */
    public function processAttendance(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        // Check if date is a holiday
        $holiday = Holiday::getHoliday($date);

        // Get weekly offday from settings (default is Friday = 5)
        $offdaySetting = Attribute::where('type', 21)->where('status', 'active')->first();
        $offdayNumber = $offdaySetting ? array_search($offdaySetting->name, ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']) : 5;
        $isWeeklyOff = Carbon::parse($date)->dayOfWeek == $offdayNumber;

        // Get machine logs for the date
        $machineLogs = DB::table('attendance_machine_logs')
            ->where('punch_date', $date)
            ->orderBy('punch_time')
            ->get();

        if ($machineLogs->isEmpty() && !$holiday && !$isWeeklyOff) {
            return back()->with('error', 'No machine data found for this date!');
        }

        DB::beginTransaction();
        try {
            $processedData = [];

            // If it's a holiday, mark all users as holiday
            if ($holiday) {
                $allUsers = User::where('status', 1)->get();
                foreach ($allUsers as $user) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'date' => $date,
                        ],
                        [
                            'status' => 'holiday',
                            'remarks' => 'Holiday: ' . $holiday->title,
                        ]
                    );
                }
                $processedData[] = 'Holiday: ' . $holiday->title;
            }

            // If it's weekly off day (configured in settings), mark all users as weekly_off
            if ($isWeeklyOff && !$holiday) {
                $allUsers = User::where('status', 1)->get();
                foreach ($allUsers as $user) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'date' => $date,
                        ],
                        [
                            'status' => 'weekly_off',
                            'remarks' => 'Weekly Off (' . ($offdaySetting ? $offdaySetting->name : 'Friday') . ')',
                        ]
                    );
                }
                $processedData[] = 'Weekly Off (' . ($offdaySetting ? $offdaySetting->name : 'Friday') . ')';
            }

            // Group by user for regular attendance processing
            $groupedLogs = $machineLogs->groupBy('user_id');

            foreach ($groupedLogs as $userId => $logs) {
                $firstPunch = $logs->first();
                $lastPunch = $logs->last();

                $user = User::with('shift')->find($userId);
                if (!$user) continue;

                $inTime = Carbon::parse($date . ' ' . $firstPunch->punch_time);
                $outTime = $logs->count() > 1 ? Carbon::parse($date . ' ' . $lastPunch->punch_time) : null;

                // Use centralized shift computation to keep attendance metrics aligned.
                $tempAtt           = new Attendance();
                $tempAtt->in_time  = $inTime;
                $tempAtt->out_time = $outTime;

                if ($user->shift) {
                    $tempAtt->computeFromShift($user->shift);
                } else {
                    $wm = $outTime ? (int) $inTime->diffInMinutes($outTime) : 0;
                    $tempAtt->attributes['in_minutes']       = $wm;
                    $tempAtt->attributes['work_hour']        = round($wm / 60, 2);
                    $tempAtt->attributes['late_time']        = 0;
                    $tempAtt->attributes['early_out']        = 0;
                    $tempAtt->attributes['overtime_minutes'] = 0;
                    $tempAtt->attributes['overtime']         = 0;
                    $tempAtt->attributes['status']           = 'present';
                }

                // Create or update attendance
                Attendance::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'date' => $date,
                    ],
                    [
                        'in_time' => $inTime->format('H:i:s'),
                        'out_time' => $outTime ? $outTime->format('H:i:s') : null,
                        'in_minutes' => $tempAtt->attributes['in_minutes'],
                        'work_hour' => $tempAtt->attributes['work_hour'],
                        'late_time' => $tempAtt->attributes['late_time'],
                        'early_out' => $tempAtt->attributes['early_out'],
                        'overtime_minutes' => $tempAtt->attributes['overtime_minutes'],
                        'overtime' => $tempAtt->attributes['overtime'],
                        'status' => strtolower($tempAtt->attributes['status']),
                    ]
                );

                $processedData[] = [
                    'user' => $user,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'late_minutes' => $tempAtt->attributes['late_time'],
                    'work_hours' => $tempAtt->attributes['work_hour'],
                ];
            }

            DB::commit();
            return back()->with('success', 'Attendance processed successfully for ' . count($processedData) . ' employees!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing attendance: ' . $e->getMessage());
        }
    }

    /**
     * Daily Attendance Report
     */
    public function dailyAttendanceReport(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');
        $status = $request->status; // all, present, late, leave, absent
        $department_id = $request->department_id;

        $query = Attendance::with(['user.department', 'user.designation'])
            ->where('date', $date);

        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        if ($department_id) {
            $query->whereHas('user.department', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $attendances = $query->get();

        // Get statistics
        $stats = [
            'total' => Attendance::where('date', $date)->count(),
            'present' => Attendance::where('date', $date)->where('status', 'present')->count(),
            'late' => Attendance::where('date', $date)->where('status', 'late')->count(),
            'absent' => Attendance::where('date', $date)->where('status', 'absent')->count(),
            'leave' => Attendance::where('date', $date)->where('status', 'leave')->count(),
            'weekly_off' => Attendance::where('date', $date)->where('status', 'weekly_off')->count(),
            'holiday' => Attendance::where('date', $date)->where('status', 'holiday')->count(),
        ];

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();

        return view(adminTheme().'payroll.attendance.daily_report', compact('attendances', 'stats', 'date', 'departments'));
    }

    /**
     * Machine Log Index - View attendance machine logs
     */
    public function machineLogIndex(Request $request)
    {
        // Get start and end dates
        $date = $request->date ?? Carbon::today()->format('Y-m-d');
        $to_date = $request->to_date ?? $date; // if to_date not given, use date

        $user_id = $request->user_id;
        $device_sn = $request->device_sn;

        $query = AttendanceMachineLog::with('user');

        // Filter by date range
        if ($date && $to_date) {
            $query->whereBetween('log_time', [
                Carbon::parse($date)->startOfDay(),
                Carbon::parse($to_date)->endOfDay()
            ]);
        }

        // Filter by user
        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        // Filter by device
        if ($device_sn) {
            $query->where('device_sn', $device_sn);
        }

        $logs = $query->orderBy('log_time', 'desc')
                ->paginate(50)
                ->appends([
                    'date' => $date,
                    'to_date' => $to_date,
                    'user_id' => $user_id,
                    'device_sn' => $device_sn,
                ]);

        // Get unique device serial numbers
        $devices = AttendanceMachineLog::distinct()->pluck('device_sn')->filter();

        // Get employees for filter
        $employees = User::where('employee_status', 'active')
            ->with(['department', 'designation'])
            ->filterByType('employee')
            ->get();

        return view(adminTheme().'payroll.attendance.machine_log_index', compact(
            'logs', 'date', 'to_date', 'devices', 'employees', 'user_id', 'device_sn'
        ));
    }

    /**
     * Attendance Summary
     */
    public function attendanceSummary(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $user_id = $request->user_id;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        if ($user_id) {
            // Individual employee summary
            $user = User::findOrFail($user_id);

            $attendances = Attendance::where('user_id', $user_id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $summary = [
                'total_days' => $startDate->daysInMonth,
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'leave' => $attendances->where('status', 'leave')->count(),
                'weekly_off' => $attendances->where('status', 'weekly_off')->count(),
                'holiday' => $attendances->where('status', 'holiday')->count(),
                'total_work_hours' => $attendances->sum('work_hour'),
                'total_overtime' => $attendances->sum('overtime'),
            ];

            return view(adminTheme().'payroll.attendance.individual_summary', compact('user', 'attendances', 'summary', 'month', 'year'));
        } else {
            // All employees summary
            $employees = User::where('status', 'active')->filterByType('employee')->get();

            $summaries = [];
            foreach ($employees as $employee) {
                $attendances = Attendance::where('user_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $summaries[] = [
                    'employee' => $employee,
                    'present' => $attendances->whereIn('status', ['present', 'late'])->count(),
                    'late' => $attendances->where('status', 'late')->count(),
                    'absent' => $attendances->where('status', 'absent')->count(),
                    'leave' => $attendances->where('status', 'leave')->count(),
                    'work_hours' => $attendances->sum('work_hour'),
                    'overtime' => $attendances->sum('overtime'),
                ];
            }

            return view(adminTheme().'payroll.attendance.all_summary', compact('summaries', 'month', 'year'));
        }
    }

    /**
     * Monthly Attendance Report
     */
    public function monthlyAttendanceReport(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $department_id = $request->department_id;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $query = User::where('status', 'active')->filterByType('employee');

        if ($department_id) {
            $query->whereHas('department', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $employees = $query->get();

        $reportData = [];
        foreach ($employees as $employee) {
            $attendances = Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->keyBy('date');

            $dailyData = [];
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->format('Y-m-d');
                $attendance = $attendances->get($dateStr);

                $dailyData[] = [
                    'date' => $currentDate->copy(),
                    'status' => $attendance ? $attendance->status : 'absent',
                    'in_time' => $attendance ? $attendance->in_time : null,
                    'out_time' => $attendance ? $attendance->out_time : null,
                ];

                $currentDate->addDay();
            }

            $reportData[] = [
                'employee' => $employee,
                'daily_data' => $dailyData,
                'present_count' => $attendances->whereIn('status', ['present', 'late'])->count(),
                'absent_count' => $attendances->where('status', 'absent')->count(),
                'leave_count' => $attendances->where('status', 'leave')->count(),
            ];
        }

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();

        return view(adminTheme().'payroll.attendance.monthly_report', compact('reportData', 'month', 'year', 'departments'));
    }

    /**
     * Monthly Attendance Summary (Grid View with Date Range)
     */
    public function monthlyAttendanceSummary(Request $request)
    {
        // Default to current month
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $employee_id = $request->employee_id;
        $department_id = $request->department_id;

        // Get employees - same as daily attendance
        $query = User::filterByType('employee')->whereIn('status', [0, 1]);

        if ($employee_id) {
            $query->where('id', $employee_id);
        }

        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        $employees = $query->orderBy('employee_id')->get();

        // Get holidays for the date range (using from_date and to_date)
        $holidays = Holiday::where('status', 'active')
            ->whereDate('from_date', '<=', $endDate->format('Y-m-d'))
            ->whereDate('to_date', '>=', $startDate->format('Y-m-d'))
            ->get();

        // Build holiday dates array
        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $current = Carbon::parse($holiday->from_date);
            $toDate = Carbon::parse($holiday->to_date);
            while ($current->lte($toDate)) {
                $holidayDates[$current->format('Y-m-d')] = $holiday->title;
                $current->addDay();
            }
        }

        // Get leaves for the date range
        $leaves = Leave::where('status', 'approved')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orWhere(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                        ->where('end_date', '>=', $endDate->format('Y-m-d'));
                });
            })
            ->get();

        // Get weekly offday
        $offdaySetting = Attribute::where('type', 21)->where('status', 'active')->first();
        $offdayNumber = $offdaySetting ? array_search($offdaySetting->name, ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']) : 5;

        // Build date range array
        $dateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateRange[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // Get attendance data - using in_time like daily attendance
        $attendances = Attendance::whereIn('user_id', $employees->pluck('id'))
            ->whereDate('in_time', '>=', $startDate->format('Y-m-d'))
            ->whereDate('in_time', '<=', $endDate->format('Y-m-d'))
            ->get()
            ->groupBy(function ($item) {
                return $item->user_id . '_' . Carbon::parse($item->in_time)->format('Y-m-d');
            });

        // Build report data
        $reportData = [];
        foreach ($employees as $employee) {
            $dailyData = [];
            $presentCount = 0;
            $absentCount = 0;
            $leaveCount = 0;
            $holidayCount = 0;
            $weeklyOffCount = 0;

            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                // Check if it's a holiday
                $isHoliday = isset($holidayDates[$dateStr]);

                // Check if it's weekly off
                $isWeeklyOff = ($dayOfWeek == $offdayNumber);

                // Get attendance record using the same key format as daily attendance
                $key = $employee->id . '_' . $dateStr;
                $attendance = $attendances->get($key)?->first();

                // Get leave for this employee on this date
                $leave = $leaves->where('user_id', $employee->id)
                    ->filter(function($l) use ($date) {
                        return $date->between($l->start_date, $l->end_date);
                    })->first();

                // Determine status
                $status = '';
                $statusClass = '';

                if ($leave) {
                    $status = 'L';
                    $statusClass = 'leave';
                    $leaveCount++;
                } elseif ($attendance) {
                    if (in_array($attendance->status, ['present', 'late'])) {
                        $status = 'P';
                        $statusClass = 'present';
                        $presentCount++;
                    } elseif ($attendance->status == 'absent') {
                        $status = 'A';
                        $statusClass = 'absent';
                        $absentCount++;
                    } elseif ($attendance->status == 'holiday') {
                        $status = 'H';
                        $statusClass = 'holiday';
                        $holidayCount++;
                    } elseif ($attendance->status == 'weekly_off') {
                        $status = 'H';
                        $statusClass = 'holiday';
                        $weeklyOffCount++;
                    } else {
                        // Any other status, show as present
                        $status = 'P';
                        $statusClass = 'present';
                        $presentCount++;
                    }
                } elseif ($isHoliday) {
                    $status = 'H';
                    $statusClass = 'holiday';
                    $holidayCount++;
                } elseif ($isWeeklyOff) {
                    $status = 'H';
                    $statusClass = 'holiday';
                    $weeklyOffCount++;
                } else {
                    $status = '-';
                    $statusClass = 'absent';
                    $absentCount++;
                }

                $dailyData[] = [
                    'date' => $date,
                    'day' => $date->format('j'),
                    'dayName' => $date->format('d M'),
                    'status' => $status,
                    'status_class' => $statusClass,
                ];
            }

            $reportData[] = [
                'employee' => $employee,
                'daily_data' => $dailyData,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'leave_count' => $leaveCount,
                'holiday_count' => $holidayCount + $weeklyOffCount,
                'total_days' => count($dateRange),
            ];
        }

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $allEmployees = User::filterByType('employee')->whereIn('status', [0, 1])->orderBy('employee_id')->get();

        return view(adminTheme().'payroll.attendance.monthly_summary', compact(
            'reportData',
            'dateRange',
            'startDate',
            'endDate',
            'departments',
            'allEmployees',
            'employee_id',
            'department_id'
        ));
    }

    public function attendanceExport(Request $request)
    {
        $startDate = Carbon::parse($request->start_date ?? Carbon::now()->startOfMonth());
        $endDate = Carbon::parse($request->end_date ?? Carbon::now()->endOfMonth());
        $department_id = $request->department_id;
        $employee_id = $request->employee_id;

        // Get employees
        $employees = User::filterByType('employee')
            ->whereIn('status', [0, 1])
            ->with(['designation', 'department']);

        if ($department_id) {
            $employees = $employees->where('department_id', $department_id);
        }
        if ($employee_id) {
            $employees = $employees->where('id', $employee_id);
        }
        $employees = $employees->get();

        // Get attendances
        $attendances = Attendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status', ['present', 'absent', 'late'])
            ->get();

        // Return Excel export
        $export = new \App\Exports\AttendanceExport(
            $attendances,
            $startDate->format('m'),
            $startDate->format('Y')
        );

        return \Maatwebsite\Excel\Facades\Excel::download($export, 'attendance_'.$startDate->format('Ym').'.xlsx');
    }

    /**
     * Individual Employee Attendance Report (Grid View)
     */
    public function individualAttendanceReport(Request $request)
    {
        $employee_id = $request->employee_id;
        $month = $request->month ?? Carbon::now()->format('Y-m');

        // Parse month
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        // Get all employees for dropdown
        $employees = User::where('customer', 1)
            ->where('employee_status', 'active')
            ->orderBy('name')
            ->get();

        $employee = null;
        $dailyData = [];
        $summary = null;

        if ($employee_id) {
            $employee = User::with(['department', 'designation'])->findOrFail($employee_id);

            // Get holidays
            $holidays = Holiday::where('status', 'active')
                ->whereDate('from_date', '<=', $endDate->format('Y-m-d'))
                ->whereDate('to_date', '>=', $startDate->format('Y-m-d'))
                ->get();

            $holidayDates = [];
            foreach ($holidays as $holiday) {
                $current = Carbon::parse($holiday->from_date);
                $toDate = Carbon::parse($holiday->to_date);
                while ($current->lte($toDate)) {
                    $holidayDates[$current->format('Y-m-d')] = $holiday->title;
                    $current->addDay();
                }
            }

            // Get weekly offday
            $offdaySetting = Attribute::where('type', 21)->where('status', 'active')->first();
            $offdayNumber = $offdaySetting ? array_search($offdaySetting->name, ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']) : 5;

            // Get leaves
            $leaves = Leave::where('user_id', $employee_id)
                ->where('status', 'approved')
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                    });
                })
                ->get();

            // Get attendance records
            $attendances = Attendance::where('user_id', $employee_id)
                ->whereDate('in_time', '>=', $startDate->format('Y-m-d'))
                ->whereDate('in_time', '<=', $endDate->format('Y-m-d'))
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->in_time)->format('Y-m-d');
                });

            // Build date range
            $dateRange = [];
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dateRange[] = $currentDate->copy();
                $currentDate->addDay();
            }

            // Calculate daily data
            $presentCount = 0;
            $absentCount = 0;
            $leaveCount = 0;
            $holidayCount = 0;
            $lateCount = 0;

            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->dayOfWeek;

                // Check holiday
                $isHoliday = isset($holidayDates[$dateStr]);
                $isWeeklyOff = ($dayOfWeek == $offdayNumber);

                // Check leave
                $leave = $leaves->filter(function($l) use ($date) {
                    return $date->between($l->start_date, $l->end_date);
                })->first();

                // Get attendance
                $attendance = $attendances->get($dateStr)?->first();

                $status = '';
                $statusClass = '';
                $inTime = '-';
                $outTime = '-';

                if ($leave) {
                    $status = 'L';
                    $statusClass = 'leave';
                    $leaveCount++;
                } elseif ($attendance) {
                    if (in_array($attendance->status, ['present', 'late'])) {
                        $status = $attendance->status == 'late' ? 'LT' : 'P';
                        $statusClass = $attendance->status == 'late' ? 'late' : 'present';
                        $presentCount++;
                        if ($attendance->status == 'late') $lateCount++;

                        // Get times
                        if ($attendance->in_time) {
                            $inTime = is_string($attendance->in_time) ?
                                substr($attendance->in_time, 0, 5) :
                                Carbon::parse($attendance->in_time)->format('H:i');
                        }
                        if ($attendance->out_time) {
                            $outTime = is_string($attendance->out_time) ?
                                substr($attendance->out_time, 0, 5) :
                                Carbon::parse($attendance->out_time)->format('H:i');
                        }
                    } elseif ($attendance->status == 'absent') {
                        $status = 'A';
                        $statusClass = 'absent';
                        $absentCount++;
                    } elseif (in_array($attendance->status, ['holiday', 'weekly_off'])) {
                        $status = 'H';
                        $statusClass = 'holiday';
                        $holidayCount++;
                    }
                } elseif ($isHoliday) {
                    $status = 'H';
                    $statusClass = 'holiday';
                    $holidayCount++;
                } elseif ($isWeeklyOff) {
                    $status = 'WO';
                    $statusClass = 'holiday';
                    $holidayCount++;
                } else {
                    $status = 'A';
                    $statusClass = 'absent';
                    $absentCount++;
                }

                $dailyData[] = [
                    'date' => $date,
                    'day' => $date->format('j'),
                    'day_name' => $date->format('D'),
                    'status' => $status,
                    'status_class' => $statusClass,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                ];
            }

            $summary = [
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => $absentCount,
                'leave' => $leaveCount,
                'holiday' => $holidayCount,
                'total' => count($dateRange),
            ];
        }

        return view(adminTheme().'payroll.attendance.individual_report', compact(
            'employee',
            'employees',
            'employee_id',
            'month',
            'dailyData',
            'summary',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Last 7/10 Days Absent Report
     */
    public function absentReport(Request $request)
    {
        $days = $request->days ?? 7; // 7 or 10
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days);

        $employees = User::where('status', 'active')->filterByType('employee')->get();

        $absentEmployees = [];
        foreach ($employees as $employee) {
            $absentCount = Attendance::where('user_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'absent')
                ->count();

            if ($absentCount >= $days) {
                $absentEmployees[] = [
                    'employee' => $employee,
                    'absent_days' => $absentCount,
                ];
            }
        }

        return view(adminTheme().'payroll.attendance.absent_report', compact('absentEmployees', 'days', 'startDate', 'endDate'));
    }

    /**
     * Invalid In Time & No Out Time Report
     */
    public function invalidAttendanceReport(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        // No out time
        $noOutTime = Attendance::with(['user'])
            ->where('date', $date)
            ->whereNull('out_time')
            ->whereNotNull('in_time')
            ->get();

        // Invalid in time (very late or very early)
        $invalidInTime = Attendance::with(['user'])
            ->where('date', $date)
            ->where('late_time', '>', 120) // More than 2 hours late
            ->get();

        return view(adminTheme().'payroll.attendance.invalid_report', compact('noOutTime', 'invalidInTime', 'date'));
    }

        /**
     * Manual Attendance List
     */
    public function manualIndex(Request $request)
    {
        $query = Attendance::with('user')->where('via', '2');
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->department_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->date) {
            $query->where('date', $request->date);
        }
        $attendances = $query->orderBy('date', 'desc')->paginate(2);
        $employees = User::where('status', 1)->filterByType('employee')->get();
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        return view('admin.payroll.attendance.manual_index', compact('attendances', 'employees', 'departments'));
    }

    /**
     * Manual Attendance Create Form
     */
    public function manualCreate()
    {
        $employees = User::where('status', 1)->filterByType('employee')->get();
        return view('admin.payroll.attendance.manual_create', compact('employees'));
    }

    /**
     * Manual Attendance Store
     */
    public function manualStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'in_time' => 'required',
            'out_time' => 'required',
            'remarks' => 'nullable|string',
        ]);

        $user = User::with('shift')->findOrFail($request->user_id);
        $shift = $user->shift;

        $inTime = Carbon::parse($request->date . ' ' . $request->in_time, 'Asia/Dhaka');
        $outTime = Carbon::parse($request->date . ' ' . $request->out_time, 'Asia/Dhaka');

        // Check if attendance already exists for this user on this date
        $attendance = Attendance::where('user_id', $request->user_id)
            ->where('date', $request->date)
            ->first();

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->user_id = $request->user_id;
            $attendance->date = $request->date;
            $attendance->via = '2';
            $attendance->device_sn = 'Manual';
            $attendance->verify_type = 'Manual_Entry';
        }

        $attendance->in_time = $inTime;
        $attendance->out_time = $outTime;
        $attendance->remarks = $request->remarks;

        // Apply shift logic
        $this->applyShiftLogic($attendance, $shift);

        $attendance->save();

        return redirect()->route('admin.attendance.manual.index')->with('success', 'Attendance created successfully.');
    }

    /**
     * Manual Attendance Edit Form
     */
    public function manualEdit($id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);
        $employees = \App\Models\User::where('status', 1)->filterByType('employee')->get();
        return view('admin.payroll.attendance.manual_edit', compact('attendance', 'employees'));
    }

    /**
     * Manual Attendance Update
     */
    public function manualUpdate(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'in_time' => 'required',
            'out_time' => 'required',
        ]);

        $user = User::with('shift')->findOrFail($request->user_id);
        $shift = $user->shift;

        $inTime = Carbon::parse($request->date . ' ' . $request->in_time, 'Asia/Dhaka');
        $outTime = Carbon::parse($request->date . ' ' . $request->out_time, 'Asia/Dhaka');

        $attendance = Attendance::findOrFail($id);
        $attendance->user_id = $request->user_id;
        $attendance->date = $request->date;
        $attendance->in_time = $inTime;
        $attendance->out_time = $outTime;

        // Apply shift logic
        $this->applyShiftLogic($attendance, $shift);

        $attendance->save();

        return redirect()->route('admin.attendance.manual.index')->with('success', 'Attendance updated successfully.');
    }

    /**
     * Manual Attendance Delete
     */
    public function manualDestroy($id)
    {
        $attendance = \App\Models\Attendance::findOrFail($id);
        $attendance->delete();
        return redirect()->route('admin.attendance.manual.index')->with('success', 'Attendance deleted successfully.');
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
        $users = User::filterByType('employee')->latest()
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
            adminTheme() . 'payroll.attendance.dailyAttendance',
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
        $employees = User::filterByType('employee')
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
            adminTheme() . 'payroll.attendance.dailyAttendanceDepartmentSummary',
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

}
