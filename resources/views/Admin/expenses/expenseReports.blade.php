@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Expenses Reports')}} </title>
@endsection @push('css')

<style type="text/css">

    .select2.select2-container{
        width:100% !important;
        display:block;
    }
    .select2.select2-container .select2-selection--single {
        border: 1px solid #ced4da;
    }
    .select2.select2-container .select2-selection__arrow {
        top: 5px;
        right: 5px;
    }

    .activity-timeline-content ul li::before{
        height: 100%;
    }
    .dropdown-toggle::after{
        display:none;
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
                         <div class="dropdown">
                            <button class="dropdown-toggle btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #ced4da;border-radius: 0;background: #e8e8e8;">
                                <i class='bx bx-dots-horizontal-rounded' ></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('admin.expenseReports', [
                                    'startDate' => Carbon\Carbon::now()->format('Y-m-d'),
                                    'endDate'   => Carbon\Carbon::now()->format('Y-m-d')
                                ]) }}">
                                    <i class='bx bx-show'></i> Today
                                </a>

                                <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('admin.expenseReports', [
                                    'startDate' => Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'),
                                    'endDate'   => Carbon\Carbon::now()->format('Y-m-d')
                                ]) }}">
                                    <i class='bx bx-show'></i> This Month
                                </a>

                                <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('admin.expenseReports', [
                                    'startDate' => Carbon\Carbon::now()->startOfYear()->format('Y-m-d'),
                                    'endDate'   => Carbon\Carbon::now()->endOfYear()->format('Y-m-d')
                                ]) }}">
                                    <i class='bx bx-show'></i> This Year
                                </a>
                            </div>

                        </div>
                        <input type="date" name="startDate" value="{{$from->format('Y-m-d')}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{$to->format('Y-m-d')}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
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
                    <div class="form-group">
                        <label>Branch</label>
                        <select class="form-control" name="branch_id" >
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                            <option value="{{$branch->id}}" {{request()->branch_id==$branch->id?'selected':''}}>{{$branch->name}}</option>
                            @endforeach
                        </select>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Summery Report</h3>

                <form action="{{ route('admin.expenseReports') }}" method="GET" target="_blank">
                    <input type="hidden" name="summery" value="true">
                    <input type="hidden" name="startDate" value="{{ request()->startDate }}">
                    <input type="hidden" name="endDate" value="{{ request()->endDate }}">
                    <input type="hidden" name="expense_type" value="{{ request()->expense_type }}">
                    <input type="hidden" name="branch_id" value="{{ request()->branch_id }}">
                    <button type="submit" class="btn-custom primary" style="padding:5px 15px;">
                        <i class="fa fa-print"></i> Print
                    </button>
                </form>

            </div>

            <ul>
                <li>
                    <i class="bx bx-check-double"></i>
                    <span>Total Expenses</span>
                    {{$expenses?numberFormat($expenses->sum('amount')):0}} BDT
                </li>

                @foreach($expenseTypes as $expenseType)
                @php
                    $total = $expenses?$expenses->where('category_id',$expenseType->id)->sum('amount'):0;
                @endphp
                @if($total > 0)
                <li>
                    <i class="bx bx-check-double"></i>
                    <span>{{$expenseType->name}}</span>
                    {{numberFormat($total,1)}}
                </li>
                @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Report Table</h3>
         <div class="dropdown">
            <a href="javascript:void(0)" class="btn-custom danger" style="padding:5px 15px;" id="ExportAction" ><i class="fa-solid fa-file-excel"></i> Export</a>
            <a href="javascript:void(0)" class="btn-custom primary" style="padding:5px 15px;" id="PrintAction" >
                <i class="fa fa-print"></i> Print
            </a>
         </div>
    </div>
    <div class="card-body">
        @if($expenses)

        <div class="PrintAreaContact">
            <style>
                .tableReport tr th{
                    padding: 5px 10px;
                    border: 1px solid #dee2e6;
                }
                .tableReport tr td{
                    padding: 5px 10px;
                    border: 1px solid #dee2e6;
                }
            </style>
            <div class="text-center mb-4">
                <img src="{{asset(general()->logo())}}" alt="logo" style="max-height: 80px;">
                <h2>{{general()->title}}</h2>
                <p>
                    {!!general()->address_one!!}
                    <br>
                    <b>Phone:</b> {{general()->mobile}}
                    <b>Email:</b> {{general()->email}}
                    <br>
                    <b>Date:</b>
                    {{ date('d M, Y') }}
                </p>
                <span style="display: inline-block;padding: 1px 25px;border: 1px solid #e3cfcf;border-radius: 5px;background: #fbfbfb;">Expense Report</span>
            </div>
            <div class="table-responsive">
                <table  class="table tableReport" >
                    <thead>
                        <tr>
                            <th style="width: 100px;min-width: 120px;">Date</th>
                            <th style="min-width: 130px;">Company Name</th>
                            <th style="min-width: 130px;">Receiver Name</th>
                            <th style="min-width: 200px;">Description</th>
                            <th style="width: 150px;min-width: 100px;">Method</th>
                            <th style="width: 150px;min-width: 120px;">Expense Head</th>
                            <th style="width: 150px;min-width: 100px;">Branch/Factory</th>
                            <th style="width: 150px;min-width: 100px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                        <tr>
                            <td>{{$expense->created_at->format('d.m.Y')}}</td>
                            <td>{{$expense->company_name}}</td>
                            <td>{{$expense->receiver_name}}</td>
                            <td>{!! nl2br(e($expense->description)) !!}</td>
                            <td>{{$expense->method?$expense->method->name:'not found'}}</td>
                            <td>{{$expense->category?$expense->category->name:'not found'}}</td>
                            <td>{{$expense->branch?$expense->branch->name:'not found'}}</td>
                            <td>{{numberFormat($expense->amount,2)}}</td>
                        </tr>
                        @endforeach
                        @if($expenses->count()==0)
                        <tr>
                            <td colspan="8" class="text-center">No Data Found</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" style="text-align: right">Total</th>
                            <th>{{numberFormat($expenses->sum('amount'),2)}}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
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
