<ul>
    @if($goods->count() > 0)
        @foreach($goods as $item)
        <li  class="addDataQuery" data-type="items"
                  data-url="{{ route('admin.purchasesRequisitionsAction', ['add-material', $requisition->id, 'item_id' => $item->id]) }}">
            <span style="font-size: 14px; width: 90%; display: inline-block;">{{ $item->name }}</span>
        </li>
        @endforeach
    @else
        <li>
            <span>No Items Found</span>
        </li>
    @endif
</ul>

