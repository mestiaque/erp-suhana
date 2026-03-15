<div id="heading1" class="card-header" style="padding: 8px 15px; border: 1px solid #00b5b8; margin-top: 3px;" role="tab" data-toggle="collapse" href="#Customlink" aria-expanded="false" aria-controls="Customlink">
    <a class="card-title lead collapsed" href="#" style="font-size: 14px;">Custom Link</a>
</div>
<div id="Customlink" style="border: 1px solid #00b5b8;border-top: none;" role="tabpanel" data-parent="#accordionWrapa1" aria-labelledby="heading1" class="collapse">
    <div class="card-content menus-items">
        <form action="{{route('admin.menusItemsPost',$menu->id)}}" method="post">
            @csrf
            <input type="hidden" name="parent" value="{{$parent->id}}" />
            <div class="card-body" style="padding:10px;">
                <div class="form-group" style="margin-bottom: 5px;">
                    <label for="menuname">Menu Name</label>
                    <input
                        type="text"
                        class="form-control form-control-sm {{$errors->has('menuname')?'error':''}}"
                        name="menuname"
                        placeholder="Enter Menu Name"
                        value="{{old('menuname')}}"
                        required=""
                    />
                    @if ($errors->has('menuname'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('menuname') }}</p>
                    @endif
                </div>
                <div class="form-group" style="margin-bottom: 5px;">
                    <label for="menulink">Menu Link</label>
                    <input
                        type="text"
                        class="form-control form-control-sm {{$errors->has('menulink')?'error':''}}"
                        name="menulink"
                        placeholder="Enter Menu Link"
                        value="{{old('menulink')}}"
                        required=""
                    />
                    @if ($errors->has('menulink'))
                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('menulink') }}</p>
                    @endif
                </div>
                @isset(json_decode(Auth::user()->permission->permission, true)['menus']['add'])
                <button type="submit" class="btn btn-sm btn-block btn-primary" style="padding:10px;"><i class="fa fa-plus"></i> Add</button>
                @endisset
            </div>
        </form>
    </div>
</div>