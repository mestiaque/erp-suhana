<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\payroll\Asset;
use App\Models\payroll\AssetDistribution;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display a listing of assets.
     */
    public function index(Request $request)
    {
        $assets = Asset::when($request->category, function($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('name')
            ->get();

        return view('admin.payroll.asset.index', compact('assets'));
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        return view('admin.payroll.asset.create');
    }

    /**
     * Store a newly created asset.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
        ]);

        Asset::create($request->all());

        return redirect()->route('admin.assets.index')->with('success', 'Asset added successfully');
    }

    /**
     * Update the asset.
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->update($request->all());
        return redirect()->route('admin.assets.index')->with('success', 'Asset updated');
    }

    /**
     * Remove the asset.
     */
    public function destroy($id)
    {
        Asset::findOrFail($id)->delete();
        return redirect()->route('admin.assets.index')->with('success', 'Asset deleted');
    }

    /**
     * Asset Distribution - list all distributions
     */
    public function distributionIndex(Request $request)
    {
        $distributions = AssetDistribution::with('asset', 'user')
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('assignment_date', 'desc')
            ->get();

        return view('admin.payroll.asset.distribution', compact('distributions'));
    }

    /**
     * Show form to assign asset to employee
     */
    public function distributionCreate()
    {
        $users = User::where('status', 1)->filterByType('employee')->get();
        $assets = Asset::where('status', 'available')->get();
        return view('admin.payroll.asset.distribution-create', compact('users', 'assets'));
    }

    /**
     * Store asset distribution
     */
    public function distributionStore(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'assignment_date' => 'required|date',
        ]);

        // Update asset status
        Asset::findOrFail($request->asset_id)->update(['status' => 'assigned']);

        AssetDistribution::create([
            'asset_id' => $request->asset_id,
            'user_id' => $request->user_id,
            'assignment_date' => $request->assignment_date,
            'condition_on_assign' => $request->condition_on_assign,
            'status' => 'active',
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.assets.distribution')->with('success', 'Asset assigned successfully');
    }

    /**
     * Return asset from employee
     */
    public function distributionReturn($id)
    {
        $distribution = AssetDistribution::findOrFail($id);
        $distribution->update([
            'return_date' => Carbon::now(),
            'condition_on_return' => 'Good',
            'status' => 'returned',
        ]);

        Asset::findOrFail($distribution->asset_id)->update(['status' => 'available']);

        return redirect()->route('admin.assets.distribution')->with('success', 'Asset returned successfully');
    }
}
