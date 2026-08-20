@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Data Change Log')}}</title>
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

    .history-card-header h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
    }

    .history-card-header p {
        margin: 4px 0 0;
        color: #6b778c;
        font-size: 12px;
    }

    .history-filter {
        padding: 12px 16px 0;
    }

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

    .history-actions {
        display: flex;
        gap: 6px;
        align-items: center;
        padding-top: 22px;
    }

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

    .user-cell strong, .ip-cell strong {
        display: block;
        color: #17233c;
        font-size: 12px;
        font-weight: 700;
    }

    .user-cell span, .ip-cell span, .muted-line {
        display: block;
        margin-top: 1px;
        color: #7b8794;
        font-size: 11px;
    }

    .event-pill {
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

    .event-pill.create { background: #e8fff5; color: #13895f; }
    .event-pill.update { background: #eaf3ff; color: #1769e0; }
    .event-pill.delete { background: #ffeceb; color: #d92d20; }

    .empty-history { padding: 28px 14px; text-align: center; color: #7b8794; }

    .history-pagination { padding: 0 16px 14px; }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">

    @include(adminTheme().'alerts')

    <div class="history-card mb-30">
        <div class="history-card-header">
            <div>
                <h2>Data Change Log</h2>
                <p class="text-muted m-0">Every create, update, and delete across the system — who changed what, from where, and what it looked like before.</p>
            </div>
        </div>

        <form method="get" action="{{route('admin.dataChangeLog')}}" class="history-filter">
            <div class="row">
                <div class="form-group col-xl-3 col-lg-4 col-md-6">
                    <label for="search">Search User</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{request('search')}}" placeholder="Name or email">
                </div>
                <div class="form-group col-xl-2 col-lg-4 col-md-6">
                    <label for="event">Event</label>
                    <select id="event" name="event" class="form-control">
                        <option value="">All</option>
                        <option value="create" {{request('event')=='create'?'selected':''}}>Create</option>
                        <option value="update" {{request('event')=='update'?'selected':''}}>Update</option>
                        <option value="delete" {{request('event')=='delete'?'selected':''}}>Delete</option>
                    </select>
                </div>
                <div class="form-group col-xl-3 col-lg-4 col-md-6">
                    <label for="model">Feature</label>
                    <select id="model" name="model" class="form-control">
                        <option value="">All</option>
                        @foreach($modelOptions as $modelClass)
                        <option value="{{$modelClass}}" {{request('model')==$modelClass?'selected':''}}>{{friendlyModelName($modelClass)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-xl-2 col-lg-4 col-md-6">
                    <label for="date_from">From</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="{{request('date_from')}}">
                </div>
                <div class="form-group col-xl-2 col-lg-4 col-md-6">
                    <label for="date_to">To</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="{{request('date_to')}}">
                </div>
                <div class="form-group col-xl-2 col-lg-4 col-md-6">
                    <div class="history-actions">
                        <button type="submit" class="history-btn"><i class="bx bx-search"></i> Filter</button>
                        <a href="{{route('admin.dataChangeLog')}}" class="history-btn secondary"><i class="bx bx-reset"></i> Reset</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="history-table-wrap">
            <div class="table-responsive">
                <table class="table history-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Feature</th>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>URL</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td><span class="event-pill {{$log->event}}">{{$log->event}}</span></td>
                            <td>
                                <strong>{{friendlyModelName($log->loggable_type)}}</strong>
                                <span class="muted-line">#{{$log->loggable_id}}</span>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <strong>{{$log->user->name ?? 'System'}}</strong>
                                    <span>{{$log->user->email ?? ''}}</span>
                                </div>
                            </td>
                            <td>
                                <div class="ip-cell">
                                    <strong>{{$log->ip_address ?? 'N/A'}}</strong>
                                </div>
                            </td>
                            <td>
                                <div class="agent-text" style="max-width:220px;word-break:break-word;">{{$log->url ?? 'N/A'}}</div>
                            </td>
                            <td>
                                <strong>{{$log->created_at->format('d M Y')}}</strong>
                                <span class="muted-line">{{$log->created_at->format('h:i A')}}</span>
                            </td>
                            <td>
                                <a href="{{route('admin.dataChangeLogShow', $log->id)}}" class="btn btn-sm btn-primary" title="View Details">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-history">
                                    <i class="bx bx-search" style="font-size: 40px;"></i>
                                    <p class="mb-0 mt-2">No data change log found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($logs->hasPages())
        <div class="history-pagination">
            {{$logs->links('pagination::bootstrap-5')}}
        </div>
        @endif
    </div>
</div>
@endsection
