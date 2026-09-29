@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Mail Notifications')}}</title>
@endsection
@push('css')
<style type="text/css">
    .mn-intro{
        display:flex; gap:12px; align-items:flex-start;
        background:#eef4ff; border:1px solid #dbe6ff; border-radius:10px;
        padding:14px 18px; margin-bottom:28px;
    }
    .mn-intro i{color:#3b6fe0; font-size:18px; margin-top:2px;}
    .mn-intro p{margin:0; color:#425372; font-size:13px; line-height:1.6;}

    .mn-group{margin-bottom:8px;}
    .mn-group-title{
        display:flex; align-items:center; gap:10px;
        font-size:16px; font-weight:700; color:#2b2f38;
        margin:30px 0 16px;
    }
    .mn-group:first-child .mn-group-title{margin-top:0;}
    .mn-group-title i{
        width:32px; height:32px; border-radius:8px;
        display:flex; align-items:center; justify-content:center;
        background:#eef4ff; color:#3b6fe0; font-size:14px;
    }
    .mn-group-title .mn-group-count{
        font-size:11px; font-weight:600; color:#8a92a3;
        background:#f2f3f6; border-radius:20px; padding:3px 10px; margin-left:2px;
    }

    .mn-card{
        background:#fff; border:1px solid #edf0f5; border-radius:12px;
        padding:20px 24px; margin-bottom:16px;
        box-shadow:0 1px 2px rgba(20,20,43,.03);
        transition:box-shadow .15s ease, border-color .15s ease;
    }
    .mn-card:hover{ box-shadow:0 6px 18px rgba(20,20,43,.06); border-color:#e3e8f2; }
    .mn-card.is-off{ background:#fbfbfc; }

    .mn-card-head{
        display:flex; justify-content:space-between; align-items:flex-start;
        gap:16px; flex-wrap:wrap; padding-bottom:16px; margin-bottom:16px;
        border-bottom:1px solid #f1f2f6;
    }
    .mn-card-title{font-weight:600; font-size:14.5px; color:#20232b;}
    .mn-card-key{
        display:inline-block; margin-top:6px; font-family:monospace; font-size:11px;
        color:#8a8f98; background:#f4f5f8; padding:2px 9px; border-radius:5px;
    }

    /* Toggle switch */
    .mn-switch-wrap{display:flex; align-items:center; gap:10px;}
    .mn-switch{position:relative; display:inline-block; width:44px; height:24px; flex-shrink:0;}
    .mn-switch input{opacity:0; width:0; height:0;}
    .mn-switch-slider{
        position:absolute; cursor:pointer; inset:0; background:#d7dbe3;
        transition:.2s; border-radius:24px;
    }
    .mn-switch-slider:before{
        content:""; position:absolute; height:18px; width:18px; left:3px; bottom:3px;
        background:#fff; transition:.2s; border-radius:50%; box-shadow:0 1px 3px rgba(0,0,0,.25);
    }
    .mn-switch input:checked + .mn-switch-slider{background:#2ecc71;}
    .mn-switch input:checked + .mn-switch-slider:before{transform:translateX(20px);}
    .mn-switch-status{font-size:12px; font-weight:700; letter-spacing:.3px; min-width:32px;}
    .mn-switch-status.on{color:#1fa971;}
    .mn-switch-status.off{color:#e1000a;}

    .mn-recipients label{
        font-size:12.5px; font-weight:600; color:#555; margin-bottom:6px; display:block;
    }
    .mn-recipients label small{font-weight:400; color:#9aa0a8;}
    .mn-save-row{margin-top:16px; display:flex; justify-content:flex-end;}
    .mn-save-btn{padding:7px 26px; border-radius:6px; font-size:13px;}

    /* select2 -> match form-control look */
    .select2-container--default .select2-selection--multiple{
        border:1px solid #dfe3ea !important; border-radius:7px !important;
        min-height:42px !important; padding:3px 6px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple{
        border-color:#3b6fe0 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice{
        background:#eef4ff !important; border:1px solid #d7e2fb !important;
        color:#2f5bea !important; border-radius:5px !important; padding:2px 8px !important;
        font-size:12.5px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        color:#7791e6 !important; margin-right:4px !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field{
        margin-top:6px !important;
    }
</style>
@endpush
@section('contents')

<div class="flex-grow-1">
<div class="breadcrumb-area">
    <h1>Setting</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Dashboard </li>
        <li class="item">Mail Notifications</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="card mb-30">
    <div class="card-header">
        <h3>Mail Notifications</h3>
    </div>
    <div class="card-body">

        <div class="mn-intro">
            <i class='bx bx-info-circle'></i>
            <p>
                যে অ্যাকশনে <strong>Mail Off</strong> রাখবেন, সেই অ্যাকশনের মেইল একদমই যাবে না।
                <strong>Recipients</strong> ফাঁকা রাখলে ওই অ্যাকশনের ডিফল্ট নিয়মেই (approver/permission অনুযায়ী) মেইল যাবে —
                নির্দিষ্ট কাউকে বেছে দিলে শুধু তাদেরকেই যাবে।
            </p>
        </div>

        @foreach($actions as $groupTitle => $groupActions)
            <div class="mn-group">
                <div class="mn-group-title">
                    <i class='bx {{ $groupTitle === "Approval" ? "bx-check-shield" : "bx-bar-chart-alt-2" }}'></i>
                    {{ $groupTitle }}
                    <span class="mn-group-count">{{ $groupActions->count() }}</span>
                </div>

                @foreach($groupActions as $action)
                    @php $uid = $loop->parent->index.'_'.$loop->index; @endphp
                    <div class="mn-card {{ $action['is_enabled'] ? '' : 'is-off' }}" id="mn-card-{{ $uid }}">
                        <form method="post" action="{{ route('admin.setting.mailNotifications.update', $action['action_key']) }}">
                            @csrf
                            <div class="mn-card-head">
                                <div>
                                    <div class="mn-card-title">{{ $action['title'] }}</div>
                                    <span class="mn-card-key">{{ $action['action_key'] }}</span>
                                </div>

                                <div class="mn-switch-wrap">
                                    <span class="mn-switch-status {{ $action['is_enabled'] ? 'on' : 'off' }}" data-status-for="{{ $uid }}">
                                        {{ $action['is_enabled'] ? 'ON' : 'OFF' }}
                                    </span>
                                    <label class="mn-switch">
                                        <input type="checkbox" name="is_enabled" value="1" data-toggle-for="{{ $uid }}" {{ $action['is_enabled'] ? 'checked' : '' }} />
                                        <span class="mn-switch-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="mn-recipients">
                                <label>Recipients <small>(ফাঁকা = ডিফল্ট recipients)</small></label>
                                <select name="recipient_user_ids[]" class="form-control select2-multi" multiple="multiple" style="width:100%">
                                    @foreach($users->groupBy(fn($u) => $u->permission->name ?? 'No Role') as $roleName => $roleUsers)
                                        <optgroup label="{{ $roleName }}">
                                            @foreach($roleUsers as $user)
                                                <option value="{{ $user->id }}" {{ in_array($user->id, $action['recipient_user_ids']) ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mn-save-row">
                                <button type="submit" class="btn btn-primary mn-save-btn">
                                    <i class='bx bx-save'></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        @endforeach

    </div>
</div>
</div>
@endsection
@push('js')
<script>
    $(function () {
        $('.select2-multi').select2({
            placeholder: 'Default recipients',
            allowClear: true,
            width: '100%'
        });

        $('[data-toggle-for]').on('change', function () {
            var key = $(this).data('toggle-for');
            var isOn = $(this).is(':checked');
            var $status = $('[data-status-for="' + key + '"]');
            $status.text(isOn ? 'ON' : 'OFF').toggleClass('on', isOn).toggleClass('off', !isOn);
            $('#mn-card-' + key).toggleClass('is-off', !isOn);
        });
    });
</script>
@endpush
