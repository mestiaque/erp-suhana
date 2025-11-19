@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Purchases Stock')}}</title>
@endsection

@push('css')
<style type="text/css"></style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Purchases Stock</h3>
             <div class="dropdown">
                 <a href="{{route('admin.purchasesReports')}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{route('admin.purchasesReports')}}">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control" />
                            <input type="date" name="endDate" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Requisition, Department" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>

                <!-- Requisition Table -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="min-width: 100px;">SL</th>
                                <th style="min-width: 150px;">Item name</th>
                                <th style="min-width: 150px;">Stock</th>
                                <th style="min-width: 200px;">Branchs</th>
                                <th style="min-width: 150px;">Last Purchases</th>
                                <th style="min-width: 100px;">Status</th>
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

