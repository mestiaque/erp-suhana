@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Notices List') }}</title>
@endsection
@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Notices List</h5>
            <div>
                <a href="{{route('admin.notices.create')}}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Notice</a>
            </div>
        </div>
        <!-- Filter Section -->
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.notices.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control form-control-sm">
                        <option value="">Select Priority</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="">Select Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-12 text-right adjustments">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.notices.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-times"></i> Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body ">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Notice Date</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notices as $key => $notice)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $notice->title }}</td>
                            <td>{{ $notice->notice_date }}</td>
                            <td>{{ $notice->start_date }}</td>
                            <td>{{ $notice->end_date }}</td>
                            <td>
                                @if($notice->priority == 'low')
                                    <span class="badge badge-info">Low</span>
                                @elseif($notice->priority == 'medium')
                                    <span class="badge badge-warning">Medium</span>
                                @else
                                    <span class="badge badge-danger">High</span>
                                @endif
                            </td>
                            <td>
                                @if($notice->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $notice->creator->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.notices.edit', $notice->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.notices.destroy', $notice->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $notices->links() }}
        </div>
    </div>
</div>
@endsection
