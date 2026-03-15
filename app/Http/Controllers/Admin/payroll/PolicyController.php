<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /**
     * Display a listing of policies.
     */
    public function index()
    {
        $policies = Policy::orderBy('type')->get();
        return view('admin.policy.index', compact('policies'));
    }

    /**
     * Show the form for creating a new policy.
     */
    public function create()
    {
        return view('admin.policy.create');
    }

    /**
     * Store a newly created policy.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'value' => 'required|numeric|min:0',
            'unit' => 'required',
        ]);

        Policy::create($request->all());

        return redirect()->route('admin.policy.index')->with('success', 'Policy added successfully');
    }

    /**
     * Update the policy.
     */
    public function update(Request $request, $id)
    {
        $policy = Policy::findOrFail($id);
        $policy->update($request->all());
        return redirect()->route('admin.policy.index')->with('success', 'Policy updated successfully');
    }

    /**
     * Remove the policy.
     */
    public function destroy($id)
    {
        Policy::findOrFail($id)->delete();
        return redirect()->route('admin.policy.index')->with('success', 'Policy deleted successfully');
    }
}
