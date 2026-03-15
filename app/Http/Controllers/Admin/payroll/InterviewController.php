<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Interview::with(['department', 'interviewer'])->latest();

        if ($request->position) {
            $query->where('position', 'like', '%'.$request->position.'%');
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->interview_type) {
            $query->where('interview_type', $request->interview_type);
        }

        if ($request->date_from) {
            $query->where('interview_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('interview_date', '<=', $request->date_to);
        }

        $interviews = $query->paginate(20);
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $interviewers = User::where('status', 1)->filterBy('employee')->get();

        return view(adminTheme().'interviews.index', compact('interviews', 'departments', 'interviewers'));
    }

    public function create()
    {
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $interviewers = User::where('status', 1)->filterBy('employee')->get();
        return view(adminTheme().'interviews.create', compact('departments', 'interviewers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidate_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department_id' => 'nullable|exists:attributes,id',
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'venue' => 'nullable|string',
            'notes' => 'nullable|string',
            'interview_type' => 'required|in:written,oral,practical,final',
        ]);

        Interview::create([
            'candidate_name' => $request->candidate_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'department_id' => $request->department_id,
            'interview_date' => $request->interview_date,
            'interview_time' => $request->interview_time,
            'venue' => $request->venue,
            'notes' => $request->notes,
            'interview_type' => $request->interview_type,
            'interviewer_id' => $request->interviewer_id,
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.interviews.index')->with('success', 'Interview scheduled successfully');
    }

    public function edit($id)
    {
        $interview = Interview::findOrFail($id);
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $interviewers = User::where('status', 1)->filterBy('employee')->get();
        return view(adminTheme().'interviews.edit', compact('interview', 'departments', 'interviewers'));
    }

    public function update(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        $request->validate([
            'candidate_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department_id' => 'nullable|exists:attributes,id',
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'venue' => 'nullable|string',
            'notes' => 'nullable|string',
            'interview_type' => 'required|in:written,oral,practical,final',
        ]);

        $interview->update([
            'candidate_name' => $request->candidate_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'department_id' => $request->department_id,
            'interview_date' => $request->interview_date,
            'interview_time' => $request->interview_time,
            'venue' => $request->venue,
            'notes' => $request->notes,
            'interview_type' => $request->interview_type,
            'interviewer_id' => $request->interviewer_id,
        ]);

        return redirect()->route('admin.interviews.index')->with('success', 'Interview updated successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        $request->validate([
            'status' => 'required|in:scheduled,pending,selected,rejected,on_hold',
            'written_marks' => 'nullable|numeric',
            'oral_marks' => 'nullable|numeric',
            'practical_marks' => 'nullable|numeric',
            'feedback' => 'nullable|string',
        ]);

        $totalMarks = ($request->written_marks ?? 0) + ($request->oral_marks ?? 0) + ($request->practical_marks ?? 0);

        $interview->update([
            'status' => $request->status,
            ' => $request->written_marks' => $request->written_marks,
            'oral_marks' => $request->oral_marks,
            'practical_marks' => $request->practical_marks,
            'total_marks' => $totalMarks,
            'feedback' => $request->feedback,
        ]);

        return redirect()->route('admin.interviews.index')->with('success', 'Interview status updated successfully');
    }

    public function destroy($id)
    {
        $interview = Interview::findOrFail($id);
        $interview->delete();
        return redirect()->route('admin.interviews.index')->with('success', 'Interview deleted successfully');
    }
}
