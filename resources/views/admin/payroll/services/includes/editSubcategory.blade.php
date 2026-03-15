@foreach($subcategories as $subCategory)

@if($category && $subCategory->id==$category->id) 

@else
<option value="{{$subCategory->id}}"
@if($category)
{{$subCategory->id==$category->parent_id?'selected':''}}
@endif
>{{str_repeat('-',$i)}} {{$subCategory->name}}</option>

@if($subCategory->subctgs->count() > 0)
@include('admin.services.includes.editSubcategory',['subcategories' =>$subCategory->subctgs,'i'=>$i+1])
@endif

@endif
@endforeach