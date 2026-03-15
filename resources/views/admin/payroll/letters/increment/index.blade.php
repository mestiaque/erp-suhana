@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Leaves List') }}</title>
@endsection

@section('contents')
<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Salary Increments</h4>
                    <a href="{{ route('admin.letters.increment.create') }}" class="btn btn-primary btn-sm">
                        <i class="feather icon-plus"></i> Create New
                    </a>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Increment Date</th>
                                        <th>Previous Salary</th>
                                        <th>Increment Amount</th>
                                        <th>New Salary</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($increments as $increment)
                                    <tr>
                                        <td>{{ $increment->user->name ?? 'N/A' }}</td>
                                        <td>{{ $increment->increment_date->format('d M Y') }}</td>
                                        <td>{{ number_format($increment->previous_salary, 2) }}</td>
                                        <td>{{ number_format($increment->increment_amount, 2) }} ({{ $increment->increment_percentage }}%)</td>
                                        <td>{{ number_format($increment->new_salary, 2) }}</td>
                                        <td>
                                            <a href="{{ route('admin.letters.increment.show', $increment->id) }}" class="btn btn-info btn-sm">
                                                <i class="feather icon-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.letters.increment.edit', $increment->id) }}" class="btn btn-warning btn-sm">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.letters.increment.print', $increment->id) }}" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="feather icon-printer"></i>
                                            </a>
                                            <form action="{{ route('admin.letters.increment.destroy', $increment->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                    <i class="feather icon-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $increments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
