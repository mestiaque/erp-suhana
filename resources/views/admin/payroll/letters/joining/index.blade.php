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
                    <h4 class="card-title">Joining Letters</h4>
                    <a href="{{ route('admin.letters.joining.create') }}" class="btn btn-primary btn-sm">
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
                                        <th>Joining Date</th>
                                        <th>Designation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($letters as $letter)
                                    <tr>
                                        <td>{{ $letter->user->name ?? 'N/A' }}</td>
                                        <td>{{ $letter->letter_date->format('d M Y') }}</td>
                                        <td>{{ $letter->joining_date->format('d M Y') }}</td>
                                        <td>{{ $letter->designation }}</td>
                                        <td>
                                            <a href="{{ route('admin.letters.joining.show', $letter->id) }}" class="btn btn-info btn-sm">
                                                <i class="feather icon-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.letters.joining.edit', $letter->id) }}" class="btn btn-warning btn-sm">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.letters.joining.print', $letter->id) }}" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="feather icon-printer"></i>
                                            </a>
                                            <form action="{{ route('admin.letters.joining.destroy', $letter->id) }}" method="POST" style="display:inline;">
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
                            {{ $letters->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
