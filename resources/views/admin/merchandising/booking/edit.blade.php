@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Booking Form') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Booking Form</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item">Bookings</li>
            <li class="item">Add Booking</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="#" method="POST" enctype="multipart/form-data" class="ajaxform_instant_reload" novalidate>
                @csrf

                {{-- Order Image & Info --}}
                <div class="row mb-4">
                    <div class="col-lg-4">
                        <div class="order-management-image">
                            <label><b>Order Item Image</b></label>
                            <label id="upload" class="upload-img">
                                <i>
                                    <img id="booking_image" class="img-preview" src="{{ old('booking_image', asset('assets/images/default.png')) }}">
                                </i>
                                <input type="file" name="booking_image" class="d-none file-input-change" accept="image/*">
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-8 order-lg-first">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label>PI No</label>
                                <select name="pi_id" id="pi_id" required class="form-control select-tow">
                                    <option value="">Select Order</option>
                                    @foreach($pis as $pi)
                                        <option value="{{ $pi->id }}" {{ old('pi_id') == $pi->id ? 'selected' : '' }}>
                                            {{ $pi->pi_no }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label>Prepared By</label>
                                <input type="text" name="prepared_by" id="booking_merchandiser" readonly class="form-control" value="{{ auth()->user()->name }}">
                            </div>
                            <div class="col-lg-6">
                                <label>Booking Date</label>
                                <input type="date" name="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}" class="form-control" required>
                            </div>
                            <div class="col-lg-6">
                                <label>Composition</label>
                                <input type="text" name="composition" class="form-control" value="{{ old('composition') }}">
                            </div>
                            <div class="col-lg-6">
                                <label>Process Loss</label>
                                <input type="text" name="meta[process_loss]" class="form-control" value="{{ old('meta.process_loss') }}">
                            </div>
                            <div class="col-lg-6">
                                <label>Others Fabric</label>
                                <input type="text" name="meta[other_fabric]" class="form-control" value="{{ old('meta.other_fabric') }}">
                            </div>
                            <div class="col-lg-6">
                                <label>Rib</label>
                                <input type="text" name="meta[rib]" class="form-control" value="{{ old('meta.rib') }}">
                            </div>
                            <div class="col-lg-6">
                                <label>Collar</label>
                                <input type="text" name="meta[collar]" class="form-control" value="{{ old('meta.collar') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Booking Items Table --}}
                <div class="table-responsive mb-4">
                    <table class="table table-bordered booking-table" id="erp-table">
                        <thead style="white-space: nowrap;">
                        <tr>
                            <th>Style</th>
                            <th>Color</th>
                            <th>Item</th>
                            <th>Shipment Date</th>
                            <th>Garments QTY</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Description Of Garments</th>
                            <th>Garments Picture</th>
                            <th>Pantone</th>
                            <th>Body Fabrication</th>
                            <th>Yarn Count For Body</th>
                            <th>Garments QTY In DZN</th>
                            <th>Consumption Body Fabric In DZN</th>
                            <th>Body Gray Fabric In KG</th>
                            <th>Description Of Garments (RIB)</th>
                            <th>Yarn Counts For RIB 1*1</th>
                            <th>Consumption RIB In DZN</th>
                            <th>RIB In KG</th>
                            <th>Yarn Counts For RIB Lycra</th>
                            <th>Receive</th>
                            <th>Balance</th>
                            <th>Gray Body Fabric</th>
                            <th>Graybody RIB (2*1)</th>
                            <th>Revised</th>

                            {{-- Collar Sizes --}}
                            <th>XS</th>
                            <th>S</th>
                            <th>M</th>
                            <th>L</th>
                            <th>XL</th>
                            <th>XXL</th>
                            <th>3XL</th>
                            <th>4XL</th>

                            {{-- Cuff --}}
                            <th>Cuff Color</th>
                            <th>Cuff Solid L</th>
                            <th>Cuff Solid 4XL</th>
                            <th>Cuff Solid 5XL</th>
                            <th>Cuff Solid 6XL</th>

                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="duplicate-row">
                            <td><input type="text" name="style[]" class="form-control" placeholder="Style" required></td>
                            <td><input type="text" name="color[]" class="form-control" placeholder="Color"></td>
                            <td><input type="text" name="item[]" class="form-control" placeholder="Item"></td>
                            <td><input type="date" name="shipment_date[]" class="form-control"></td>
                            <td><input type="number" name="qty[]" class="form-control qty" value="0"></td>
                            <td><input type="number" name="unit_price[]" class="form-control unit_price" value="0"></td>
                            <td><input type="number" name="total_price[]" class="form-control total_price" readonly></td>
                            <td><input type="text" name="data[desc_garments][]" class="form-control"></td>
                            <td><input type="file" name="data[images][]" class="form-control" accept="image/*"></td>
                            <td><input type="text" name="data[pantone][]" class="form-control"></td>
                            <td><input type="text" name="data[body_fab][]" class="form-control"></td>
                            <td><input type="text" name="data[yarn_count_body][]" class="form-control"></td>
                            <td><input type="text" name="data[garments_qty_dzn][]" class="form-control"></td>
                            <td><input type="text" name="data[body_fab_dzn][]" class="form-control"></td>
                            <td><input type="text" name="data[body_gray_fab][]" class="form-control"></td>
                            <td><input type="text" name="data[desc_garments_rib][]" class="form-control"></td>
                            <td><input type="text" name="data[yarn_count_rib][]" class="form-control"></td>
                            <td><input type="text" name="data[consump_rib_dzn][]" class="form-control"></td>
                            <td><input type="text" name="data[rib_kg][]" class="form-control"></td>
                            <td><input type="text" name="data[yarn_count_rib_lycra][]" class="form-control"></td>
                            <td><input type="text" name="data[receive][]" class="form-control"></td>
                            <td><input type="text" name="data[balance][]" class="form-control"></td>
                            <td><input type="text" name="data[gray_body_fab][]" class="form-control"></td>
                            <td><input type="text" name="data[gray_body_rib][]" class="form-control"></td>
                            <td><input type="text" name="data[revised][]" class="form-control"></td>

                            {{-- Collar Sizes --}}
                            <td><input type="text" name="collar_size_qty[xs][]" class="form-control"></td>
                            <td><input type="text" name="collar_size_qty[s][]" class="form-control"></td>
                            <td><input type="text" name="collar_size_qty[m][]" class="form-control"></td>
                            <td><input type="text" name="collar_size_qty[l][]" class="form-control"></td>
                            <td><input type="text" name="collar_size_qty[xl][]" class="form-control"></td>
                            <td><input type="text" name="collar_size_qty[xxl][]" class="form-control"></td>
                            <td><input type="text" name="collar_size_qty[3xl][]" class="form-control"></td>
                            <td><input type="text" name="collar_size_qty[4xl][]" class="form-control"></td>

                            {{-- Cuff --}}
                            <td><input type="text" name="cuff_color[]" class="form-control"></td>
                            <td><input type="text" name="cuff_solid[l][]" class="form-control"></td>
                            <td><input type="text" name="cuff_solid[4xl][]" class="form-control"></td>
                            <td><input type="text" name="cuff_solid[5xl][]" class="form-control"></td>
                            <td><input type="text" name="cuff_solid[6xl][]" class="form-control"></td>

                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td class="total_qty">0</td>
                            <td></td>
                            <td class="final_total_price">0</td>
                            <td colspan="33"></td>
                        </tr>
                        </tfoot>
                    </table>
                    <button type="button" class="btn btn-success btn-sm add-row mt-2">Add Row</button>
                </div>

                {{-- Form Buttons --}}
                <div class="text-center mt-4">
                    <button type="reset" class="btn btn-secondary m-2">Reset</button>
                    <button type="submit" class="btn btn-primary m-2">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Add new row
    $('.add-row').click(function() {
        let row = $('#erp-table tbody .duplicate-row:first').clone();
        row.find('input').val('');
        $('#erp-table tbody').append(row);
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        if($('#erp-table tbody tr').length > 1){
            $(this).closest('tr').remove();
            updateTotals();
        }
    });

    // Auto-calculate total price and totals
    $(document).on('input', '.qty, .unit_price', function() {
        updateTotals();
    });

    function updateTotals(){
        let totalQty = 0;
        let finalTotal = 0;
        $('#erp-table tbody tr').each(function() {
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            let price = parseFloat($(this).find('.unit_price').val()) || 0;
            let total = qty * price;
            $(this).find('.total_price').val(total.toFixed(2));
            totalQty += qty;
            finalTotal += total;
        });
        $('.total_qty').text(totalQty);
        $('.final_total_price').text(finalTotal.toFixed(2));
    }
});
</script>
@endpush
@push('css')
    <style>
        .booking-table td{
            min-width: 15rem !important;
        }
    </style>
@endpush
