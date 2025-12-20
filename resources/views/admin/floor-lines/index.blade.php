@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Floor/Lines List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Floor/Lines List</h3>
            <div class="dropdown">
                @can('designations.add')
                <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddLine" style="padding:5px 15px;">
                    <i class="bx bx-plus"></i> Line
                </a>
                @endcan
                <a href="{{route('admin.floorLines')}}" class="btn-custom yellow">
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
                        <form action="{{route('admin.floorLines')}}">
                            <div class="row">
                                <div class="col-md-12 mb-0">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Floor/Lines Name" class="form-control {{$errors->has('search')?'error':''}}" />
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
            <form action="{{route('admin.floorLines')}}">
                <div class="row">
                    <div class="col-md-4">
                        @if(auth()->user()->hasPermission('designations.edit')  || auth()->user()->hasPermission('designations.delete'))
                        <div class="input-group mb-1">
                            <select class="form-control form-control-sm rounded-0" name="action" required="">
                                <option value="">Select Action</option>
                                @can('designations.edit')
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                @endcan
                                @can('designations.delete')
                                <option value="5">Delete</option>
                                @endcan
                            </select>
                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <ul class="statuslist">
                            <li><a href="{{route('admin.floorLines')}}">All ({{$total->total}})</a></li>
                            <li><a href="{{route('admin.floorLines',['status'=>'active'])}}">Active ({{$total->active}})</a></li>
                            <li><a href="{{route('admin.floorLines',['status'=>'inactive'])}}">Inactive ({{$total->inactive}})</a></li>
                        </ul>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="min-width: 100px;width: 100px;padding-right:0;">
                                    @if(auth()->user()->hasPermission('designations.edit')  || auth()->user()->hasPermission('designations.delete'))
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
                                    @else All @endif
                                </th>
                                <th style="min-width: 200px;">Floor</th>
                                <th style="min-width: 200px;">Line</th>
                                <th style="min-width: 200px;width:200px;">Capacity P/H</th>
                                <th style="min-width: 100px;width:100px;">Date</th>
                                <th style="min-width: 100px;width:100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lines as $i=>$line)
                            <tr>
                                <td>
                                    @if(auth()->user()->hasPermission('designations.edit')  || auth()->user()->hasPermission('designations.delete'))
                                        <div class="checkbox">
                                            <input class="inp-cbx" id="cbx_{{$line->id}}" type="checkbox" name="checkid[]" value="{{$line->id}}" style="display: none;" />
                                            <label class="cbx" for="cbx_{{$line->id}}">
                                                <span>
                                                    <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                    </svg>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                    <span style="margin:0 5px;">{{$lines->currentpage()==1?$i+1:$i+($lines->perpage()*($lines->currentpage() - 1))+1}}</span>
                                    @if($line->status=='active')
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
                                    <span>{{$line->name}}</span>
                                </td>
                                <td>{{$line->slug}}</td>
                                <td>{{$line->capacity}}</td>
                                <td>{{$line->created_at->format('d.m.Y')}}</td>
                                <td class="text-center">
                                    @if(auth()->user()->hasPermission('designations.edit')  || auth()->user()->hasPermission('designations.delete'))
                                    @can('designations.edit')
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#EditLine_{{$line->id}}" class="btn-custom success">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    @endcan
                                    @can('designations.delete')
                                    <a href="{{route('admin.floorLinesAction',['delete',$line->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                    @endcan
                                    @else -- @endif

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$lines->links('pagination')}}
                </div>
            </form>


        </div>
    </div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddLine" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.floorLinesAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Line</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Floor* </label>
                        <select class="form-control" name="floor">
                            <option value="">Select Floor</option>
                            <option value="Floor 1">Floor 1</option>
                            <option value="Floor 2">Floor 2</option>
                        </select>
                        @if ($errors->has('floor'))
                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('floor') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="name">Line Name* </label>
                        <input type="text" class="form-control {{$errors->has('line')?'error':''}}" value="{{old('line')}}" name="line" placeholder="Enter line" required="">
                        @if ($errors->has('floor'))
                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('floor') }}</p>
                        @endif
                    </div>
                </div>
    	   		
    	   		<div class="form-group">
    			    <label for="name">Line Capacity Per/Hour* </label>
                    <input type="number" class="form-control {{$errors->has('capacity')?'error':''}}" value="{{old('capacity')}}" name="capacity" placeholder="Enter capacity" required="">
    				@if ($errors->has('capacity'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('capacity') }}</p>
    				@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Line</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($lines as $i=>$dpm)
 <div class="modal fade text-left" id="EditLine_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.floorLinesAction',['update',$dpm->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Line</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Floor* </label>
                        <select class="form-control" name="floor">
                            <option value="">Select Floor</option>
                            <option value="Floor 1" {{$dpm->name=='Floor 1'?'selected':''}}>Floor 1</option>
                            <option value="Floor 2" {{$dpm->name=='Floor 2'?'selected':''}}>Floor 2</option>
                        </select>
                        @if ($errors->has('floor'))
                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('floor') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="name">Line Name* </label>
                        <input type="text" class="form-control {{$errors->has('line')?'error':''}}" value="{{$dpm->slug?:old('line')}}" name="line" placeholder="Enter line" required="">
                        @if ($errors->has('floor'))
                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('floor') }}</p>
                        @endif
                    </div>
                </div>
    	   		
    	   		<div class="form-group">
    			    <label for="name">Line Capacity Per/Hour* </label>
                    <input type="number" class="form-control {{$errors->has('capacity')?'error':''}}" value="{{$dpm->capacity?:old('capacity')}}" name="capacity" placeholder="Enter capacity" required="">
    				@if ($errors->has('capacity'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('capacity') }}</p>
    				@endif
             	</div>
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
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Line</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach



@endsection @push('js') @endpush

