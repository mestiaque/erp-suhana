<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PayrollController extends Controller
{
    // 1. Salary Setup
    public function salarySetup()
    {
        return view(adminTheme().'payroll.salary_setup');
    }
    public function salarySetupAction($action, $id = null) { return view(adminTheme().'payroll.salary_setup_action', compact('action', 'id')); }

    // 2. Attendance Summary
    public function attendance() { return view(adminTheme().'payroll.attendance'); }
    public function attendanceAction($action, $id = null) { return view(adminTheme().'payroll.attendance_action', compact('action', 'id')); }

    // 3. Generate Payslip
    public function payslip() { return view(adminTheme().'payroll.generate_payslip'); }
    public function payslipAction($action, $id = null) { return view(adminTheme().'payroll.generate_payslip_action', compact('action', 'id')); }

    // 4. Bonus & Allowance
    public function bonus() { return view(adminTheme().'payroll.bonus_allowance'); }
    public function bonusAction($action, $id = null) { return view(adminTheme().'payroll.bonus_allowance_action', compact('action', 'id')); }

    // 5. Deductions & Loan
    public function deductions() { return view(adminTheme().'payroll.deductions'); }
    public function deductionsAction($action, $id = null) { return view(adminTheme().'payroll.deductions_action', compact('action', 'id')); }

    // 6. Overtime (OT) Entry
    public function overtime() { return view(adminTheme().'payroll.overtime'); }
    public function overtimeAction($action, $id = null) { return view(adminTheme().'payroll.overtime_action', compact('action', 'id')); }

    // 7. Salary Disbursement
    public function disbursement() { return view(adminTheme().'payroll.disbursement'); }
    public function disbursementAction($action, $id = null) { return view(adminTheme().'payroll.disbursement_action', compact('action', 'id')); }

    // 8. Payroll Reports
    public function reports() { return view(adminTheme().'payroll.reports'); }




}
