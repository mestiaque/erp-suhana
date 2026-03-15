@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Holiday List') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Holiday List</h5>
            <div>
                <a href="{{route('admin.holiday.create')}}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Holiday</a>
            </div>
        </div>
        <div class="card-body ">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $key => $holiday)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $holiday->title }}</td>
                            <td>
                                @if($holiday->type == 'National')
                                    <span class="badge badge-danger">{{ $holiday->type }}</span>
                                @elseif($holiday->type == 'Festival')
                                    <span class="badge badge-warning">{{ $holiday->type }}</span>
                                @elseif($holiday->type == 'General')
                                    <span class="badge badge-info">{{ $holiday->type }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $holiday->type }}</span>
                                @endif
                            </td>
                            <td>{{ date('d M Y', strtotime($holiday->from_date)) }}</td>
                            <td>{{ date('d M Y', strtotime($holiday->to_date)) }}</td>
                            <td>{{ $holiday->days }}</td>
                            <td>
                                @if($holiday->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $holiday->remarks ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.holiday.edit', $holiday->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                <a href="{{ route('admin.holiday.destroy', $holiday->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this holiday?')"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No holidays found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
