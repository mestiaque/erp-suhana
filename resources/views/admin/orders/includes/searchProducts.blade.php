<ul>
    @if($products->count() > 0)
        @foreach($products as $product)
        <li class="addDataQuery"
            data-url="{{ route('admin.ordersAction', ['add-item', $order->id, 'item_id' => $product->id]) }}"
            data-id="{{ $product->id }}"
            data-name="{{ $product->name }}">
            <span>{{ $product->name }}</span>
        </li>
        @endforeach
    @else
        <li>
            <span>No Products Found</span>
        </li>
    @endif
</ul>
