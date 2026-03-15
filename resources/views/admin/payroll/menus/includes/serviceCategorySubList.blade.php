@foreach($subcategories as $subctg)
<option value="{{$subctg->id}}"> {{Str::repeat("-",$i)}} {{$subctg->name}}</option>

@if($subctg->subctgs->count() >0)
  @include(adminTheme().'menus.includes.serviceCategorySubList',['subcategories' => $subctg->subctgs,'i'=>$i+1])
@endif

@endforeach