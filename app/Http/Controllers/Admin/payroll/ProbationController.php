<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Probation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProbationController extends Controller
{
    public function index(Request $request)
    {
        $query = Probation::with(['user', 'reviewer', 'user.department'])->latest();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->confirmation_status) {
            $query->where('confirmation_status', $request->confirmation_status);
        }

        if ($request->date_from) {
            $query->where('probation_start_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('probation_start_date', '<=', $request->date_to);
        }

        $probations = $query->paginate(20);
        $users = User::where('status', 1)->filterBy('employee')->get();

        return view(adminTheme().'probations.index', compact('probations', 'users'));
    }

    public function create()
    {
        $users = User::where('status', 1)->filterBy('employee')->get();
        return view(adminTheme().'probations.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'probation_start_date' => 'required|date',
            'probation_end_date' => 'required|date|after:probation_start_date',
            'months' => 'required|integer|min:1',
        ]);

        $startDate = Carbon::parse($request->probation_start_date);
        $endDate = Carbon::parse($request->probation_end_date);
        $months = $startDate->diffInMonths($endDate);

        Probation::create([
            'user_id' => $request->user_id,
            'probation_start_date' => $request->probation_start_date,
            'probation_end_date' => $request->probation_end_date,
            'months' => $months,
            'status' => 'active',
            'confirmation_status' => 'pending',
        ]);

        return redirect()->route('admin.probations.index')->with('success', 'Probation period created successfully');
    }

    public function edit($id)
    {
        $probation = Probation::findOrFail($id);
        $users = User::where('status', 1)->filterBy('employee')->get();
        return view(adminTheme().'probations.edit', compact('probation', 'users'));
    }

    public function update(Request $request, $id)
    {
        $probation = Probation::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'probation_start_date' => 'required|date',
            'probation_end_date' => 'required|date|after:probation_start_date',
            'months' => 'required|integer|min:1',
        ]);

        $startDate = Carbon::parse($request->probation_start_date);
        $endDate = Carbon::parse($request->probation_end_date);
        $months = $startDate->diffInMonths($endDate);

        $probation->update([
            'user_id' => $request->user_id,
            'probation_start_date' => $request->probation_start_date,
            'probation_end_date' => $request->probation_end_date,
            'months' => $months,
        ]);

        return redirect()->route('admin.probations.index')->with('success', 'Probation period updated successfully');
    }

    public function confirm(Request $request, $id)
    {
        $probation = Probation::findOrFail($id);

        $request->validate([
            'confirmation_status' => 'required|in:confirmed,rejected',
            'confirmation_notes' => 'nullable|string',
        ]);

        $probation->update([
            'confirmation_status' => $request->confirmation_status,
            'confirmation_notes' => $request->confirmation_notes,
            'confirmation_date' => $request->confirmation_status === 'confirmed' ? Carbon::now() : null,
            'status' => $request->confirmation_status === 'confirmed' ? 'completed' : 'active',
            'reviewed_by' => Auth::id(),
        ]);

        return redirect()->route('admin.probations.index')->with('success', 'Probation confirmation updated successfully');
    }

    public function extend(Request $request, $id)
    {
        $probation = Probation::findOrFail($id);

        $request->validate([
            'new_end_date' => 'required|date|after:probation_end_date',
        ]);

        $oldEndDate = Carbon::parse($probation->probation_end_date);
        $newEndDate = Carbon::parse($request->new_end_date);
        $extraMonths = $oldEndDate->diffInMonths($newEndDate);

        $probation->update([
            'probation_end_date' => $request->new_end_date,
            'months' => $probation->months + $extraMonths,
            'status' => 'extended',
            'performance_notes' => $request->performance_notes,
        ]);

        return redirect()->route('admin.probations.index')->with('success', 'Probation period extended successfully');
    }

    public function destroy($id)
    {
        $probation = Probation::findOrFail($id);
        $probation->delete();
        return redirect()->route('admin.probations.index')->with('success', 'Probation record deleted successfully');
    }
}
