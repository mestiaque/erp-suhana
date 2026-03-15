@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Leave Summary') }}</title>
@endsection

@push('css')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .leave-balanced {
        font-weight: bold;
    }
    .leave-remaining-positive {
        color: green;
    }
    .leave-remaining-negative {
        color: red;
    }
</style>
@endpush

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Leave Summary</h5>
            <div>
                <form method="GET" action="{{ route('admin.leaves.summary') }}" class="form-inline">
                    <div class="form-group">
                        <label for="year" class="mr-2">Year: </label>
                        <select name="year" id="year" class="form-control form-control-sm" onchange="this.form.submit()">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>
        </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="2" class="text-center align-middle">SL</th>
                        <th rowspan="2" class="text-center align-middle">Employee</th>
                        <th rowspan="2" class="text-center align-middle">Department</th>
                        <th rowspan="2" class="text-center align-middle">Designation</th>
                        <th colspan="{{ count($leaveTypes) + 1 }}" class="text-center">
                            Leave Details (Days)
                        </th>
                        <th rowspan="2" class="text-center align-middle">Total Taken</th>
                        <th rowspan="2" class="text-center align-middle">Available</th>
                    </tr>
                    <tr>
                        @foreach($leaveTypes as $type)
                            <th class="text-center">{{ $type->name }}</th>
                        @endforeach
                        <th class="text-center">Total Allowed</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sl = 1; $grandTotalTaken = 0; $grandTotalAllowed = 0; @endphp
                    @foreach($summaryData as $data)
                        <tr>
                            <td class="text-center">{{ $sl++ }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($data['user'] && $data['user']->photo)
                                        <img src="{{ asset('uploads/user_photo/' . $data['user']->photo) }}" alt="{{ $data['user']->name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover; margin-right: 10px;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 35px; height: 35px; background-color: {{ random_color($data['user']->id ?? 0) }}; margin-right: 10px;">
                                            {{ strtoupper(substr($data['user']->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $data['user']->name }}</strong><br>
                                        <small class="text-muted">{{ $data['user']->employee_id ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ optional($data['user']->department)->name ?? '-' }}</td>
                            <td>{{ optional($data['user']->designation)->name ?? '-' }}</td>

                            @php $totalAllowed = 0; @endphp
                            @foreach($data['leaves'] as $leave)
                                <td class="text-center">
                                    @if($leave['allowed'] > 0)
                                        <span class="text-danger">{{ $leave['taken'] }}</span>
                                        <span class="text-muted">/</span>
                                        <span class="{{ $leave['remaining'] >= 0 ? 'leave-remaining-positiveX' : 'leave-remaining-negative' }}">
                                            {{ $leave['remaining'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                @php $totalAllowed += $leave['allowed']; @endphp
                            @endforeach

                            <td class="text-center font-weight-bold">{{ $totalAllowed }}</td>
                            <td class="text-center font-weight-bold text-danger">{{ $data['total_taken'] }}</td>
                            <td class="text-center font-weight-bold {{ $totalAllowed - $data['total_taken'] >= 0 ? 'leave-remaining-positive' : 'leave-remaining-negative' }}">
                                {{ $totalAllowed - $data['total_taken'] }}
                            </td>
                        </tr>
                        @php $grandTotalTaken += $data['total_taken']; $grandTotalAllowed += $totalAllowed; @endphp
                    @endforeach

                    @if(count($summaryData) == 0)
                        <tr>
                            <td colspan="{{ 6 + count($leaveTypes) }}" class="text-center">
                                No employees found
                            </td>
                        </tr>
                    @else

                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
