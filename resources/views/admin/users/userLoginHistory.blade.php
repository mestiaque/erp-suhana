@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Login History - '.($user->name ?? 'User'))}}</title>
@endsection

@push('css')
<style>
    .user-history-page {
        color: #17233c;
    }

    .user-history-card {
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 8px;
        box-shadow: 0 12px 32px rgba(21, 32, 56, 0.06);
        overflow: hidden;
    }

    .user-history-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding: 22px 24px;
        background: linear-gradient(135deg, #f6fbff 0%, #eef7f2 100%);
        border-bottom: 1px solid #e8edf5;
    }

    .user-history-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #122033;
    }

    .user-history-header p {
        margin: 6px 0 0;
        color: #6b778c;
        font-size: 13px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 34px;
        padding: 7px 12px;
        border-radius: 6px;
        color: #1458c8;
        background: #eaf3ff;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
    }

    .back-btn:hover {
        background: #ddeeff;
        text-decoration: none;
    }

    .user-history-table-wrap {
        padding: 0 18px 18px;
    }

    .user-history-table {
        margin-bottom: 0;
        font-size: 12px !important;
    }

    .user-history-table thead th {
        border: 0;
        vertical-align: middle;
        white-space: nowrap;
        padding-left: 10px;
        padding-right: 10px;
    }

    .user-history-table tbody td {
        padding: 14px 10px;
        vertical-align: top;
        border-top: 1px solid #edf1f6;
    }

    .ip-cell strong,
    .agent-text strong {
        display: block;
        color: #17233c;
        font-size: 13px;
        font-weight: 700;
    }

    .ip-cell span,
    .muted-line,
    .agent-text span {
        display: block;
        margin-top: 3px;
        color: #7b8794;
        font-size: 12px;
    }

    .agent-text {
        max-width: 320px;
        color: #4a5568;
        line-height: 1.5;
        word-break: break-word;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
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
        padding: 42px 18px;
        text-align: center;
        color: #7b8794;
    }

    @media only screen and (max-width: 575px) {
        .user-history-header {
            flex-direction: column;
            padding: 18px;
        }

        .back-btn {
            width: 100%;
            justify-content: center;
        }

        .user-history-table-wrap {
            padding-left: 12px;
            padding-right: 12px;
        }
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Login History - {{$user->name ?? 'User'}}</h1>

        <ol class="breadcrumb">
            <li class="item">
                <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">Dashboard</li>
            <li class="item">
                <a href="{{route('admin.loginHistory')}}">Login History</a>
            </li>
            <li class="item">{{$user->name ?? 'User'}}</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    <div class="user-history-page">
        <div class="user-history-card mb-30">
            <div class="user-history-header">
                <div>
                    <h3>All Login Records - {{$user->name ?? 'User'}}</h3>
                    <p>Total {{$loginLogs->total()}} login records found.</p>
                </div>
                <a href="{{route('admin.loginHistory')}}" class="back-btn"><i class="bx bx-arrow-back"></i> Back to Login History</a>
            </div>

            <div class="user-history-table-wrap">
                <div class="table-responsive">
                    <table class="table user-history-table">
                        <thead>
                            <tr>
                                <th>Login Time</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Last Active</th>
                                <th>Last Logout</th>
                                <th>Browser / Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loginLogs as $log)
                            @php
                                $data = $log->login_data ?? [];
                                $publicIp = data_get($data, 'public_ip') ?: data_get($data, 'ip', 'N/A');
                                $requestIp = data_get($data, 'ip');
                                $lastActive = $log->last_active_log;
                                $lastLogout = $log->last_logout_log;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{$log->created_at->format('d M Y')}}</strong>
                                    <span class="muted-line">{{$log->created_at->format('h:i A')}}</span>
                                    <span class="muted-line">{{$log->created_at->diffForHumans()}}</span>
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-history">
                                        <i class="bx bx-search" style="font-size: 40px;"></i>
                                        <p class="mb-0 mt-2">No login history found for this user.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($loginLogs->hasPages())
            <div class="user-history-table-wrap">
                {{$loginLogs->links('pagination::bootstrap-5')}}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
