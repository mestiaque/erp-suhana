<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\payroll\Attendance;
use App\Models\payroll\Leave;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with(['user', 'leaveType', 'approver', 'user.department'])->latest();

        // Filter by employee
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by leave type
        if ($request->leave_type_id) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by department (through user relationship)
        if ($request->department_id) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by date range (start_date)
        if ($request->start_date_from) {
            $query->where('start_date', '>=', $request->start_date_from);
        }

        if ($request->start_date_to) {
            $query->where('start_date', '<=', $request->start_date_to);
        }

        $leaves = $query->paginate(20);

        // For modal forms and filters
        $leaveTypes = Attribute::where('type', 20)->where('status', 'active')->get();
        $users = User::where('status', 1)->filterByType('employee')->get();
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();

        // Get leave balance for each user (approved leaves only)
        $leaveBalances = [];
        foreach ($users as $user) {
            foreach ($leaveTypes as $type) {
                $approvedDays = Leave::where('user_id', $user->id)
                    ->where('leave_type_id', $type->id)
                    ->where('status', 'approved')
                    ->sum('days');
                $leaveBalances[$user->id][$type->id] = [
                    'allowed' => $type->qty ?? 0,
                    'taken' => $approvedDays,
                    'remaining' => ($type->qty ?? 0) - $approvedDays
                ];
            }
        }

        return view(adminTheme().'payroll.leaves.index', compact('leaves', 'leaveTypes', 'users', 'departments', 'leaveBalances'));
    }

    public function create()
    {
        $leaveTypes = Attribute::where('type', 20)->where('status', 'active')->get();
        $users = User::where('status', 1)->filterByType('employee')->get(); // For admin to apply on behalf of user
        return view(adminTheme().'payroll.leaves.create', compact('leaveTypes', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:attributes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id', // If admin applies for someone else
        ]);

        $leave = new Leave();
        $leave->user_id = $request->user_id ?? Auth::id();
        $leave->leave_type_id = $request->leave_type_id;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $leave->days = $start->diffInDays($end) + 1;

        $leave->reason = $request->reason;
        $leave->status = 'pending';
        $leave->save();

        return redirect()->route('admin.leaves.index')->with('success', 'Leave application submitted successfully.');
    }

    public function edit($id)
    {
        $leave = Leave::findOrFail($id);
        $leaveTypes = Attribute::where('type', 20)->where('status', 'active')->get();
        return view(adminTheme().'payroll.leaves.edit', compact('leave', 'leaveTypes'));
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        if ($request->has('status')) {
            // Approval/Rejection logic
            $request->validate([
                'status' => 'required|in:approved,rejected,pending',
                'rejection_reason' => 'nullable|required_if:status,rejected|string',
            ]);

            $leave->status = $request->status;
            if ($request->status == 'approved') {
                $leave->approved_by = Auth::id();
                //attencence mark as leave
                //
                $period = CarbonPeriod::create(
                    $leave->start_date,
                    $leave->end_date
                );

                foreach ($period as $date) {

                    Attendance::updateOrCreate(
                        [
                            'user_id' => $leave->user_id,
                            'date' => $date->format('Y-m-d'),
                        ],
                        [
                            'status' => 'Leave',
                        ]
                    );
                }

            }
            if ($request->status == 'rejected') {
                $leave->rejection_reason = $request->rejection_reason;
            }
        } else {
            // Update details logic
            $request->validate([
                'leave_type_id' => 'required|exists:attributes,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'nullable|string',
            ]);

            $leave->leave_type_id = $request->leave_type_id;
            $leave->start_date = $request->start_date;
            $leave->end_date = $request->end_date;

            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $leave->days = $start->diffInDays($end) + 1;

            $leave->reason = $request->reason;
        }

        $leave->save();

        return redirect()->route('admin.leaves.index')->with('success', 'Leave updated successfully.');
    }

    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
        if ($leave->status == 'approved') {
            return redirect()->route('admin.leaves.index')->with('error', 'Approved leave cannot be deleted. Please contact HR for further assistance.');
        }
        $leave->delete();
        return redirect()->route('admin.leaves.index')->with('success', 'Leave deleted successfully.');
    }

    // Leave Types Management
    public function types()
    {
        $types = Attribute::where('type', 20)->latest()->paginate(20);
        return view(adminTheme().'payroll.leaves.types.index', compact('types'));
    }

    public function typesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'qty' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $type = new Attribute();
        $type->name = $request->name;
        $type->qty = $request->qty;
        $type->type = 20; // Leave Type
        $type->status = $request->status;
        $type->save();

        return redirect()->back()->with('success', 'Leave Type created successfully.');
    }

    public function typesUpdate(Request $request, $id)
    {
        $type = Attribute::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:191',
            'qty' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $type->name = $request->name;
        $type->qty = $request->qty;
        $type->status = $request->status;
        $type->save();

        return redirect()->back()->with('success', 'Leave Type updated successfully.');
    }

    public function typesDestroy($id)
    {
        $type = Attribute::findOrFail($id);
        $type->delete();
        return redirect()->back()->with('success', 'Leave Type deleted successfully.');
    }


        /**
     * Show manual leave create form (admin)
     */
    public function manualCreate()
    {
        $employees = \App\Models\User::where('status', 1)->filterByType('employee')->get();
        return view('admin.payroll.leaves.manual_create', compact('employees'));
    }

    /**
     * Store manual leave (admin)
     */
    public function manualStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $leave = new Leave();
        $leave->user_id = $request->user_id;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->reason = $request->reason;
        $leave->status = 'approved';
        $leave->created_by = Auth::id();
        $leave->save();

        return redirect()->route('admin.leaves.index')->with('success', 'Leave created successfully.');
    }

    /**
     * Leave Summary - shows all employees' leave balance and taken leaves (Yearly)
     */
    public function summary(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;

        $users = User::where('status', 1)->filterByType('employee')
            ->with(['department', 'designation'])
            ->get();

        $leaveTypes = Attribute::where('type', 20)->where('status', 'active')->get();

        // Get all approved leaves for the selected year
        $leaves = Leave::where('status', 'approved')
            ->whereYear('start_date', $year)
            ->get();

        // Organize leaves by user and type
        $userLeaves = [];
        foreach ($leaves as $leave) {
            $userId = $leave->user_id;
            $typeId = $leave->leave_type_id;
            if (!isset($userLeaves[$userId])) {
                $userLeaves[$userId] = [];
            }
            if (!isset($userLeaves[$userId][$typeId])) {
                $userLeaves[$userId][$typeId] = 0;
            }
            $userLeaves[$userId][$typeId] += $leave->days;
        }

        // Build summary data
        $summaryData = [];
        foreach ($users as $user) {
            $userData = [
                'user' => $user,
                'leaves' => [],
                'total_taken' => 0,
            ];

            foreach ($leaveTypes as $type) {
                $taken = $userLeaves[$user->id][$type->id] ?? 0;
                $allowed = $type->qty ?? 0;
                $userData['leaves'][] = [
                    'type' => $type,
                    'allowed' => $allowed,
                    'taken' => $taken,
                    'remaining' => $allowed - $taken,
                ];
                $userData['total_taken'] += $taken;
            }

            $summaryData[] = $userData;
        }

        return view(adminTheme().'payroll.leaves.summary', compact('summaryData', 'leaveTypes', 'year'));
    }
}
