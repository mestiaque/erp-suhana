@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Grades List')}}</title>
@endsection
@push('css')
<style type="text/css"></style>
@endpush
@section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Grades List</h3>
         <div class="dropdown">
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddGrade" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> Grade
             </a>
             <a href="{{route('admin.grades')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <div class="accordion-box">
            <div class="accordion">
                <div class="accordion-item">
                 <a class="accordion-title" href="javascript:void(0)">
                     <i class="bx bx-filter-alt"></i>
                    Search click Here..
                 </a>
                 <div class="accordion-content" style="border:1px solid #e1000a;border-top:0;">
                    <form action="{{route('admin.grades')}}">
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Grade Name" class="form-control {{$errors->has('search')?'error':''}}" />
                                    <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
        <br>
        <form action="{{route('admin.grades')}}">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            <option value="1">Active</option>
                            <option value="2">Inactive</option>
                            <option value="5">Delete</option>
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                    </div>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <ul class="statuslist mb-0">
                        <li><a href="{{route('admin.grades')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.grades',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                        <li><a href="{{route('admin.grades',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
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
                            </th>
                            <th style="min-width: 200px;">Name</th>
                            <th style="min-width: 300px;">Description</th>
                            <th style="min-width: 120px;">Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grades as $i=>$grade)
                        <tr>
                            <td>
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$grade->id}}" type="checkbox" name="checkid[]" value="{{$grade->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$grade->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                <span style="margin:0 5px;">{{$grades->currentpage()==1?$i+1:$i+($grades->perpage()*($grades->currentpage() - 1))+1}}</span>
                                @if($grade->status=='active')
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif
                            </td>
                            <td>
                                <span>{{$grade->name}}</span>
                            </td>
                            <td>
                                @php
                                    $data = json_decode($grade->description, true);
                                @endphp
                                <ul>
                                    <li>Basic Salary: {{ $data['basic_salary'] ?? 0 }}%</li>
                                    <li>House Rent: {{ $data['house_rent'] ?? 0 }}%</li>
                                    <li>Medical Allowance: {{ $data['medical_allowance'] ?? 0 }}TK</li>
                                    <li>Transport Allowance: {{ $data['transport_allowance'] ?? 0 }}TK</li>
                                    <li>Food Allowance: {{ $data['food_allowance'] ?? 0 }}TK</li>
                                    <li>Attendance Bonus: {{ $data['attendance_bonus'] ?? 0 }}TK</li>
                                    <li>Other Allowance: {{ $data['other_allowance'] ?? 0 }}TK</li>
                                    <li>Stamp Charge: {{ $data['stamp_charge'] ?? 0 }}TK</li>
                                </ul>
                            </td>
                            <td>{{$grade->created_at->format('d-m-Y')}}</td>
                            <td class="center">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditGrade_{{$grade->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{route('admin.gradesAction',['delete',$grade->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$grades->links('pagination')}}
            </div>
        </form>


    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddGrade" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.gradesAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Grade</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Name* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>

                <!-- JSON INPUTS ADDED HERE -->
                <div class="form-group">
                    <label>Basic Salary (%)</label>
                    <input type="text" class="form-control" name="json[basic_salary]" placeholder="%">
                </div>

                <div class="form-group">
                    <label>House Rent (%)</label>
                    <input type="text" class="form-control" name="json[house_rent]" placeholder="%">
                </div>

                <div class="form-group">
                    <label>Medical Allowance (TK)</label>
                    <input type="text" class="form-control" name="json[medical_allowance]" placeholder="TK">
                </div>

                <div class="form-group">
                    <label>Transport Allowance (TK)</label>
                    <input type="text" class="form-control" name="json[transport_allowance]" placeholder="TK">
                </div>

                <div class="form-group">
                    <label>Food Allowance (TK)</label>
                    <input type="text" class="form-control" name="json[food_allowance]" placeholder="TK">
                </div>

                <div class="form-group">
                    <label>Attendance Bonus (TK)</label>
                    <input type="text" class="form-control" name="json[attendance_bonus]" placeholder="TK">
                </div>

                <div class="form-group">
                    <label>Other Allowance (TK)</label>
                    <input type="text" class="form-control" name="json[other_allowance]" placeholder="TK">
                </div>

                <div class="form-group">
                    <label>Stamp Charge (TK)</label>
                    <input type="text" class="form-control" name="json[stamp_charge]" placeholder="TK">
                </div>
                <!-- END JSON INPUTS -->

    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Grade</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($grades as $i=>$dpm)
@php
$json = json_decode($dpm->description, true);
@endphp
 <div class="modal fade text-left" id="EditGrade_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.gradesAction',['update',$dpm->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Grade</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Name* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" value="{{$dpm->name?:old('name')}}" name="name" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>

                <!-- JSON INPUTS EDIT MODE -->
                <div class="form-group">
                    <label>Basic Salary (%)</label>
                    <input type="text" class="form-control" name="json[basic_salary]" value="{{$json['basic_salary'] ?? ''}}">
                </div>

                <div class="form-group">
                    <label>House Rent (%)</label>
                    <input type="text" class="form-control" name="json[house_rent]" value="{{$json['house_rent'] ?? ''}}">
                </div>

                <div class="form-group">
                    <label>Medical Allowance (TK)</label>
                    <input type="text" class="form-control" name="json[medical_allowance]" value="{{$json['medical_allowance'] ?? ''}}">
                </div>

                <div class="form-group">
                    <label>Transport Allowance (TK)</label>
                    <input type="text" class="form-control" name="json[transport_allowance]" value="{{$json['transport_allowance'] ?? ''}}">
                </div>

                <div class="form-group">
                    <label>Food Allowance (TK)</label>
                    <input type="text" class="form-control" name="json[food_allowance]" value="{{$json['food_allowance'] ?? ''}}">
                </div>

                <div class="form-group">
                    <label>Attendance Bonus (TK)</label>
                    <input type="text" class="form-control" name="json[attendance_bonus]" value="{{$json['attendance_bonus'] ?? ''}}">
                </div>

                <div class="form-group">
                    <label>Other Allowance (TK)</label>
                    <input type="text" class="form-control" name="json[other_allowance]" value="{{$json['other_allowance'] ?? ''}}">
                </div>

                <div class="form-group">
                    <label>Stamp Charge (TK)</label>
                    <input type="text" class="form-control" name="json[stamp_charge]" value="{{$json['stamp_charge'] ?? ''}}">
                </div>
                <!-- END JSON INPUTS -->

             	<div class="row">
                 	<div class="col-md-6 form-group">
                 	    <label for="name">Status</label><br>
                 	    <div class="checkbox">
                             <input class="inp-cbx" id="status_{{$dpm->id}}" type="checkbox" name="status" style="display: none;" {{$dpm->status=='active'?'checked':''}} />
                             <label class="cbx" for="status_{{$dpm->id}}">
                                 <span>
                                     <svg width="12px" height="10px" viewbox="0 0 12 10">
                                         <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                     </svg>
                                 </span>
                                 Active
                             </label>
                         </div>
                 	</div>
                    <div class="col-md-6 form-group">
                        <label for="name">Publish Date*</label>
                        <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$dpm->created_at->format('Y-m-d')}}" name="created_at" required="">
                        @if ($errors->has('created_at'))
    					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
    					@endif
                    </div>
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Grade</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach



@endsection
@push('js')
@endpush
