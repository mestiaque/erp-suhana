@extends(adminTheme().'layouts.app') @section('title')
<title>{{$createDate->format('F')}} - {{$createDate->format('Y')}} {{general()->title}} Salary Sheet</title>
@endsection @push('css')
<style type="text/css">

</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Salary Sheet view</h3>
        <div class="dropdown">
            <a href="{{route('admin.salarySheet')}}" class="btn-custom success"><i class="bx bx-list"></i> Back</a>
            @isset(json_decode(Auth::user()->permission->permission, true)['salarySheet']['add'])
            <a href="{{route('admin.salarySheetAction',$createDate->format('Y-m'))}}" class="btn-custom success"><i class="bx bx-edit"></i> Edit</a>
            @endisset
            <a href="{{route('admin.salarySheetAction',[$action,$createDate->format('Y-m')])}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
            </a>
        </div>
    </div>
    <div class="card-body">
            @include(adminTheme().'alerts')
            
            <div class="table-responsive">
                <h5>Corporate Employee: {{$corporateSalaries->count()}}</h5>
                <table id="example" class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0;">SL</th>
                            <th style="min-width: 200px;">Name</th>
                            <th style="min-width: 120px;">Basic (BDT)</th>
                            <th style="min-width: 120px;">DPS/Home</th>
                            <th style="min-width: 100px;">Phone</th>
                            <th style="min-width: 100px;">Salary</th>
                            <th style="min-width: 100px;">Allowances</th>
                            <th style="min-width: 100px;">Bonus</th>
                            <th style="min-width: 100px;">Deduction</th>
                            <th style="min-width: 100px;">Due/Loan</th>
                            <th style="min-width: 120px;width:120px;">Net Salary</th>
                            <th style="min-width: 120px;width:120px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($corporateSalaries as $i=>$salary)
                        <tr>
                            <td>{{$i+1}}</td>
                            <td>{{$salary->user?$salary->user->name:''}}</td>
                            <td>{{priceFormat($salary->salary_amount)}}</td>
                            <td>{{priceFormat($salary->home_bill)}}</td>
                            <td>{{priceFormat($salary->mobile_bill)}}</td>
                            <td>{{priceFormat($salary->salary_amount+$salary->mobile_bill+$salary->home_bill)}}</td>
                            <td>{{priceFormat($salary->allowances_amount)}}</td>
                            <td>{{priceFormat($salary->bonus_amount)}}</td>
                            <td>{{priceFormat($salary->deduction_amount)}}</td>
                            <td>0</td>
                            <td>{{priceFormat($salary->net_salary_amount)}}</td>
                            <td>{!!$salary->remarks!!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <h5>Factory Employee: {{$salaries->count()}}</h5>
                <table id="example2" class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px; width: 100px;padding-right:0;">
                                SL
                            </th>
                            <th style="min-width: 200px;">Employee Name</th>
                            <th style="min-width: 120px;">Basic (BDT)</th>
                            <th style="min-width: 120px;">Working Day</th>
                            <th style="min-width: 100px;">Att. Bouns</th>
                            <th style="min-width: 100px;">Amount/day</th>
                            <th style="min-width: 100px;">Salary</th>
                            <th style="min-width: 120px;">O.T Hours.</th>
                            <th style="min-width: 120px;">Amont/Hours.</th>
                            <th style="min-width: 120px;">O.T Value.</th>
                            <th style="min-width: 100px;">Allowances</th>
                            <th style="min-width: 100px;">Bonus</th>
                            <th style="min-width: 100px;">Deduction</th>
                            <th style="min-width: 100px;">Due/Loan</th>
                            <th style="min-width: 120px;width:120px;">Net Salary</th>
                            <th style="min-width: 120px;width:120px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaries as $ii=>$salary2)
                        <tr>
                            <td>
                                 {{$ii+1}}
                            </td>
                            <td>{{$salary2->user?$salary2->user->name:''}}</td>
                            <td>{{priceFormat($salary2->salary_amount)}}</td>
                            <td>{{$salary2->employee_working_day}} - <small>({{$salary2->total_working_day}})</small></td>
                            <td>{{$salary2->bonus_working_day}}</td>
                            <td>{{priceFormat($salary2->working_day_rate)}}</td>
                            <td>{{priceFormat($salary2->woking_salary_amount)}}</td>
                            <td>{{$salary2->over_time_hour}} Hours</td>
                            <td>{{priceFormat($salary2->over_time_hour_rate)}}</td>
                            <td>{{priceFormat($salary2->over_time_amount)}}</td>
                            <td>{{priceFormat($salary2->allowances_amount)}}</td>
                            <td>{{priceFormat($salary2->bonus_amount)}}</td>
                            <td>{{priceFormat($salary2->deduction_amount)}}</td>
                            <td>0</td>
                            <td>{{priceFormat($salary2->net_salary_amount)}}</td>
                            <td>{!!$salary2->remarks!!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

    </div>
</div>
</div>

@endsection 
@push('js') 
<script>
    $(document).ready(function(){
          
        $('#example').DataTable( {
	        dom: 'Bfrtip',
	        buttons: [
	            'excel', 'pdf', 'print'
	        ]
	    } );
        $('#example2').DataTable( {
	        dom: 'Bfrtip',
	        buttons: [
	            'excel', 'pdf', 'print'
	        ]
	    } );
    });
</script>
@endpush