@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Production Planning List') }}</title>
@endsection

@push('css')
<style type="text/css">

</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Planning List</h3>
             <div class="dropdown">
                @can('samples.add')
                 <a href="{{ route('admin.productionPlanningAction','create') }}" class="btn-custom primary" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add Planning
                 </a>
                 @endcan
                 <a href="{{ route('admin.samples') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.productionPlanning') }}">
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Buyer, Style" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Status Filter -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <ul class="statuslist p-0">
                        <li><a href="{{ route('admin.samples') }}">All ({{ $totals->total }})</a></li>
                        <li><a href="{{ route('admin.samples',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li><a href="{{ route('admin.samples',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                        <li><a href="{{ route('admin.samples',['status'=>'completed']) }}">Completed ({{ $totals->completed }})</a></li>
                        <li><a href="{{ route('admin.samples',['status'=>'cancel']) }}">Cancelled ({{ $totals->cancel }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- Samples Table -->
            <div class="table-responsive">
                <table class="table table-striped table-borderd">
                    <thead>
                        <tr>
                            <th style="width: 80px">SL</th>
                            <th style="width: ">Buyer</th>
                            <th style="width: 150px">Style</th>
                            <th style="width: 150px">Items</th>
                            <th style="width: 150px">Status</th>
                            <th style="width: 150px">Date</th>
                            <th style="width: 150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
