@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Requisition List')}}</title>
@endsection

@push('css')
<style type="text/css"></style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Requisition List</h3>
             <div class="dropdown">
                {{-- @isset(json_decode(Auth::user()->permission->permission, true)['requision']['add']) --}}
                 <a href="{{route('admin.purchasesRequisitionsAction','create')}}" class="btn-custom primary" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add Requisition
                 </a>
                {{-- @endisset --}}
                 <a href="{{route('admin.purchasesRequisitions')}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{route('admin.purchasesRequisitions')}}">
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

            <!-- Bulk Actions -->
            <form action="{{route('admin.purchasesRequisitions')}}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group mb-1">
                            <select class="form-control form-control-sm rounded-0" name="action" required>
                                <option value="">Select Action</option>
                                <option value="1">Pending</option>
                                <option value="2">Approved</option>
                                <option value="3">Rejected</option>
                                <option value="4">Delete</option>
                            </select>
                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Sure?')">Apply</button>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <ul class="statuslist p-0">
                            <li><a href="{{route('admin.purchasesRequisitions')}}">All ({{$totals->total}})</a></li>
                            <li><a href="{{route('admin.purchasesRequisitions',['status'=>'pending'])}}">Pending ({{$totals->pending}})</a></li>
                            <li><a href="{{route('admin.purchasesRequisitions',['status'=>'approved'])}}">Approved ({{$totals->approved}})</a></li>
                            <li><a href="{{route('admin.purchasesRequisitions',['status'=>'rejected'])}}">Rejected ({{$totals->rejected}})</a></li>
                            <li><a href="{{route('admin.purchasesRequisitions',['status'=>'trash'])}}">Trash ({{$totals->trash}})</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Requisition Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="min-width: 100px;">SL</th>
                                <th style="min-width: 150px;">Requisition No</th>
                                <th style="min-width: 150px;">Department</th>
                                <th style="min-width: 150px;">Designation</th>
                                <th style="min-width: 200px;">Name/ID</th>
                                <th style="min-width: 200px;">Requested By</th>
                                <th style="min-width: 150px;">Items</th>
                                <th style="min-width: 100px;">Status</th>
                                <th style="min-width: 100px;">Date</th>
                                <th style="min-width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requisitions as $i=>$req)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td><a href="{{route('admin.purchasesRequisitionsAction',['view',$req->id])}}" target="_blank">{{$req->requisition_no}}</a></td>
                                <td>{{$req->department?->name ?? '--'}}</td>
                                <td>{{$req->designation?->name ?? '--'}}</td>
                                <td>{{$req->name}} {{$req->employe_number?'- '.$req->employe_number:''}}</td>
                                <td>{{$req->user?->name}}</td>
                                <td>{{$req->items()->count()}} Items</td>
                                <td>
                                    @if($req->status=='temp')
                                        <span class="badge badge-secondary">Temp</span>
                                    @elseif($req->status=='pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($req->status=='approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($req->status=='rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{$req->created_at->format('d.m.Y')}}</td>
                                <td>
                                    <a href="{{route('admin.purchasesRequisitionsAction',['edit',$req->id])}}" class="btn-custom"><i class="bx bx-edit"></i></a>
                                    <a href="{{route('admin.purchasesRequisitionsAction',['delete',$req->id])}}" onclick="return confirm('Are You Want To Delete?')" class="btn-custom danger"><i class="bx bx-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$requisitions->links('pagination')}}
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
@endpush

