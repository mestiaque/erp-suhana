@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Items of Goods')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">


<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Items of Goods</h3>
         <div class="dropdown">
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddItem" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> Item
             </a>
             <a href="{{route('admin.servicesAction','import')}}" class="btn-custom info"  style="padding:5px 15px;">
                 <i class="bx bx-download"></i> Import
             </a>
             <a href="{{route('admin.services')}}" class="btn-custom yellow">
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
                    <form action="{{route('admin.services')}}">
                        <div class="row">
                            <div class="col-md-4 mb-0">
                                <select class="form-control {{$errors->has('category')?'error':''}}" name="category">
                                    <option value="">Select category</option>
                                    @foreach(App\Models\Attribute::where('type',6)->where('parent_id',null)->get(['id','name']) as $ctg)
                                    <option value="{{$ctg->id}}" >{{$ctg->name}}</option>
                                        @foreach($ctg->subCtgs as $ctg)
                                        <option value="{{$ctg->id}}" >- {{$ctg->name}}</option>
                                            @foreach($ctg->subCtgs as $ctg)
                                                <option value="{{$ctg->id}}" >-- {{$ctg->name}}</option>
                                                    @foreach($ctg->subCtgs as $ctg)
                                                        <option value="{{$ctg->id}}" >--- {{$ctg->name}}</option>
                                                    @endforeach
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 mb-0">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Item Name" class="form-control {{$errors->has('search')?'error':''}}" />
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
        <form action="{{route('admin.services')}}">
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
                        <li><a href="{{route('admin.services')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.services',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                        <li><a href="{{route('admin.services',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
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
                            <th style="min-width: 150px;width:150px">Price</th>
                            <th style="min-width: 100px;width:100px;">Unit</th>
                            <th style="min-width: 150px;width:150px;">Category</th>
                            <th style="min-width: 120px;width:120px;">Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $i=>$service)
                        <tr>
                            <td>
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$service->id}}" type="checkbox" name="checkid[]" value="{{$service->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$service->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                <span style="margin:0 5px;">{{$services->currentpage()==1?$i+1:$i+($services->perpage()*($services->currentpage() - 1))+1}}</span>
                                @if($service->status=='active')
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
                                <span>{{$service->name}}</span>
                            </td>
                            <td>{{$service->item_price}}</td>
                            <td>
                                <span>{{$service->unit?$service->unit->name:'No Unit'}}</span>
                            </td>

                            <td>
                                <span>{{$service->category?$service->category->name:'No Category'}}</span>
                            </td>
                            <td>{{$service->created_at->format('d-m-Y')}}</td>
                            <td class="center">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditItem_{{$service->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{route('admin.servicesAction',['delete',$service->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$services->links('pagination')}}
            </div>
        </form>


    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddItem" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.servicesAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Item</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Item Name* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
             	<div class="row">
                 	<div class="form-group col-md-7">
                 	    <label for="short_name">Item Price </label>
                        <input type="number" class="form-control {{$errors->has('price')?'error':''}}" step="any" value="{{old('price')}}" name="price" placeholder="Enter Price">
        				@if ($errors->has('price'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('price') }}</p>
        				@endif
                 	</div>
                 	<div class="form-group col-md-5">
        			    <label for="unit">Item Unit</label>
                        <select class="form-control {{$errors->has('unit')?'error':''}}" name="unit">
                            <option value="">Select Unit</option>
                            @foreach(App\Models\PostExtra::where('type',1)->get(['id','name']) as $unit)
                            <option value="{{$unit->id}}" >{{$unit->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('unit'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('unit') }}</p>
        				@endif
                 	</div>
             	</div>
    			 <div class="row">
                 	<div class="col-md-6 form-group">
                 	    <label for="name">Status</label><br>
                 	    <div class="checkbox">
                             <input class="inp-cbx" id="status" type="checkbox" name="status" style="display: none;" checked="" />
                             <label class="cbx" for="status">
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
                        <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" name="created_at" required="">
                        @if ($errors->has('created_at'))
    					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
    					@endif
                    </div>
             	</div>
             	<div class="form-group">
    			    <label for="category">Item Category</label>
                    <select class="form-control {{$errors->has('category')?'error':''}}" name="category">
                        <option value="">Select category</option>
                        @foreach(App\Models\Attribute::where('type',6)->where('parent_id',null)->get(['id','name']) as $ctg)
                        <option value="{{$ctg->id}}" >{{$ctg->name}}</option>
                            @foreach($ctg->subCtgs as $ctg)
                            <option value="{{$ctg->id}}" >- {{$ctg->name}}</option>
                                @foreach($ctg->subCtgs as $ctg)
                                    <option value="{{$ctg->id}}" >-- {{$ctg->name}}</option>
                                        @foreach($ctg->subCtgs as $ctg)
                                            <option value="{{$ctg->id}}" >--- {{$ctg->name}}</option>
                                        @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
    				@if ($errors->has('category'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('category') }}</p>
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
@foreach($services as $i=>$dpm)
 <div class="modal fade text-left" id="EditItem_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.servicesAction',['update',$dpm->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Item</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="name">Item Name* </label>
                    <input type="text" class="form-control {{$errors->has('name')?'error':''}}" value="{{$dpm->name?:old('name')}}" name="name" placeholder="Enter Name" required="">
    				@if ($errors->has('name'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
    				@endif
             	</div>
             	<div class="row">
                 	<div class="form-group col-md-7">
                 	    <label for="short_name">Item Price </label>
                        <input type="number" class="form-control {{$errors->has('price')?'error':''}}" step="any" value="{{$dpm->item_price?:old('price')}}" name="price" placeholder="Enter Price">
        				@if ($errors->has('price'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('price') }}</p>
        				@endif
                 	</div>
                 	<div class="form-group col-md-5">
        			    <label for="unit">Item Unit</label>
                        <select class="form-control {{$errors->has('unit')?'error':''}}" name="unit">
                            <option value="">Select Unit</option>
                            @foreach(App\Models\PostExtra::where('type',1)->get(['id','name']) as $unit)
                            <option value="{{$unit->id}}" {{$dpm->unit_id==$unit->id?'selected':''}} >{{$unit->name}}</option>
                            @endforeach
                        </select>
        				@if ($errors->has('unit'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('unit') }}</p>
        				@endif
                 	</div>
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
             	<div class="form-group">
    			    <label for="category">Item Category</label>
                    <select class="form-control {{$errors->has('category')?'error':''}}" name="category">
                        <option value="">Select category</option>
                        @foreach(App\Models\Attribute::where('type',6)->where('parent_id',null)->get(['id','name']) as $ctg)
                        <option value="{{$ctg->id}}" {{$dpm->category_id==$ctg->id?'selected':''}} >{{$ctg->name}}</option>
                            @foreach($ctg->subCtgs as $ctg)
                            <option value="{{$ctg->id}}" {{$dpm->category_id==$ctg->id?'selected':''}} >- {{$ctg->name}}</option>
                                @foreach($ctg->subCtgs as $ctg)
                                    <option value="{{$ctg->id}}" {{$dpm->category_id==$ctg->id?'selected':''}} >-- {{$ctg->name}}</option>
                                        @foreach($ctg->subCtgs as $ctg)
                                            <option value="{{$ctg->id}}" {{$dpm->category_id==$ctg->id?'selected':''}} >--- {{$ctg->name}}</option>
                                        @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
    				@if ($errors->has('category'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('category') }}</p>
    				@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Item</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach

@endsection @push('js') @endpush
