<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\Performance;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /**
     * Display a listing of performances.
     */
    public function index(Request $request)
    {
        $performances = Performance::with('user', 'reviewer')
            ->when($request->year, function($q) use ($request) {
                $q->where('year', $request->year);
            })
            ->when($request->quarter, function($q) use ($request) {
                $q->where('quarter', $request->quarter);
            })
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->get();

        $years = range(date('Y'), date('Y') - 5);

        return view('admin.payroll.performance.index', compact('performances', 'years'));
    }

    /**
     * Show the form for creating a new performance review.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        $years = range(date('Y'), date('Y') - 5);
        return view('admin.payroll.performance.create', compact('users', 'years'));
    }

    /**
     * Store a newly created performance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required',
            'quarter' => 'required',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        Performance::create([
            'user_id' => $request->user_id,
            'reviewer_id' => auth()->id(),
            'year' => $request->year,
            'quarter' => $request->quarter,
            'rating' => $request->rating,
            'attendance_score' => $request->attendance_score ?? 0,
            'task_completion' => $request->task_completion ?? 0,
            'teamwork' => $request->teamwork ?? 0,
            'initiative' => $request->initiative ?? 0,
            'punctuality' => $request->punctuality ?? 0,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'comments' => $request->comments,
            'goals' => $request->goals,
            'status' => 'reviewed',
        ]);

        return redirect()->route('admin.performance.index')->with('success', 'Performance review added successfully');
    }

    /**
     * Update the performance.
     */
    public function update(Request $request, $id)
    {
        $performance = Performance::findOrFail($id);

        $performance->update([
            'rating' => $request->rating,
            'attendance_score' => $request->attendance_score ?? 0,
            'task_completion' => $request->task_completion ?? 0,
            'teamwork' => $request->teamwork ?? 0,
            'initiative' => $request->initiative ?? 0,
            'punctuality' => $request->punctuality ?? 0,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'comments' => $request->comments,
            'goals' => $request->goals,
        ]);

        return redirect()->route('admin.performance.index')->with('success', 'Performance updated successfully');
    }

    /**
     * Remove the performance.
     */
    public function destroy($id)
    {
        Performance::findOrFail($id)->delete();
        return redirect()->route('admin.performance.index')->with('success', 'Performance deleted successfully');
    }
}
