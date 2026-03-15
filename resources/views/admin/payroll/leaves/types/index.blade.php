@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Leave Types')}}</title>
@endsection

@section('contents')
    <div class="flex-grow-1">
        @include(adminTheme().'alerts')
        <div class="card mt-3">
            <div class="card-content collapse show">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leave Types</h5>
                    <button class="btn btn-sm btn-success d-print-none" data-toggle="modal" data-target="#addTypeModal"><i class="bx bx-plus"></i> Add Leave Type</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Qty (Days)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($types as $index => $type)
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td>{{$type->name}}</td>
                                    <td>{{$type->qty ?? 0}} Days</td>
                                    <td>
                                        @if($type->status == 'active')
                                            <span class="badge bg-success text-white">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editTypeModal{{$type->id}}"><i class="fa fa-edit"></i></button>
                                        <form action="{{route('admin.leaves.types.destroy', $type->id)}}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                @endforeach
                            </tbody>
                        </table>
                        {{$types->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($types as $type)
        <div class="modal fade" id="editTypeModal{{$type->id}}" tabindex="-1" role="dialog" aria-labelledby="editTypeModalLabel{{$type->id}}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTypeModalLabel{{$type->id}}">Edit Leave Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('admin.leaves.types.update', $type->id)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" value="{{$type->name}}" required>
                            </div>
                            <div class="form-group">
                                <label for="qty">Quantity (Days)</label>
                                <input type="number" class="form-control" name="qty" value="{{$type->qty ?? 0}}" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status">
                                    <option value="active" {{$type->status == 'active' ? 'selected' : ''}}>Active</option>
                                    <option value="inactive" {{$type->status == 'inactive' ? 'selected' : ''}}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Modal -->
    <div class="modal fade" id="addTypeModal" tabindex="-1" role="dialog" aria-labelledby="addTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTypeModalLabel">Add Leave Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.leaves.types.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="qty">Quantity (Days)</label>
                            <input type="number" class="form-control" name="qty" min="0" value="0" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
