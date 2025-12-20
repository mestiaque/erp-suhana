@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Buyer List')}}</title>
@endsection
@push('css')
<style type="text/css">

 @media (max-width: 1400px) {
        table tr td {
            font-size: 12px;
        }
        .table thead th {
            font-size: 14px;
        }
 }

</style>
@endpush
@section('contents')


<div class="flex-grow-1">
<!-- Start -->
<div class="card mb-30">
    @include(adminTheme().'alerts')
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Buyer List</h3>
         <div class="dropdown">
            @can('buyers.add')
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddBuyer">
                 <i class="bx bx-plus"></i> Buyer
             </a>
             @endcan
             <a href="{{route('admin.buyers')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">

        <form action="{{route('admin.buyers')}}">
           <div class="row">
               <div class="col-md-7 mb-1">
                   <div class="input-group">
                       <input type="date" name="startDate" value="{{request()->startDate?:''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                       <input type="date" value="{{request()->endDate?:''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                   </div>
               </div>
               <div class="col-md-5 mb-1">
                   <div class="input-group">
                       <input type="text" name="search" value="{{request()->search?:''}}" placeholder="User Name, Email, Mobile, Country" class="form-control {{$errors->has('search')?'error':''}}" />
                       <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                   </div>
               </div>
           </div>
       </form>

        <br>

        <form action="{{route('admin.buyers')}}">
            <div class="row">
                <div class="col-md-4">
                    @if(can('buyers.edit')  || can('buyers.delete'))
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            @can('buyers.edit')
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            @endcan
                            @can('buyers.delete')
                            <option value="5">Delete</option>
                            @endcan
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                    </div>
                    @endif
                </div>

                <div class="col-md-4"></div>

                <div class="col-md-4">
                    <ul class="statuslist">
                        <li><a href="{{route('admin.buyers')}}" class="{{request()->status?'':'active'}}" >All ({{$total->total}})</a></li>
                        <li><a href="{{route('admin.buyers',['status'=>'active'])}}" class="{{request()->status=='active'?'active':''}}" >Active ({{$total->active}})</a></li>
                        <li><a href="{{route('admin.buyers',['status'=>'inactive'])}}" class="{{request()->status=='inactive'?'active':''}}" >Inactive ({{$total->inactive}})</a></li>
                        @if($total->deleted > 0)
                            <li><a href="{{route('admin.buyers',['view'=>'deleted'])}}" class="text-danger" >Deleted ({{$total->deleted}})</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0; position: relative;">
                                 @if(can('buyers.edit')  || can('buyers.delete'))
                                <div class="checkbox mr-3">
                                     <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />
                                     <label class="cbx" for="checkall">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                         All <span class="checkCounter"></span>
                                     </label>
                                 </div>
                                 @else All @endif
                            </th>
                            <th style="min-width: 70px; width: 70px;">Image</th>
                            <th style="min-width: 200px;">Name</th>
                            <th style="min-width: 150px;">Company</th>
                            <th style="min-width: 150px;">Email</th>
                            <th style="min-width: 150px;">Country</th>
                            <th style="min-width: 200px;">Address</th>
                            {{-- <th style="min-width: 100px;">Total Purchase</th>
                            <th style="min-width: 100px;">Due</th>
                            <th style="min-width: 100px;">Paid</th> --}}
                            <th style="min-width: 95px;">Join Date</th>
                            <th style="min-width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($users as $i=>$user)
                        <tr>
                            <td>
                                @if(can('buyers.edit')  || can('buyers.delete'))
                                <div class="checkbox">
                                    <input class="inp-cbx" id="cbx_{{$user->id}}" type="checkbox" name="checkid[]" value="{{$user->id}}" style="display: none;" />
                                    <label class="cbx" for="cbx_{{$user->id}}">
                                        <span><svg width="12" height="10"><polyline points="1.5 6 4.5 9 10.5 1"></polyline></svg></span>
                                    </label>
                                </div>
                                @endif

                                <span>{{$users->currentpage()==1?$i+1:$i+($users->perpage()*($users->currentpage() - 1))+1}}</span>

                                @if($user->status)
                                <span style="color:#43d39e;font-size:20px;"><i class="bx bx-check-circle"></i></span>
                                @else
                                <span style="color:#FF9800;font-size:20px;"><i class="bx bx-analyse"></i></span>
                                @endif
                            </td>

                            <td style="padding: 0 3px;"><img src="{{asset($user->image())}}" style="max-width: 60px; max-height: 50px;" /></td>

                            <td>
                                <a href="{{route('admin.buyersAction',['view',$user->id])}}" target="_blank">{{$user->name}}</a>
                                @if($user->permission)
                                <br><span class="badge {{$user->permission->id==1?'badge-success':'badge-info'}}">{{$user->permission->name}}</span>
                                @endif
                            </td>

                            <td>{{$user->company_name  ?? '--'}}</td>
                            <td>{{$user->email ?? '--'}}</td>
                            <td>{{$user->country_text ?? '--'}}</td>
                            <td>{{$user->fullAddress()}}</td>

                            {{-- <td>{{priceFullFormat($user->orders->where('status','approved')->sum('grand_total'))}}</td>
                            <td style="color:red;">{{priceFullFormat($user->duePurchaseAmount())}}</td>
                            <td>{{priceFullFormat($user->orders->where('status','approved')->sum('paid_amount'))}}</td> --}}
                            <td>{{$user->created_at->format('d.m.Y')}}</td>

                            <td class="text-center">
                                @if(can('buyers.edit')  || can('buyers.delete'))
                                @can('buyers.edit')
                                <a href="{{route('admin.buyersAction',['edit',$user->id])}}" class="btn-custom success"><i class="bx bx-edit"></i></a>
                                @endcan
                                @can('buyers.delete')
                                <a href="{{route('admin.buyersAction',['delete',$user->id])}}" onclick="return confirm('Are You Want To Delete')" class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                                @endcan
                                @else -- @endif
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </form>

        {{ $users->links('pagination::bootstrap-4') }}
    </div>
</div>
</div>

<!-- Add Buyer Modal -->
<div class="modal fade text-left" id="AddBuyer" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
        <form action="{{route('admin.buyersAction','create')}}" method="post">
            @csrf
       <div class="modal-header">
         <h4 class="modal-title">Add Buyer</h4>
         <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
       </div>

       <div class="modal-body">
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" class="form-control" name="name" placeholder="Enter Customer Name" required>
            </div>
            <div class="form-group">
                <label>Buying/Agent Name</label>
                <input type="text" class="form-control" name="company_name" placeholder="Enter Buying/Agent Name">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="mail" class="form-control" name="email" placeholder="Enter Email">
            </div>
            <div class="form-group">
                <label>Mobile</label>
                {{-- <input type="text" class="form-control" name="mobile" placeholder="Enter Mobile"> --}}
                <input type="tel" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" minlength="11" maxlength="11" pattern="[0-9]{11}" title="Please enter exactly 11 digits" oninput="this.value = this.value.slice(0, 11);" placeholder="Please enter exactly 11 digits with start 0" required>
            </div>
            <div class="form-group">
                <label>Country</label>
                <select name="country" id="" class="form-control">
                    <option value="">-- Select Country --</option>
                    @foreach (geoData(1) as $c)
                        <option value="{{ $c->name }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Buying House/Agent Address</label>
                <input type="text" class="form-control" name="address" placeholder="Enter Address">
            </div>

       </div>

       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Buyer</button>
       </div>

       </form>
     </div>
   </div>
</div>

@endsection
@push('js')
@endpush
