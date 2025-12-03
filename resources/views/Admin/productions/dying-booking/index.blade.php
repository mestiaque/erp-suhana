@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('knitting List') }}</title>
@endsection

@push('css')
<style type="text/css">

</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Dying Booking List</h3>
             <div class="dropdown">
                 <a href="{{ route('admin.dyingBooking') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.dyingBooking') }}">
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

                </div>
            </div>

            <!-- Samples Table -->
            <div class="table-responsive">
                <table class="table table-striped table-borderd">
                    <thead>
                        <tr>
                            <th style="width: 80px">SL</th>
                            <th style="width: 150px">Order No</th>
                            <th style="width: 150px">Style No</th>
                            <th style="width: 150px">Merchant</th>
                            <th style="min-width:200px">Buyer</th>
                            <th style="width: 150px">Total Qty</th>
                            <th style="width: 150px">Composition</th>
                            <th style="width: 150px">GSM</th>
                            <th style="width: 150px">Size</th>
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
