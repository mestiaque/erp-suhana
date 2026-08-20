@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('My Profile')}}</title>
@endsection
@push('css')
<style type="text/css">
    .profile-page {
        color: #17233c;
    }

    .profile-row {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 18px;
        align-items: start;
    }

    .profile-card {
        max-width: 100%;
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 8px;
        box-shadow: 0 12px 32px rgba(21, 32, 56, 0.06);
        overflow: hidden;
    }

    .profile-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding: 22px 24px;
        background: linear-gradient(135deg, #f6fbff 0%, #eef7f2 100%);
        border-bottom: 1px solid #e8edf5;
    }

    .profile-hero h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #122033;
    }

    .profile-hero p {
        margin: 6px 0 0;
        color: #6b778c;
        font-size: 13px;
    }

    .profile-edit-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 34px;
        padding: 7px 12px;
        border-radius: 6px;
        color: #ffffff;
        background: #f2a900;
        font-weight: 700;
        font-size: 13px;
        box-shadow: 0 8px 18px rgba(242, 169, 0, 0.24);
    }

    .profile-edit-btn:hover {
        color: #ffffff;
        background: #d99500;
        text-decoration: none;
    }

    .profile-body {
        padding: 24px;
        padding-top: 5px !important;
        text-align: center;
    }

    .profile-photo {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid #ffffff;
        box-shadow: 0 10px 24px rgba(21, 32, 56, 0.14);
        background: #eef1f5;
    }

    .profile-name {
        margin: 14px 0 6px;
        font-size: 20px;
        font-weight: 700;
        color: #101828;
        word-break: break-word;
    }

    .profile-role {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #eaf3ff;
        color: #1458c8;
        font-size: 12px;
        font-weight: 700;
    }

    .profile-info-list {
        margin-top: 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        text-align: left;
    }

    .profile-info-item {
        padding: 12px 14px;
        border: 1px solid #e8edf5;
        border-radius: 8px;
        background: #fbfcfe;
    }

    .profile-info-item i {
        color: #18a575;
        font-size: 16px;
        margin-right: 6px;
    }

    .profile-info-item label {
        display: block;
        margin: 0 0 4px;
        color: #667085;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .profile-info-item p {
        margin: 0;
        color: #17233c;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.5;
        word-break: break-word;
    }

    .history-card {
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 8px;
        box-shadow: 0 12px 32px rgba(21, 32, 56, 0.06);
        overflow: hidden;
    }

    .history-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding: 22px 24px;
        background: linear-gradient(135deg, #f6fbff 0%, #eef7f2 100%);
        border-bottom: 1px solid #e8edf5;
    }

    .history-card-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #122033;
    }

    .history-card-header p {
        margin: 6px 0 0;
        color: #6b778c;
        font-size: 13px;
    }

    .history-table-wrap {
        padding: 0 18px 18px;
    }

    .history-pagination {
        padding: 0 18px 18px;
    }

    .history-filter {
        padding: 18px 18px 0;
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

    .history-table {
        margin-bottom: 0;
        font-size: 12px !important;
    }

    .history-table thead th {
        border: 0;
        vertical-align: middle;
        white-space: nowrap;
        padding-left: 10px;
        padding-right: 10px;
    }

    .history-table tbody td {
        padding: 12px 10px;
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
        max-width: 260px;
        color: #4a5568;
        line-height: 1.5;
        word-break: break-word;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
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
        padding: 34px 18px;
        text-align: center;
        color: #7b8794;
    }

    @media only screen and (max-width: 991px) {
        .profile-row {
            grid-template-columns: 1fr;
        }
    }

    @media only screen and (max-width: 575px) {
        .profile-hero,
        .history-card-header {
            flex-direction: column;
            padding: 18px;
        }

        .profile-edit-btn {
            width: 100%;
            justify-content: center;
        }

        .profile-body {
            padding: 20px 18px;
        }

        .history-table-wrap {
            padding-left: 12px;
            padding-right: 12px;
        }
    }
</style>
@endpush @section('contents')

<div class="flex-grow-1">
@include(adminTheme().'alerts')

<div class="profile-page">
    <div class="profile-row">
        <div class="profile-card">
            <div class="profile-hero">
                <div>
                    <h3>My Profile</h3>
                    <p>Essential account information.</p>
                </div>
                <a href="{{route('admin.editProfile')}}" class="profile-edit-btn"><i class="bx bx-edit"></i> Edit</a>
            </div>

            <div class="profile-body">
                <img src="{{$user->image()}}" class="profile-photo" alt="Profile photo" onerror="this.onerror=null;this.src='{{asset('medies/profile.png')}}'">
                <div class="profile-name">{{$user->name ?: 'N/A'}}</div>
                <span class="profile-role"><i class="bx bx-shield-quarter"></i> {{$user->permission->name ?? 'N/A'}}</span>

                <div class="profile-info-list">
                    <div class="profile-info-item">
                        <label><i class="bx bx-id-card"></i> Employee ID</label>
                        <p>{{$user->employee_id ?: 'N/A'}}</p>
                    </div>
                    <div class="profile-info-item">
                        <label><i class="bx bx-mobile"></i> Mobile</label>
                        <p>{{$user->mobile ?: 'N/A'}}</p>
                    </div>
                    <div class="profile-info-item">
                        <label><i class="bx bx-envelope"></i> Email</label>
                        <p>{{$user->email ?: 'N/A'}}</p>
                    </div>
                    <div class="profile-info-item">
                        <label><i class="bx bx-pen"></i> Signature</label>
                        @if($user->signature)
                        <p><img src="{{$user->signatureUrl()}}" alt="Signature" style="max-width:150px;max-height:60px;object-fit:contain;"></p>
                        @else
                        <p>N/A</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="history-card">
            <div class="history-card-header">
                <div>
                    <h3>Login History</h3>
                    <p>Your login records ({{$loginLogs->total()}} total).</p>
                </div>
            </div>

            <div class="history-table-wrap">
                <div class="table-responsive">
                    <table class="table history-table">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Login Time</th>
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-history">
                                        <i class="bx bx-search" style="font-size: 36px;"></i>
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
                {{$loginLogs->onEachSide(1)->links('pagination::bootstrap-5')}}
            </div>
            @endif
        </div>

        <div class="history-card" style="grid-column: span 2;">
            <div class="history-card-header">
                <div>
                    <h3>Data Change Log</h3>
                    <p>Every create, update, and delete you personally made ({{$changeLogs->total()}} total).</p>
                </div>
            </div>

            <form method="get" action="{{route('admin.myProfile')}}" class="history-filter">
                <div class="row">
                    <div class="form-group col-xl-3 col-lg-4 col-md-6">
                        <label for="log_event">Event</label>
                        <select id="log_event" name="log_event" class="form-control">
                            <option value="">All</option>
                            <option value="create" {{request('log_event')=='create'?'selected':''}}>Create</option>
                            <option value="update" {{request('log_event')=='update'?'selected':''}}>Update</option>
                            <option value="delete" {{request('log_event')=='delete'?'selected':''}}>Delete</option>
                        </select>
                    </div>
                    <div class="form-group col-xl-3 col-lg-4 col-md-6">
                        <label for="log_model">Feature</label>
                        <select id="log_model" name="log_model" class="form-control">
                            <option value="">All</option>
                            @foreach($changeLogModelOptions as $modelClass)
                            <option value="{{$modelClass}}" {{request('log_model')==$modelClass?'selected':''}}>{{friendlyModelName($modelClass)}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-xl-2 col-lg-4 col-md-6">
                        <label for="log_date_from">From</label>
                        <input type="date" id="log_date_from" name="log_date_from" class="form-control" value="{{request('log_date_from')}}">
                    </div>
                    <div class="form-group col-xl-2 col-lg-4 col-md-6">
                        <label for="log_date_to">To</label>
                        <input type="date" id="log_date_to" name="log_date_to" class="form-control" value="{{request('log_date_to')}}">
                    </div>
                    <div class="form-group col-xl-2 col-lg-4 col-md-6">
                        <div class="history-actions">
                            <button type="submit" class="history-btn"><i class="bx bx-search"></i> Filter</button>
                            <a href="{{route('admin.myProfile')}}" class="history-btn secondary"><i class="bx bx-reset"></i> Reset</a>
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
                                <th>URL</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($changeLogs as $log)
                            <tr>
                                <td><span class="event-pill {{$log->event}}">{{$log->event}}</span></td>
                                <td>
                                    <strong>{{friendlyModelName($log->loggable_type)}}</strong>
                                    <span class="muted-line">#{{$log->loggable_id}}</span>
                                </td>
                                <td>
                                    <div class="agent-text" style="max-width:220px;">{{$log->url ?? 'N/A'}}</div>
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
                                <td colspan="5">
                                    <div class="empty-history">
                                        <i class="bx bx-search" style="font-size: 36px;"></i>
                                        <p class="mb-0 mt-2">No data change log found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($changeLogs->hasPages())
            <div class="history-pagination">
                {{$changeLogs->onEachSide(1)->links('pagination::bootstrap-5')}}
            </div>
            @endif
        </div>
    </div>
</div>

</div>

@endsection

@push('js')
@endpush
