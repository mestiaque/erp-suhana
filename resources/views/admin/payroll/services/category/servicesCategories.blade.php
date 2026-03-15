@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Category List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Product Category List</h3>
         <div class="dropdown">
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddUnit" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i>  Category
             </a>
             <a href="{{route('admin.productCategory')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;">SL</th>
                            <th style="min-width: 300px;">Name</th>
                            <th style="min-width: 200px;">Parent</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $i=>$unit)
                        <tr>
                            <td>
                                <span style="margin:0 5px;">{{$categories->currentpage()==1?$i+1:$i+($categories->perpage()*($categories->currentpage() - 1))+1}}</span>
                            </td>
                            <td>
                                <span>{{$unit->name}}</span>
                            </td>
                            <td>
                                @if($unit->parent)
                                {{$unit->parent->name}}
                                @else
                                <span class="badge badge-info">Parent</span>
                                @endif
                            </td>
                            <td class="center">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditUnit_{{$unit->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{route('admin.productCategoryAction',['delete',$unit->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$categories->links('pagination')}}
            </div>
        
        
    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddUnit" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.productCategoryAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Product Category</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="unit">Title* </label>
                    <input type="text" class="form-control {{$errors->has('title')?'error':''}}" name="title" placeholder="Enter title" required="">
    				@if ($errors->has('title'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
    				@endif
             	</div>
    	   		<div class="form-group">
    			    <label for="unit">Parent Ctg </label>
                    <select name="parent_id" class="form-control">
                        <option value="">Select Category</option>
                        @foreach($parents as $parent) 
                        <option value="{{$parent->id}}" >{{$parent->name}}</option>
                        @if($parent->subctgs->count() > 0) @include('admin.services.includes.editSubcategory',['subcategories' =>$parent->subctgs,'category'=>null, 'i'=>1]) @endif @endforeach
                    </select>
                    @if ($errors->has('parent_id'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('parent_id') }}</p>
                    @endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Product Category</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($categories as $i=>$category)
 <div class="modal fade text-left" id="EditUnit_{{$category->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.productCategoryAction',['update',$category->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Product Category</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="title">Title* </label>
                    <input type="text" class="form-control {{$errors->has('title')?'error':''}}" value="{{$category->name?:old('title')}}" name="title" placeholder="Enter title" required="">
    				@if ($errors->has('title'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
    				@endif
             	</div>
             	<div class="form-group">
                    <label for="parent_id">Parent Category</label>
                    <select name="parent_id" class="form-control">
                        <option value="">Select Category</option>

                        @foreach($parents as $parent) @if($parent->id==$category->id) @else
                        <option value="{{$parent->id}}" {{$parent->id==$category->parent_id?'selected':''}}>{{$parent->name}}</option>
                        @if($parent->subctgs->count() > 0) @include('admin.services.includes.editSubcategory',['subcategories' =>$parent->subctgs,'category'=>$category, 'i'=>1]) @endif @endif @endforeach
                    </select>
                    @if ($errors->has('parent_id'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('parent_id') }}</p>
                    @endif
                </div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Update Product Unit</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endforeach


@endsection @push('js') @endpush
