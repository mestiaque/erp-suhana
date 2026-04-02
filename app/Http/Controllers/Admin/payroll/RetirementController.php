<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\EmployeeRetirement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RetirementController extends Controller
{
    /**
     * Display a listing of retirements.
     */
    public function index(Request $request)
    {
        $retirements = EmployeeRetirement::with('user')
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payroll.retirement.index', compact('retirements'));
    }

    /**
     * Show the form for creating a new retirement.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        return view('admin.payroll.retirement.create', compact('users'));
    }

    /**
     * Store a newly created retirement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'retirement_date' => 'required|date',
            'type' => 'required',
            'settlement_amount' => 'required|numeric|min:0',
            'reason' => 'nullable',
        ]);

        EmployeeRetirement::create([
            'user_id' => $request->user_id,
            'retirement_date' => $request->retirement_date,
            'type' => $request->type,
            'settlement_amount' => $request->settlement_amount,
            'provident_fund' => $request->provident_fund ?? 0,
            'gratuity' => $request->gratuity ?? 0,
            'notice_period_days' => $request->notice_period_days ?? 0,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.retirement.index')->with('success', 'Retirement request submitted successfully');
    }

    /**
     * Approve or reject the retirement.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,processed',
        ]);

        $retirement = EmployeeRetirement::findOrFail($id);
        $retirement->update([
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.retirement.index')->with('success', 'Retirement updated successfully');
    }

    /**
     * Remove the retirement.
     */
    public function destroy($id)
    {
        EmployeeRetirement::findOrFail($id)->delete();
        return redirect()->route('admin.retirement.index')->with('success', 'Retirement deleted successfully');
    }
}
