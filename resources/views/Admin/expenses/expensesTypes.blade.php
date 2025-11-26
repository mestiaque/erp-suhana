@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Expenses List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Expense Types</h3>
         <div class="dropdown">
            @can('expenses_type.add')
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddTypes" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> Type
             </a>
             @endcan
             <a href="{{route('admin.expensesTypes')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

        <div class="row">
            <div class="col-md-4">
                @if(auth()->user()->hasPermission('expenses_type.edit') || auth()->user()->hasPermission('expenses_type.delete'))
                <div class="input-group mb-1">
                    <select class="form-control form-control-sm rounded-0 actionSelect" name="action" required="">
                        <option value="">Select Action</option>
                        @can('expenses_type.edit')
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                        @endcan
                        @can('expenses_type.edit')
                        <option value="5">Delete</option>
                        @endcan
                    </select>
                    <button class="btn btn-sm btn-primary rounded-0 SubmitAction">Action</button>
                    @endif
                </div>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <form action="{{route('admin.expensesTypes')}}">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="input-group">
                                <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search head" class="form-control form-control-sm {{$errors->has('search')?'error':''}}" />
                                <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <form class="actionForm" action="{{route('admin.expensesTypes')}}">
            <input type="hidden" name="action" value="" class="actionInput">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                @if(auth()->user()->hasPermission('expenses_type.edit') || auth()->user()->hasPermission('expenses_type.delete'))
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
                                @else All
                                @endif
                            </th>
                            <th style="min-width: 200px;">Name</th>
                            <th style="min-width: 300px;">Description</th>
                            <th style="min-width: 120px;">Created Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $i=>$type)
                        <tr>
                            <td>
                                @if(auth()->user()->hasPermission('expenses_type.edit') || auth()->user()->hasPermission('expenses_type.delete'))
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$type->id}}" type="checkbox" name="checkid[]" value="{{$type->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$type->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                 @endif
                                <span style="margin:0 5px;">{{$categories->currentpage()==1?$i+1:$i+($categories->perpage()*($categories->currentpage() - 1))+1}}</span>
                                @if($type->status=='active')
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
                                <span>{{$type->name}}</span>
                            </td>
                            <td>
                                <span>{!!$type->description!!}</span>
                            </td>
                            <td>{{$type->created_at->format('d.m.Y')}}</td>
                            <td class="text-center">
                                @if(auth()->user()->hasPermission('expenses_type.edit') || auth()->user()->hasPermission('expenses_type.view'))
                                @can('expenses_type.edit')
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditType_{{$type->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endcan
                                @can('expenses_type.delete')
                                <a href="{{route('admin.expensesTypesAction',['delete',$type->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                @endcan
                                @else --  @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$categories->links('pagination::bootstrap-4')}}
            </div>
        </form>


    </div>
</div>
</div>


<!-- Add Modal -->
 <div class="modal fade text-left" id="AddTypes" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	    <form action="{{route('admin.expensesTypesAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Type</h4>
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
    			<div class="form-group">
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description"></textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
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

<!--Edit Modal -->
@foreach($categories as $i=>$dpm)
 <div class="modal fade text-left" id="EditType_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.expensesTypesAction',['update',$dpm->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Type</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Title* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" value="{{$dpm->name?:old('name')}}" name="name" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
    			 <div class="form-group">
    				<label for="name">Description</label>
					<textarea name="description" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{!!$dpm->description!!}</textarea>
					@if ($errors->has('description'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
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
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Type</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach



@endsection
@push('js')
<script>
    $(document).ready(function(){
        $('.actionSelect').on('click',function(){
            var action = $(this).val();
            $('.actionInput').val(action);
        });

        $('.SubmitAction').on('click',function(){
            var action = $('.actionInput').val();
            if(action==''){
                alert('Please select any action');
                return false;
            }
            var checked = $('input[name="checkid[]"]:checked').length;
            if(checked<=0){
                alert('Please select at least one checkbox');
                return false;
            }

             var url = "{{ route('admin.expensesTypes') }}?action=" + action;
            $('.actionForm').attr('action', url);
            $('.actionForm').submit();
        });

    });
</script>
@endpush
