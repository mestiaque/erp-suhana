 <ul style="padding:0;">
    @if(App\Models\ReffMember::latest()->where('status','active')
        ->where(function($q){
          if(request()->reff_search){
              $q->where('name','LIKE','%'.request()->reff_search.'%');
          }
        })
        ->limit(10)->count() > 0)
        @foreach(App\Models\ReffMember::latest()->where('status','active')
        ->where(function($q){
          if(request()->reff_search){
              $q->where('name','LIKE','%'.request()->reff_search.'%');
          }
        })
        ->limit(10)->get() as $member)
        <li>
            <span style="font-size: 14px;width: 90%;display: inline-block;">{{$member->name}}</span>
            <span class="btn-custom yellow reffAdd" data-name="{{$member->name}}" style="margin-left: 10px;cursor: pointer;position: absolute;right: 5px;padding: 3px 10px;height: 25px;"><i class="bx bx-plus"></i></span>
        </li>
        @endforeach
    @else
    <li>
        <span>No Reff/Title Found</span>
    </li>
    @endif
</ul>