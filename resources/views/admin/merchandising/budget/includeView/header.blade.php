
<table class="table table-sm table-bordered align-middle headerTable">
    <tbody>
        <tr>
            <th width="10%">P.I. No.</th>
            <td width="30%">{{ $budget->pi_no }}</td>
            <th width="10%">BUYER</th>
            <td width="30%">{{ $budget->buyer ?? '' }}</td>
            <th width="10%" class="text-center">GMTS PICTURE</th>
        </tr>

        <tr>
            <th>TOTAL STYLES</th>
            <td>{{ $budget->total_styles ?? '' }}</td>

            <th>TOTAL POs</th>
            <td>{{ $budget->total_pos ?? '' }}</td>

            <td width="10%" rowspan="5">
                {{-- <img src="" alt="Attachment"> --}}
            </td>
        </tr>

        <tr>
            <th>ITEM</th>
            <td></td>
            <th></th>
            <td></td>
        </tr>

        <tr>
            <th>L/C NO.</th>
            <td></td>

            <th>L/C DT.</th>
            <td></td>
        </tr>

        <tr>
            <th>L/C VALUE</th>
            <td></td>

            <th>SHIP DATE</th>
            <td></td>
        </tr>

        <tr>
            <th>P.I. VALUE</th>
            <td>{{ $budget->pi_value ?? 0.00 }}</td>

            <th>TOTAL QTY (PCS)</th>
            <td>{{ $budget->total_qty ?? 0 }}</td>
        </tr>
    </tbody>
</table>
