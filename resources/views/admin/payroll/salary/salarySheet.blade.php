@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Salary Sheet')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Salary Sheet</h3>
        <div class="dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['salarySheet']['add'])
            <a href="javascript:void(0)" data-toggle="modal" data-target="#AddSalary" class="btn-custom success"><i class="bx bx-plus"></i> Salary Sheet</a>
            @endisset
            <a href="{{route('admin.salarySheet')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
            </a>
        </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

            <form action="{{route('admin.salarySheet')}}">
                <div class="row">
                    <div class="col-md-12 mb-0">
                        <div class="input-group">
                            <select class="form-control" name="year">
                                <option value="">Select year</option>
                                    @php
                                        $currentYear = date('Y');
                                    @endphp

                                    @for ($y = 2000; $y <= 2099; $y++)
                                        <option value="{{ $y }}"
                                            {{ ($year ?? $currentYear) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                            </select>
                            <select class="form-control" name="month">
                                <option value="">Select Month</option>
                                <option value="01" {{request()->month=='01'?'selected':''}} >January</option>
                                <option value="02" {{request()->month=='02'?'selected':''}}>February</option>
                                <option value="03" {{request()->month=='03'?'selected':''}}>March</option>
                                <option value="04" {{request()->month=='04'?'selected':''}}>April</option>
                                <option value="05" {{request()->month=='05'?'selected':''}}>May</option>
                                <option value="06" {{request()->month=='06'?'selected':''}}>June</option>
                                <option value="07" {{request()->month=='07'?'selected':''}}>July</option>
                                <option value="08" {{request()->month=='08'?'selected':''}}>August</option>
                                <option value="09" {{request()->month=='09'?'selected':''}}>September</option>
                                <option value="10" {{request()->month=='10'?'selected':''}}>October</option>
                                <option value="11" {{request()->month=='11'?'selected':''}}>November</option>
                                <option value="12" {{request()->month=='12'?'selected':''}}>December</option>

                            </select>
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
                            <th style="min-width: 60px;width: 60px;">
                                SL
                            </th>
                            <th style="min-width: 200px;">Month - Year</th>
                            <th style="min-width: 300px;">Net Salary</th>
                            <th style="min-width: 120px;">Employee</th>
                            <th style="min-width: 60px;width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaries as $i=>$salary)
                        <tr>
                            <td>
                                <span style="margin:0 5px;">{{$i+1}}</span>
                            </td>
                            <td>
                                @if(isset(json_decode(Auth::user()->permission->permission, true)['salarySheet']['view']))
                                <a href="{{route('admin.salarySheetAction',['export',$salary->created_at->format('Y-m')])}}" target="_blank">{{$salary->created_at->format('F - Y')}}</a>
                                @else
                                {{$salary->created_at->format('F - Y')}}
                                @endif
                            </td>
                            <td>
                                BDT {{priceFormat($salary->total_salary)}}
                            </td>
                            <td>{{$salary->employee}}</td>
                            <td>
                                @isset(json_decode(Auth::user()->permission->permission, true)['salarySheet']['add'])
                                <a href="{{route('admin.salarySheetAction',$salary->created_at->format('Y-m'))}}" class="btn-custom yellow"><i class="bx bx-edit"></i></a>
                                @endisset
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </form>
    </div>
</div>
</div>

@isset(json_decode(Auth::user()->permission->permission, true)['salarySheet']['add'])
<!-- Add Modal -->
 <div class="modal fade text-left" id="AddSalary" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	    <form action="{{route('admin.salarySheetAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Create Salary Sheet</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Month - Year* </label>
                    <input type="month" class="form-control month-input {{$errors->has('month')?'error':''}}" name="month" required="" style="width: 170px;">
    				@if ($errors->has('month'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('month') }}</p>
    				@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Submit</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endisset

@endsection
@push('js')
<script>
    $(document).ready(function(){

    });
</script>
@endpush
