@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('User Profile View')}}</title>
@endsection

@push('css')
<style>
    .profileTable tr th{
        padding:5px 8px;
    }
    .profileTable tr td{
        padding:5px 8px;
    }
    .info ul {
        list-style: none;
        padding: 0;
        margin-top: 15px;
    }
    .info ul li{
        margin:10px 0;
    }
    .info ul li span b {
        font-size: 14px;
    }
    
    .info ul li i {
        font-size: 20px;
    }
    
    .info ul li span {
        line-height: 18px;
    }
    .fileTable tr td {
        padding: 5px;
    }
    
    .fileTable tr th {
        padding: 5px;
    }
</style>
@endpush
@section('contents')
<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Profile View</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item"><a href="{{route('admin.usersCustomer')}}">Employee List</a></li>
        <li class="item">Profile View</li>
    </ol>
</div>
 
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-30">
                <div class="card-body">
                    <img src="{{asset($user->image())}}">
                    <br>
                    <div class="info">
                        <ul>
                            <li class="d-flex"><i class="bx bx-user mr-2 pt-2"></i> <span><b>ID</b><br>{{$user->employee_id}}</span></li>
                            <li class="d-flex"><i class="bx bx-mobile mr-2 pt-2"></i> <span><b>Mobile</b><br>{{$user->mobile}}</span></li>
                            <li class="d-flex"><i class="bx bx-envelope mr-2 pt-2"></i><span><b>Email</b><br>{{$user->email}}</span></li>
                            <li class="d-flex"><i class="bx bx-check-shield mr-2 pt-2"></i><span><span><b>Designation</b><br>{{$user->designation?$user->designation->name:''}}</span></li>
                            <li class="d-flex"><i class="bx bx-briefcase mr-2 pt-2"></i> <span><span><b>Department</b><br>{{$user->department?$user->department->name:''}}</span></li>
                        </ul>
                        <div class="content">
                            {{$user->profile}}
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
        <div class="col-md-8">
             <!-- Start -->
            <div class="card mb-30">
                <div class="card-header d-flex justify-content-between align-items-center">
                     <h3>Salary Detials</h3>
                     <a href="{{route('admin.usersCustomerAction',['view',$user->id])}}"  class="btn-custom yellow"><i class="bx bx-edit"></i> Back</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Basic</th>
                                    <th>O.T Value</th>
                                    <th>Due/Loan</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($salaries->count() > 0)
                                @foreach($salaries as $salary)
                                <tr>
                                    <td>{{$salary->created_at->format('F Y')}}</td>
                                    <td>{{priceFormat($salary->salary_amount)}}</td>
                                    <td>{{priceFormat($salary->over_time_amount)}}</td>
                                    <td>{{priceFormat($salary->deduction_amount)}}</td>
                                    <td>{{priceFormat($salary->net_salary_amount)}}</td>
                                    <td>
                                        @if($salary->status=='paid')
                                        <span>Paid</span>
                                        @else
                                        <span>Unpaid</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="6" style="text-align:center;">No Data</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        
                        {{$salaries->links('pagination')}}
                    </div>
                </div>
            </div>
            

        </div>
    </div>
</div>



@endsection
@push('js')



@endpush