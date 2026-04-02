<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\payroll\Attendance;
use App\Models\payroll\Bonus;
use App\Models\payroll\Deduction;
use App\Models\payroll\Leave;
use App\Models\payroll\Loan;
use App\Models\payroll\Overtime;
use App\Models\payroll\ProvidentFund;
use App\Models\payroll\Roaster;
use App\Models\payroll\SalaryAdvance;
use App\Models\payroll\SalarySheet;
use App\Models\payroll\Shift;
use App\Models\payroll\Tax;
use App\Models\payroll\WorkingHour;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollManagementController extends Controller
{
    /**
     * Payroll Dashboard
     */
    public function index(Request $request)
    {
        try {
            $month = $request->month ?? Carbon::now()->month;
            $year = $request->year ?? Carbon::now()->year;

            $salaries = SalarySheet::with(['user'])
                ->where('month', $month)
                ->where('year', $year)
                ->paginate(25);

            $summary = [
                'total_employees' => SalarySheet::where('month', $month)->where('year', $year)->count(),
                'total_gross' => SalarySheet::where('month', $month)->where('year', $year)->sum('gross_salary'),
                'total_deduction' => SalarySheet::where('month', $month)->where('year', $year)->sum('total_deduction'),
                'total_net' => SalarySheet::where('month', $month)->where('year', $year)->sum('net_salary'),
                'paid_count' => SalarySheet::where('month', $month)->where('year', $year)->where('payment_status', 'paid')->count(),
                'pending_count' => SalarySheet::where('month', $month)->where('year', $year)->where('payment_status', 'pending')->count(),
            ];

            $departments = [];
            try {
                $departments = Attribute::where('type', 3)->where('status', 1)->get();
            } catch (\Exception $e) {
                // Ignore
            }

            return view(adminTheme().'payroll.payroll.index', compact('salaries', 'summary', 'month', 'year', 'departments'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading payroll: ' . $e->getMessage());
        }
    }

    /**
     * Process Payroll for a month - Enhanced with shift, leave, roster integration
     */
    public function processSalary(Request $request)
    {
        // Validate
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'attendance_start_date' => 'nullable|date',
            'attendance_end_date' => 'nullable|date|after_or_equal:attendance_start_date',
        ]);

        $month = $request->month;
        $year = $request->year;

        // Check if processing current incomplete month
        $today = Carbon::today();
        $selectedMonth = Carbon::createFromDate($year, $month, 1);
        $isCurrentMonth = $selectedMonth->isSameMonth($today);
        $isCompletedMonth = $selectedMonth->endOfMonth()->lt($today);

        // Warning for incomplete month processing
        $monthWarning = '';
        if ($isCurrentMonth && !$request->force_process) {
            $monthWarning = ' (Note: This is the current month - only days up to today are counted)';
        }

        // Check if already processed
        $existing = SalarySheet::where('month', $month)->where('year', $year)->count();
        if ($existing > 0 && !$request->reprocess) {
            return back()->with('error', 'Salary for this month already processed. Enable reprocess to update.');
        }

        $defaultStartDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $defaultEndDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // For current month, default range ends at today.
        $defaultActualEndDate = $isCurrentMonth ? $today->copy() : $defaultEndDate->copy();

        // Allow custom attendance range from salary sheet page.
        $startDate = $request->attendance_start_date
            ? Carbon::parse($request->attendance_start_date)->startOfDay()
            : $defaultStartDate->copy();

        $actualEndDate = $request->attendance_end_date
            ? Carbon::parse($request->attendance_end_date)->endOfDay()
            : $defaultActualEndDate->copy();

        if ($startDate->gt($actualEndDate)) {
            return back()->with('error', 'Attendance start date must be before or equal to end date.');
        }

        // Calculate working days (up to actual end date)
        $workingDays = $this->calculateWorkingDays($startDate, $actualEndDate);

        // Get all active employees - using User table directly since salary info is there
        $employees = User::where('employee_status', 'active')
            ->where('status', 1)
            ->filterByType('employee')
            ->get();

        if ($employees->isEmpty()) {
            return back()->with('error', 'No active employees found. Please add employees first.');
        }

        DB::beginTransaction();
        try {
            $processedCount = 0;

            foreach ($employees as $employee) {
                // Get gross salary directly from User table - this is the main salary figure
                $monthlyGrossSalary = $employee->gross_salary ?? 0;

                // Get salary components from User table
                $basicSalary = $employee->basic_salary ?? 0;
                $houseRent = $employee->house_rent ?? 0;
                $medicalAllowance = $employee->medical_allowance ?? 0;
                $transportAllowance = $employee->transport_allowance ?? 0;
                $foodAllowance = $employee->food_allowance ?? 0;
                $conveyanceAllowance = $employee->conveyance_allowance ?? 0;

                // Get other allowances and deductions from User table
                $attendanceBonus = $employee->attendance_bonus ?? 0;
                $otherAllowanceFromUser = $employee->other_allowance ?? 0;
                $stampCharge = $employee->stamp_charge ?? 0;
                $providentFund = $employee->provident_fund ?? 0;

                // If components are not properly set (both basic and house_rent are 0), calculate from gross
                // OR if basic_salary equals gross_salary (bug case), recalculate
                if ($monthlyGrossSalary > 0 && $basicSalary == 0 && $houseRent == 0) {
                    // Default distribution: Basic 50%, House Rent 30%, Medical 5%, Transport 5%, Food 5%, Conveyance 5%
                    $basicSalary = $monthlyGrossSalary * 0.50;
                    $houseRent = $monthlyGrossSalary * 0.30;
                    $medicalAllowance = $monthlyGrossSalary * 0.05;
                    $transportAllowance = $monthlyGrossSalary * 0.05;
                    $foodAllowance = $monthlyGrossSalary * 0.05;
                    $conveyanceAllowance = $monthlyGrossSalary * 0.05;
                } elseif ($monthlyGrossSalary > 0 && $basicSalary > 0 && $basicSalary == $monthlyGrossSalary) {
                    // Fix for bug where basic_salary was set equal to gross_salary - recalculate
                    $basicSalary = $monthlyGrossSalary * 0.50;
                    $houseRent = $monthlyGrossSalary * 0.30;
                    $medicalAllowance = $monthlyGrossSalary * 0.05;
                    $transportAllowance = $monthlyGrossSalary * 0.05;
                    $foodAllowance = $monthlyGrossSalary * 0.05;
                    $conveyanceAllowance = $monthlyGrossSalary * 0.05;
                }
                // Otherwise, use the stored values from User table

                // Get employee's shift (from roster or default shift)
                $employeeShift = $this->getEmployeeShift($employee, $startDate);

                // Calculate working hours per day based on shift
                $hoursPerDay = 8;
                if ($employeeShift) {
                    $hoursPerDay = $this->calculateShiftHours($employeeShift);
                }

                // Get attendance for the month.
                // For custom ranges, calculate directly from attendance table.
                if ($request->attendance_start_date || $request->attendance_end_date) {
                    $attendances = Attendance::where('user_id', $employee->id)
                        ->whereBetween('date', [$startDate->format('Y-m-d'), $actualEndDate->format('Y-m-d')])
                        ->get();

                    $presentDays = $attendances->filter(function ($a) {
                        return strtolower((string) $a->status) === 'present';
                    })->count();
                    $lateDays = $attendances->filter(function ($a) {
                        return strtolower((string) $a->status) === 'late';
                    })->count();
                    $leaveDays = $attendances->filter(function ($a) {
                        return strtolower((string) $a->status) === 'leave';
                    })->count();
                    $holidayDays = $attendances->filter(function ($a) {
                        return strtolower((string) $a->status) === 'holiday';
                    })->count();
                    $weeklyOffDays = $attendances->filter(function ($a) {
                        return strtolower((string) $a->status) === 'weekly_off';
                    })->count();
                    $attendanceSummary = [
                        'days_counted' => $startDate->copy()->startOfDay()->diffInDays($actualEndDate->copy()->startOfDay()) + 1,
                    ];
                } else {
                    $attendanceSummary = \getMonthlyAttendanceSummary($employee->id, $year, $month);
                    $presentDays = $attendanceSummary['present'];
                    $lateDays = $attendanceSummary['late'];
                    $leaveDays = $attendanceSummary['leave'];
                    $holidayDays = $attendanceSummary['holiday'];
                    $weeklyOffDays = $attendanceSummary['weekly_off'];
                }

                $absentDays = $attendanceSummary['absent'] ?? 0;

                // ============ NEW LOGIC ============
                // Holiday + Weekend = treated as paid days (like present)
                // For DISPLAY: Holiday column = Holiday + Weekend
                // For SALARY: Holiday/Weekend are paid, so not deducted

                // Combined holiday (Holiday + Weekend for display)
                $combinedHolidayDays = $holidayDays + $weeklyOffDays;

                // Actual present for display (only real attendance)
                $actualPresentDays = $presentDays + $lateDays;

                // Total days in period
                $totalDaysCounted = $attendanceSummary['days_counted'] ?? 0;
                if ($totalDaysCounted == 0) {
                    $totalDaysCounted = Carbon::createFromDate($year, $month, 1)->daysInMonth;
                    if ($isCurrentMonth) {
                        $totalDaysCounted = Carbon::today()->day;
                    }
                }

                // Working days = Total days - Holiday - Weekend
                $actualWorkingDays = max(0, $totalDaysCounted - $combinedHolidayDays);

                // Absent = Working days - Present - Late - Leave
                $absentDays = max(0, $actualWorkingDays - $actualPresentDays - $leaveDays);

                // For salary: Holiday/Weekend are PAID (consider as present)
                // So total paid days = Present + Late + Leave + Holiday + Weekend
                $paidDays = $actualPresentDays + $leaveDays + $combinedHolidayDays;
                // ============ END NEW LOGIC ============

                // Calculate work hours and overtime (use actualEndDate for current month)
                $attendances = Attendance::where('user_id', $employee->id)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $actualEndDate->format('Y-m-d')])
                    ->get();
                $overtimeHours = $attendances->sum('overtime');
                $overtimeMinutes = $attendances->sum('overtime_minutes');

                // Convert overtime minutes to hours if needed
                if ($overtimeMinutes > 0) {
                    $overtimeHours += $overtimeMinutes / 60;
                }

                // Leave days are now calculated using centralized function (getMonthlyAttendanceSummary)

                // Calculate weekend days (for info purposes)
                $weekendDays = $this->calculateWeekendDays($startDate, $actualEndDate);

                // Monthly gross salary is already set above from User table (gross_salary field)

                // Calculate per day salary based on total calendar days or working days
                // For Bangladesh: Salary is monthly fixed, so we pay full salary minus absent deductions
                $totalCalendarDays = $totalDaysCounted; // Days in period (up to today for current month)
                $perDaySalary = $totalCalendarDays > 0 ? $monthlyGrossSalary / $totalCalendarDays : 0;
                $perHourRate = $hoursPerDay > 0 ? $monthlyGrossSalary / ($totalCalendarDays * $hoursPerDay) : 0;

                // Calculate overtime amount (1.5x rate)
                $overtimeAmount = $overtimeHours * $perHourRate * 1.5;

                // ============ SALARY CALCULATION ============
                // Holiday + Weekend = PAID (full pay)
                // Present + Late = PAID (late gets 90%)
                // Leave = PAID (full pay)
                // Absent = NOT PAID (deducted)

                $dailyEarning = $perDaySalary;

                // Calculate earnings for each category
                $presentEarning = $presentDays * $dailyEarning;           // Present = full pay
                $lateEarning = $lateDays * ($dailyEarning * 0.9);         // Late = 90% pay
                $lateDeduction = $lateDays * ($dailyEarning * 0.1);       // Late deduction = 10%
                $leaveEarning = $leaveDays * $dailyEarning;               // Leave = full pay
                $holidayEarning = $combinedHolidayDays * $dailyEarning;   // Holiday/Weekend = full pay

                // Absent deduction = absent days * per day salary
                $absentDeduction = $absentDays * $dailyEarning;

                // Total base earnings = All paid days
                // For monthly salary, start with gross and deduct absents
                $totalEarning = $monthlyGrossSalary - $absentDeduction - $lateDeduction;

                // Ensure minimum earning is basic salary (even if many absents)
                if ($totalEarning < $basicSalary && $basicSalary > 0) {
                    $totalEarning = $basicSalary;
                }

                // For display purposes
                $displayGrossSalary = $monthlyGrossSalary;

                // ============ INTEGRATE NEW FEATURES ============

                // 1. Get Overtime (General & Special) from Overtime model
                $overtimeEarning = Overtime::where('user_id', $employee->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->where('status', 'approved')
                    ->sum('amount');

                // 2. Get Bonus from Bonus model
                $bonusEarning = Bonus::where('user_id', $employee->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->where('status', 'approved')
                    ->sum('amount');

                // 3. Get Deductions (late fine, absent fine, etc.)
                $deductionAmount = Deduction::where('user_id', $employee->id)
                    ->where('month', $month)
                    ->where('status', 'deducted')
                    ->sum('amount');

                // 4. Get Tax from Tax model or calculate
                $taxRecord = Tax::where('user_id', $employee->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();
                $tax = $taxRecord ? $taxRecord->net_tax : $this->calculateTax($monthlyGrossSalary);

                // 5. Get Loan installment deduction
                $loanDeductionAmount = Loan::where('user_id', $employee->id)
                    ->where('status', 'active')
                    ->sum('monthly_installment');

                // 6. Get Provident Fund from ProvidentFund model
                $pfRecord = ProvidentFund::where('user_id', $employee->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->where('status', 'active')
                    ->first();
                $providentFund = $pfRecord ? ($pfRecord->employee_contribution + $pfRecord->company_contribution) : ($monthlyGrossSalary * 0.05);

                // 7. Get Salary Advance deduction
                $salaryAdvanceDeduction = SalaryAdvance::where('user_id', $employee->id)
                    ->where('status', 'disbursed')
                    ->sum('monthly_deduction');

                // 8. Get Working Hours/Grass Time extra payment
                $grassTimeAmount = WorkingHour::where('user_id', $employee->id)
                    ->where('date', '>=', $year . '-' . $month . '-01')
                    ->where('date', '<=', $year . '-' . $month . '-31')
                    ->where('status', 'approved')
                    ->sum('grass_hours');
                // Convert grass hours to amount (assuming hourly rate)
                $grassTimeEarning = $grassTimeAmount * ($basicSalary / 200); // Assuming 200 working hours per month

                // Add overtime, bonus, grass time to earnings
                $totalEarning += $overtimeEarning + $bonusEarning + $grassTimeEarning;

                // ============ END NEW FEATURES ============

                // Calculate tax (simplified) - tax on full gross salary
                $tax = $this->calculateTax($monthlyGrossSalary);

                // Use provident fund from User table or default 5%
                $providentFund = $providentFund > 0 ? $providentFund : ($monthlyGrossSalary * 0.05);

                // Stamp charge as deduction - use from User table
                $stampDeduction = $stampCharge > 0 ? $stampCharge : 0;

                // Loan deduction (from Loan model)
                $loanDeduction = $loanDeductionAmount;

                // Total deductions
                $totalDeduction = $lateDeduction + $tax + $providentFund + $loanDeduction + $stampDeduction + $deductionAmount + $salaryAdvanceDeduction;

                // Net salary
                $netSalary = $totalEarning - $totalDeduction;

                // Ensure net salary doesn't go below zero
                if ($netSalary < 0) {
                    $netSalary = 0;
                }

                // Determine salary type based on joining date
                $joiningDate = $employee->joining_date ? Carbon::parse($employee->joining_date) : null;
                $salaryType = 'regular';

                if ($joiningDate && $joiningDate->month == $month && $joiningDate->year == $year) {
                    $salaryType = 'new_employee';
                }

                if ($employee->employee_status == 'retired' && $employee->retirement_date) {
                    $retirementDate = Carbon::parse($employee->retirement_date);
                    if ($retirementDate->month == $month && $retirementDate->year == $year) {
                        $salaryType = 'retired_employee';
                    }
                }

                // Get payment method from bank info
                $bankInfo = $employee->employeeBankInfo()->where('is_primary', 'yes')->first();
                $paymentMethod = $bankInfo ? ($bankInfo->payment_method ?? 'cash') : 'cash';

                // Create or update salary sheet
                SalarySheet::updateOrCreate(
                    ['user_id' => $employee->id, 'month' => $month, 'year' => $year],
                    [
                        'basic_salary' => $basicSalary,
                        'house_rent' => $houseRent,
                        'medical_allowance' => $medicalAllowance,
                        'transport_allowance' => $transportAllowance,
                        'other_allowance' => $foodAllowance + $conveyanceAllowance + $attendanceBonus + $otherAllowanceFromUser,
                        'gross_salary' => $monthlyGrossSalary,
                        'overtime_amount' => $overtimeEarning,
                        'special_overtime_amount' => 0, // Can be extended for special overtime type
                        'grass_time_amount' => $grassTimeEarning,
                        'bonus' => $attendanceBonus,
                        'other_bonus' => $bonusEarning,
                        'total_earning' => $totalEarning,
                        'absent_deduction' => $absentDeduction,
                        'late_deduction' => $lateDeduction,
                        'tax' => $tax,
                        'provident_fund' => $providentFund,
                        'loan_deduction' => $loanDeduction,
                        'salary_advance_deduction' => $salaryAdvanceDeduction,
                        'deduction' => $deductionAmount,
                        'other_deduction' => $stampDeduction,
                        'total_deduction' => $totalDeduction,
                        'net_salary' => $netSalary,
                        'working_days' => $actualWorkingDays,
                        'present_days' => $actualPresentDays,
                        'absent_days' => $absentDays,
                        'leave_days' => $leaveDays,
                        'holiday_days' => $combinedHolidayDays, // Holiday + Weekend
                        'overtime_hours' => $overtimeHours,
                        'payment_method' => $paymentMethod,
                        'payment_status' => 'pending',
                        'salary_type' => $salaryType,
                    ]
                );

                $processedCount++;
            }

            DB::commit();
            return back()->with('success', 'Salary processed successfully for ' . $processedCount . ' employees!' . $monthWarning);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing salary: ' . $e->getMessage());
        }
    }

    /**
     * Get employee's shift (from roster or default)
     */
    private function getEmployeeShift(User $employee, Carbon $date)
    {
        // Check if there's a roster for this date
        $roaster = Roaster::where('user_id', $employee->id)
            ->where('roster_date', $date->format('Y-m-d'))
            ->first();

        if ($roaster && $roaster->shift_id) {
            return Shift::find($roaster->shift_id);
        }

        // Fall back to employee's default shift
        if ($employee->shift) {
            return $employee->shift;
        }

        // Fall back to shift from employee info
        if ($employee && $employee->shift) {
            return $employee->shift;
        }

        return null;
    }

    /**
     * Calculate working hours from shift
     */
    private function calculateShiftHours(Shift $shift): float
    {
        if (!$shift->shift_starting_time || !$shift->shift_closing_time) {
            return 8; // Default 8 hours
        }

        $start = Carbon::parse($shift->shift_starting_time);
        $end = Carbon::parse($shift->shift_closing_time);

        // Handle next day shifts
        if ($shift->shift_closing_time_next_day) {
            $end->addDay();
        }

        return $start->diffInHours($end);
    }

    /**
     * Calculate approved leave days for the month
     */
    private function calculateApprovedLeaveDays(int $userId, Carbon $startDate, Carbon $endDate): int
    {
        $leaves = Leave::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->whereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })->orWhere(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate->format('Y-m-d'))
                      ->where('end_date', '>=', $startDate->format('Y-m-d'));
                });
            })
            ->get();

        $totalDays = 0;
        foreach ($leaves as $leave) {
            $leaveStart = Carbon::parse($leave->start_date)->max($startDate);
            $leaveEnd = Carbon::parse($leave->end_date)->min($endDate);
            $totalDays += $leaveStart->diffInDays($leaveEnd) + 1;
        }

        return $totalDays;
    }

    /**
     * Calculate weekend days in the month
     */
    private function calculateWeekendDays(Carbon $startDate, Carbon $endDate): int
    {
        $weekendDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Exclude Friday (5) and Saturday (6) - weekend in Bangladesh
            if (in_array($currentDate->dayOfWeek, [5, 6])) {
                $weekendDays++;
            }
            $currentDate->addDay();
        }

        return $weekendDays;
    }

    /**
     * Calculate tax (simplified progressive tax for Bangladesh)
     */
    private function calculateTax(float $monthlyGrossSalary): float
    {
        $annualSalary = $monthlyGrossSalary * 12;
        $tax = 0;

        // Bangladesh tax slabs (simplified)
        if ($annualSalary <= 350000) {
            $tax = 0;
        } elseif ($annualSalary <= 500000) {
            $tax = ($annualSalary - 350000) * 0.05 / 12;
        } elseif ($annualSalary <= 750000) {
            $tax = (150000 * 0.05 + ($annualSalary - 500000) * 0.10) / 12;
        } elseif ($annualSalary <= 1150000) {
            $tax = (150000 * 0.05 + 250000 * 0.10 + ($annualSalary - 750000) * 0.15) / 12;
        } elseif ($annualSalary <= 1700000) {
            $tax = (150000 * 0.05 + 250000 * 0.10 + 400000 * 0.15 + ($annualSalary - 1150000) * 0.20) / 12;
        } else {
            $tax = (150000 * 0.05 + 250000 * 0.10 + 400000 * 0.15 + 550000 * 0.20 + ($annualSalary - 1700000) * 0.25) / 12;
        }

        return round($tax, 2);
    }

    /**
     * Calculate working days in a month (excluding Fridays/Saturdays)
     */
    private function calculateWorkingDays($startDate, $endDate)
    {
        $workingDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Exclude Friday (5) and Saturday (6) - weekend in Bangladesh
            if (!in_array($currentDate->dayOfWeek, [5, 6])) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    /**
     * Salary Sheet View (Bank/Cash/All)
     */
    public function salarySheet(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $paymentMethod = $request->payment_method;
        $salaryType = $request->salary_type;
        $paymentStatus = $request->payment_status;
        $department_id = $request->department_id;
        $employee_id = $request->employee_id;
        $query = SalarySheet::with(['user'])
            ->where('month', $month)
            ->where('year', $year);

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($salaryType) {
            $query->where('salary_type', $salaryType);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($department_id) {
            $query->whereHas('user.department', function ($q) use ($department_id) {
                $q->where('id', $department_id);
            });
        }

        if ($employee_id) {
            $query->where('user_id', $employee_id);
        }

        $salaries = $query->get();

        $summary = [
            'total_employees' => $salaries->count(),
            'total_gross' => $salaries->sum('gross_salary'),
            'total_earning' => $salaries->sum('total_earning'),
            'total_deduction' => $salaries->sum('total_deduction'),
            'total_net' => $salaries->sum('net_salary'),
        ];

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $designations = Attribute::where('type', 4)->where('status', 'active')->get();
        $employees = User::where('status', 1)->filterByType('employee')->get();

        return view(adminTheme().'payroll.payroll.salary_sheet', compact('salaries', 'summary', 'month', 'year', 'departments', 'designations', 'employees'));
    }

    /**
     * Salary Summary
     */
    public function salarySummary(Request $request)
    {
        // Handle month format (Y-m)
        $monthParam = $request->month ?? date('Y-m');
        $year = date('Y', strtotime($monthParam . '-01'));
        $month = date('n', strtotime($monthParam . '-01'));

        // Group by department
        $departmentSummary = SalarySheet::select(
                'users.department_id',
                DB::raw('COUNT(salary_sheets.id) as employee_count'),
                DB::raw('SUM(salary_sheets.basic_salary) as total_basic'),
                DB::raw('SUM(salary_sheets.house_rent + salary_sheets.medical_allowance + salary_sheets.transport_allowance + salary_sheets.other_allowance) as total_allowances'),
                DB::raw('SUM(salary_sheets.gross_salary) as gross_salary'),
                DB::raw('SUM(salary_sheets.total_deduction) as total_deductions'),
                DB::raw('SUM(salary_sheets.net_salary) as net_salary')
            )
            ->join('users', 'salary_sheets.user_id', '=', 'users.id')
            ->where('salary_sheets.month', $month)
            ->where('salary_sheets.year', $year)
            ->groupBy('users.department_id')
            ->get();

        // Get departments
        $deptIds = $departmentSummary->pluck('department_id')->filter()->toArray();
        $departments = [];
        if (!empty($deptIds)) {
            $departments = Attribute::where('type', 3)->whereIn('id', $deptIds)->get()->keyBy('id');
        }

        // Add department name to each record
        foreach ($departmentSummary as $dept) {
            $dept->department_name = isset($departments[$dept->department_id]) ? $departments[$dept->department_id]->name : 'No Department';
        }

        // Payment method summary
        $paymentMethodSummary = SalarySheet::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(net_salary) as total')
            )
            ->where('month', $month)
            ->where('year', $year)
            ->groupBy('payment_method')
            ->get();

        return view(adminTheme().'payroll.payroll.salary_summary', compact('departmentSummary', 'departments', 'paymentMethodSummary', 'month', 'year'));
    }

    /**
     * Daily Salary Sheet
     */
    public function dailySalarySheet(Request $request)
    {
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $attendances = Attendance::with(['user'])
            ->where('date', $date)
            ->get();

        $dailySalaries = [];
        foreach ($attendances as $attendance) {
            $user = $attendance->user;
            // Use User table for salary data as per requirement
            $grossSalary = $user->gross_salary ?? 0;

            // If gross_salary is not set, calculate from components
            if ($grossSalary == 0) {
                $grossSalary = ($user->basic_salary ?? 0) + ($user->house_rent ?? 0) +
                              ($user->medical_allowance ?? 0) + ($user->transport_allowance ?? 0) +
                              ($user->food_allowance ?? 0) + ($user->conveyance_allowance ?? 0);
            }

            $perDaySalary = $grossSalary / 30; // Simplified calculation

            $dailySalaries[] = [
                'user' => $user,
                'employeeInfo' => $user,
                'attendance' => $attendance,
                'daily_salary' => $perDaySalary,
            ];
        }

        return view(adminTheme().'payroll.payroll.daily_salary_sheet', compact('dailySalaries', 'date'));
    }

    /**
     * Pay Slip Generate
     */
    public function paySlip($salarySheetId)
    {
        $salarySheet = SalarySheet::with([
            'user.department',
            'user.designation',
            'user.employeeBankInfo' => function($q) {
                $q->where('is_primary', 'yes');
            }
        ])->findOrFail($salarySheetId);
        return view(adminTheme().'payroll.payroll.pay_slip', compact('salarySheet'));
    }

    /**
     * Generate Bulk Payslips for a month
     */
    public function bulkPaySlip(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $department_id = $request->department_id;

        $query = SalarySheet::with(['user.department', 'user.designation'])
            ->where('month', $month)
            ->where('year', $year);

        if ($department_id) {
            $query->whereHas('user.department', function($q) use ($department_id) {
                $q->where('id', $department_id);
            });
        }

        $salaries = $query->get();

        return view(adminTheme().'payroll.payroll.bulk_pay_slip', compact('salaries', 'month', 'year'));
    }

    /**
     * Export Salary Sheet to Excel
     */
    public function exportSalarySheet(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $salaries = SalarySheet::with(['user.department', 'user.designation'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Return as download or view for printing
        return view(adminTheme().'payroll.payroll.salary_sheet_export', compact('salaries', 'month', 'year'));
    }

    /**
     * Salary Sheet Export/Print for salary sheet page
     */
    public function salarySheetExport(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $salaries = SalarySheet::with(['user.department', 'user.designation'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Return view for printing
        return view(adminTheme().'payroll.payroll.salary_sheet_export', compact('salaries', 'month', 'year'));
    }

    /**
     * Mark salary as paid
     */
    public function markPaid(Request $request, $id)
    {
        $salarySheet = SalarySheet::findOrFail($id);
        $salarySheet->payment_status = 'paid';
        $salarySheet->payment_date = $request->payment_date ?? Carbon::now();
        $salarySheet->save();

        return back()->with('success', 'Salary marked as paid!');
    }

    /**
     * Bulk mark as paid
     */
    public function bulkMarkPaid(Request $request)
    {
        $request->validate([
            'salary_ids' => 'required|array',
            'payment_date' => 'required|date',
        ]);

        SalarySheet::whereIn('id', $request->salary_ids)
            ->update([
                'payment_status' => 'paid',
                'payment_date' => $request->payment_date,
            ]);

        return back()->with('success', count($request->salary_ids) . ' salaries marked as paid!');
    }

    /**
     * Held up salary
     */
    public function heldUpSalary(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $heldSalaries = SalarySheet::with(['user'])
            ->where('month', $month)
            ->where('year', $year)
            ->where('payment_status', 'held')
            ->get();

        return view(adminTheme().'payroll.payroll.held_salary', compact('heldSalaries', 'month', 'year'));
    }

    /**
     * Mark salary as held
     */
    public function markHeld(Request $request, $id)
    {
        $salarySheet = SalarySheet::findOrFail($id);
        $salarySheet->payment_status = 'held';
        $salarySheet->remarks = $request->remarks;
        $salarySheet->save();

        return back()->with('success', 'Salary held!');
    }

    /**
     * Update/Modify salary
     */
    public function updateSalary(Request $request, $id)
    {
        $salarySheet = SalarySheet::findOrFail($id);

        // Update allowances and deductions
        if ($request->has('bonus')) {
            $salarySheet->bonus = $request->bonus;
        }
        if ($request->has('tax')) {
            $salarySheet->tax = $request->tax;
        }
        if ($request->has('provident_fund')) {
            $salarySheet->provident_fund = $request->provident_fund;
        }
        if ($request->has('loan_deduction')) {
            $salarySheet->loan_deduction = $request->loan_deduction;
        }
        if ($request->has('other_deduction')) {
            $salarySheet->other_deduction = $request->other_deduction;
        }
        if ($request->has('overtime_amount')) {
            $salarySheet->overtime_amount = $request->overtime_amount;
        }

        // Recalculate totals
        $salarySheet->total_earning = $salarySheet->gross_salary + $salarySheet->overtime_amount + $salarySheet->bonus;
        $salarySheet->total_deduction = $salarySheet->absent_deduction + $salarySheet->late_deduction +
                                       $salarySheet->tax + $salarySheet->provident_fund +
                                       $salarySheet->loan_deduction + $salarySheet->other_deduction;
        $salarySheet->net_salary = $salarySheet->total_earning - $salarySheet->total_deduction;

        $salarySheet->save();

        return back()->with('success', 'Salary updated successfully!');
    }

    /**
     * Payroll Report - Detailed payroll summary report
     */
    public function payrollReport(Request $request)
    {
        // Handle month format (Y-m)
        $monthParam = $request->month ?? date('Y-m');
        $year = date('Y', strtotime($monthParam . '-01'));
        $month = date('n', strtotime($monthParam . '-01'));

        // Get all salary sheets for the month with user and department info
        $salaries = SalarySheet::with(['user.department', 'user.designation'])
            ->where('month', $month)
            ->where('year', $year)
            ->paginate(25);

        // Get summary statistics
        $summary = [
            'total_employees' => SalarySheet::where('month', $month)->where('year', $year)->count(),
            'total_gross' => SalarySheet::where('month', $month)->where('year', $year)->sum('gross_salary'),
            'total_deduction' => SalarySheet::where('month', $month)->where('year', $year)->sum('total_deduction'),
            'total_net' => SalarySheet::where('month', $month)->where('year', $year)->sum('net_salary'),
            'paid_count' => SalarySheet::where('month', $month)->where('year', $year)->where('payment_status', 'paid')->count(),
            'pending_count' => SalarySheet::where('month', $month)->where('year', $year)->where('payment_status', 'pending')->count(),
            'held_count' => SalarySheet::where('month', $month)->where('year', $year)->where('payment_status', 'held')->count(),
        ];

        // Get departments for filter
        $departments = Attribute::where('type', 3)->where('status', 1)->get();

        return view(adminTheme().'payroll.payroll.payroll_report', compact('salaries', 'summary', 'month', 'year', 'departments', 'monthParam'));
    }
}
