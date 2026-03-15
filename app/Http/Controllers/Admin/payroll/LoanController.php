<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of loans.
     */
    public function index(Request $request)
    {
        $loans = Loan::with('user')
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->type, function($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.loan.index', compact('loans'));
    }

    /**
     * Show the form for creating a new loan.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterBy('employee')->get();
        return view('admin.loan.create', compact('users'));
    }

    /**
     * Store a newly created loan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'principal_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'total_installments' => 'required|integer|min:1',
        ]);

        $principal = $request->principal_amount;
        $interest = ($principal * $request->interest_rate / 100);
        $totalAmount = $principal + $interest;
        $monthlyInstallment = $totalAmount / $request->total_installments;

        Loan::create([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'principal_amount' => $principal,
            'interest_rate' => $request->interest_rate,
            'total_amount' => $totalAmount,
            'monthly_installment' => $monthlyInstallment,
            'total_installments' => $request->total_installments,
            'paid_installments' => 0,
            'remaining_installments' => $request->total_installments,
            'disbursement_date' => $request->disbursement_date,
            'first_installment_date' => $request->first_installment_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.loan.index')->with('success', 'Loan request submitted');
    }

    /**
     * Approve or reject the loan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,active',
        ]);

        $loan = Loan::findOrFail($id);
        
        $updateData = [
            'status' => $request->status,
            'admin_remark' => $request->admin_remark,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ];

        if ($request->status === 'active') {
            $updateData['disbursement_date'] = $request->disbursement_date ?? Carbon::now();
        }

        $loan->update($updateData);

        return redirect()->route('admin.loan.index')->with('success', 'Loan updated');
    }

    /**
     * Remove the loan.
     */
    public function destroy($id)
    {
        Loan::findOrFail($id)->delete();
        return redirect()->route('admin.loan.index')->with('success', 'Loan deleted');
    }
}
