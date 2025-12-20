@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Products List') }}</title>
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
        <h3>Products List</h3>
        <div class="dropdown">
            @can('dev.all')
            <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#ProductModal" style="padding:5px 15px;">
                <i class="bx bx-plus"></i> Add Product
            </a>
            @endcan
            <a href="{{ route('admin.products') }}" class="btn-custom yellow">
                <i class="bx bx-rotate-left"></i>
            </a>
        </div>
    </div>

    <div class="card-body">
        @include(adminTheme().'alerts')

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.products') }}">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Product Name / SKU" class="form-control {{ $errors->has('search') ? 'error' : '' }}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Bulk Action Form -->
        <form method="GET" action="{{ route('admin.products') }}">
            <div class="row mb-2">
                <div class="col-md-4">
                    @if(can('dev.all'))
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required>
                            <option value="">Select Action</option>
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            <option value="5">Delete</option>
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Sure?')">Action</button>
                    </div>
                    @endif
                </div>
                <div class="col-md-8">
                    {{-- <ul class="statuslist">
                        <li><a href="{{ route('admin.products') }}">All ({{ $report->total }})</a></li>
                        <li><a href="{{ route('admin.products', ['status' => 'active']) }}">Active ({{ $report->active }})</a></li>
                        <li><a href="{{ route('admin.products', ['status' => 'inactive']) }}">Inactive ({{ $report->inactive }})</a></li>
                    </ul> --}}
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="min-width:50px;">
                                @if(can('dev.all'))
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
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Style</th>
                            <th>Size</th>
                            <th>Fabric</th>
                            <th>Color</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th style="width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $i => $p)
                        <tr>
                            <td>
                                @if(can('dev.all'))
                                <div class="checkbox">
                                    <input class="inp-cbx" id="cbx_{{ $p->id }}" type="checkbox" name="checkid[]" value="{{ $p->id }}" style="display: none;" />
                                    <label class="cbx" for="cbx_{{ $p->id }}">
                                        <span>
                                            <svg width="12px" height="10px" viewBox="0 0 12 10">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                                @endif
                                <span>{{ $products->currentPage() == 1 ? $i + 1 : $i + ($products->perPage() * ($products->currentPage() - 1)) + 1 }}</span>
                                @if($p->status == 1)
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif
                            </td>
                            <td>{{ $p->sku }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->style->name ?? '--' }}</td>
                            <td>{{ $p->size->name ?? '--' }}</td>
                            <td>{{ $p->fabric->name ?? '--' }}</td>
                            <td>{{ $p->color->name ?? '--' }}</td>
                            <td>{{ number_format($p->price, 2) }}</td>
                            <td>{{ $p->created_at->format('d.m.Y') }}</td>
                            <td class="text-center">
                                @can('dev.all')
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#Edit_{{ $p->id }}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{ route('admin.productsAction', ['delete', $p->id]) }}" class="btn-custom danger" onclick="return confirm('Are You Sure To Delete?')">
                                    <i class="bx bx-trash"></i>
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $products->links('pagination') }}
            </div>
        </form>
    </div>
</div>
</div>

<!-- Add Product Modal -->
<div class="modal fade text-left" id="ProductModal" tabindex="-1" data-backdrop="static" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Product</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @can('dev.all')
                <form action="{{ route('admin.productsAction', ['add']) }}" method="POST">
                    @csrf
                    <div class="form-group mb-2">
                        <label>SKU *</label>
                        <input type="text" class="form-control" name="sku" placeholder="Enter SKU" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Name *</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Product Name" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Style</label>
                        <select class="form-control" name="style_id">
                            <option value="">Select Style</option>
                            @foreach($styles as $style)
                            <option value="{{ $style->id }}">{{ $style->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Size</label>
                        <select class="form-control" name="size_id">
                            <option value="">Select Size</option>
                            @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Fabric</label>
                        <select class="form-control" name="fabric_id">
                            <option value="">Select Fabric</option>
                            @foreach($fabrics as $fabric)
                            <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Color</label>
                        <select class="form-control" name="color_id">
                            <option value="">Select Color</option>
                            @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" placeholder="Enter Price">
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

<!-- Edit Product Modals -->
@foreach($products as $p)
<div class="modal fade text-left" id="Edit_{{ $p->id }}" tabindex="-1" data-backdrop="static" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Product</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @can('dev.all')
                <form action="{{ route('admin.productsAction', ['update', $p->id]) }}" method="POST">
                    @csrf
                    <div class="form-group mb-2">
                        <label>SKU *</label>
                        <input type="text" class="form-control" name="sku" value="{{ $p->sku }}" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Name *</label>
                        <input type="text" class="form-control" name="name" value="{{ $p->name }}" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Style</label>
                        <select class="form-control" name="style_id">
                            <option value="">Select Style</option>
                            @foreach($styles as $style)
                            <option value="{{ $style->id }}" {{ $p->style_id==$style->id?'selected':'' }}>{{ $style->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Size</label>
                        <select class="form-control" name="size_id">
                            <option value="">Select Size</option>
                            @foreach($sizes as $size)
                            <option value="{{ $size->id }}" {{ $p->size_id==$size->id?'selected':'' }}>{{ $size->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Fabric</label>
                        <select class="form-control" name="fabric_id">
                            <option value="">Select Fabric</option>
                            @foreach($fabrics as $fabric)
                            <option value="{{ $fabric->id }}" {{ $p->fabric_id==$fabric->id?'selected':'' }}>{{ $fabric->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Color</label>
                        <select class="form-control" name="color_id">
                            <option value="">Select Color</option>
                            @foreach($colors as $color)
                            <option value="{{ $color->id }}" {{ $p->color_id==$color->id?'selected':'' }}>{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" value="{{ $p->price }}">
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
