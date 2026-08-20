@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Data Change Log Details')}}</title>
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
        padding: 14px 16px;
        background: linear-gradient(135deg, #f6fbff 0%, #eef7f2 100%);
        border-bottom: 1px solid #e8edf5;
    }

    .history-card-header h2 { margin: 0; font-size: 17px; font-weight: 700; }

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        padding: 14px 16px;
    }

    .meta-item {
        background: #f9fbfd;
        border: 1px solid #edf1f6;
        border-radius: 6px;
        padding: 8px 10px;
    }

    .meta-item span {
        display: block;
        color: #667085;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .meta-item strong {
        display: block;
        margin-top: 2px;
        color: #17233c;
        font-size: 13px;
        word-break: break-word;
    }

    .diff-table-wrap { padding: 0 16px 16px; }

    .diff-table { width: 100%; font-size: 12px; border-collapse: collapse; }

    .diff-table th {
        background: #f2f5fa;
        text-align: left;
        padding: 8px 10px;
        font-size: 11px;
        text-transform: uppercase;
        color: #667085;
        border-bottom: 1px solid #e8edf5;
    }

    .diff-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #edf1f6;
        vertical-align: top;
        word-break: break-word;
    }

    .diff-old { color: #d92d20; background: #fff5f5; }
    .diff-new { color: #13895f; background: #f2fdf8; }

    @media only screen and (max-width: 767px) {
        .meta-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">

    @include(adminTheme().'alerts')

    @php
        $data = is_array($log->data) ? $log->data : (json_decode($log->data, true) ?: []);
        $old = $data['old'] ?? null;
        $new = $data['new'] ?? null;
        $keys = collect(array_keys((array) $old))->merge(array_keys((array) $new))->unique()
            ->sortBy(fn ($key) => friendlyFieldName($key));

        $formatValue = function ($value) {
            if ($value === null) {
                return null;
            }
            if (is_bool($value)) {
                return $value ? 'Yes' : 'No';
            }
            if (is_array($value)) {
                return json_encode($value);
            }

            return $value;
        };
    @endphp

    <div class="history-card mb-30">
        <div class="history-card-header d-flex justify-content-between align-items-center">
            <h2>{{friendlyModelName($log->loggable_type)}} — {{ucfirst($log->event)}} (#{{$log->loggable_id}})</h2>
            <a href="{{route('admin.dataChangeLog')}}" class="history-btn secondary" style="display:inline-flex;padding:7px 12px;border-radius:5px;background:#eef1f5;color:#17233c;font-size:13px;font-weight:700;">
                <i class="bx bx-arrow-back"></i> Back
            </a>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <span>User</span>
                <strong>{{$log->user->name ?? 'System'}}</strong>
            </div>
            <div class="meta-item">
                <span>IP Address</span>
                <strong>{{$log->ip_address ?? 'N/A'}}</strong>
            </div>
            <div class="meta-item">
                <span>Time</span>
                <strong>{{$log->created_at->format('d M Y, h:i:s A')}}</strong>
            </div>
            <div class="meta-item">
                <span>Method / URL</span>
                <strong>{{$log->method}} — {{$log->url ?? 'N/A'}}</strong>
            </div>
            <div class="meta-item" style="grid-column: span 4;">
                <span>User Agent</span>
                <strong>{{$log->user_agent ?? 'N/A'}}</strong>
            </div>
        </div>

        <div class="diff-table-wrap">
            @if($keys->isEmpty())
            <p class="text-muted">No field-level data recorded for this event.</p>
            @else
            <table class="diff-table">
                <thead>
                    <tr>
                        <th style="width:20%;">Field</th>
                        <th style="width:40%;">Before</th>
                        <th style="width:40%;">After</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($keys as $key)
                    @php
                        $oldVal = is_array($old) ? ($old[$key] ?? null) : null;
                        $newVal = is_array($new) ? ($new[$key] ?? null) : null;
                        $oldDisplay = $formatValue($oldVal);
                        $newDisplay = $formatValue($newVal);
                    @endphp
                    <tr>
                        <td><strong>{{friendlyFieldName($key)}}</strong></td>
                        <td class="diff-old">{{$oldDisplay === null ? '—' : $oldDisplay}}</td>
                        <td class="diff-new">{{$newDisplay === null ? '—' : $newDisplay}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection
