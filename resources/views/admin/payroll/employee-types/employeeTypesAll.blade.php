@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Classification List') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    @include(adminTheme().'alerts')

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Classification List</h3>
            <div class="d-flex align-items-center">
                <a href="javascript:void(0)" class="btn-custom primary mr-2" data-toggle="modal" data-target="#AddClassificationModal">
                    <i class="bx bx-plus"></i> Classification
                </a>
                <a href="{{ route('admin.employeeType') }}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 80px;">#</th>
                            <th style="min-width: 220px;">Name</th>
                            <th style="min-width: 260px;">Description</th>
                            <th style="min-width: 120px;">Status</th>
                            <th style="min-width: 140px;">Date</th>
                            <th style="min-width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeeTypes as $i => $type)
                            <tr>
                                <td>
                                    {{ $employeeTypes->currentPage() == 1 ? $i + 1 : $i + ($employeeTypes->perPage() * ($employeeTypes->currentPage() - 1)) + 1 }}
                                </td>
                                <td>{{ $type->name }}</td>
                                <td>{!! $type->description !!}</td>
                                <td>
                                    @if($type->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $type->created_at?->format('d-m-Y') }}</td>
                                <td>
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#EditClassificationModal_{{ $type->id }}" class="btn-custom success">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No classification found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $employeeTypes->links('pagination') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="AddClassificationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.employeeTypeAction', 'create') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add Classification</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name*</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'error' : '' }}" placeholder="Enter classification name" required>
                        @if ($errors->has('name'))
                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control {{ $errors->has('description') ? 'error' : '' }}" placeholder="Enter description"></textarea>
                        @if ($errors->has('description'))
                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($employeeTypes as $type)
<div class="modal fade text-left" id="EditClassificationModal_{{ $type->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.employeeTypeAction', ['update', $type->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Edit Classification</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name*</label>
                        <input type="text" name="name" value="{{ $type->name }}" class="form-control" placeholder="Enter classification name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" placeholder="Enter description">{{ $type->description }}</textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label>Status</label>
                        <div class="checkbox">
                            <input class="inp-cbx" id="status_{{ $type->id }}" type="checkbox" name="status" style="display: none;" {{ $type->status == 'active' ? 'checked' : '' }} />
                            <label class="cbx" for="status_{{ $type->id }}">
                                <span>
                                    <svg width="12px" height="10px" viewbox="0 0 12 10">
                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                    </svg>
                                </span>
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
