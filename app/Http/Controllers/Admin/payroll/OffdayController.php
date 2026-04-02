<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class OffdayController extends Controller
{
    /**
     * Display offday settings
     */
    public function index()
    {
        $offday = Attribute::where('type', 21)->first();
        return view('admin.payroll.offday.index', compact('offday'));
    }

    /**
     * Update offday settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'offday' => 'required|integer|min:0|max:6',
        ]);

        $offday = Attribute::where('type', 21)->first();

        if (!$offday) {
            $offday = new Attribute();
            $offday->type = 21;
        }

        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $offday->name = $days[$request->offday];
        $offday->status = $request->status ?? 'active';
        $offday->save();

        return redirect()->back()->with('success', 'Offday updated successfully!');
    }
}
