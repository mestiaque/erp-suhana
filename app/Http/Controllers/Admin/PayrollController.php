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

public function generateStructure()
{
    dd(1);
    $structure = [
        'commercial' => [
            'btb_lc' => 'Bank BTB LC',
            'export_lc' => 'Export LC/Sales Contact',
            'purchase_orders' => 'Purchase Orders',
            'proforma_invoice' => 'Proforma Invoice (PI)',
            'invoices' => 'Commercial Invoice',
            'packing_list' => 'Packing List',
            'shipping_docs' => 'Shipping Bill/Docs',
            'export_realization' => 'Export Realization',
            'reports' => 'Commercial Reports'
        ],
        'payroll' => [
            'salary_setup' => 'Salary Setup',
            'attendance' => 'Attendance Summary',
            'generate_payslip' => 'Generate Payslip',
            'bonus_allowance' => 'Bonus & Allowance',
            'deductions' => 'Deductions & Loan',
            'overtime' => 'Overtime (OT) Entry',
            'disbursement' => 'Salary Disbursement',
            'reports' => 'Payroll Reports'
        ]
    ];

    // পাথটি নিশ্চিত করুন আপনার থিম ফোল্ডার অনুযায়ী।
    // যদি আপনার ভিউ পাথ 'resources/views/admin/default/commercial' হয়, তবে নিচের পাথ পরিবর্তন করুন।
    $basePath = resource_path('views/admin/');

    foreach ($structure as $folderName => $files) {
        $directoryPath = $basePath . $folderName;

        // ফোল্ডার তৈরি
        if (!\Illuminate\Support\Facades\File::isDirectory($directoryPath)) {
            \Illuminate\Support\Facades\File::makeDirectory($directoryPath, 0755, true, true);
        }

        foreach ($files as $fileName => $title) {
            $filePath = $directoryPath . '/' . $fileName . '.blade.php';

            // ফাইল আগে থেকে থাকলে তা ডিলিট করে নতুন করে তৈরির জন্য File::exists চেক সরিয়ে দিতে পারেন
            // অথবা নিচের কোডটি ব্যবহার করুন:
            $content = "@extends(adminTheme().'layouts.app')

                        @section('contents')
                        <div class=\"flex-grow-1\">
                            <div class=\"card mb-30\">
                                <div class=\"card-header d-flex justify-content-between align-items-center\">
                                    <h3>$title List</h3>
                                    <div class=\"dropdown\">
                                        <a href=\"#\" class=\"btn-custom primary\" style=\"padding:5px 15px;\">
                                            <i class=\"bx bx-plus\"></i> Add New
                                        </a>
                                    </div>
                                </div>

                                <div class=\"card-body\">
                                    @include(adminTheme().'alerts')
                                    <div class=\"table-responsive\">
                                        <table class=\"table table-striped\">
                                            <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Reference No</th>
                                                    <th>Details</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>DEMO-2026-001</td>
                                                    <td>Sample Data for $title</td>
                                                    <td>$ 0.00</td>
                                                    <td><span class=\"badge badge-success\">Active</span></td>
                                                    <td>" . date('d.m.Y') . "</td>
                                                    <td>
                                                        <a href=\"#\" class=\"btn-custom success\"><i class=\"bx bx-edit\"></i></a>
                                                        <a href=\"#\" class=\"btn-custom danger\"><i class=\"bx bx-trash\"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endsection";

            \Illuminate\Support\Facades\File::put($filePath, $content);
        }
    }

    return "All Commercial and Payroll blade files have been generated successfully! [Time: " . now() . "]";
}


}
