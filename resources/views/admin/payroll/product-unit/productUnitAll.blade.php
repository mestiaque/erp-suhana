@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Product Unit List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Product Unit List</h3>
         <div class="dropdown">
             <a href="javascript:void(0)" class="btn-custom primary" data-toggle="modal" data-target="#AddUnit" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i>  Unit
             </a>
             <a href="{{route('admin.productUnits')}}" class="btn-custom yellow">
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
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productUnits as $i=>$unit)
                        <tr>
                            <td>
                                <span style="margin:0 5px;">{{$productUnits->currentpage()==1?$i+1:$i+($productUnits->perpage()*($productUnits->currentpage() - 1))+1}}</span>
                            </td>
                            <td>
                                <span>{{$unit->name}}</span>
                            </td>
                            <td class="center">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#EditUnit_{{$unit->id}}" class="btn-custom success">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="{{route('admin.productUnitsAction',['delete',$unit->id])}}" class="btn-custom danger" onclick="return confirm('Are You Want To Delete?')"><i class="bx bx-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$productUnits->links('pagination')}}
            </div>
        
        
    </div>
</div>
</div>

<!-- Add Modal -->
 <div class="modal fade text-left" id="AddUnit" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.productUnitsAction','create')}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Add Product Unit</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="unit">Unit* </label>
                    <input type="text" class="form-control {{$errors->has('unit')?'error':''}}" name="unit" placeholder="Enter Unit" required="">
    				@if ($errors->has('unit'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('unit') }}</p>
    				@endif
             	</div>
    	   </div>
    	   <div class="modal-footer">
    		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
    		 <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Add Product Unit</button>
    	   </div>
	   </form>
	 </div>
   </div>
 </div>

<!--Edit Modal -->
@foreach($productUnits as $i=>$dpm)
 <div class="modal fade text-left" id="EditUnit_{{$dpm->id}}" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 <form action="{{route('admin.productUnitsAction',['update',$dpm->id])}}" method="post">
	   	  @csrf
    	   <div class="modal-header">
    		 <h4 class="modal-title">Edit Product Unit</h4>
    		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    		   <span aria-hidden="true">&times; </span>
    		 </button>
    	   </div>
    	   <div class="modal-body">
    	   		<div class="form-group">
    			    <label for="unit">Unit* </label>
                    <input type="text" class="form-control {{$errors->has('unit')?'error':''}}" value="{{$dpm->name?:old('unit')}}" name="unit" placeholder="Enter Unit" required="">
    				@if ($errors->has('unit'))
    				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('unit') }}</p>
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