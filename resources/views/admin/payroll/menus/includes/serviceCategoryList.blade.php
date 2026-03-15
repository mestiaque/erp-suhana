<div class="card-header" style="padding: 8px 15px; border: 1px solid #00b5b8; margin: 2px 0;" data-toggle="collapse" href="#accordion4" aria-expanded="false" aria-controls="accordion4">
    <a class="card-title lead collapsed" href="#" style="font-size: 14px;">Service Categories</a>
</div>
<div id="accordion4" style="border: 1px solid #00b5b8;" role="tabpanel" data-parent="#accordionWrapa1" class="collapse" aria-expanded="false">
    <div class="card-content">
        <div class="card-body" style="padding:10px;">
            <form action="{{route('admin.menusItemsPost',$menu->id)}}" method="post">
                @csrf
                <input type="hidden" name="parent" value="{{$parent->id}}" />
                <div class="form-group" style="margin-bottom: 5px;">
                    <label for="name">Select Categories</label>
                    <select data-placeholder="Select Categories..." name="productCategories[]" class="select2 form-control" multiple="multiple" required="">
                        @foreach($productCategories as $i=>$productCtg)
                        <option value="{{$productCtg->id}}">{{$productCtg->name}}</option>
                        
                        @if($productCtg->subctgs->count() >0) @include(adminTheme().'menus.includes.serviceCategorySubList',['subcategories' => $productCtg->subctgs,'i'=>1]) @endif
                        
                        @endforeach
                    </select>
                </div>
                @isset(json_decode(Auth::user()->permission->permission, true)['menus']['add'])
                <button type="submit" class="btn btn-sm btn-block btn-primary" style="padding:10px;" onclick="return confirm('Are You Want To Add?')"><i class="fa fa-plus"></i> Add</button>
                @endisset
            </form>
        </div>
    </div>
</div>