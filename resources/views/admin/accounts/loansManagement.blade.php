@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Loan Management')}}</title>
@endsection @push('css')
<style type="text/css">
    .searchlist ul {
        list-style: none;
        padding: 5px;
    }
    
    .searchlist ul li {
        border-top: 1px solid #dbd6d6;
        padding: 5px 0;
    }
    .searchlist ul li img {
        width: 35px;
        height: 35px;
        border-radius: 100%;
        border: 1px solid #dbd6d6;
        padding: 2px;
        margin-right: 10px;
    }
    
</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Loan Management</h3>
        <div class="dropdown">

             <a href="javascript:void(0)" data-toggle="modal" data-target="#AddLoan" class="btn-custom success">Add Loan</a>
             <a href="{{route('admin.loansManagement')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
        </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        <form action="{{route('admin.loansManagement')}}">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" name="endDate" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Employee Name, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        
        <br>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 60px;width: 60px;">SL</th>
                            <th style="min-width: 150px;">Name</th>
                            <th style="min-width: 100px;">Description</th>
                            <th style="min-width: 150px;">Account</th>
                            <th style="min-width: 100px;">Amount</th>
                            <th style="min-width: 100px;">Paid</th>
                            <th style="min-width: 120px;">Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transections as $i=>$loan)
                        <tr>
                            <td>
                                <span style="margin:0 5px;">{{$transections->currentpage()==1?$i+1:$i+($transections->perpage()*($transections->currentpage() - 1))+1}}</span>
                                @if($loan->status=='paid')
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif
                            </td>
                            <td>{{$loan->user?$loan->user->name:''}}</td>
                            <td>{{$loan->billing_note}}</td>
                            <td>{{$loan->account?$loan->account->name:''}}</td>
                            <td>{{priceFormat($loan->amount)}}</td>
                            <td>{{priceFormat($loan->paid_balance)}}</td>
                            <td>{{$loan->created_at->format('d-m-Y')}}</td>
                            <td class="center">
                                <a href="{{route('admin.loansManagementAction',['edit',$loan->id])}}"  class="btn-custom success">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{route('admin.loansManagementAction',['delete',$loan->id])}}"  class="btn-custom danger">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$transections->links('pagination')}}
            </div>
        
        
    </div>
</div>
</div>

<!-- Modal -->
 <div class="modal fade text-left" id="AddLoan" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	   <div class="modal-body">
	   		<label for="name">Search Employee </label>
            <div class="input-group">
                <input type="text" class="form-control {{$errors->has('name')?'error':''}} SearchQuery" data-type="company" data-url="{{route('admin.loansManagementAction','search-employee')}}" placeholder="Search Name, Mobile.." required="">
			    <div class="input-group-append">
                    <span class="input-group-text"><i class='bx bx-search'></i></span>
                </div>
			</div>
			<div class="employeeSearch searchlist" style="height:200px;overflow:auto;">
		    </div>
	   </div>
	 </div>
   </div>
 </div>

@endsection 
@push('js')

<script>
    
    $(document).on('keyup','.SearchQuery',function(){
            var url =$(this).data('url');
            var search =$(this).val();
            
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data: {'search':search},
              success : function(data){
                    $('.employeeSearch').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
                
        });
    
</script>


@endpush