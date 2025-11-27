@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Purchases Items List')}}</title>
@endsection @push('css')
<style type="text/css">
    .loadImg {
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
        text-align: center;
        padding-top: 100px;
    }

    .loadImg img {
        max-width: 100px;
    }

</style>
@endpush @section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Purchases Items List</h3>
         <div class="dropdown">
            @can('purchases_items_units.list')
             <a href="javascript:void(0)" class="btn-custom success" data-toggle="modal" data-target="#Units" style="padding:5px 15px;">
                 <i class="bx bx-list"></i> Units
             </a>
             @endcan
             @can('purchases_items_categories.list')
             <a href="javascript:void(0)" class="btn-custom info" data-toggle="modal" data-target="#Categories" style="padding:5px 15px;">
                 <i class="bx bx-list"></i> Categories
             </a>
             @endcan
             @can('purchases_items.add')
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddItem" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> items
             </a>
             @endcan
             <a href="{{route('admin.purchasesItems')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.purchasesItems')}}">
            <div class="row">
                <div class="col-md-12 mb-0">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Item Name" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.purchasesItems')}}">
            <div class="row">
                <div class="col-md-4">
                    @if(can('purchases_items.edit') || can('purchases_items.delete'))
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            @can('purchases_items.edit')
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                            @endcan
                            @can('purchases_items.delete')
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
                        <li><a href="{{route('admin.purchasesItems')}}">All ({{$report->total}})</a></li>
                        <li><a href="{{route('admin.purchasesItems',['status'=>'active'])}}">Active ({{$report->active}})</a></li>
                        <li><a href="{{route('admin.purchasesItems',['status'=>'inactive'])}}">Inactive ({{$report->inactive}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                @if(can('purchases_items.edit') || can('purchases_items.delete'))
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
                            <th style="min-width: 200px;">Name</th>
                            <th style="min-width: 100px;">Unit</th>
                            <th style="min-width: 150px;">Category</th>
                            <th style="min-width: 300px;">Description</th>
                            <th style="min-width: 120px;">Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($goodsItems as $i=>$designation)
                        <tr>
                            <td style="position:relative;">
                                @if(can('purchases_items.edit') || can('purchases_items.delete'))
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$designation->id}}" type="checkbox" name="checkid[]" value="{{$designation->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$designation->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                 @endif
                                <span style="margin:0 5px;">{{$goodsItems->currentpage()==1?$i+1:$i+($goodsItems->perpage()*($goodsItems->currentpage() - 1))+1}}</span>
                                @if($designation->status=='active')
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
                                <span>{{$designation->name}}</span>
                            </td>
                            <td>
                                <span>{{$designation->unit?$designation->unit->name:'--'}}</span>
                            </td>
                            <td>
                                <span>{{$designation->category?$designation->category->name:'--'}}</span>
                            </td>
                            <td>
                                <span>{!!$designation->description!!}</span>
                            </td>
                            <td>{{$designation->created_at->format('d-m-Y')}}</td>
                            <td class="text-center">
                                @if(can('purchases_items.edit') || can('purchases_items.delete'))
                                @can('purchases_items.edit')
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditDesignations_{{$designation->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endcan
                                @can('purchases_items.delete')
                                <a href="{{route('admin.purchasesItemsAction',['delete',$designation->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                                @endcan
                            </td>
                            @else -- @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$goodsItems->links('pagination')}}
            </div>
        </form>


    </div>
</div>
</div>

<!-- Unit Modal -->
<div class="modal fade text-left" id="Units" tabindex="-1" data-backdrop="static" role="dialog">
   <div class="modal-dialog" role="document">
	    <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Units List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times; </span>
                </button>
            </div>
            <div class="modal-body">
                @can('purchases_items_units.add')
                <div class="addUnit">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control mr-2 {{$errors->has('name')?'error':''}} addUnitInput" name="name" placeholder="Enter Unit Name" required="">
                        <button type="submit" data-url="{{route('admin.purchasesItemsAction','addUnit')}}" class="btn btn-primary addUnitBtn"><i class="bx bx-plus"></i> Add Unit</button>
                    </div>
                </div>
                @endcan
                <div class="UnitsTableArea" style="position:relative;">
                    <div class="table-responsive UnitsTable" style="min-height:300px;">
                        @include(adminTheme().'purchases-items.includes.unitsTable')
                    </div>
                </div>
            </div>
	    </div>
    </div>
</div>

 <!-- Category Modal -->
 <div class="modal fade text-left" id="Categories" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Categories List</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times; </span>
            </button>
        </div>
        <div class="modal-body">
            @can('purchases_items_categories.add')
            <div class="addCaterogy">
                <div class="input-group mb-2">
                    <input type="text" class="form-control mr-2 {{$errors->has('name')?'error':''}} addCtgInput" name="name" placeholder="Enter Category Name" required="">
                    <button type="submit" data-url="{{route('admin.purchasesItemsAction','addCtg')}}" class="btn btn-primary addCtgBtn"><i class="bx bx-plus"></i> Add Category</button>
                </div>
            </div>
            @endcan
            <div class="CtgTableArea" style="position:relative;">
                <div class="table-responsive CtgTable" style="min-height:300px;">
                    @include(adminTheme().'purchases-items.includes.categoryTable')
                </div>
            </div>
        </div>
	 </div>
   </div>
 </div>

<!-- Add Modal -->
<div class="modal fade text-left" id="AddItem" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
	    <div class="modal-content">
            <form action="{{route('admin.purchasesItemsAction','create')}}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add Item</h4>
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
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="unit">Unit </label>
                                <select class="form-control {{$errors->has('unit_id')?'error':''}}" name="unit_id">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                    <option value="{{$unit->id}}">{{$unit->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('unit_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('unit_id') }}</p>
                                @endif
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="category">Category</label>
                                <select class="form-control {{$errors->has('category_id')?'error':''}}" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('category_id'))
                                <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('category_id') }}</p>
                                @endif
                            </div>
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
                    <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Item</button>
                </div>
            </form>
	    </div>
    </div>
</div>

<!--Edit Modal -->
@foreach($goodsItems as $i=>$dpm)
 <div class="modal fade text-left" id="EditDesignations_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.purchasesItemsAction',['update',$dpm->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Item</h4>
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
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="unit">Unit </label>
                        <select class="form-control {{$errors->has('unit_id')?'error':''}}" name="unit_id">
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                            <option value="{{$unit->id}}" {{$unit->id==$dpm->unit_id?'selected':''}} >{{$unit->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('unit_id'))
                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('unit_id') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="category">Category</label>
                        <select class="form-control {{$errors->has('category_id')?'error':''}}" name="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{$category->id}}" {{$category->id==$dpm->category_id?'selected':''}} >{{$category->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('category_id'))
                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('category_id') }}</p>
                        @endif
                    </div>
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
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update</button>
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
        let reloadAfterClose = false;

        $('#Units').on('hidden.bs.modal', function () {
            if (reloadAfterClose) {
                location.reload();
            }
            reloadAfterClose = false;
        });

        $('#Categories').on('hidden.bs.modal', function () {
            if (reloadAfterClose) {
                location.reload();
            }
            reloadAfterClose = false;
        });

        // Add Unit
        $(document).on('click', '.editUnit', function () {
            let url = $(this).data('url');
            let tr = $(this).closest('tr');
            let name = tr.find('.unitName').text();
            $('.addUnitInput').val(name);
            $('.addUnitBtn')
                .data('update', url)
                .removeClass('addUnitBtn')
                .addClass('updateUnitBtn')
                .text('Update');
        });

        $(document).on('click', '.updateUnitBtn', function () {
            var url =$(this).data('update');
            var unitName = $('.addUnitInput').val();
            if(unitName==''){
                alert('Please Enter Unit Name');
                return false;
            }

            $('.updateUnitBtn').prop('disabled',true);
            $('.UnitsTableArea').prepend('<div class="loadImg"><img src="{{asset('medies/loading.gif')}}"></div>');
            $.ajax({
                url:url,
                data:{name:unitName},
                success:function(data){
                    $('.loadImg').remove();
                    if(data.status=='success'){
                        reloadAfterClose = true;
                        $('.UnitsTable').empty().append(data.view);
                        $('.addUnitInput').val('');
                    }else{
                        alert(data.message?data.message:'Error Occurred! Please Try Again.');
                    }
                    $('.updateUnitBtn').prop('disabled',false);

                    $('.updateUnitBtn')
                    .data('update', '')
                    .removeClass('updateUnitBtn')
                    .addClass('addUnitBtn')
                    .text('Add Unit');

                }
            });

        });

        $(document).on('click','.deleteUnit', function(){
            var url =$(this).data('url');

            if(!confirm('Are You Want To Delete?')){
                return false;
            }
            $('.UnitsTableArea').prepend('<div class="loadImg"><img src="{{asset('medies/loading.gif')}}"></div>');
            $.ajax({
                url:url,
                success:function(data){
                    $('.loadImg').remove();
                    if(data.status=='success'){
                        reloadAfterClose = true;
                        $('.UnitsTable').empty().append(data.view);
                    }else{
                        alert('Error Occurred! Please Try Again.');
                    }
                }
            });
        });

        $(document).on('click', '.addUnitBtn', function () {
            var url =$(this).data('url');
            var unitName = $('.addUnitInput').val();
            if(unitName==''){
                alert('Please Enter Unit Name');
                return false;
            }
            $('.addUnitBtn').prop('disabled',true);
            $('.UnitsTableArea').prepend('<div class="loadImg"><img src="{{asset('medies/loading.gif')}}"></div>');
            $.ajax({
                url:url,
                data:{name:unitName},
                success:function(data){
                    $('.loadImg').remove();
                    if(data.status=='success'){
                        reloadAfterClose = true;
                        $('.UnitsTable').empty().append(data.view);
                        $('.addUnitInput').val('');
                    }else{
                        alert(data.message?data.message:'Error Occurred! Please Try Again.');
                    }
                },
                error(){
                    alert('Error Occurred! Please Try Again.');
                },
                complete:function(){
                    $('.addUnitBtn').prop('disabled',false);
                }
            });
        });

        // Add Category
        $(document).on('click', '.addCtgBtn', function () {
            var url =$(this).data('url');
            var ctgName = $('.addCtgInput').val();
            if(ctgName==''){
                alert('Please Enter Unit Name');
                return false;
            }
            $('.addCtgBtn').prop('disabled',true);
            $('.CtgTableArea').prepend('<div class="loadImg"><img src="{{asset('medies/loading.gif')}}"></div>');
            $.ajax({
                url:url,
                data:{name:ctgName},
                success:function(data){
                    $('.loadImg').remove();
                    if(data.status=='success'){
                        reloadAfterClose = true;
                        $('.CtgTable').empty().append(data.view);
                        $('.addCtgInput').val('');
                    }else{
                        alert(data.message?data.message:'Error Occurred! Please Try Again.');
                    }
                },
                error(){
                    alert('Error Occurred! Please Try Again.');
                },
                complete:function(){
                    $('.addCtgBtn').prop('disabled',false);
                }
            });
        });

        $(document).on('click', '.editCtg', function () {
            let url = $(this).data('url');
            let tr = $(this).closest('tr');
            let name = tr.find('.ctgName').text();
            $('.addCtgInput').val(name);
            $('.addCtgBtn')
                .data('update', url)
                .removeClass('addCtgBtn')
                .addClass('updateCtgBtn')
                .text('Update');
        });

        $(document).on('click', '.updateCtgBtn', function () {
            var url =$(this).data('update');
            var ctgName = $('.addCtgInput').val();
            if(ctgName==''){
                alert('Please Enter Category Name');
                return false;
            }

            $('.updateCtgBtn').prop('disabled',true);
            $('.CtgTableArea').prepend('<div class="loadImg"><img src="{{asset('medies/loading.gif')}}"></div>');
            $.ajax({
                url:url,
                data:{name:ctgName},
                success:function(data){
                    $('.loadImg').remove();
                    if(data.status=='success'){
                        reloadAfterClose = true;
                        $('.CtgTable').empty().append(data.view);
                        $('.addCtgInput').val('');
                    }else{
                        alert(data.message?data.message:'Error Occurred! Please Try Again.');
                    }
                    $('.updateCtgBtn').prop('disabled',false);

                    $('.updateCtgBtn')
                    .data('update', '')
                    .removeClass('updateCtgBtn')
                    .addClass('addCtgBtn')
                    .text('Add Category');

                }
            });

        });

        $(document).on('click','.deleteCtg', function(){
            var url =$(this).data('url');

            if(!confirm('Are You Want To Delete?')){
                return false;
            }
            $('.CtgTableArea').prepend('<div class="loadImg"><img src="{{asset('medies/loading.gif')}}"></div>');
            $.ajax({
                url:url,
                success:function(data){
                    $('.loadImg').remove();
                    if(data.status=='success'){
                        reloadAfterClose = true;
                        $('.CtgTable').empty().append(data.view);
                    }else{
                        alert('Error Occurred! Please Try Again.');
                    }
                }
            });
        });



    });

</script>

@endpush

