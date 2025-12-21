<ul id="purchaseSearchResults" class="list-group mt-1">
    @foreach($purchases as $p)
        <li class="list-group-item list-group-item-action selectPurchase px-2 py-1" style="cursor:pointer;" data-val="{{ $p->order_no }}">
            {{ $p->order_no }}
        </li>
    @endforeach
</ul>
