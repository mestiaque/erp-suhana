@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Approvals')}}</title>
@endsection

@push('css')
<style>
    .history-card {
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 6px;
        box-shadow: 0 6px 18px rgba(21, 32, 56, 0.05);
        overflow: hidden;
    }

    .history-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 16px;
        background: linear-gradient(135deg, #f6fbff 0%, #eef7f2 100%);
        border-bottom: 1px solid #e8edf5;
    }

    .history-card-header h2 { margin: 0; font-size: 17px; font-weight: 700; }
    .history-card-header p { margin: 4px 0 0; color: #6b778c; font-size: 12px; }

    .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #fff5db;
        color: #b76a00;
        font-weight: 700;
        font-size: 12px;
    }

    .history-filter { padding: 12px 16px 0; }

    .history-filter .form-group label {
        color: #667085;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .history-filter .form-control {
        min-height: 34px;
        border: 1px solid #dce4ef;
        border-radius: 6px;
        color: #17233c;
        font-size: 13px;
        box-shadow: none;
        padding: 6px 10px;
    }

    .history-actions { display: flex; gap: 6px; align-items: center; padding-top: 22px; }

    .history-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 34px;
        padding: 7px 12px;
        border: 0;
        border-radius: 5px;
        color: #ffffff;
        background: #1769e0;
        font-weight: 700;
        font-size: 13px;
    }

    .history-btn:hover { color: #ffffff; text-decoration: none; }
    .history-btn.secondary { color: #17233c; background: #eef1f5; }

    .history-table-wrap { padding: 0 16px 14px; }
    .history-table { margin-bottom: 0; font-size: 11px !important; }

    .history-table thead th {
        border: 0;
        vertical-align: middle;
        white-space: nowrap;
        padding: 8px 6px;
        color: white;
        font-weight: 700;
        font-size: 11px;
    }

    .history-table tbody td {
        padding: 8px 6px;
        vertical-align: top;
        border-top: 1px solid #edf1f6;
    }

    .user-cell strong { display: block; color: #17233c; font-size: 12px; font-weight: 700; }
    .user-cell span, .muted-line { display: block; margin-top: 1px; color: #7b8794; font-size: 11px; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        text-transform: uppercase;
    }

    .status-pill.pending { background: #fff4e6; color: #b76a00; }
    .status-pill.approved { background: #e8fff5; color: #13895f; }
    .status-pill.rejected { background: #ffeceb; color: #d92d20; }
    .status-pill.cancelled { background: #eef1f5; color: #6b778c; }

    .empty-history { padding: 28px 14px; text-align: center; color: #7b8794; }
    .history-pagination { padding: 0 16px 14px; }
    .history-table .btn-sm { padding: 3px 8px; font-size: 11px; }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">

    @include(adminTheme().'alerts')

    <div class="history-card mb-30">
        <div class="history-card-header">
            <div>
                <h2>Approvals</h2>
                <p class="text-muted m-0">Centralized approval requests raised from any module across the system.</p>
            </div>
            <span class="summary-pill"><i class="bx bx-time-five"></i> {{$pendingCount}} Pending</span>
        </div>

        <form method="get" action="{{route('admin.approvals.index')}}" class="history-filter">
            <div class="row">
                <div class="form-group col-xl-3 col-lg-4 col-md-6">
                    <label for="search">Search Title</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{request('search')}}" placeholder="Approval title">
                </div>
                <div class="form-group col-xl-2 col-lg-4 col-md-6">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All (pending first)</option>
                        <option value="pending" {{request('status')=='pending'?'selected':''}}>Pending</option>
                        <option value="approved" {{request('status')=='approved'?'selected':''}}>Approved</option>
                        <option value="rejected" {{request('status')=='rejected'?'selected':''}}>Rejected</option>
                        <option value="cancelled" {{request('status')=='cancelled'?'selected':''}}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group col-xl-3 col-lg-4 col-md-6">
                    <label for="module">Module</label>
                    <select id="module" name="module" class="form-control">
                        <option value="">All</option>
                        @foreach($moduleOptions as $moduleKey)
                        <option value="{{$moduleKey}}" {{request('module')==$moduleKey?'selected':''}}>{{$moduleKey}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-xl-2 col-lg-4 col-md-6">
                    <div class="history-actions">
                        <button type="submit" class="history-btn"><i class="bx bx-search"></i> Filter</button>
                        <a href="{{route('admin.approvals.index')}}" class="history-btn secondary"><i class="bx bx-reset"></i> Reset</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="history-table-wrap">
            <div class="table-responsive">
                <table class="table history-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Module</th>
                            <th>Title</th>
                            <th>Requested By</th>
                            <th>Requested At</th>
                            <th>Decided By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvals as $approval)
                        <tr>
                            <td><span class="status-pill {{$approval->status}}">{{$approval->status}}</span></td>
                            <td><span class="muted-line">{{$approval->module}}</span></td>
                            <td>
                                <strong>{{$approval->title}}</strong>
                                @if($approval->description)
                                <span class="muted-line">{{ \Illuminate\Support\Str::limit($approval->description, 60) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="user-cell">
                                    <strong>{{$approval->requestedBy->name ?? 'System'}}</strong>
                                </div>
                            </td>
                            <td>
                                <strong>{{$approval->created_at->format('d M Y')}}</strong>
                                <span class="muted-line">{{$approval->created_at->format('h:i A')}}</span>
                            </td>
                            <td>
                                @if($approval->approvedBy)
                                <strong>{{$approval->approvedBy->name}}</strong>
                                <span class="muted-line">{{$approval->approved_at?->format('d M Y h:i A')}}</span>
                                @else
                                <span class="muted-line">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                    <a href="{{$approval->url}}" class="btn btn-sm btn-secondary" title="Open Page"><i class="bx bx-link-external"></i></a>

                                    @if($approval->isPending())
                                    @can('approvals.approve')
                                    <form method="post" action="{{route('admin.approvals.approve', $approval->id)}}" onsubmit="return confirm('Approve this request?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="bx bx-check"></i></button>
                                    </form>
                                    @endcan

                                    @can('approvals.reject')
                                    <button type="button" class="btn btn-sm btn-danger" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{$approval->id}}"><i class="bx bx-x"></i></button>

                                    <div class="modal fade" id="rejectModal{{$approval->id}}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post" action="{{route('admin.approvals.reject', $approval->id)}}">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject: {{$approval->title}}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="mb-1">Reason</label>
                                                        <textarea name="remarks" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-history">
                                    <i class="bx bx-search" style="font-size: 40px;"></i>
                                    <p class="mb-0 mt-2">No approval requests found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($approvals->hasPages())
        <div class="history-pagination">
            {{$approvals->links('pagination::bootstrap-5')}}
        </div>
        @endif
    </div>
</div>
@endsection
