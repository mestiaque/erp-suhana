@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Login History')}}</title>
@endsection

@push('css')
<style>
    .login-history-page {
        color: #17233c;
    }

    .history-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .summary-card,
    .history-card {
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 6px;
        box-shadow: 0 6px 18px rgba(21, 32, 56, 0.05);
    }

    .summary-card {
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .summary-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
        border-radius: 6px;
        font-size: 18px;
        background: #eef7f2;
        color: #18a575;
    }

    .summary-card.warning .summary-icon {
        background: #fff5db;
        color: #c68300;
    }

    .summary-card.info .summary-icon {
        background: #eaf3ff;
        color: #1769e0;
    }

    .summary-card span {
        display: block;
        color: #667085;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .summary-card strong {
        display: block;
        margin-top: 2px;
        color: #122033;
        font-size: 20px;
        line-height: 1.1;
    }

    .history-card {
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

    .history-card-header h3 {
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

    .login-history-page .form-group label {
        color: #667085;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .login-history-page .form-control {
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

    .history-btn:hover {
        color: #ffffff;
        text-decoration: none;
    }

    .history-btn.secondary {
        color: #17233c;
        background: #eef1f5;
    }

    .history-table-wrap {
        padding: 0 16px 14px;
    }

    .history-table {
        margin-bottom: 0;
        font-size: 11px !important;
    }

    .history-table thead th {
        border: 0;
        vertical-align: middle;
        white-space: nowrap;
        padding: 8px 6px;
        color: #6b778c;
        font-weight: 700;
        font-size: 11px;
    }

    .history-table tbody td {
        padding: 8px 6px;
        vertical-align: top;
        border-top: 1px solid #edf1f6;
    }

    .user-cell strong,
    .ip-cell strong {
        display: block;
        color: #17233c;
        font-size: 12px;
        font-weight: 700;
    }

    .user-cell span,
    .ip-cell span,
    .muted-line {
        display: block;
        margin-top: 1px;
        color: #7b8794;
        font-size: 11px;
    }

    .agent-text {
        max-width: 240px;
        color: #4a5568;
        line-height: 1.4;
        word-break: break-word;
        font-size: 11px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-pill.active {
        background: #e8fff5;
        color: #13895f;
    }

    .status-pill.inactive {
        background: #fff4e6;
        color: #b76a00;
    }

    .empty-history {
        padding: 28px 14px;
        text-align: center;
        color: #7b8794;
    }

    .history-pagination {
        padding: 0 16px 14px;
    }

    .history-table .btn-sm {
        padding: 3px 8px;
        font-size: 11px;
    }

    @media only screen and (max-width: 991px) {
        .history-summary {
            grid-template-columns: 1fr;
        }
    }

    @media only screen and (max-width: 575px) {
        .history-card-header,
        .history-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .history-filter,
        .history-table-wrap,
        .history-pagination {
            padding-left: 14px;
            padding-right: 14px;
        }

        .history-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">

    @include(adminTheme().'alerts')

    <div class="login-history-page">
        <div class="history-summary">
            <div class="summary-card warning">
                <span class="summary-icon"><i class="bx bx-log-in-circle"></i></span>
                <div>
                    <span>Today Login</span>
                    <strong>{{$todayLoginCount}}</strong>
                </div>
            </div>
            <div class="summary-card">
                <span class="summary-icon"><i class="bx bx-radio-circle-marked"></i></span>
                <div>
                    <span>Active Now</span>
                    <strong>{{$activeUserCount}}</strong>
                </div>
            </div>
            <div class="summary-card info">
                <span class="summary-icon"><i class="bx bx-user-check"></i></span>
                <div>
                    <span>Tracked Users</span>
                    <strong>{{$trackedUserCount}}</strong>
                </div>
            </div>
        </div>

        <div class="history-card mb-30">
            <div class="history-card-header">
                <div>
                    <h3>User Login History</h3>
                    <p>Active status is calculated from activity within the last {{$toleranceMinutes}} minutes.</p>
                </div>
            </div>

            <form method="get" action="{{route('admin.loginHistory')}}" class="history-filter">
                <div class="row">
                    <div class="form-group col-xl-3 col-lg-4 col-md-6">
                        <label for="search">Search User</label>
                        <input type="text" id="search" name="search" class="form-control" value="{{request('search')}}" placeholder="Name, email, mobile, ID">
                    </div>
                    <div class="form-group col-xl-2 col-lg-4 col-md-6">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">All</option>
                            <option value="active" {{request('status')=='active'?'selected':''}}>Active</option>
                            <option value="inactive" {{request('status')=='inactive'?'selected':''}}>Inactive</option>
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
                    <div class="form-group col-xl-1 col-lg-4 col-md-6">
                        <label for="per_page">Show</label>
                        <select id="per_page" name="per_page" class="form-control">
                            @foreach([25, 50, 100] as $size)
                            <option value="{{$size}}" {{request('per_page', 25)==$size?'selected':''}}>{{$size}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-xl-2 col-lg-4 col-md-6">
                        <div class="history-actions">
                            <button type="submit" class="history-btn"><i class="bx bx-search"></i> Filter</button>
                            <a href="{{route('admin.loginHistory')}}" class="history-btn secondary"><i class="bx bx-reset"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="history-table-wrap">
                <div class="table-responsive">
                    <table class="table history-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Public IP</th>
                                <th>Login Time</th>
                                <th>Current Status</th>
                                <th>Last Active</th>
                                <th>Last Logout</th>
                                <th>User Agent</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loginLogs as $log)
                            @php
                                $user = $log->user;
                                $data = $log->login_data ?? [];
                                $publicIp = data_get($data, 'public_ip') ?: data_get($data, 'ip', 'N/A');
                                $requestIp = data_get($data, 'ip');
                                $lastActive = $log->last_active_log;
                                $lastLogout = $log->last_logout_log;
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <strong>{{$user->name ?? 'N/A'}}</strong>
                                        <span>{{$user->employee_id ?? 'No Employee ID'}}</span>
                                        <span>{{$user->email ?? $user->mobile ?? ''}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="ip-cell">
                                        <strong>{{$publicIp}}</strong>
                                        @if($requestIp && $requestIp !== $publicIp)
                                        <span>Request IP: {{$requestIp}}</span>
                                        @endif
                                        <span>{{data_get($data, 'url', '')}}</span>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{$log->created_at->format('d M Y')}}</strong>
                                    <span class="muted-line">{{$log->created_at->format('h:i A')}}</span>
                                    <span class="muted-line">{{$log->created_at->diffForHumans()}}</span>
                                </td>
                                <td>
                                    @if($log->is_active_now)
                                    <span class="status-pill active"><i class="bx bx-check-circle"></i> Active</span>
                                    @else
                                    <span class="status-pill inactive"><i class="bx bx-time-five"></i> Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lastActive)
                                    <strong>{{$lastActive->created_at->format('d M Y')}}</strong>
                                    <span class="muted-line">{{$lastActive->created_at->format('h:i A')}}</span>
                                    <span class="muted-line">{{$lastActive->created_at->diffForHumans()}}</span>
                                    @else
                                    <span class="muted-line">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lastLogout)
                                    <strong>{{$lastLogout->created_at->format('d M Y')}}</strong>
                                    <span class="muted-line">{{$lastLogout->created_at->format('h:i A')}}</span>
                                    <span class="muted-line">{{$lastLogout->created_at->diffForHumans()}}</span>
                                    @else
                                    <span class="muted-line">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="agent-text">{{data_get($data, 'user_agent', 'N/A')}}</div>
                                </td>
                                <td>
                                    @can('login_history.view')
                                    <a href="{{route('admin.userLoginHistory', $user->id)}}" class="btn btn-sm btn-primary" title="View Full History">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-history">
                                        <i class="bx bx-search" style="font-size: 40px;"></i>
                                        <p class="mb-0 mt-2">No login history found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($loginLogs->hasPages())
            <div class="history-pagination">
                {{$loginLogs->links('pagination::bootstrap-5')}}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
