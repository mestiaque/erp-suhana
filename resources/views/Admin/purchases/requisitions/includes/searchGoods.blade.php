<ul>
    @if($goods->count() > 0)
        @foreach($goods as $item)
        <li style="position: relative; padding: 5px 0;">
            <span style="font-size: 14px; width: 90%; display: inline-block;">{{ $item->name }}</span>

            <span class="btn-custom yellow addDataQuery"
                  data-type="items"
                  data-url="{{ route('admin.purchasesRequisitionsAction', ['add-item', $requisition->id, 'item_id' => $item->id]) }}"
                  style="margin-left: 10px; cursor: pointer; position: absolute; right: 5px;">
                  <i class="bx bx-plus"></i>
            </span>
            <br>
            <b>
                {{-- $ {{ $item->price ?? 0 }} {{ $item->unit ?? '' }} --}}
            </b>
        </li>
        @endforeach
    @else
        <li>
            <span>No Items Found</span>
        </li>
    @endif
</ul>
