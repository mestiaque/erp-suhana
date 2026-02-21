<table class="table table-sm" id="yarnTable">
    <thead class="bot">
        <tr>
            <th class="bol bot bob">SL/No.</th>
            <th class="bol bot bob">ITEM</th>
            <th class="bol bot bob">Description</th>
            <th class="bol bot bob">Creditor</th>
            <th class="bol bot bob">Qty</th>
            <th class="bol bot bob">Unit Price ($)</th>
            <th class="bol bot bob">TTL US $</th>
            <th class="bol bot bob">Item Wise Total Value</th>
            <th class="bol bot bob">%</th>
            <th class="bol bot bob">Company Name</th>
            <th class="bol bot bob bor">Payment Value</th>
        </tr>
    </thead>

    <tbody>

        {{-- ================= YARN ================= --}}
        @if($budget->yarns->count())
            @foreach($budget->yarns as $yarn)
            <tr>
                @if($loop->first)
                    <th class="bol  bob" rowspan="{{ $budget->yarns->count() + 1 }}">1</th>
                    <th class="bol  bob" rowspan="{{ $budget->yarns->count() + 1 }}">Yarn</th>
                @endif

                <td>{{ $yarn->description }}</td>
                <td>{{ $yarn->supplier }}</td>
                <td>{{ (int)$yarn->qty }} Kgs</td>
                <td>${{ $yarn->unit_price }}</td>
                <td>${{ $yarn->ttl_usd }}</td>

                @if($loop->first)
                    <td class="bob" rowspan="{{ $budget->yarns->count() + 1 }}">${{ $yarn->item_total }}</td>
                    <td class="bob" rowspan="{{ $budget->yarns->count() + 1 }}">{{ $yarn->percent }}%</td>
                    <td class="bob" rowspan="{{ $budget->yarns->count() + 1 }}">{{ $yarn->company_name }}</td>
                    <td class="bob bor" rowspan="{{ $budget->yarns->count() + 1 }}">{{ $yarn->payment_value }}</td>
                @endif
            </tr>

            @endforeach
            <tr>
                <th colspan="2" class="text-end bob"><strong>Yarn Total Amount:</strong></th>
                <th colspan="" class="bob"><strong>{{ $budget->yarns->sum('qty') }} Kgs</strong></th>
                <td class="bob"></td>
                <td class="bob"></td>
            </tr>
        @endif


        {{-- ================= KNITTING ================= --}}
        @if($budget->knittings->count())
            @foreach($budget->knittings as $knit)
            <tr>
                @if($loop->first)
                    <th class="bol bob" rowspan="{{ $budget->knittings->count() }}">2</th>
                    <th class="bob" rowspan="{{ $budget->knittings->count() }}">Knitting</th>
                @endif

                <td class="{{ $loop->last ? 'bob' : '' }}">{{ $knit->description }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ $knit->supplier }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ (int)$knit->qty }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ $knit->unit_price }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ $knit->ttl_usd }}</td>
                @if($loop->first)
                    <td class="bob" rowspan="{{ $budget->knittings->count() }}">${{ $knit->item_total }}</td>
                    <td class="bob" rowspan="{{ $budget->knittings->count() }}">{{ $knit->percent }}%</td>
                    <td class="bob" rowspan="{{ $budget->knittings->count() }}">{{ $knit->company_name }}</td>
                    <td class="bob bor" rowspan="{{ $budget->knittings->count() }}">{{ $knit->payment_value }}</td>
                @endif
            </tr>
            @endforeach
        @endif


        {{-- ================= DYEING ================= --}}
        @if($budget->dyeings->count())
            @foreach($budget->dyeings as $dye)
            <tr>
                @if($loop->first)
                    <th class="bob bol" rowspan="{{ $budget->dyeings->count() }}">3</th>
                    <th class="bob" rowspan="{{ $budget->dyeings->count() }}">Dyeing</th>
                @endif

                <td class="{{ $loop->last ? 'bob' : '' }}">{{ $dye->description }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ $dye->supplier }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ (int)$dye->qty }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ $dye->unit_price }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ $dye->ttl_usd }}</td>

                @if($loop->first)
                    <td class="bob" rowspan="{{ $budget->dyeings->count() }}">${{ $dye->item_total }}</td>
                    <td class="bob" rowspan="{{ $budget->dyeings->count() }}">{{ $dye->percent }}%</td>
                    <td class="bob" rowspan="{{ $budget->dyeings->count() }}">{{ $dye->company_name }}</td>
                    <td class="bob bor" rowspan="{{ $budget->dyeings->count() }}">{{ $dye->payment_value }}</td>
                @endif
            </tr>
            @endforeach
        @endif


        {{-- ================= ACCESSORIES ================= --}}
        @if($budget->accessories->count())

            @foreach($budget->accessories as $acc)
            @if($loop->first)
                    <tr>
                        <th class="bob bol" rowspan="{{ $budget->accessories->count() + 1}}">4</th>
                        <th class="bob" rowspan="{{ $budget->accessories->count() + 1}}">Accessories</th>
                        <th>Item:</th>
                        <th></th>
                        <th>Quantity (Doz)</th>
                        <th>Price/Doz</th>
                        <th>TTL US $</th>
                        <td class="bob" rowspan="{{ $budget->accessories->count() + 1 }}">${{ $acc->item_total }}</td>
                        <td class="bob" rowspan="{{ $budget->accessories->count() + 1 }}">{{ $acc->percent }}%</td>
                        <td class="bob" rowspan="{{ $budget->accessories->count() + 1 }}">{{ $acc->company_name }}</td>
                        <td class="bob bor" rowspan="{{ $budget->accessories->count() + 1 }}">{{ $acc->payment_value }}</td>
                    </tr>
            @endif
            <tr>
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ $acc->description }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ $acc->supplier }}</td>
                {{-- <td class="{{ $loop->last ? 'bob' : '' }}">{{ (int)$acc->qty }}</td> --}}
                {{-- <td class="{{ $loop->last ? 'bob' : '' }}">${{ $acc->unit_price }}</td> --}}
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ number_format($acc->qty / 12, 2) }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ number_format($acc->unit_price * 12, 2) }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ $acc->ttl_usd }}</td>
            </tr>
            @endforeach
        @endif


        {{-- ================= PRINT & EMBROIDERY ================= --}}
        @if($budget->printEmbroidery->count())
            @foreach($budget->printEmbroidery as $pe)

                @if($loop->first)
                    <tr>
                        <th class="bob bol" rowspan="{{ $budget->printEmbroidery->count() + 1}}">5</th>
                        <th class="bob" rowspan="{{ $budget->printEmbroidery->count() + 1}}">Print & Embroidery</th>
                        <th>Item:</th>
                        <th></th>
                        <th>Quantity (Doz)</th>
                        <th>Price/Doz</th>
                        <th>TTL US $</th>
                        <td class="bob" rowspan="{{ $budget->printEmbroidery->count() + 1 }}">${{ $pe->item_total }}</td> {{-- $acc to $pe--}}
                        <td class="bob" rowspan="{{ $budget->printEmbroidery->count() + 1 }}">{{ $pe->percent }}%</td>
                        <td class="bob" rowspan="{{ $budget->printEmbroidery->count() + 1 }}">{{ $pe->company_name }}</td>
                        <td class="bob bor" rowspan="{{ $budget->printEmbroidery->count() + 1 }}">{{ $pe->payment_value }}</td>
                    </tr>
                @endif
            <tr>
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ $pe->description }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ $pe->supplier }}</td>
                {{-- <td class="{{ $loop->last ? 'bob' : '' }}">{{ (int)$pe->qty }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ $pe->unit_price }}</td> --}}
                <td class="{{ $loop->last ? 'bob' : '' }}">{{ number_format($pe->qty / 12, 2) }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ number_format($pe->unit_price * 12, 2) }}</td>
                <td class="{{ $loop->last ? 'bob' : '' }}">${{ $pe->ttl_usd }}</td>
            </tr>
            @endforeach
        @endif


        {{-- ================= CM ================= --}}
        @if($budget->cms->count())
            @foreach($budget->cms as $cm)
                @if($loop->first)
                    <tr>
                        <th class="bob bol" rowspan="{{ $budget->cms->count() + 1}}">6</th>
                        <th class="bob" rowspan="{{ $budget->cms->count() + 1}}">CM</th>
                        <th>Item:</th>
                        <th></th>
                        <th>Quantity (Doz)</th>
                        <th colspan="">CM/Doz</th>
                        <th>TTL US $</th>
                        <td class="bob" rowspan="{{ $budget->cms->count() + 1 }}">${{ $cm->item_total }}</td> {{-- $acc to $cm --}}
                        <td class="bob" rowspan="{{ $budget->cms->count() + 1 }}">{{ $cm->percent }}%</td>
                        <td class="bob" rowspan="{{ $budget->cms->count() + 1 }}">{{ $cm->company_name }}</td>
                        <td class="bob bor" rowspan="{{ $budget->cms->count() + 1 }}">{{ $cm->payment_value }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="{{ $loop->last ? 'bob' : '' }}">{{ $cm->description }}</td>
                    <td class="{{ $loop->last ? 'bob' : '' }}">{{ $cm->supplier }}</td>
                    {{-- <td class="{{ $loop->last ? 'bob' : '' }}">{{ (int)$cm->qty }}</td>
                    <td class="{{ $loop->last ? 'bob' : '' }}">${{ $cm->unit_price }}</td> --}}
                    <td class="{{ $loop->last ? 'bob' : '' }}">{{ number_format($cm->qty / 12, 2) }}</td>
                    <td class="{{ $loop->last ? 'bob' : '' }}">${{ number_format($cm->unit_price * 12, 2) }}</td>
                    <td class="{{ $loop->last ? 'bob' : '' }}">${{ $cm->ttl_usd }}</td>
                </tr>
            @endforeach
        @endif


        {{-- ================= TESTING & INSPECTION ================= --}}
        @php
            $test = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'test')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th class="bob bol">7</th>
            <th class="bob" colspan="2">{{ $test->description ?? '' }}</th>
            <td class="bob">{{ $test->supplier ?? '' }}</td>
            <td class="bob">{{ $test->qty ?? '' }}</td>
            <td class="bob">{{ $test->unit_price ?? '' }}</td>
            <td class="bob">{{ $test->ttl_usd ?? '' }}</td>
            <td class="bob">{{ $test->item_total ?? '' }}</td>
            <td class="bob">{{ $test->percent ?? '' }}</td>
            <td class="bob">{{ $test->company_name ?? '' }}</td>
            <td class="bob bor">{{ $test->payment_value ?? '' }}</td>
        </tr>

        @php
            $buying = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'buying_commission')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th class="bob bol">8</th>
            <th class="bob" colspan="2">{{ $buying->description ?? '' }}</th>
            <td class="bob">{{ $buying->supplier ?? '' }}</td>
            <td class="bob">{{ $buying->qty ?? '' }}</td>
            <td class="bob">{{ $buying->unit_price ?? '' }}</td>
            <td class="bob">{{ $buying->ttl_usd ?? '' }}</td>
            <td class="bob">{{ $buying->item_total ?? '' }}</td>
            <td class="bob">{{ $buying->percent ?? '' }}</td>
            <td class="bob">{{ $buying->company_name ?? '' }}</td>
            <td class="bob bor">{{ $buying->payment_value ?? '' }}</td>
        </tr>

        @php
            $local = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'local_transportation')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th class="bob bol">9</th>
            <th class="bob" colspan="2">{{ $local->description ?? '' }}</th>
            <td class="bob">{{ $local->supplier ?? '' }}</td>
            <td class="bob">{{ $local->qty ?? '' }}</td>
            <td class="bob">{{ $local->unit_price ?? '' }}</td>
            <td class="bob">{{ $local->ttl_usd ?? '' }}</td>
            <td class="bob">{{ $local->item_total ?? '' }}</td>
            <td class="bob">{{ $local->percent ?? '' }}</td>
            <td class="bob">{{ $local->company_name ?? '' }}</td>
            <td class="bob bor">{{ $local->payment_value ?? '' }}</td>
        </tr>

        @php
            $bankCommercial = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'bank_commercial')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th class="bob bol">10</th>
            <th class="bob" colspan="2">{{ $bankCommercial->description ?? '' }}</th>
            <td class="bob">{{ $bankCommercial->supplier ?? '' }}</td>
            <td class="bob">{{ $bankCommercial->qty ?? '' }}</td>
            <td class="bob">{{ $bankCommercial->unit_price ?? '' }}</td>
            <td class="bob">{{ $bankCommercial->ttl_usd ?? '' }}</td>
            <td class="bob">{{ $bankCommercial->item_total ?? '' }}</td>
            <td class="bob">{{ $bankCommercial->percent ?? '' }}</td>
            <td class="bob">{{ $bankCommercial->company_name ?? '' }}</td>
            <td class="bob bor">{{ $bankCommercial->payment_value ?? '' }}</td>
        </tr>

        @php
            $commission = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'commission_percent')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th class="bob bol">11</th>
            <th class="bob" colspan="2">{{ $commission->description ?? '' }}</th>
            <td class="bob">{{ $commission->supplier ?? '' }}</td>
            <td class="bob">{{ $commission->qty ?? '' }}</td>
            <td class="bob">{{ $commission->unit_price ?? '' }}</td>
            <td class="bob">{{ $commission->ttl_usd ?? '' }}</td>
            <td class="bob">{{ $commission->item_total ?? '' }}</td>
            <td class="bob">{{ $commission->percent ?? '' }}</td>
            <td class="bob">{{ $commission->company_name ?? '' }}</td>
            <td class="bob bor">{{ $commission->payment_value ?? '' }}</td>
        </tr>

        @php
            $freight = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'freight')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th class="bob bol">12</th>
            <th class="bob" colspan="2">{{ $freight->description ?? '' }}</th>
            <td class="bob">{{ $freight->supplier ?? '' }}</td>
            <td class="bob">{{ $freight->qty ?? '' }}</td>
            <td class="bob">{{ $freight->unit_price ?? '' }}</td>
            <td class="bob">{{ $freight->ttl_usd ?? '' }}</td>
            <td class="bob">{{ $freight->item_total ?? '' }}</td>
            <td class="bob">{{ $freight->percent ?? '' }}</td>
            <td class="bob">{{ $freight->company_name ?? '' }}</td>
            <td class="bob bor">{{ $freight->payment_value ?? '' }}</td>
        </tr>


















    </tbody>
</table>
