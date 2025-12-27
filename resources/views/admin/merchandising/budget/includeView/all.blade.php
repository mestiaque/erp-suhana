<table class="table table-bordered table-sm" id="yarnTable">
    <thead>
        <tr>
            <th>SL/No.</th>
            <th>ITEM</th>
            <th>Description</th>
            <th>Supplier</th>
            <th>Qty</th>
            <th>Unit Price ($)</th>
            <th>TTL US $</th>
            <th>Item Wise Total Value</th>
            <th>%</th>
            <th>Company Name</th>
            <th>Payment Value</th>
        </tr>
    </thead>

    <tbody>

        {{-- ================= YARN ================= --}}
        @if($budget->yarns->count())
            @foreach($budget->yarns as $yarn)
            <tr>
                @if($loop->first)
                    <th rowspan="{{ $budget->yarns->count() + 1 }}">1</th>
                    <th rowspan="{{ $budget->yarns->count() + 1 }}">Yarn</th>
                @endif

                <td>{{ $yarn->description }}</td>
                <td>{{ $yarn->supplier }}</td>
                <td>{{ (int)$yarn->qty }} Kgs</td>
                <td>${{ $yarn->unit_price }}</td>
                <td>${{ $yarn->ttl_usd }}</td>

                @if($loop->first)
                    <td rowspan="{{ $budget->yarns->count() + 1 }}">${{ $yarn->item_total }}</td>
                    <td rowspan="{{ $budget->yarns->count() + 1 }}">{{ $yarn->percent }}%</td>
                    <td rowspan="{{ $budget->yarns->count() + 1 }}">{{ $yarn->company_name }}</td>
                    <td rowspan="{{ $budget->yarns->count() + 1 }}">{{ $yarn->payment_value }}</td>
                @endif
            </tr>

            @endforeach
            <tr>
                <th colspan="2" class="text-end"><strong>Yarn Total Amount:</strong></th>
                <th colspan=""><strong>{{ $budget->yarns->sum('qty') }} Kgs</strong></th>
                <td></td>
                <td></td>
            </tr>
        @endif


        {{-- ================= KNITTING ================= --}}
        @if($budget->knittings->count())
            @foreach($budget->knittings as $knit)
            <tr>
                @if($loop->first)
                    <th rowspan="{{ $budget->knittings->count() }}">2</th>
                    <th rowspan="{{ $budget->knittings->count() }}">Knitting</th>
                @endif

                <td>{{ $knit->description }}</td>
                <td>{{ $knit->supplier }}</td>
                <td>{{ (int)$knit->qty }}</td>
                <td>${{ $knit->unit_price }}</td>
                <td>${{ $knit->ttl_usd }}</td>

                @if($loop->first)
                    <td rowspan="{{ $budget->knittings->count() }}">${{ $knit->item_total }}</td>
                    <td rowspan="{{ $budget->knittings->count() }}">{{ $knit->percent }}%</td>
                    <td rowspan="{{ $budget->knittings->count() }}">{{ $knit->company_name }}</td>
                    <td rowspan="{{ $budget->knittings->count() }}">{{ $knit->payment_value }}</td>
                @endif
            </tr>
            @endforeach
        @endif


        {{-- ================= DYEING ================= --}}
        @if($budget->dyeings->count())
            @foreach($budget->dyeings as $dye)
            <tr>
                @if($loop->first)
                    <th rowspan="{{ $budget->dyeings->count() }}">3</th>
                    <th rowspan="{{ $budget->dyeings->count() }}">Dyeing</th>
                @endif

                <td>{{ $dye->description }}</td>
                <td>{{ $dye->supplier }}</td>
                <td>{{ (int)$dye->qty }}</td>
                <td>${{ $dye->unit_price }}</td>
                <td>${{ $dye->ttl_usd }}</td>

                @if($loop->first)
                    <td rowspan="{{ $budget->dyeings->count() }}">${{ $dye->item_total }}</td>
                    <td rowspan="{{ $budget->dyeings->count() }}">{{ $dye->percent }}%</td>
                    <td rowspan="{{ $budget->dyeings->count() }}">{{ $dye->company_name }}</td>
                    <td rowspan="{{ $budget->dyeings->count() }}">{{ $dye->payment_value }}</td>
                @endif
            </tr>
            @endforeach
        @endif


        {{-- ================= ACCESSORIES ================= --}}
        @if($budget->accessories->count())

            @foreach($budget->accessories as $acc)
            @if($loop->first)
                    <tr>
                        <th rowspan="{{ $budget->accessories->count() + 1}}">4</th>
                        <th rowspan="{{ $budget->accessories->count() + 1}}">Accessories</th>
                        <th>Item:</th>
                        <th></th>
                        <th>Quantity (Doz)</th>
                        <th>Price/Doz</th>
                        <th>TTL US $</th>
                        <td rowspan="{{ $budget->accessories->count() + 1 }}">${{ $acc->item_total }}</td>
                        <td rowspan="{{ $budget->accessories->count() + 1 }}">{{ $acc->percent }}%</td>
                        <td rowspan="{{ $budget->accessories->count() + 1 }}">{{ $acc->company_name }}</td>
                        <td rowspan="{{ $budget->accessories->count() + 1 }}">{{ $acc->payment_value }}</td>
                    </tr>
            @endif
            <tr>
                <td>{{ $acc->description }}</td>
                <td>{{ $acc->supplier }}</td>
                <td>{{ (int)$acc->qty }}</td>
                <td>${{ $acc->unit_price }}</td>
                <td>${{ $acc->ttl_usd }}</td>
            </tr>
            @endforeach
        @endif


        {{-- ================= PRINT & EMBROIDERY ================= --}}
        @if($budget->printEmbroidery->count())
            @foreach($budget->printEmbroidery as $pe)

                @if($loop->first)
                    <tr>
                        <th rowspan="{{ $budget->printEmbroidery->count() + 1}}">5</th>
                        <th rowspan="{{ $budget->printEmbroidery->count() + 1}}">Print & Embroidery</th>
                        <th>Item:</th>
                        <th></th>
                        <th>Quantity (Doz)</th>
                        <th>Price/Doz</th>
                        <th>TTL US $</th>
                        <td rowspan="{{ $budget->printEmbroidery->count() + 1 }}">${{ $acc->item_total }}</td>
                        <td rowspan="{{ $budget->printEmbroidery->count() + 1 }}">{{ $acc->percent }}%</td>
                        <td rowspan="{{ $budget->printEmbroidery->count() + 1 }}">{{ $acc->company_name }}</td>
                        <td rowspan="{{ $budget->printEmbroidery->count() + 1 }}">{{ $acc->payment_value }}</td>
                    </tr>
                @endif
            <tr>
                <td>{{ $pe->description }}</td>
                <td>{{ $pe->supplier }}</td>
                <td>{{ (int)$pe->qty }}</td>
                <td>${{ $pe->unit_price }}</td>
                <td>${{ $pe->ttl_usd }}</td>
            </tr>
            @endforeach
        @endif


        {{-- ================= CM ================= --}}
        @if($budget->cms->count())
            @foreach($budget->cms as $cm)
                @if($loop->first)
                    <tr>
                        <th rowspan="{{ $budget->cms->count() + 1}}">6</th>
                        <th rowspan="{{ $budget->cms->count() + 1}}">CM</th>
                        <th>Item:</th>
                        <th></th>
                        <th>Quantity (Doz)</th>
                        <th colspan="">CM/Doz</th>
                        <th>TTL US $</th>
                        <td rowspan="{{ $budget->cms->count() + 1 }}">${{ $acc->item_total }}</td>
                        <td rowspan="{{ $budget->cms->count() + 1 }}">{{ $acc->percent }}%</td>
                        <td rowspan="{{ $budget->cms->count() + 1 }}">{{ $acc->company_name }}</td>
                        <td rowspan="{{ $budget->cms->count() + 1 }}">{{ $acc->payment_value }}</td>
                    </tr>
                @endif
                <tr>
                    <td>{{ $cm->description }}</td>
                    <td>{{ $cm->supplier }}</td>
                    <td>{{ (int)$cm->qty }}</td>
                    <td>${{ $cm->unit_price }}</td>
                    <td>${{ $cm->ttl_usd }}</td>
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
            <th>7</th>
            <th colspan="2">{{ $test->description ?? '' }}</th>
            <td>{{ $test->supplier ?? '' }}</td>
            <td>{{ $test->qty ?? '' }}</td>
            <td>{{ $test->unit_price ?? '' }}</td>
            <td>{{ $test->ttl_usd ?? '' }}</td>
            <td>{{ $test->item_total ?? '' }}</td>
            <td>{{ $test->percent ?? '' }}</td>
            <td>{{ $test->company_name ?? '' }}</td>
            <td>{{ $test->payment_value ?? '' }}</td>
        </tr>

        @php
            $buying = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'buying_commission')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th>8</th>
            <th colspan="2">{{ $buying->description ?? '' }}</th>
            <td>{{ $buying->supplier ?? '' }}</td>
            <td>{{ $buying->qty ?? '' }}</td>
            <td>{{ $buying->unit_price ?? '' }}</td>
            <td>{{ $buying->ttl_usd ?? '' }}</td>
            <td>{{ $buying->item_total ?? '' }}</td>
            <td>{{ $buying->percent ?? '' }}</td>
            <td>{{ $buying->company_name ?? '' }}</td>
            <td>{{ $buying->payment_value ?? '' }}</td>
        </tr>

        @php
            $local = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'local_transportation')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th>9</th>
            <th colspan="2">{{ $local->description ?? '' }}</th>
            <td>{{ $local->supplier ?? '' }}</td>
            <td>{{ $local->qty ?? '' }}</td>
            <td>{{ $local->unit_price ?? '' }}</td>
            <td>{{ $local->ttl_usd ?? '' }}</td>
            <td>{{ $local->item_total ?? '' }}</td>
            <td>{{ $local->percent ?? '' }}</td>
            <td>{{ $local->company_name ?? '' }}</td>
            <td>{{ $local->payment_value ?? '' }}</td>
        </tr>

        @php
            $bankCommercial = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'bank_commercial')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th>10</th>
            <th colspan="2">{{ $bankCommercial->description ?? '' }}</th>
            <td>{{ $bankCommercial->supplier ?? '' }}</td>
            <td>{{ $bankCommercial->qty ?? '' }}</td>
            <td>{{ $bankCommercial->unit_price ?? '' }}</td>
            <td>{{ $bankCommercial->ttl_usd ?? '' }}</td>
            <td>{{ $bankCommercial->item_total ?? '' }}</td>
            <td>{{ $bankCommercial->percent ?? '' }}</td>
            <td>{{ $bankCommercial->company_name ?? '' }}</td>
            <td>{{ $bankCommercial->payment_value ?? '' }}</td>
        </tr>

        @php
            $commission = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'commission_percent')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th>11</th>
            <th colspan="2">{{ $commission->description ?? '' }}</th>
            <td>{{ $commission->supplier ?? '' }}</td>
            <td>{{ $commission->qty ?? '' }}</td>
            <td>{{ $commission->unit_price ?? '' }}</td>
            <td>{{ $commission->ttl_usd ?? '' }}</td>
            <td>{{ $commission->item_total ?? '' }}</td>
            <td>{{ $commission->percent ?? '' }}</td>
            <td>{{ $commission->company_name ?? '' }}</td>
            <td>{{ $commission->payment_value ?? '' }}</td>
        </tr>

        @php
            $freight = (isset($budget) && isset($budget->tests))
                ? $budget->tests->where('key', 'freight')->first()
                : null;
        @endphp
        <tr class="testRow">
            <th>12</th>
            <th colspan="2">{{ $freight->description ?? '' }}</th>
            <td>{{ $freight->supplier ?? '' }}</td>
            <td>{{ $freight->qty ?? '' }}</td>
            <td>{{ $freight->unit_price ?? '' }}</td>
            <td>{{ $freight->ttl_usd ?? '' }}</td>
            <td>{{ $freight->item_total ?? '' }}</td>
            <td>{{ $freight->percent ?? '' }}</td>
            <td>{{ $freight->company_name ?? '' }}</td>
            <td>{{ $freight->payment_value ?? '' }}</td>
        </tr>


















    </tbody>
</table>
