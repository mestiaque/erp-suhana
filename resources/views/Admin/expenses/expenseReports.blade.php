@extends(adminTheme().'layouts.app') @section('title')
<title>Expenses Reports </title>
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
         <h3>Expenses Reports</h3>
         <div class="dropdown">
             <a href="{{route('admin.expenseReports')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.expenseReports')}}">
            <div class="row">
                <div class="col-md-4 mb-1">
                    <label>Date To Date</label>
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-3 mb-1">
                    <div class="form-group">
                        <label>Expense Type</label>
                        <select class="select2" name="expense_type" data-placeholder="Select Expense Type">
                            <option value="">Select Expense Type</option>
                            @foreach($expenseTypes as $expenseType)
                            <option value="{{$expenseType->id}}" {{request()->expense_type==$expenseType->id?'selected':''}}>{{$expenseType->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 mb-1">
                    <label>Search </label>
                    <div class="input-group">
                        <select class="select2" name="search" data-placeholder="Select Reff/Title">
                            <option value="">Select Reff/Title</option>
                            @foreach($reffTitles as $reffTitle)
                            <option value="{{$reffTitle->id}}" {{request()->search==$reffTitle->id?'selected':''}}>{{$reffTitle->name}}</option>
                            @endforeach
                        </select>
                        <!--<input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Expense" class="form-control {{$errors->has('search')?'error':''}}" />-->
                    </div>
                </div>
                <div class="col-md-2 mb-1">
                        <label>Action</label> <br>
                        <button type="submit" class="btn btn-success btn-sm btn-block">Search</button>
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
                    <span>Total Expenses</span>
                    {{$expenses?priceFormat($expenses->sum('amount')):0}} BDT
                </li>

                @foreach($expenseTypes as $expenseType)
                <li>
                    <i class="bx bx-check-double"></i>
                    <span>{{$expenseType->name}}</span>
                    {{$expenses?priceFormat($expenses->where('category_id',$expenseType->id)->sum('amount')):0}} BDT
                </li>
                @endforeach

                
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
        @if($expenses)
        <div class="table-responsive">
        <table id="example" class="display nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Method</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Method</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{$expense->created_at->format('d.m.Y')}}</td>
                <td>{{$expense->name}}</td>
                <td>{{$expense->method?$expense->method->name:'not found'}}</td>
                <td>{{$expense->category?$expense->category->name:'not found'}}</td>
                <td>{{priceFormat($expense->amount)}}</td>
                <td>{!!$expense->description!!}</td>
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