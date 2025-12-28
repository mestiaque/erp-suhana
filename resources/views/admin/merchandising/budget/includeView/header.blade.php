
<table class="table table-sm table-bordered align-middle headerTable">
    <tbody>
        <tr>
            <th class="bol bot" width="10%">P.I. No.</th>
            <td class="bot" width="30%">{{ $budget->pi_no }}</td>
            <th class="bot" width="10%">BUYER</th>
            <td class="bot" width="30%">{{ $budget->buyer ?? '' }}</td>
            <th width="10%" class="text-center bot bor">GMTS PICTURE</th>
        </tr>

        <tr>
            <th class="bol">TOTAL STYLES</th>
            <td>{{ $budget->total_styles ?? '' }}</td>

            <th>TOTAL POs</th>
            <td>{{ $budget->total_pos ?? '' }}</td>

            <td class="bor bob" width="10%" rowspan="5">
                {{-- <img src="" alt="Attachment"> --}}
            </td>
        </tr>

        <tr>
            <th class="bol">ITEM</th>
            <td></td>
            <th></th>
            <td class=""></td>
        </tr>

        <tr>
            <th class="bol">L/C NO.</th>
            <td></td>

            <th class="bol">L/C DT.</th>
            <td></td>
        </tr>

        <tr>
            <th class="bol">L/C VALUE</th>
            <td></td>

            <th>SHIP DATE</th>
            <td></td>
        </tr>

        <tr>
            <th class="bol bob">P.I. VALUE</th>
            <td class="bob">{{ $budget->pi_value ?? 0.00 }}</td>

            <th class="bob">TOTAL QTY (PCS)</th>
            <td class="bob">{{ $budget->total_qty ?? 0 }}</td>
        </tr>
    </tbody>
</table>
