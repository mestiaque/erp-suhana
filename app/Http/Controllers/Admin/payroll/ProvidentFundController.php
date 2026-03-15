<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\ProvidentFund;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProvidentFundController extends Controller
{
    /**
     * Display a listing of provident funds.
     */
    public function index(Request $request)
    {
        $providentFunds = ProvidentFund::with('user')
            ->when($request->year, function($q) use ($request) {
                $q->where('year', $request->year);
            })
            ->when($request->month, function($q) use ($request) {
                $q->where('month', $request->month);
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        $years = range(date('Y'), date('Y') - 5);
        
        return view('admin.provident-fund.index', compact('providentFunds', 'years'));
    }

    /**
     * Show the form for creating a new provident fund entry.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterBy('employee')->get();
        $years = range(date('Y'), date('Y') - 5);
        return view('admin.provident-fund.create', compact('users', 'years'));
    }

    /**
     * Store a newly created provident fund.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'employee_contribution' => 'required|numeric|min:0',
            'company_contribution' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'year' => 'required',
            'month' => 'required',
        ]);

        $total = $request->employee_contribution + $request->company_contribution;

        ProvidentFund::create([
            'user_id' => $request->user_id,
            'employee_contribution' => $request->employee_contribution,
            'company_contribution' => $request->company_contribution,
            'total_amount' => $total,
            'interest_rate' => $request->interest_rate,
            'year' => $request->year,
            'month' => $request->month,
            'status' => 'active',
        ]);

        return redirect()->route('admin.provident-fund.index')->with('success', 'Provident Fund added successfully');
    }

    /**
     * Update the provident fund.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_contribution' => 'required|numeric|min:0',
            'company_contribution' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
        ]);

        $pf = ProvidentFund::findOrFail($id);
        $total = $request->employee_contribution + $request->company_contribution;

        $pf->update([
            'employee_contribution' => $request->employee_contribution,
            'company_contribution' => $request->company_contribution,
            'total_amount' => $total,
            'interest_rate' => $request->interest_rate,
        ]);

        return redirect()->route('admin.provident-fund.index')->with('success', 'Provident Fund updated successfully');
    }

    /**
     * Remove the provident fund.
     */
    public function destroy($id)
    {
        ProvidentFund::findOrFail($id)->delete();
        return redirect()->route('admin.provident-fund.index')->with('success', 'Provident Fund deleted successfully');
    }
}
