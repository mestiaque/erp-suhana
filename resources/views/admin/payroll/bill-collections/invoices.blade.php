@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Bill Collections')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Bill Collections</h3>
         <div class="dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['pi']['add'])
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddBill" style="padding:5px 15px;">
                 <i class="bx bx-search"></i> Search Bill
             </a>
            @endisset
            <a href="{{ route('admin.billCollection', [
            'export' => 'report',
            'startDate' => request()->startDate,
            'endDate' => request()->endDate,
            'search' => request()->search]) }}" class="btn-custom yellow">
                         <i class="bx bx-export"></i> Export
                     </a>
             <a href="{{route('admin.billCollection')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.billCollection')}}">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Invoice, billing Name" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.billCollection')}}">
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-8">
                    <ul class="statuslist mb-0">
                        <li><a href="{{route('admin.billCollection',['status'=>'all'])}}">All ({{$billcollections->total()}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                <!--@if(isset(json_decode(Auth::user()->permission->permission, true)['pi']['delete']))-->
                                <!--<div class="checkbox mr-3">-->
                                <!-- <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />-->
                                <!-- <label class="cbx" for="checkall">-->
                                <!--     <span>-->
                                <!--         <svg width="12px" height="10px" viewbox="0 0 12 10">-->
                                <!--             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>-->
                                <!--         </svg>-->
                                <!--     </span>-->
                                <!--     All <span class="checkCounter"></span> -->
                                <!-- </label>-->
                                <!--</div>-->
                                <!--@else-->
                                <!--@endif-->
                                SL
                            </th>
                            <th style="min-width: 100px;">Inv No</th>
                            <th style="min-width: 150px;">Billing</th>
                            <th style="min-width: 150px;">Title</th>
                            <th style="min-width: 100px;">Total</th>
                            <th style="min-width: 100px;">Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($billcollections as $i=>$invoice)
                        <tr>
                            <td>
                                <span style="margin:0 5px;">{{$billcollections->currentpage()==1?$i+1:$i+($billcollections->perpage()*($billcollections->currentpage() - 1))+1}}</span>
                            </td>
                            <td>
                                <a href="{{route('admin.billCollectionAction',['view',$invoice->sale->id])}}" target="_blank">{{$invoice->sale->invoice}}</a>
                            </td>
                            <td>
                                 @if($invoice->company)
                                    <a href="{{route('admin.companiesAction',['sales',$invoice->company->id])}}" >{{$invoice->billing_name}}</a>
                                @else
                                    {{$invoice->billing_name}}
                                @endif
                            </td>
                            <td>
                                @if($invoice->sale->emi_status)
                                    @if(str_contains($invoice->billing_reason, 'Installment'))
                                        EMI Installment
                                    @else
                                        EMI Down payment
                                    @endif
                                @else
                                {{$invoice->billing_reason}}
                                @endif
                            </td>
                            <td>{{$invoice->currency}} {{number_format($invoice->amount,2)}}</td>
                            <td>{{$invoice->created_at->format('d.m.Y')}}</td>
                            <td>
                                <a href="{{route('admin.billCollectionAction',['edit',$invoice->sale->id])}}" class="btn-custom"><i class="bx bx-edit"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>BDT {{number_format($billcollections->sum('amount'),2)}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                {{$billcollections->links('pagination')}}
            </div>
        </form>
    </div>
</div>
</div>


<!-- Add Modal -->
<div class="modal fade text-left" id="AddBill" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	    <form action="{{route('admin.billCollectionAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Search Bill</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="invoice">Invoice No*</label>
                    <input type="text" class="form-control {{$errors->has('invoice')?'error':''}}" name="invoice" placeholder="Enter invoice" required="">
    				@if ($errors->has('invoice'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('invoice') }}</p>
    				@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Search Bill</button>
    	   </div>
	   </form>
	 </div>
   </div>
</div>


@endsection @push('js') @endpush
