<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of taxes.
     */
    public function index(Request $request)
    {
        $taxes = Tax::with('user')
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

        return view('admin.payroll.tax.index', compact('taxes', 'years'));
    }

    /**
     * Show the form for creating a new tax.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        $years = range(date('Y'), date('Y') - 5);
        return view('admin.payroll.tax.create', compact('users', 'years'));
    }

    /**
     * Store a newly created tax.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gross_salary' => 'required|numeric|min:0',
            'year' => 'required',
            'month' => 'required',
        ]);

        $taxableIncome = $request->gross_salary - ($request->rebate ?? 0);

        // Simple tax calculation (can be customized based on tax rules)
        $taxAmount = 0;
        if ($taxableIncome > 0) {
            if ($taxableIncome <= 300000) {
                $taxAmount = 0;
            } elseif ($taxableIncome <= 400000) {
                $taxAmount = ($taxableIncome - 300000) * 0.05;
            } elseif ($taxableIncome <= 700000) {
                $taxAmount = 5000 + ($taxableIncome - 400000) * 0.10;
            } elseif ($taxableIncome <= 1100000) {
                $taxAmount = 5000 + 30000 + ($taxableIncome - 700000) * 0.15;
            } else {
                $taxAmount = 5000 + 30000 + 60000 + ($taxableIncome - 1100000) * 0.20;
            }
        }

        $netTax = max(0, $taxAmount - ($request->rebate ?? 0));

        Tax::create([
            'user_id' => $request->user_id,
            'gross_salary' => $request->gross_salary,
            'taxable_income' => $taxableIncome,
            'tax_amount' => $taxAmount,
            'rebate' => $request->rebate ?? 0,
            'net_tax' => $netTax,
            'year' => $request->year,
            'month' => $request->month,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.tax.index')->with('success', 'Tax calculated successfully');
    }

    /**
     * Remove the tax.
     */
    public function destroy($id)
    {
        Tax::findOrFail($id)->delete();
        return redirect()->route('admin.tax.index')->with('success', 'Tax deleted');
    }
}
