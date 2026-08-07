<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $r)
    {
        $perPage = (int) $r->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100]) ? $perPage : 25;

        $query = Approval::with(['requestedBy', 'approvedBy']);

        if ($r->filled('status')) {
            $query->where('status', $r->status);
        } else {
            $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected', 'cancelled')");
        }

        if ($r->filled('module')) {
            $query->where('module', $r->module);
        }

        if ($r->filled('search')) {
            $query->where('title', 'like', '%'.$r->search.'%');
        }

        $approvals = $query->latest()->paginate($perPage)->withQueryString();

        $moduleOptions = Approval::select('module')->distinct()->pluck('module');

        $pendingCount = Approval::pending()->count();

        return view(adminTheme().'approvals.index', [
            'approvals' => $approvals,
            'moduleOptions' => $moduleOptions,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function approve(Approval $approval, Request $r, ApprovalService $service)
    {
        if (!$approval->isPending()) {
            session()->flash('error', 'This request has already been actioned.');
            return redirect()->back();
        }

        $service->approve($approval, $r->user(), $r->input('remarks'));

        session()->flash('success', 'Approved successfully.');
        return redirect()->back();
    }

    public function reject(Approval $approval, Request $r, ApprovalService $service)
    {
        if (!$approval->isPending()) {
            session()->flash('error', 'This request has already been actioned.');
            return redirect()->back();
        }

        $r->validate(['remarks' => 'required|string|max:1000']);

        $service->reject($approval, $r->user(), $r->input('remarks'));

        session()->flash('success', 'Rejected successfully.');
        return redirect()->back();
    }
}
