<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\Termination;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TerminationController extends Controller
{
    public function index(Request $request)
    {
        $query = Termination::with(['user', 'approver', 'user.department'])->latest();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->termination_type) {
            $query->where('termination_type', $request->termination_type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->where('termination_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('termination_date', '<=', $request->date_to);
        }

        $terminations = $query->paginate(20);
        $users = User::where('status', 1)->filterByType('employee')->get();

        return view(adminTheme().'payroll.terminations.index', compact('terminations', 'users'));
    }

    public function create()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        return view(adminTheme().'payroll.terminations.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'termination_date' => 'required|date',
            'termination_type' => 'required|in:resignation,dismissal,retirement,death,contract_end,other',
            'reason' => 'required|string',
            'notice_period' => 'nullable|string',
        ]);

        Termination::create([
            'user_id' => $request->user_id,
            'termination_date' => $request->termination_date,
            'termination_type' => $request->termination_type,
            'reason' => $request->reason,
            'notice_period' => $request->notice_period,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.terminations.index')->with('success', 'Termination request created successfully');
    }

    public function edit($id)
    {
        $termination = Termination::findOrFail($id);
        $users = User::where('status', 1)->filterByType('employee')->get();
        return view(adminTheme().'payroll.terminations.edit', compact('termination', 'users'));
    }

    public function update(Request $request, $id)
    {
        $termination = Termination::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'termination_date' => 'required|date',
            'termination_type' => 'required|in:resignation,dismissal,retirement,death,contract_end,other',
            'reason' => 'required|string',
            'notice_period' => 'nullable|string',
        ]);

        $termination->update([
            'user_id' => $request->user_id,
            'termination_date' => $request->termination_date,
            'termination_type' => $request->termination_type,
            'reason' => $request->reason,
            'notice_period' => $request->notice_period,
        ]);

        return redirect()->route('admin.terminations.index')->with('success', 'Termination request updated successfully');
    }

    public function approve(Request $request, $id)
    {
        $termination = Termination::findOrFail($id);

        $termination->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'exit_interview_notes' => $request->exit_interview_notes,
            'documents' => $request->documents,
        ]);

        // Update user status to terminated
        $user = $termination->user;
        $user->update(['employee_status' => 'terminated']);

        return redirect()->route('admin.terminations.index')->with('success', 'Termination approved successfully');
    }

    public function reject(Request $request, $id)
    {
        $termination = Termination::findOrFail($id);

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $termination->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.terminations.index')->with('success', 'Termination rejected');
    }

    public function destroy($id)
    {
        $termination = Termination::findOrFail($id);
        $termination->delete();
        return redirect()->route('admin.terminations.index')->with('success', 'Termination record deleted successfully');
    }
}
