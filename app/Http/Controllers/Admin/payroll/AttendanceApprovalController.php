<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\Attendance;
use App\Models\payroll\AttendanceApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceApprovalController extends Controller
{
    /**
     * Display a listing of attendance approvals.
     */
    public function index(Request $request)
    {
        $approvals = AttendanceApproval::with('user')
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->from_date, function($q) use ($request) {
                $q->where('attendance_date', '>=', $request->from_date);
            })
            ->when($request->to_date, function($q) use ($request) {
                $q->where('attendance_date', '<=', $request->to_date);
            })
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('admin.payroll.attendance-approval.index', compact('approvals'));
    }

    /**
     * Show the form for creating a new attendance approval request.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        return view('admin.payroll.attendance-approval.create', compact('users'));
    }

    /**
     * Store a newly created attendance approval.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'requested_status' => 'required',
            'reason' => 'nullable',
        ]);

        // Get original attendance status
        $attendance = Attendance::where('user_id', $request->user_id)
            ->whereDate('date', $request->attendance_date)
            ->first();

        AttendanceApproval::create([
            'user_id' => $request->user_id,
            'attendance_date' => $request->attendance_date,
            'in_time' => $request->in_time,
            'out_time' => $request->out_time,
            'original_status' => $attendance ? $attendance->status : null,
            'requested_status' => $request->requested_status,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.attendance-approval.index')->with('success', 'Attendance approval request submitted');
    }

    /**
     * Approve or reject the attendance change.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_remark' => 'nullable',
        ]);

        $approval = AttendanceApproval::findOrFail($id);

        // Update the attendance record if approved
        if ($request->status === 'approved') {
            Attendance::updateOrCreate(
                [
                    'user_id' => $approval->user_id,
                    'date' => $approval->attendance_date,
                ],
                [
                    'status' => $approval->requested_status,
                    'in_time' => $approval->in_time,
                    'out_time' => $approval->out_time,
                ]
            );
        }

        $approval->update([
            'status' => $request->status,
            'admin_remark' => $request->admin_remark,
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.attendance-approval.index')->with('success', 'Attendance approval updated successfully');
    }

    /**
     * Remove the attendance approval.
     */
    public function destroy($id)
    {
        AttendanceApproval::findOrFail($id)->delete();
        return redirect()->route('admin.attendance-approval.index')->with('success', 'Attendance approval deleted successfully');
    }
}
