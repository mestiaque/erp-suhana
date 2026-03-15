@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Appointment Letters') }}</title>
@endsection

@section('contents')
<section class="flex-grow-1">
    @include(adminTheme().'alerts')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Appointment Letters</h4>
                    <a href="{{ route('admin.letters.appointment.create') }}" class="btn btn-primary btn-sm">
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
                                        <th>Letter Date</th>
                                        <th>Position</th>
                                        <th>Salary</th>
                                        <th>Joining Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($letters as $letter)
                                    <tr>
                                        <td>{{ $letter->user->name ?? 'N/A' }}</td>
                                        <td>{{ $letter->letter_date->format('d M Y') }}</td>
                                        <td>{{ $letter->position }}</td>
                                        <td>{{ number_format($letter->salary, 2) }}</td>
                                        <td>{{ $letter->joining_date->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.letters.appointment.show', $letter->id) }}" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.letters.appointment.edit', $letter->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.letters.appointment.print', $letter->id) }}" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="fa fa-print"></i>
                                            </a>
                                            <form action="{{ route('admin.letters.appointment.destroy', $letter->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $letters->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
