@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Fabrics List') }}</title>
@endsection

@push('css')
<style type="text/css">
    .loadImg {
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
        text-align: center;
        padding-top: 100px;
    }
    .loadImg img {
        max-width: 100px;
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">

<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Fabrics List</h3>
        <div class="dropdown">
            @can('dev.all')
            <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#Fabrics" style="padding:5px 15px;">
                <i class="bx bx-plus"></i> Add Fabric
            </a>
            @endcan
            <a href="{{ route('admin.fabrics') }}" class="btn-custom yellow">
                <i class="bx bx-rotate-left"></i>
            </a>
        </div>
    </div>

    <div class="card-body">
        @include(adminTheme().'alerts')

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.fabrics') }}">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Fabric Name" class="form-control {{ $errors->has('search') ? 'error' : '' }}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Bulk Action Form -->
        <form method="GET" action="{{ route('admin.fabrics') }}">
            <div class="row mb-2">
                <div class="col-md-4">
                    @if(can('dev.all') || can('dev.all'))
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required>
                            <option value="">Select Action</option>
                            @can('dev.all')
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            @endcan
                            @can('dev.all')
                            <option value="5">Delete</option>
                            @endcan
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Sure?')">Action</button>
                    </div>
                    @endif
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <ul class="statuslist">
                        <li><a href="{{ route('admin.fabrics') }}">All ({{ $report->total }})</a></li>
                        <li><a href="{{ route('admin.fabrics', ['status' => 'active']) }}">Active ({{ $report->active }})</a></li>
                        <li><a href="{{ route('admin.fabrics', ['status' => 'inactive']) }}">Inactive ({{ $report->inactive }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="min-width:100px;">
                                @if(can('dev.all') || can('dev.all'))
                                <div class="checkbox mr-3">
                                    <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />
                                    <label class="cbx" for="checkall">
                                        <span>
                                            <svg width="12px" height="10px" viewBox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </svg>
                                        </span>
                                        All <span class="checkCounter"></span>
                                    </label>
                                </div>
                                @else All @endif
                            </th>
                            <th style="min-width:200px;">Name</th>
                            <th style="min-width:300px;">Description</th>
                            <th style="min-width:120px;">Date</th>
                            <th style="min-width:100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $d)
                        <tr>
                            <td style="position:relative;">
                                @if(can('dev.all') || can('dev.all'))
                                <div class="checkbox">
                                    <input class="inp-cbx" id="cbx_{{ $d->id }}" type="checkbox" name="checkid[]" value="{{ $d->id }}" style="display: none;" />
                                    <label class="cbx" for="cbx_{{ $d->id }}">
                                        <span>
                                            <svg width="12px" height="10px" viewBox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                                @endif
                                <span style="margin:0 5px;">{{ $data->currentPage() == 1 ? $i + 1 : $i + ($data->perPage() * ($data->currentPage() - 1)) + 1 }}</span>
                                @if($d->status == 'active')
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif
                            </td>
                            <td>{{ $d->name }}</td>
                            <td>{!! $d->description ?? '--' !!}</td>
                            <td>{{ $d->created_at->format('d.m.Y') }}</td>
                            <td class="text-center">
                                @if(can('dev.all') || can('dev.all'))
                                @can('dev.all')
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#Edit_{{ $d->id }}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endcan
                                @can('dev.all')
                                <a href="{{ route('admin.fabricsAction', ['delete', $d->id]) }}" class="btn-custom danger" onclick="return confirm('Are You Sure To Delete?')">
                                    <i class="bx bx-trash"></i>
                                </a>
                                @endcan
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $data->links('pagination') }}
            </div>
        </form>
    </div>
</div>
</div>

<!-- Add Modal -->
<div class="modal fade text-left" id="Fabrics" tabindex="-1" data-backdrop="static" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Fabric</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @can('dev.all')
                <form action="{{ route('admin.fabricsAction', ['add']) }}" method="POST">
                    @csrf
                    <div class="form-group mb-2">
                        <label>Name *</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'error' : '' }}" name="name" placeholder="Enter Fabric Name" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Description</label>
                        <textarea class="form-control" name="description" placeholder="Enter Fabric Description"></textarea>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add</button>
                    </div>
                </form>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($data as $d)
<div class="modal fade text-left" id="Edit_{{ $d->id }}" tabindex="-1" data-backdrop="static" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Fabric</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @can('dev.all')
                <form action="{{ route('admin.fabricsAction', ['update', $d->id]) }}" method="POST">
                    @csrf
                    <div class="form-group mb-2">
                        <label>Name *</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? 'error' : '' }}" name="name" value="{{ $d->name }}" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Description</label>
                        <textarea class="form-control" name="description">{{ $d->description }}</textarea>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update</button>
                    </div>
                </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
