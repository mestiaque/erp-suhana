@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Working Hours') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h5 class="mb-0">Working Hours / Grass Time</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.working-hours.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Planned</th>
                    <th>Actual</th>
                    <th>Overtime</th>
                    <th>Grass Hours</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hours as $hour)
                <tr>
                    <td>{{ $hour->user->name ?? 'N/A' }}</td>
                    <td>{{ $hour->date }}</td>
                    <td>{{ $hour->planned_hours }}</td>
                    <td>{{ $hour->actual_hours }}</td>
                    <td>{{ $hour->overtime_hours }}</td>
                    <td>{{ $hour->grass_hours }}</td>
                    <td>
                        @if($hour->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-success">Approved</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
