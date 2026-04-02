<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\WorkingHour;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkingHourController extends Controller
{
    /**
     * Display a listing of working hours.
     */
    public function index(Request $request)
    {
        $hours = WorkingHour::with('user')
            ->when($request->user_id, function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->from_date, function($q) use ($request) {
                $q->where('date', '>=', $request->from_date);
            })
            ->when($request->to_date, function($q) use ($request) {
                $q->where('date', '<=', $request->to_date);
            })
            ->orderBy('date', 'desc')
            ->get();

        $users = User::where('status', 1)->filterByType('employee')->get();

        return view('admin.payroll.working-hour.index', compact('hours', 'users'));
    }

    /**
     * Show the form for creating a new working hour entry.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        return view('admin.payroll.working-hour.create', compact('users'));
    }

    /**
     * Store a newly created working hour.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        WorkingHour::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'planned_hours' => $request->planned_hours ?? '09:00:00',
            'actual_hours' => $request->actual_hours ?? 0,
            'overtime_hours' => $request->overtime_hours ?? 0,
            'late_hours' => $request->late_hours ?? 0,
            'grass_hours' => $request->grass_hours ?? 0,
            'status' => 'pending',
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.working-hours.index')->with('success', 'Working hours added successfully');
    }

    /**
     * Approve the working hours.
     */
    public function update(Request $request, $id)
    {
        $hour = WorkingHour::findOrFail($id);
        $hour->update([
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);
        return redirect()->route('admin.working-hours.index')->with('success', 'Working hours approved');
    }

    /**
     * Remove the working hour.
     */
    public function destroy($id)
    {
        WorkingHour::findOrFail($id)->delete();
        return redirect()->route('admin.working-hours.index')->with('success', 'Working hours deleted');
    }
}
