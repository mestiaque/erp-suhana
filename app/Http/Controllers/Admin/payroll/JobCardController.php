<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Holiday;
use App\Models\Attribute;
use App\Models\Roaster;
use App\Models\EmployeeIncrement;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    /**
     * Display Job Card
     */
    public function index(Request $request)
    {
        $users = User::where('status', 1)->filterBy('employee')->get();

        $selectedUser = null;
        $month = $request->month ?? Carbon::now()->format('Y-m');

        $summary = null;
        $dailyData = [];
        $increments = collect();

        if ($request->user_id) {
            $selectedUser = User::with(['department', 'designation', 'divisionData', 'section', 'shift', 'line', 'grade', 'increments'])->findOrFail($request->user_id);

            // Get employee increments
            $increments = EmployeeIncrement::where('user_id', $request->user_id)->orderBy('increment_date', 'desc')->get();

            $year = Carbon::parse($month)->year;
            $monthNum = Carbon::parse($month)->month;

            // Get shift time from roaster or default
            $defaultInTime = '09:00:00';
            $roasterSample = Roaster::where('user_id', $request->user_id)->first();
            if ($roasterSample) {
                $defaultInTime = $roasterSample->in_time;
            }

            // Get all dates in month
            $dates = Carbon::parse($month)->startOfMonth()->toPeriod(Carbon::parse($month)->endOfMonth());

            // Calculate daily data using centralized function
            $present = 0;
            $late = 0;
            $absent = 0;
            $leaveCount = 0;
            $holiday = 0;
            $weeklyOff = 0;
            $totalWorkHours = 0;

            foreach ($dates as $date) {
                $dateStr = $date->format('Y-m-d');

                // Use centralized function for attendance status
                $status = getAttendanceStatus($request->user_id, $date);

                $statusCode = $status['status'];
                $inTime = $status['in_time'] ? (is_string($status['in_time']) ? substr($status['in_time'], 0, 5) : Carbon::parse($status['in_time'])->format('H:i')) : '-';
                $outTime = $status['out_time'] ? (is_string($status['out_time']) ? substr($status['out_time'], 0, 5) : Carbon::parse($status['out_time'])->format('H:i')) : '-';
                $workHours = $status['work_hours'] ?? '-';

                // Count based on status
                switch ($statusCode) {
                    case 'P':
                        $present++;
                        $totalWorkHours += $status['work_hours'];
                        break;
                    case 'LT':
                        $late++;
                        $totalWorkHours += $status['work_hours'];
                        break;
                    case 'L':
                        $leaveCount++;
                        break;
                    case 'H':
                        $holiday++;
                        break;
                    case 'WO':
                        $weeklyOff++;
                        break;
                    case 'A':
                        $absent++;
                        break;
                }

                $dailyData[] = [
                    'date' => $date->format('d'),
                    'day' => substr($date->format('l'), 0, 3),
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'work_hours' => $workHours,
                    'status' => $statusCode,
                ];
            }

            $summary = [
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'leave' => $leaveCount,
                'holiday' => $holiday,
                'weekly_off' => $weeklyOff,
                'total_work_hours' => $totalWorkHours,
            ];
        }

        return view('admin.jobcard.index', compact('users', 'selectedUser', 'month', 'summary', 'dailyData', 'increments'));
    }

    /**
     * Print Job Card - Opens in new blank page
     */
    public function print(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $month = $request->month ?? Carbon::now()->format('Y-m');
        
        $selectedUser = User::with(['department', 'designation', 'divisionData', 'section', 'shift', 'line', 'grade', 'increments'])->findOrFail($request->user_id);
        
        // Get employee increments
        $increments = EmployeeIncrement::where('user_id', $request->user_id)->orderBy('increment_date', 'desc')->get();

        $year = Carbon::parse($month)->year;
        $monthNum = Carbon::parse($month)->month;

        // Get shift time from roaster or default
        $defaultInTime = '09:00:00';
        $roasterSample = Roaster::where('user_id', $request->user_id)->first();
        if ($roasterSample) {
            $defaultInTime = $roasterSample->in_time;
        }

        // Get all dates in month
        $dates = Carbon::parse($month)->startOfMonth()->toPeriod(Carbon::parse($month)->endOfMonth());

        // Calculate daily data using centralized function
        $present = 0;
        $late = 0;
        $absent = 0;
        $leaveCount = 0;
        $holiday = 0;
        $weeklyOff = 0;
        $totalWorkHours = 0;

        foreach ($dates as $date) {
            $dateStr = $date->format('Y-m-d');

            // Use centralized function for attendance status
            $status = getAttendanceStatus($request->user_id, $date);

            $statusCode = $status['status'];
            $inTime = $status['in_time'] ? (is_string($status['in_time']) ? substr($status['in_time'], 0, 5) : Carbon::parse($status['in_time'])->format('H:i')) : '-';
            $outTime = $status['out_time'] ? (is_string($status['out_time']) ? substr($status['out_time'], 0, 5) : Carbon::parse($status['out_time'])->format('H:i')) : '-';
            $workHours = $status['work_hours'] ?? '-';

            // Count based on status
            switch ($statusCode) {
                case 'P':
                    $present++;
                    $totalWorkHours += $status['work_hours'];
                    break;
                case 'LT':
                    $late++;
                    $totalWorkHours += $status['work_hours'];
                    break;
                case 'L':
                    $leaveCount++;
                    break;
                case 'H':
                    $holiday++;
                    break;
                case 'WO':
                    $weeklyOff++;
                    break;
                case 'A':
                    $absent++;
                    break;
            }

            $dailyData[] = [
                'date' => $date->format('d'),
                'day' => substr($date->format('l'), 0, 3),
                'in_time' => $inTime,
                'out_time' => $outTime,
                'work_hours' => $workHours,
                'status' => $statusCode,
            ];
        }

        $summary = [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'leave' => $leaveCount,
            'holiday' => $holiday,
            'weekly_off' => $weeklyOff,
            'total_work_hours' => $totalWorkHours,
        ];

        return view('admin.jobcard.print', compact('selectedUser', 'month', 'summary', 'dailyData', 'increments'));
    }
}
