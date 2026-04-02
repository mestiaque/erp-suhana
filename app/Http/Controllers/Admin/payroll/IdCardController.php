<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class IdCardController extends Controller
{
    /**
     * Display ID card form
     */
    public function index(Request $request)
    {
        $users = User::where('status', 1)->filterByType('employee')->get();

        $selectedUsers = [];
        if ($request->user_ids) {
            $selectedUsers = User::with(['department', 'designation'])
                ->whereIn('id', $request->user_ids)
                ->get();
        }

        return view('admin.payroll.idcard.index', compact('users', 'selectedUsers'));
    }

    /**
     * Print ID cards on blank page
     */
    public function print(Request $request)
    {
        $users = User::where('status', 1)->filterByType('employee')->get();

        $selectedUsers = [];
        if ($request->user_ids) {
            $selectedUsers = User::with(['department', 'designation'])
                ->whereIn('id', $request->user_ids)
                ->get();
        }

        return view('admin.payroll.idcard.print', compact('users', 'selectedUsers'));
    }
}
