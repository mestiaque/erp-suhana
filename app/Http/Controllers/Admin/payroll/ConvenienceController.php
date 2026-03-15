<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\ConvenienceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConvenienceController extends Controller
{
    /**
     * Display a listing of convenience requests.
     */
    public function index(Request $request)
    {
        $requests = ConvenienceRequest::with('user')
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.convenience.index', compact('requests'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterBy('employee')->get();
        return view('admin.convenience.create', compact('users'));
    }

    /**
     * Store a newly created request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'amount' => 'required|numeric|min:0',
            'reason' => 'nullable',
        ]);

        ConvenienceRequest::create([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.convenience.index')->with('success', 'Request submitted successfully');
    }

    /**
     * Approve or reject the request.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_remark' => 'nullable',
        ]);

        $convenience = ConvenienceRequest::findOrFail($id);
        $convenience->update([
            'status' => $request->status,
            'admin_remark' => $request->admin_remark,
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.convenience.index')->with('success', 'Request updated successfully');
    }

    /**
     * Remove the request.
     */
    public function destroy($id)
    {
        ConvenienceRequest::findOrFail($id)->delete();
        return redirect()->route('admin.convenience.index')->with('success', 'Request deleted successfully');
    }
}
