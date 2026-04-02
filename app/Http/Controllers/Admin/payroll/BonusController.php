<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\Bonus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    /**
     * Display a listing of bonuses.
     */
    public function index(Request $request)
    {
        $bonuses = Bonus::with('user')
            ->when($request->month, function($q) use ($request) {
                $q->where('month', $request->month);
            })
            ->when($request->type, function($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.payroll.bonus.index', compact('bonuses'));
    }

    /**
     * Show the form for creating a new bonus.
     */
    public function create()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        return view('admin.payroll.bonus.create', compact('users'));
    }

    /**
     * Store a newly created bonus.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'amount' => 'required|numeric|min:0',
            'month' => 'required',
        ]);

        Bonus::create([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'percentage' => $request->percentage ?? 0,
            'month' => $request->month,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.bonus.index')->with('success', 'Bonus added successfully');
    }

    /**
     * Approve the bonus.
     */
    public function update(Request $request, $id)
    {
        $bonus = Bonus::findOrFail($id);
        $bonus->update([
            'status' => $request->status,
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('admin.bonus.index')->with('success', 'Bonus updated');
    }

    /**
     * Remove the bonus.
     */
    public function destroy($id)
    {
        Bonus::findOrFail($id)->delete();
        return redirect()->route('admin.bonus.index')->with('success', 'Bonus deleted');
    }
}
