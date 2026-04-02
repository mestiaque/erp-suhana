<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the holidays.
     */
    public function index()
    {
        $holidays = Holiday::orderBy('from_date', 'desc')->get();
        return view('admin.payroll.holiday.index', compact('holidays'));
    }

    /**
     * Show the form for creating a new holiday.
     */
    public function create()
    {
        return view('admin.payroll.holiday.create');
    }

    /**
     * Store a newly created holiday in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'remarks' => 'nullable|string',
        ]);

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        $days = $fromDate->diffInDays($toDate) + 1;

        Holiday::create([
            'title' => $request->title,
            'type' => $request->type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'remarks' => $request->remarks,
            'days' => $days,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('admin.holiday.index')->with('success', 'Holiday created successfully!');
    }

    /**
     * Show the form for editing the specified holiday.
     */
    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);
        return view('admin.payroll.holiday.edit', compact('holiday'));
    }

    /**
     * Update the specified holiday in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'remarks' => 'nullable|string',
        ]);

        $holiday = Holiday::findOrFail($id);

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        $days = $fromDate->diffInDays($toDate) + 1;

        $holiday->update([
            'title' => $request->title,
            'type' => $request->type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'remarks' => $request->remarks,
            'days' => $days,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('admin.holiday.index')->with('success', 'Holiday updated successfully!');
    }

    /**
     * Remove the specified holiday from storage.
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()->route('admin.holiday.index')->with('success', 'Holiday deleted successfully!');
    }
}
