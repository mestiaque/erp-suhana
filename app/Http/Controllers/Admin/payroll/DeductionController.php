<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\Deduction;
use App\Models\User;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    /**
     * Display a listing of deductions.
     */
    public function index(Request $request)
    {
        $deductions = Deduction::with('user')
            ->when($request->month, function($q) use ($request) {
                $q->where('month', $request->month);
            })
            ->when($request->type, function($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.payroll.deduction.index', compact('deductions'));
    }

    /**
     * Show the form for creating a new deduction.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        return view('admin.payroll.deduction.create', compact('users'));
    }

    /**
     * Store a newly created deduction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'amount' => 'required|numeric|min:0',
            'month' => 'required',
        ]);

        Deduction::create([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'month' => $request->month,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.deductions.index')->with('success', 'Deduction added successfully');
    }

    /**
     * Update the deduction status.
     */
    public function update(Request $request, $id)
    {
        $deduction = Deduction::findOrFail($id);
        $deduction->update(['status' => $request->status]);
        return redirect()->route('admin.deductions.index')->with('success', 'Deduction updated');
    }

    /**
     * Remove the deduction.
     */
    public function destroy($id)
    {
        Deduction::findOrFail($id)->delete();
        return redirect()->route('admin.deductions.index')->with('success', 'Deduction deleted');
    }
}
