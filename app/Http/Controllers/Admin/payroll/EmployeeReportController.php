<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\payroll\EmployeeIncrement;
use App\Models\payroll\Leave;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeReportController extends Controller
{
    /**
     * Employee Report - Main List
     */
    public function index(Request $request)
    {
        $departments = Attribute::filterBy('department')->where('status', 'active')->get();
        $designations = Attribute::filterBy('designation')->where('status', 'active')->get();

        $query = User::with(['department', 'designation'])
            ->filterByType('employee');

        // Search filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_id', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->department_id) {
            $query->whereHas('department', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->designation_id) {
            $query->whereHas('designation', function($q) use ($request) {
                $q->where('designation_id', $request->designation_id);
            });
        }

        if ($request->employee_status) {
            $status = $request->employee_status;

            if ($status == 'terminated') {
                $query->whereHas('terminations', function($q) {
                    $q->where('status', 'approved');
                });
            } elseif ($status == 'retired') {
                $query->whereHas('retirements', function($q) {
                    $q->where('status', 'approved');
                });
            } elseif ($status == 'probation') {
                $query->whereHas('probations');
            } elseif ($status == 'active') {
                $query->where('status', 1)
                       ->whereDoesntHave('terminations', function($q) {
                           $q->where('status', 'approved');
                       })
                       ->whereDoesntHave('retirements', function($q) {
                           $q->where('status', 'approved');
                       })
                       ->whereDoesntHave('probations');
            } elseif ($status == 'inactive') {
                $query->where('status', 0);
            }
        }

        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        $employees = $query->orderBy('name')->get();

        $status = [
            'total'      => $employees->count(),
            'probation'  => $employees->where('employee_status', 'probation')->count(),
            'active'     => $employees->where('employee_status', 'active')->count(),
            'inactive'   => $employees->where('employee_status', 'inactive')->count(),
            'retired'    => $employees->where('employee_status', 'retired')->count(),
            'terminated' => $employees->where('employee_status', 'terminated')->count(),
        ];

        return view(adminTheme().'payroll.reports.employees.index', compact('employees', 'departments', 'designations', 'status'));
    }

    /**
     * Male & Female Employee List
     */
    public function genderWiseReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get(); // Department list

        $query = User::where('customer', '=', 1)->with('department');

        if ($request->gender) {
            $query->where('gender', '=', $request->gender);
        }

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        $employees = $query->get();

        $stats = [
            'male' => User::where('customer', '=', 1)->where('gender', '=', 'male')->count(),
            'female' => User::where('customer', '=', 1)->where('gender', '=', 'female')->count(),
            'other' => User::where('customer', '=', 1)->where('gender', '=', 'other')->count(),
        ];

        return view(adminTheme().'payroll.reports.employees.gender_wise', compact('employees', 'stats', 'departments'));
    }

    /**
     * Active/Inactive Employee List
     */
    public function statusWiseReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();

        $query = User::where('customer', '=', 1)->with('department');

        if ($request->status) {
            $query->where('employee_status', '=', $request->status);
        }

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        $employees = $query->get();

        $stats = [
            'active' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')->count(),
            'inactive' => User::where('customer', '=', 1)->where('employee_status', '=', 'inactive')->count(),
            'retired' => User::where('customer', '=', 1)->where('employee_status', '=', 'retired')->count(),
        ];

        return view(adminTheme().'payroll.reports.employees.status_wise', compact('employees', 'stats', 'departments'));
    }

    /**
     * Newly Joined Employees
     */
    public function newlyJoinedReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();
        $from_date = $request->from_date ?? Carbon::now()->subMonth()->format('Y-m-d');
        $to_date = $request->to_date ?? Carbon::now()->format('Y-m-d');

        $query = User::where('customer', '=', 1)
            ->with('department')
            ->whereBetween('joining_date', [$from_date, $to_date]);

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        $employees = $query->orderBy('joining_date', 'desc')->get();

        return view(adminTheme().'payroll.reports.employees.newly_joined', compact('employees', 'departments'));
    }

    /**
     * Retired Employees List
     */
    public function retiredReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();

        $query = User::where('customer', '=', 1)
            ->with('department')
            ->where('employee_status', '=', 'retired')
            ->whereNotNull('retirement_date');

        if ($request->from_date) {
            $query->where('retirement_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->where('retirement_date', '<=', $request->to_date);
        }

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        $employees = $query->orderBy('retirement_date', 'desc')->get();

        return view(adminTheme().'payroll.reports.employees.retired', compact('employees', 'departments'));
    }

    /**
     * Month Wise Increment Report
     */
    public function monthWiseIncrementReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();
        $month = $request->month;

        $query = EmployeeIncrement::with(['user.department']);

        if ($month) {
            $query->whereMonth('effective_date', Carbon::parse($month)->month)
                  ->whereYear('effective_date', Carbon::parse($month)->year);
        }

        if ($request->department_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', '=', $request->department_id);
            });
        }

        if ($request->type) {
            $query->where('type', '=', $request->type);
        }

        $increments = $query->orderBy('effective_date', 'desc')->get();

        return view(adminTheme().'payroll.reports.employees.increment', compact('increments', 'departments'));
    }

    /**
     * Service Milestone Completion Report
     */
    public function serviceCompletedReport(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();
        $years = $request->years; // 1, 3, 5, 10

        $query = User::where('customer', '=', 1)
            ->with('department')
            ->where('employee_status', '=', 'active')
            ->whereNotNull('joining_date');

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        // Filter by service years if specified
        if ($years) {
            $targetDate = Carbon::now()->subYears($years);
            $query->whereDate('joining_date', '<=', $targetDate->format('Y-m-d'));
        }

        $employees = $query->orderBy('joining_date', 'asc')->get();

        // Calculate stats
        $stats = [
            '1_year' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')
                ->whereNotNull('joining_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) >= 1')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) < 3')
                ->count(),
            '3_years' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')
                ->whereNotNull('joining_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) >= 3')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) < 5')
                ->count(),
            '5_years' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')
                ->whereNotNull('joining_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) >= 5')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) < 10')
                ->count(),
            '10_years' => User::where('customer', '=', 1)->where('employee_status', '=', 'active')
                ->whereNotNull('joining_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, joining_date, NOW()) >= 10')
                ->count(),
        ];

        return view(adminTheme().'payroll.reports.employees.service_completed', compact('employees', 'departments', 'stats'));
    }

    /**
     * Full Bengali Employee List (for Government/Official purposes)
     */
    public function bengaliEmployeeList(Request $request)
    {
        $departments = Attribute::where('parent', 9)->get();

        $query = User::where('customer', '=', 1)->with('department');

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        if ($request->status) {
            $query->where('employee_status', '=', $request->status);
        }

        $employees = $query->orderBy('name', 'asc')->get();

        return view(adminTheme().'payroll.reports.employees.bengali_list', compact('employees', 'departments'));
    }

    /**
     * Leave Reports - Month & Year Wise
     */
    public function leaveReport(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $leave_type_id = $request->leave_type_id;
        $user_id = $request->user_id;
        $status = $request->status;

        $query = Leave::with(['user', 'leaveType', 'approver']);

        if ($month && $year) {
            $query->whereMonth('start_date', $month)->whereYear('start_date', $year);
        }

        if ($leave_type_id) {
            $query->where('leave_type_id', $leave_type_id);
        }

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $leaves = $query->paginate(50);

        $leaveTypes = Attribute::filterBy('leave_type')->where('status', 'active')->get();
        $employees = User::filterByType('employee')->get();

        return view(adminTheme().'payroll.reports.leave_report', compact('leaves', 'leaveTypes', 'employees', 'month', 'year'));
    }

    /**
     * Department/Division/Section-wise Reports
     */
    public function departmentWiseReport(Request $request)
    {
        $departments = Attribute::filterBy('department')->get();

        $query = User::where('customer', '=', 1)->with('department');

        if ($request->department_id) {
            $query->where('department_id', '=', $request->department_id);
        }

        if ($request->status) {
            $query->where('employee_status', '=', $request->status);
        }

        $employees = $query->get();

        // Department-wise statistics
        $departmentStats = User::where('customer', '=', 1)
            ->select(
                'department_id',
                DB::raw('COUNT(*) as total_employees'),
                DB::raw('SUM(CASE WHEN employee_status = "active" THEN 1 ELSE 0 END) as active_count'),
                DB::raw('SUM(CASE WHEN employee_status = "inactive" THEN 1 ELSE 0 END) as inactive_count'),
                DB::raw('SUM(CASE WHEN gender = "male" THEN 1 ELSE 0 END) as male_count'),
                DB::raw('SUM(CASE WHEN gender = "female" THEN 1 ELSE 0 END) as female_count'),
                DB::raw('AVG(basic_salary) as avg_salary')
            )
            ->groupBy('department_id')
            ->get()
            ->map(function($item) {
                $dept = Attribute::filterBy('department')->find($item->department_id);
                $item->department_name = $dept ? $dept->name : 'N/A';
                return $item;
            });

        $summary = $departments->map(function($dept) {
            return [
                'department_name' => $dept->name,
                'employee_count' => User::where('customer', '=', 1)->where('department_id', '=', $dept->id)->filterByType('employee')->count()
            ];
        });

        return view(adminTheme().'payroll.reports.employees.department_wise', compact('employees', 'departments', 'departmentStats', 'summary'));
    }

    /**
     * Generate ID Card
     */
    public function idCard(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterByType('employee')->get();

        if ($request->has('employee_ids')) {
            $selectedEmployees = User::whereIn('id', $request->employee_ids)->get();
            return view(adminTheme().'payroll.documents.id_card', compact('selectedEmployees', 'employees'));
        }

        return view(adminTheme().'payroll.documents.id_card', compact('employees'));
    }

    /**
     * Employee Personal Information Sheet
     */
    public function personalInfoSheet(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterByType('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'payroll.documents.personal_info', compact('employee', 'employees'));
        }

        return view(adminTheme().'payroll.documents.personal_info', compact('employees'));
    }

    /**
     * Appointment Letter
     */
    public function appointmentLetter(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterByType('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'payroll.documents.appointment_letter', compact('employee', 'employees'));
        }

        return view(adminTheme().'payroll.documents.appointment_letter', compact('employees'));
    }

    /**
     * Joining Letter
     */
    public function joiningLetter(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterByType('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'payroll.documents.joining_letter', compact('employee', 'employees'));
        }

        return view(adminTheme().'payroll.documents.joining_letter', compact('employees'));
    }

    /**
     * Increment Letter
     */
    public function incrementLetter(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterByType('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'payroll.documents.increment_letter', compact('employee', 'employees'));
        }

        return view(adminTheme().'payroll.documents.increment_letter', compact('employees'));
    }

    /**
     * Service Confirmation Letter
     */
    public function confirmationLetter(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterByType('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'payroll.documents.confirmation_letter', compact('employee', 'employees'));
        }

        return view(adminTheme().'payroll.documents.confirmation_letter', compact('employees'));
    }

    /**
     * Generate Pay Slip
     */
    public function paySlip(Request $request)
    {
        $employees = User::where('customer', '=', 1)->where('employee_status', '=', 'active')->filterByType('employee')->get();

        if ($request->has('employee_id')) {
            $employee = User::with('department')->findOrFail($request->employee_id);
            return view(adminTheme().'payroll.documents.pay_slip', compact('employee', 'employees'));
        }

        return view(adminTheme().'payroll.documents.pay_slip', compact('employees'));
    }

    /**
     * Age Identification Letter
     */
    public function ageIdentificationLetter($userId)
    {
        $employee = User::filterByType('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.age_identification', compact('employee'));
        return $pdf->stream('age_identification_' . $employee->name . '.pdf');
    }

    /**
     * Employee Job Ledger
     */
    public function jobLedger($userId)
    {
        $employee = User::with([
            'attendances', 'leaves', 'salarySheets', 'increments'
        ])->filterByType('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.job_ledger', compact('employee'));
        return $pdf->stream('job_ledger_' . $employee->name . '.pdf');
    }

    /**
     * Nominee Form
     */
    public function nomineeForm($userId)
    {
        $employee = User::filterByType('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.nominee_form', compact('employee'));
        return $pdf->stream('nominee_form_' . $employee->name . '.pdf');
    }

    /**
     * Resign Letter
     */
    public function resignLetter($userId)
    {
        $employee = User::filterByType('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.resign_letter', compact('employee'));
        return $pdf->stream('resign_letter_' . $employee->name . '.pdf');
    }

    /**
     * Commitment Letter
     */
    public function commitmentLetter($userId)
    {
        $employee = User::filterByType('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.commitment_letter', compact('employee'));
        return $pdf->stream('commitment_letter_' . $employee->name . '.pdf');
    }

    /**
     * Settlement Letter
     */
    public function settlementLetter($userId)
    {
        $employee = User::filterByType('employee')->findOrFail($userId);

        if (!$employee || !in_array($employee->employee_status, ['retired', 'resigned'])) {
            return back()->with('error', 'Settlement letter only for retired/resigned employees!');
        }

        $pdf = PDF::loadView('admin.documents.settlement_letter', compact('employee'));
        return $pdf->stream('settlement_letter_' . $employee->name . '.pdf');
    }

    /**
     * Job Application Form
     */
    public function jobApplicationForm($userId)
    {
        $employee = User::with([
             'employeeEducation', 'employeeExperience'
        ])->filterByType('employee')->findOrFail($userId);

        $pdf = PDF::loadView('admin.documents.job_application', compact('employee'));
        return $pdf->stream('job_application_' . $employee->name . '.pdf');
    }
}
