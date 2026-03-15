<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Overtime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Overtime::with(['user', 'approver', 'user.department'])->latest();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->overtime_type) {
            $query->where('overtime_type', $request->overtime_type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->where('overtime_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('overtime_date', '<=', $request->date_to);
        }

        $overtimes = $query->paginate(20);
        $users = User::where('status', 1)->filterBy('employee')->get();
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();

        return view(adminTheme().'overtimes.index', compact('overtimes', 'users', 'departments'));
    }

    public function create()
    {
        $users = User::where('status', 1)->filterBy('employee')->get();
        return view(adminTheme().'overtimes.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'overtime_type' => 'required|in:general,special',
            'overtime_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'reason' => 'nullable|string',
        ]);

        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $hours = $endTime->diffInMinutes($startTime) / 60;

        $rate = $request->overtime_type === 'special' ? (general()->special_overtime_rate ?? 200) : (general()->general_overtime_rate ?? 100);
        $amount = $hours * $rate;

        Overtime::create([
            'user_id' => $request->user_id,
            'overtime_type' => $request->overtime_type,
            'overtime_date' => $request->overtime_date,
            'year' => Carbon::parse($request->overtime_date)->year,
            'month' => Carbon::parse($request->overtime_date)->month,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'hours' => $hours,
            'rate' => $rate,
            'amount' => $amount,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.overtimes')->with('success', 'Overtime created successfully');
    }

    public function edit($id)
    {
        $overtime = Overtime::findOrFail($id);
        $users = User::where('status', 1)->filterBy('employee')->get();
        return view(adminTheme().'overtimes.edit', compact('overtime', 'users'));
    }

    public function update(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'overtime_type' => 'required|in:general,special',
            'overtime_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'reason' => 'nullable|string',
        ]);

        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $hours = $endTime->diffInMinutes($startTime) / 60;

        $rate = $request->overtime_type === 'special' ? (general()->special_overtime_rate ?? 200) : (general()->general_overtime_rate ?? 100);
        $amount = $hours * $rate;

        $overtime->update([
            'user_id' => $request->user_id,
            'overtime_type' => $request->overtime_type,
            'overtime_date' => $request->overtime_date,
            'year' => Carbon::parse($request->overtime_date)->year,
            'month' => Carbon::parse($request->overtime_date)->month,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'hours' => $hours,
            'rate' => $rate,
            'amount' => $amount,
            'reason' => $request->reason,
        ]);

        return redirect()->route('admin.overtimes')->with('success', 'Overtime updated successfully');
    }

    public function approve($id)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);
        return redirect()->route('admin.overtimes')->with('success', 'Overtime approved successfully');
    }

    public function reject(Request $request, $id)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        return redirect()->route('admin.overtimes')->with('success', 'Overtime rejected');
    }

    public function destroy($id)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->delete();
        return redirect()->route('admin.overtimes')->with('success', 'Overtime deleted successfully');
    }
}
