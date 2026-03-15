<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $query = Notice::with(['creator'])->latest();

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->where('notice_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('notice_date', '<=', $request->date_to);
        }

        $notices = $query->paginate(20);

        return view(adminTheme().'notices.index', compact('notices'));
    }

    public function create()
    {
        return view(adminTheme().'notices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'notice_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:active,inactive',
        ]);

        Notice::create([
            'title' => $request->title,
            'description' => $request->description,
            'notice_date' => $request->notice_date,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'priority' => $request->priority,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.notices')->with('success', 'Notice created successfully');
    }

    public function edit($id)
    {
        $notice = Notice::findOrFail($id);
        return view(adminTheme().'notices.edit', compact('notice'));
    }

    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'notice_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:active,inactive',
        ]);

        $notice->update([
            'title' => $request->title,
            'description' => $request->description,
            'notice_date' => $request->notice_date,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'priority' => $request->priority,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.notices')->with('success', 'Notice updated successfully');
    }

    public function destroy($id)
    {
        $notice = Notice::findOrFail($id);
        $notice->delete();
        return redirect()->route('admin.notices')->with('success', 'Notice deleted successfully');
    }
}
