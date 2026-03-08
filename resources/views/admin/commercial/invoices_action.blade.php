@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($action == 'create' ? 'Create Commercial Invoice' : 'Edit Commercial Invoice') }}</title>
@endsection

@push('css')
<style>
    .form-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    .form-section h5 {
        margin-bottom: 15px;
        color: #333;
        font-weight: 600;
    }
    .item-row {
        background: #fff;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .remove-item {
        color: #dc3545;
        cursor: pointer;
    }
    .total-summary {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
    .total-summary .row {
        margin-bottom: 10px;
    }
    .grand-total {
        font-size: 18px;
        font-weight: bold;
        color: #28a745;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush


@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb Area -->
    <div class="breadcrumb-area">
        <h1>{{ $action == 'create' ? 'Create Commercial Invoice' : 'Edit Commercial Invoice' }}</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">
                <a href="{{ route('admin.commercial.invoice') }}">Commercial Invoices</a>
            </li>
            <li class="item">{{ $action == 'create' ? 'Create Invoice' : 'Edit Invoice' }}</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>{{ $action == 'create' ? 'Create New Invoice' : 'Edit Invoice' }} #{{ $action == 'create' ? $invoiceNo : $invoice->invoice_no }}</h3>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{ $route }}" method="POST" id="invoiceForm">
                @csrf

                <!-- Invoice Info Section -->
                <div class="form-section">
                    <h5><i class="bx bx-file"></i> Invoice Information</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Invoice No</label>
                                <input type="text" name="invoice_no" value="{{ $action == 'create' ? $invoiceNo : $invoice->invoice_no }}" class="form-control" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" value="{{ $action == 'create' ? date('Y-m-d') : $invoice->invoice_date }}" class="form-control" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Buyer <span class="text-danger">*</span></label>
                                <select name="buyer_id" id="buyerSelect" class="form-control select2" required>
                                    <option value="">Select Buyer</option>
                                    @foreach($buyers as $buyer)
                                        <option value="{{ $buyer->id }}"
                                            data-name="{{ $buyer->name }}"
                                            data-address="{{ $buyer->fullAddress() }}"
                                            data-mobile="{{ $buyer->mobile }}"
                                            {{ ($action == 'edit' && $invoice->buyer_id == $buyer->id) ? 'selected' : '' }}>
                                            {{ $buyer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ ($action == 'edit' && $invoice->status == 1) ? 'selected' : '' }}>Pending</option>
                                    <option value="2" {{ ($action == 'edit' && $invoice->status == 2) ? 'selected' : '' }}>Approved</option>
                                    <option value="3" {{ ($action == 'edit' && $invoice->status == 3) ? 'selected' : '' }}>Shipped</option>
                                    <option value="4" {{ ($action == 'edit' && $invoice->status == 4) ? 'selected' : '' }}>Delivered</option>
                                    <option value="5" {{ ($action == 'edit' && $invoice->status == 5) ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Buyer Name</label>
                                <input type="text" name="buyer_name" id="buyerName" value="{{ $action == 'edit' ? $invoice->buyer_name : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Buyer Address</label>
                                <textarea name="buyer_address" id="buyerAddress" class="form-control" rows="1">{{ $action == 'edit' ? $invoice->buyer_address : '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Buyer Contact</label>
                                <input type="text" name="buyer_contact" id="buyerContact" value="{{ $action == 'edit' ? $invoice->buyer_contact : '' }}" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LC & Shipment Info -->
                <div class="form-section">
                    <h5><i class="bx bx-ship"></i> LC & Shipment Information</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>LC No</label>
                                <input type="text" name="lc_no" value="{{ $action == 'edit' ? $invoice->lc_no : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>LC Date</label>
                                <input type="date" name="lc_date" value="{{ $action == 'edit' ? $invoice->lc_date : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>PI No</label>
                                <input type="text" name="pi_no" value="{{ $action == 'edit' ? $invoice->pi_no : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Shipment Date</label>
                                <input type="date" name="shipment_date" value="{{ $action == 'edit' ? $invoice->shipment_date : '' }}" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Delivery Date</label>
                                <input type="date" name="delivery_date" value="{{ $action == 'edit' ? $invoice->delivery_date : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Shipment From</label>
                                <input type="text" name="shipment_from" value="{{ $action == 'edit' ? $invoice->shipment_from : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Shipment To</label>
                                <input type="text" name="shipment_to" value="{{ $action == 'edit' ? $invoice->shipment_to : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Country of Origin</label>
                                <input type="text" name="country_of_origin" value="{{ $action == 'edit' ? $invoice->country_of_origin : 'Bangladesh' }}" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Destination Country</label>
                                <input type="text" name="destination_country" value="{{ $action == 'edit' ? $invoice->destination_country : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Carrier</label>
                                <input type="text" name="carrier" value="{{ $action == 'edit' ? $invoice->carrier : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Vessel/Flight No</label>
                                <input type="text" name="vessel_flight_no" value="{{ $action == 'edit' ? $invoice->vessel_flight_no : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Container No</label>
                                <input type="text" name="container_no" value="{{ $action == 'edit' ? $invoice->container_no : '' }}" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Seal No</label>
                                <input type="text" name="seal_no" value="{{ $action == 'edit' ? $invoice->seal_no : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Marks & No</label>
                                <input type="text" name="marks_no" value="{{ $action == 'edit' ? $invoice->marks_no : '' }}" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description of Goods</label>
                                <textarea name="description_of_goods" class="form-control" rows="1">{{ $action == 'edit' ? $invoice->description_of_goods : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="form-section">
                    <h5><i class="bx bx-list-ul"></i> Invoice Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="min-width: 50px;">SL</th>
                                    <th style="min-width: 200px;">Description</th>
                                    <th style="min-width: 100px;">HS Code</th>
                                    <th style="min-width: 80px;">Unit</th>
                                    <th style="min-width: 100px;">Quantity</th>
                                    <th style="min-width: 100px;">Unit Price</th>
                                    <th style="min-width: 120px;">Total Price</th>
                                    <th style="min-width: 80px;">Carton Qty</th>
                                    <th style="min-width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                @if($action == 'edit' && $invoice->items->count() > 0)
                                    @foreach($invoice->items as $index => $item)
                                    <tr class="item-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <input type="text" name="items[{{ $index }}][description]" value="{{ $item->description }}" class="form-control" placeholder="Description" />
                                            <input type="hidden" name="items[{{ $index }}][item_no]" value="{{ $item->item_no }}" />
                                        </td>
                                        <td><input type="text" name="items[{{ $index }}][hs_code]" value="{{ $item->hs_code }}" class="form-control" placeholder="HS Code" /></td>
                                        <td>
                                            <select name="items[{{ $index }}][unit_id]" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" class="form-control qty" step="0.01" /></td>
                                        <td><input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" class="form-control price" step="0.01" /></td>
                                        <td><input type="number" name="items[{{ $index }}][total_price]" value="{{ $item->total_price }}" class="form-control total" step="0.01" readonly /></td>
                                        <td><input type="text" name="items[{{ $index }}][carton_qty]" value="{{ $item->carton_qty }}" class="form-control" /></td>
                                        <td><span class="remove-item" onclick="removeRow(this)"><i class="bx bx-trash"></i></span></td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr class="item-row">
                                    <td>1</td>
                                    <td>
                                        <input type="text" name="items[0][description]" class="form-control" placeholder="Description" />
                                        <input type="hidden" name="items[0][item_no]" value="1" />
                                    </td>
                                    <td><input type="text" name="items[0][hs_code]" class="form-control" placeholder="HS Code" /></td>
                                    <td>
                                        <select name="items[0][unit_id]" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[0][quantity]" class="form-control qty" step="0.01" value="0" /></td>
                                    <td><input type="number" name="items[0][unit_price]" class="form-control price" step="0.01" value="0" /></td>
                                    <td><input type="number" name="items[0][total_price]" class="form-control total" step="0.01" value="0" readonly /></td>
                                    <td><input type="text" name="items[0][carton_qty]" class="form-control" /></td>
                                    <td><span class="remove-item" onclick="removeRow(this)"><i class="bx bx-trash"></i></span></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                        <i class="bx bx-plus"></i> Add Item
                    </button>
                </div>

                <!-- Amount Section -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-section">
                            <h5><i class="bx bx-note"></i> Remarks</h5>
                            <textarea name="remarks" class="form-control" rows="3">{{ $action == 'edit' ? $invoice->remarks : '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="total-summary">
                            <h5><i class="bx bx-calculator"></i> Amount Summary</h5>
                            <div class="row">
                                <div class="col-6">Total Quantity:</div>
                                <div class="col-6 text-right">
                                    <input type="number" name="total_qty" id="totalQty" value="{{ $action == 'edit' ? $invoice->total_qty : 0 }}" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">Total Amount:</div>
                                <div class="col-6 text-right">
                                    <input type="number" name="total_amount" id="totalAmount" value="{{ $action == 'edit' ? $invoice->total_amount : 0 }}" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">Discount:</div>
                                <div class="col-6 text-right">
                                    <input type="number" name="discount" id="discount" value="{{ $action == 'edit' ? $invoice->discount : 0 }}" class="form-control" step="0.01" onchange="calculateTotal()" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">Tax:</div>
                                <div class="col-6 text-right">
                                    <input type="number" name="tax" id="tax" value="{{ $action == 'edit' ? $invoice->tax : 0 }}" class="form-control" step="0.01" onchange="calculateTotal()" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">Shipping Cost:</div>
                                <div class="col-6 text-right">
                                    <input type="number" name="shipping_cost" id="shippingCost" value="{{ $action == 'edit' ? $invoice->shipping_cost : 0 }}" class="form-control" step="0.01" onchange="calculateTotal()" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">Insurance:</div>
                                <div class="col-6 text-right">
                                    <input type="number" name="insurance" id="insurance" value="{{ $action == 'edit' ? $invoice->insurance : 0 }}" class="form-control" step="0.01" onchange="calculateTotal()" />
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">Currency:</div>
                                <div class="col-6 text-right">
                                    <select name="currency" id="currency" class="form-control" onchange="calculateTotal()">
                                        <option value="USD" {{ ($action == 'edit' && $invoice->currency == 'USD') ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ ($action == 'edit' && $invoice->currency == 'EUR') ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ ($action == 'edit' && $invoice->currency == 'GBP') ? 'selected' : '' }}>GBP</option>
                                        <option value="BDT" {{ ($action == 'edit' && $invoice->currency == 'BDT') ? 'selected' : '' }}>BDT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">Exchange Rate:</div>
                                <div class="col-6 text-right">
                                    <input type="number" name="exchange_rate" id="exchangeRate" value="{{ $action == 'edit' ? $invoice->exchange_rate : 1 }}" class="form-control" step="0.01" onchange="calculateTotal()" />
                                </div>
                            </div>
                            <hr>
                            <div class="row grand-total">
                                <div class="col-6">Grand Total:</div>
                                <div class="col-6 text-right">
                                    <span id="grandTotal">{{ $action == 'edit' ? number_format($invoice->grand_total, 2) : '0.00' }}</span>
                                    <span id="currencyLabel">{{ $action == 'edit' ? $invoice->currency : 'USD' }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">In BDT:</div>
                                <div class="col-6 text-right">
                                    <span id="totalInBdt">{{ $action == 'edit' ? number_format($invoice->total_in_bdt, 2) : '0.00' }}</span> BDT
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('admin.commercial.invoice') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">{{ $action == 'create' ? 'Create Invoice' : 'Update Invoice' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        // Buyer selection auto-fill
        $('#buyerSelect').on('change', function() {
            var option = $(this).find('option:selected');
            $('#buyerName').val(option.data('name') || '');
            $('#buyerAddress').val(option.data('address') || '');
            $('#buyerContact').val(option.data('mobile') || '');
        });

        // Trigger buyer fill on edit
        @if($action == 'edit')
        $('#buyerSelect').trigger('change');
        @endif

        // Calculate on item change
        $(document).on('change', '.qty, .price', function() {
            calculateItemTotal($(this).closest('.item-row'));
            calculateAllTotals();
        });
    });

    function addItem() {
        var rowCount = $('#itemsBody tr').length;
        var units = @json($units->toArray());
        var unitOptions = '<option value="">Select</option>';
        units.forEach(function(unit) {
            unitOptions += '<option value="' + unit.id + '">' + unit.name + '</option>';
        });

        var html = '<tr class="item-row">' +
            '<td>' + (rowCount + 1) + '</td>' +
            '<td>' +
                '<input type="text" name="items[' + rowCount + '][description]" class="form-control" placeholder="Description" />' +
                '<input type="hidden" name="items[' + rowCount + '][item_no]" value="' + (rowCount + 1) + '" />' +
            '</td>' +
            '<td><input type="text" name="items[' + rowCount + '][hs_code]" class="form-control" placeholder="HS Code" /></td>' +
            '<td><select name="items[' + rowCount + '][unit_id]" class="form-control">' + unitOptions + '</select></td>' +
            '<td><input type="number" name="items[' + rowCount + '][quantity]" class="form-control qty" step="0.01" value="0" /></td>' +
            '<td><input type="number" name="items[' + rowCount + '][unit_price]" class="form-control price" step="0.01" value="0" /></td>' +
            '<td><input type="number" name="items[' + rowCount + '][total_price]" class="form-control total" step="0.01" value="0" readonly /></td>' +
            '<td><input type="text" name="items[' + rowCount + '][carton_qty]" class="form-control" /></td>' +
            '<td><span class="remove-item" onclick="removeRow(this)"><i class="bx bx-trash"></i></span></td>' +
        '</tr>';

        $('#itemsBody').append(html);
    }

    function removeRow(btn) {
        if ($('#itemsBody tr').length > 1) {
            $(btn).closest('tr').remove();
            renumberRows();
            calculateAllTotals();
        }
    }

    function renumberRows() {
        $('#itemsBody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
            $(this).find('input[name*="[item_no]"]').attr('name', 'items[' + index + '][item_no]');
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }

    function calculateItemTotal(row) {
        var qty = parseFloat(row.find('.qty').val()) || 0;
        var price = parseFloat(row.find('.price').val()) || 0;
        var total = qty * price;
        row.find('.total').val(total.toFixed(2));
    }

    function calculateAllTotals() {
        var totalQty = 0;
        var totalAmount = 0;

        $('.item-row').each(function() {
            var qty = parseFloat($(this).find('.qty').val()) || 0;
            var price = parseFloat($(this).find('.price').val()) || 0;
            var itemTotal = qty * price;

            totalQty += qty;
            totalAmount += itemTotal;

            $(this).find('.total').val(itemTotal.toFixed(2));
        });

        $('#totalQty').val(totalQty.toFixed(2));
        $('#totalAmount').val(totalAmount.toFixed(2));

        calculateTotal();
    }

    function calculateTotal() {
        var totalAmount = parseFloat($('#totalAmount').val()) || 0;
        var discount = parseFloat($('#discount').val()) || 0;
        var tax = parseFloat($('#tax').val()) || 0;
        var shippingCost = parseFloat($('#shippingCost').val()) || 0;
        var insurance = parseFloat($('#insurance').val()) || 0;

        var grandTotal = totalAmount - discount + tax + shippingCost + insurance;

        $('#grandTotal').text(grandTotal.toFixed(2));

        var currency = $('#currency').val();
        $('#currencyLabel').text(currency);

        var exchangeRate = parseFloat($('#exchangeRate').val()) || 1;
        var totalInBdt = grandTotal * exchangeRate;

        $('#totalInBdt').text(totalInBdt.toFixed(2));
    }
</script>
@endpush
