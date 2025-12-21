@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Customer Users')}}</title>
@endsection
@push('css')

@endpush
@section('contents')


@include(adminTheme().'alerts')
<div class="flex-grow-1">
<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Mer List</h3>
         <div class="dropdown">

         </div>
    </div>
    <div class="card-body">

        <form action="{{route('admin.buyers')}}">
            <div class="row">
                <div class="col-md-8">
                     <form action="{{route('admin.buyers')}}">
                        @if(request()->view == 'deleted')
                            <input type="hidden" name="view" value="deleted">
                        @endif
                        <div class="row">
                            <div class="col-md-5 mb-1">
                                <div class="input-group">
                                    <input type="date" name="startDate" value="{{request()->startDate?:''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                                    <input type="date" value="{{request()->endDate?:''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                                </div>
                            </div>

                            <div class="col-md-5 mb-1">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{request()->search?:''}}" placeholder="User Name, Email, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
                                    <button type="submit" class="btn btn-success btn-sm rounded-0 mr-1">Search</button>
                                    <a href="{{route('admin.buyers', ['view' => 'deleted'])}}" class="btn-custom yellow" style="height: auto;">
                                        <i class="bx bx-rotate-left"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <ul class="statuslist">
                        <li><a href="{{route('admin.buyers')}}" class="{{request()->status?'':'active'}}" >All ({{$total->total}})</a></li>
                        <li><a href="{{route('admin.buyers',['status'=>'active'])}}" class="{{request()->status=='active'?'active':''}}" >Active ({{$total->active}})</a></li>
                        <li><a href="{{route('admin.buyers',['status'=>'inactive'])}}" class="{{request()->status=='inactive'?'active':''}}" >Inactive ({{$total->inactive}})</a></li>
                        <li><a href="{{route('admin.buyers',['view'=>'deleted'])}}" target="_blank" class="text-danger" >Deleted ({{$total->deleted}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0; position: relative;">
                                All
                            </th>
                            <th style="min-width: 70px; width: 70px;">Image</th>
                            <th style="min-width: 200px; width: 200px;">Name</th>
                            <th style="min-width: 100px; width: 100px;">ID Number</th>
                            <th style="min-width: 150px;">Mobile / Email</th>
                            <th style="min-width: 100px;">Designation</th>
                            <th style="min-width: 90px;">Join Date</th>
                            <th style="min-width: 90px;">Deleted Time</th>
                            <th style="min-width: 90px;">Deleted By</th>
                            <th style="min-width: 80px; width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i=>$user)
                        <tr>
                            <td style=" position: relative;">

                                <span style="margin:0 5px;">{{$users->currentpage()==1?$i+1:$i+($users->perpage()*($users->currentpage() - 1))+1}}</span>
                            </td>
                            <td style="padding: 0 3px;">
                                <span>
                                    <img src="{{asset($user->image())}}" style="max-width: 60px; max-height: 50px;" />
                                </span>
                            </td>
                            <td><a href="{{route('admin.buyersAction',['view',$user->id])}}" target="_blank" class="invoice-action-view mr-1">{{$user->name}}</a>
                                @if($user->permission)
                                <br><span class="badge {{$user->permission->id==1?'badge-success':'badge-info'}}">{{$user->permission->name}}</span>
                                @endif
                            </td>
                            <td>{{ $user->employee_id ?? '--' }}</td>
                            <td>{{$user->mobile ?? $user->email ?? '--'}}</td>
                            <td>
                                @if($user->designation)
                                <span style="color: #009688;font-weight: bold;">{{$user->designation->name}}</span>
                                @else
                                <span style="color: #FF9800;">No Designation</span>
                                @endif
                            </td>
                            <td>{{$user->created_at->format('d.m.Y')}}</td>
                            <td>{{ $user->deleted_at?->format('d.m.Y, h:i A') }}</td>

                            <td>
                                {{$user->deletedBy?->name }}</td>
                            <td style="padding: 8px 5px; text-align: center;">
                                @if(can('employee.delete'))
                                    <a href="{{ route('admin.buyersAction', ['restore', $user->id]) }}" class="btn-custom success" onclick="return confirm('Are you sure you want to restore this user?')">
                                        <i class="bx bx-reset"></i>
                                    </a>
                                    <a href="{{ route('admin.buyersAction', ['force-delete', $user->id]) }}" class="btn-custom danger" onclick="return confirm('Are you sure you want to permanently delete this user?')">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                @else -- @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        {{$users->links('pagination')}}
    </div>
</div>
</div>





@endsection
@push('js')
@endpush
