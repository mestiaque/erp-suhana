<div class="mt-4">
    <label>Terms & Conditions</label>
    <div id="payment-terms-list">
        @php
            // Predefined terms
            $defaultTerms = [
                'PAYMENT'           => 'LC AT SIGHT',
                'BUYING HOUSE SERVICE CHARGE'      => '3.5%',
                'TRADE TERM'        => 'FOB, BY SEA',
                'PORT OF LOADING'   => 'CHOTTROGRAM PORT, BANGLADESH',
                'PORT OF DISCHARGE' => 'ANY PORT IN JAPAN',
                'FINAL DESTINATION' => 'JAPAN',
                'BILL OF LADING'    => 'FULL SET 3/3 SHIPPED ON BOARD CLEAN OCEAN BILL OF LADING OUT OF THE ORDER OF ANY BANK IN BANGLADESH AND ENDORSED TO THE LC ISSUING BANK MARKED FREIGHT COLLECT',
                'PARTIAL SHIPMENT'  => 'ALLOWED',
                'TRANSSHIPMENT'     => 'ALLOWED',
                'TOLERANCE'         => '+/- 5%',
                'DOCUMENTATION'     => 'AS PER LC TERMS',
                'COUNTRY OF ORIGIN' => 'BANGLADESH'
            ];

            // Previously saved terms
            $savedTerms = json_decode($pi->terms ?? '{}', true);
            $allTerms = $defaultTerms;
            foreach ($savedTerms as $key => $value) {
                $allTerms[$key] = $value;
            }
            $termIndex = count($allTerms);
        @endphp

        <ul class="list-group" style="list-style:none;" id="termsUl">
            @foreach($allTerms as $key => $value)
                <li class="mb-2">
                    <div class="d-flex gap-2 align-items-center">
                        <input type="checkbox"
                            name="terms[{{ $loop->index }}][checked]"
                            class="form-control form-control-sm"
                            style="width: 20px !important"
                            {{ isset($savedTerms[$key]) ? 'checked' : '' }}> &nbsp;&nbsp;

                        <input type="text"
                            name="terms[{{ $loop->index }}][key]"
                            value="{{ $key }}"
                            class="form-control form-control-sm"
                            style="width:30%; height: 24px;"> &nbsp;&nbsp;:&nbsp;&nbsp;

                        <input type="text"
                            name="terms[{{ $loop->index }}][value]"
                            value="{{ $value }}"
                            class="form-control form-control-sm"
                            style="width:70%; height: 24px;">
                    </div>
                </li>
            @endforeach
        </ul>

    </div>

    <button type="button" id="add-term-btn" class="btn btn-sm btn-success mt-2">+ Add Term</button>
</div>


@push('js')
    <script>
        $(document).ready(function() {
            let termIndex = {{ $termIndex }}; // শুরু index

            $('#add-term-btn').click(function() {
                const newLi = `
                <li class="mb-2">
                    <div class="d-flex gap-2 align-items-center">
                        <!-- checkbox -->
                        <input type="checkbox"
                            name="terms[${termIndex}][checked]"
                            class="form-control form-control-sm" checked
                            style="width: 20px !important;" /> &nbsp;&nbsp;

                        <!-- editable key -->
                        <input type="text"
                            name="terms[${termIndex}][key]"
                            class="form-control form-control-sm"
                            style="width:30%; height:24px;"
                            placeholder="Key" /> &nbsp;&nbsp;:&nbsp;&nbsp;

                        <!-- editable value -->
                        <input type="text"
                            name="terms[${termIndex}][value]"
                            class="form-control form-control-sm"
                            style="width:70%; height:24px;"
                            placeholder="Value" />
                    </div>
                </li>
                `;

                $('#termsUl').append(newLi); // prepend করা, list এর শুরুতে যাবে
                termIndex++; // index increment
            });

        });

    </script>
@endpush
