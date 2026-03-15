<div class="card-header" style="padding: 8px 15px; border: 1px solid #00b5b8; margin-top: 3px;" data-toggle="collapse" href="#accordion3" aria-expanded="false" aria-controls="accordion3">
    <a class="card-title lead collapsed" href="#" style="font-size: 14px;">Post Categories</a>
</div>
<div id="accordion3" style="border: 1px solid #00b5b8;border-top: none;" role="tabpanel" data-parent="#accordionWrapa1" class="collapse" aria-expanded="false">
    <div class="card-content">
        <div class="card-body" style="padding:10px;">
            <form action="{{route('admin.menusItemsPost',$menu->id)}}" method="post">
                @csrf
                <input type="hidden" name="parent" value="{{$parent->id}}" />
                <div class="form-group" style="margin-bottom: 5px;">
                    <label for="name">Select Categories</label>
                    <select data-placeholder="Select Categories..." name="blogCategories[]" class="select2 form-control" multiple="multiple" required="">
                        @foreach($blogCategories as $i=>$bctg)
                        <option value="{{$bctg->id}}">{{$bctg->name}}</option>
                        @if($bctg->subctgs->count() >0) @include(adminTheme().'menus.includes.postCategorySubList',['subcategories' => $bctg->subctgs,'i'=>1]) @endif
                        @endforeach
                    </select>
                </div>
                @isset(json_decode(Auth::user()->permission->permission, true)['menus']['add'])
                <button type="submit" class="btn btn-sm btn-block btn-primary" style="padding:10px;"><i class="fa fa-plus"></i> Add</button>
                @endisset
            </form>
        </div>
    </div>
</div>