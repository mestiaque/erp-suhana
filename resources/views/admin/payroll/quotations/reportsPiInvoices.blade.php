@extends(adminTheme().'layouts.app') @section('title')
<title>PI Reports </title>
@endsection @push('css')

<style type="text/css">
    
    .select2.select2-container{
        width:100% !important;
        display:block;
    }
    .select2.select2-container .select2-selection--single {
        height: 38px;
        padding: 5px;
        border: 1px solid #ced4da;
    }
    .select2.select2-container .select2-selection__arrow {
        top: 5px;
        right: 5px;
    }
    
    .activity-timeline-content ul li::before{
        height: 100%;
    }
    
    @media only screen and (min-width: 769px) {
        
        .activity-timeline-content ul li {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }
    
</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>PI Reports</h3>
         <div class="dropdown">
             <a href="{{route('admin.piReports')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.piReports')}}">
            <div class="row">
                <div class="col-md-4 mb-1">
                    <label>Date To Date</label>
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-4 mb-1">
                    <div class="form-group">
                        <label>Merchandiser</label>
                        <select class="select2" name="merchandiser" data-placeholder="Select Merchandiser">
                            <option value="">Select Merchandiser</option>
                            @foreach($merchandisers as $merchandiser)
                            <option value="{{$merchandiser->id}}" {{request()->merchandiser==$merchandiser->id?'selected':''}}>{{$merchandiser->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 mb-1">
                    <div class="form-group">
                        <label>Company</label>
                        <select class="select2" name="company" data-placeholder="Select Company">
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                            <option value="{{$company->id}}" {{request()->company==$company->id?'selected':''}} >{{$company->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 mb-1">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="">Select Status</option>
                            <option value="pending" {{request()->status=='pending'?'selected':''}}>Pending</option>
                            <option value="confirmed" {{request()->status=='confirmed'?'selected':''}}>Confirmed</option>
                            <option value="completed" {{request()->status=='completed'?'selected':''}}>Completed</option>
                            <option value="cancelled" {{request()->status=='cancelled'?'selected':''}}>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-8 mb-1">
                    <label>Search PI No</label>
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Pi Numbers" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-30 pt-2">
    <div class="card-body activity-timeline-chart-box" style="position: relative;">
        <div class="activity-timeline-content">
            <div class="card-header">
                <h3>Summery Report</h3>
            </div>

            <ul>
                <li>
                    <i class="bx bx-check-double"></i>
                    <span>Total PI</span>
                    {{$reports['totalPi']}} Invoices
                </li>

                <li>
                    <i class="bx bx-check-double"></i>
                    <span>Value $</span>
                    {{$invoices?priceFormat($reports['totalPiValue']):0}}$
                </li>

                <li>
                    <i class="bx bx-check-double"></i>
                    <span>Open LC </span>
                     {{$reports['openLc']}} Invoices
                </li>

                <li>
                    <i class="bx bx-check-double"></i>
                    <span>Pending LC</span>
                    {{$reports['pendingLc']}} Invoices
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Report Table</h3>
    </div>
    <div class="card-body">
        @if($invoices)
        <div class="table-responsive">
        <table id="example" class="display nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>PI Date</th>
                <th>PI No</th>
                <th>Merchandiser</th>
                <th>Company</th>
                <th>Value $</th>
                <th>Status</th>
                <th>LC Number</th>
                <th>LC Date</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>PI Date</th>
                <th>PI No</th>
                <th>Merchandiser</th>
                <th>Company</th>
                <th>Value $</th>
                <th>Status</th>
                <th>LC Number</th>
                <th>LC Date</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{$invoice->created_at->format('d.m.Y')}}</td>
                <td><a href="{{route('admin.piInvoicesAction',['view',$invoice->id])}}" target="_blank">{{$invoice->invoice}}</a> @if($invoice->hasSubInvoices->count() > 0)({{$invoice->hasSubInvoices->count()}}) @endif</td>
                <td>{{$invoice->marchantize?$invoice->marchantize->name:'not found'}}</td>
                <td>{{$invoice->company?$invoice->company->name:'not found'}}</td>
                <td>${{priceFormat($invoice->grand_total)}}</td>
                <td>{{$invoice->order_status}}</td>
                <td>
                    @if($lc =$invoice->hasLcOrders()->latest()->first())
                        {{$lc->order?$lc->order->invoice:'Not found'}}
                    @endif
                    @if($invoice->hasLcOrders->count() > 1)
                     - ({{$invoice->hasLcOrders->count()}})
                    @endif
                </td>
                
                <td>
                    @if($lc =$invoice->hasLcOrders()->latest()->first())
                    {{$lc->order?$lc->order->created_at->format('d.m.Y'):'Not found'}}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        </div>
        @else
        <span>No Report Data Found</span>
        @endif
    </div>
</div>





</div>
@endsection 
@push('js')



<script>
    $(document).ready(function () {
        $(".select2").each(function () {
            var placeHolder = $(this).data('placeholder');
            
            $(this).select2({
                placeholder: placeHolder,
                allowClear: true
            });
        });
        
        
        $('#example').DataTable( {
	        dom: 'Bfrtip',
	        buttons: [
	            'excel', 'pdf', 'print'
	        ]
	    } );
        
    });

</script>

@endpush