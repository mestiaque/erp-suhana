<div class="table-responsive" style="min-height: 100px;">
    <table class="table table-bordered orderTable">
        <tr>
            <th class="px-2 pb-1" style="width: 20px;">SL</th>
            <th class="px-2 pb-1" style="width: 180px;">Composition</th>
            <th class="px-2 pb-1" style="width: 80px;">GSM</th>
            <th class="px-2 pb-1" style="width: 80px;">Color</th>
            <th class="px-2 pb-1" style="width: 80px;">Size</th>
            <th class="px-2 pb-1" style="width: 80px;">Quantity</th>
            <th class="px-2 pb-1" style="width: 150px;">Comments</th>
            <th class="p-1" style="width: 60px;text-align:center;vertical-align: middle;">
                <span class="btn-custom success addItem"
                      data-url="{{ route('admin.samplesAction',['add-item',$sample->id]) }}">
                      <i class="bx bx-plus"></i>
                </span>
            </th>
        </tr>

        @if($sample->items->count() > 0)
            @foreach($sample->items as $i => $item)
                <tr class="itemRow" data-item="{{ $item->id }}">
                    <td class="p-1 text-start" style="vertical-align: bottom; text-align: center;">{{ $i+1 }}</td>

                    <td class="p-1">
                        <textarea class="form-control form-control-sm updateItem"
                                  data-name="composition"
                                  data-url="{{ route('admin.samplesAction',['update-item',$sample->id,'item_id'=>$item->id]) }}"
                                  style="height:31px;"
                                  placeholder="Composition">{{ $item->composition }}</textarea>
                    </td>

                    <td class="p-1">
                        <input type="text"
                               class="form-control form-control-sm updateItem"
                               data-name="gsm"
                               data-url="{{ route('admin.samplesAction',['update-item',$sample->id,'item_id'=>$item->id]) }}"
                               value="{{ $item->gsm }}">
                    </td>

                    <td class="p-1">
                        <input type="text"
                               class="form-control form-control-sm updateItem"
                               data-name="color"
                               data-url="{{ route('admin.samplesAction',['update-item',$sample->id,'item_id'=>$item->id]) }}"
                               value="{{ $item->color }}">
                    </td>

                    <td class="p-1">
                        <input type="text"
                               class="form-control form-control-sm updateItem"
                               data-name="size"
                               data-url="{{ route('admin.samplesAction',['update-item',$sample->id,'item_id'=>$item->id]) }}"
                               value="{{ $item->size }}">
                    </td>

                    <td class="p-1">
                        <input type="number"
                               class="form-control form-control-sm updateItem qty"
                               data-name="quantity"
                               data-url="{{ route('admin.samplesAction',['update-item',$sample->id,'item_id'=>$item->id]) }}"
                               value="{{ $item->quantity }}">
                    </td>

                    <td class="p-1">
                        <textarea class="form-control form-control-sm updateItem"
                                  data-name="comments"
                                  data-url="{{ route('admin.samplesAction',['update-item',$sample->id,'item_id'=>$item->id]) }}"
                                  style="height:31px;"
                                  placeholder="Comments">{{ $item->comments }}</textarea>
                    </td>

                    <td class="p-1" style="text-align:center;">
                        <span class="btn-custom danger removeItem"
                              data-url="{{ route('admin.samplesAction',['remove-item',$sample->id,'item_id'=>$item->id]) }}">
                              <i class="bx bx-trash"></i>
                        </span>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" style="text-align:center;color:#aaa;">No Item</td>
            </tr>
        @endif
    </table>
</div>
