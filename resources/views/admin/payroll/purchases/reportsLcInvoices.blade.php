@extends(adminTheme().'layouts.app') @section('title')
<title>LC Reports </title>
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
         <h3>LC Reports</h3>
         <div class="dropdown">
             <a href="{{route('admin.lcReports')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.lcReports')}}">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <label>Date To Date</label>
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-6 mb-1">
                    <label>Search LC No</label>
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Lc Number" class="form-control {{$errors->has('search')?'error':''}}" />
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
                    <span>Total LC</span>
                    {{$invoices?$invoices->count():0}} Invoices
                </li>

                <li>
                    <i class="bx bx-check-double"></i>
                    <span>Value $</span>
                    {{$invoices?priceFormat($invoices->sum('lc_total_value')):0}} $
                </li>

                <li>
                    <i class="bx bx-check-double"></i>
                    <span>Parchase TK </span>
                    {{$invoices?priceFormat($invoices->sum('paid_amount')):0}} TK
                </li>

                <li>
                    <i class="bx bx-check-double"></i>
                    <span>PI</span>
                    {{$invoices?$invoices->sum('total_items'):0}} Pi
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
                <th>LC Number</th>
                <th>Value $</th>
                <th>Bank</th>
                <th>Open Date</th>
                <th>Submited Date</th>
                <th>Received Date</th>
                <th>Matuirity Date</th>
                <th>Purchase Date</th>
                <th>Purchase Tk</th>
                <th>Remarks</th>
                <th>Total PI</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>LC Number</th>
                <th>Value $</th>
                <th>Bank</th>
                <th>Open Date</th>
                <th>Submited Date</th>
                <th>Received Date</th>
                <th>Matuirity Date</th>
                <th>Purchase Date</th>
                <th>Parchase TK</th>
                <th>Remarks</th>
                <th>Total PI</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td><a href="{{route('admin.lcInvoicesAction',['view',$invoice->id])}}" target="_blank">{{$invoice->invoice}}</a></td>
                <td>${{priceFormat($invoice->lc_total_value)}}</td>
                <td>
                    {{$invoice->lc_open_bank}}
                    @if($invoice->balance_bank)
                    - {{$invoice->balance_bank}}
                    @endif
                </td>
                <td>{{$invoice->created_at->format('d.m.Y')}}</td>
                <td>{{$invoice->pending_at?Carbon\Carbon::parse($invoice->pending_at)->format('d.m.Y'):''}}</td>
                <td>{{$invoice->confirmed_at?Carbon\Carbon::parse($invoice->confirmed_at)->format('d.m.Y'):''}}</td>
                <td>{{$invoice->despass_date?Carbon\Carbon::parse($invoice->despass_date)->format('d.m.Y'):''}}</td>
                <td>{{$invoice->shipped_at?Carbon\Carbon::parse($invoice->shipped_at)->format('d.m.Y'):''}}</td>
                <td>TK {{priceFormat($invoice->paid_amount)}}</td>
                <td>{{$invoice->note}}</td>
                <td>{{$invoice->items()->count()}}</td>
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