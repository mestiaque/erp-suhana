<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryAdvance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryAdvanceController extends Controller
{
    /**
     * Display a listing of salary advances.
     */
    public function index(Request $request)
    {
        $advances = SalaryAdvance::with('user')
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.salary-advance.index', compact('advances'));
    }

    /**
     * Show the form for creating a new salary advance.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterBy('employee')->get();
        return view('admin.salary-advance.create', compact('users'));
    }

    /**
     * Store a newly created salary advance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'requested_amount' => 'required|numeric|min:0',
            'installment_months' => 'required|integer|min:1',
            'reason' => 'nullable',
        ]);

        $monthlyDeduction = $request->requested_amount / $request->installment_months;

        SalaryAdvance::create([
            'user_id' => $request->user_id,
            'requested_amount' => $request->requested_amount,
            'approved_amount' => $request->requested_amount,
            'installment_months' => $request->installment_months,
            'monthly_deduction' => $monthlyDeduction,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.salary-advance.index')->with('success', 'Salary advance request submitted');
    }

    /**
     * Approve or reject the salary advance.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,disbursed',
            'approved_amount' => 'nullable|numeric|min:0',
        ]);

        $advance = SalaryAdvance::findOrFail($id);
        
        $updateData = [
            'status' => $request->status,
            'admin_remark' => $request->admin_remark,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ];

        if ($request->approved_amount) {
            $updateData['approved_amount'] = $request->approved_amount;
            $updateData['monthly_deduction'] = $request->approved_amount / $advance->installment_months;
        }

        if ($request->status === 'disbursed') {
            $updateData['disbursement_date'] = Carbon::now();
        }

        $advance->update($updateData);

        return redirect()->route('admin.salary-advance.index')->with('success', 'Salary advance updated');
    }

    /**
     * Remove the salary advance.
     */
    public function destroy($id)
    {
        SalaryAdvance::findOrFail($id)->delete();
        return redirect()->route('admin.salary-advance.index')->with('success', 'Salary advance deleted');
    }
}
