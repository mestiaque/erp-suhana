@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Budget Form') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Budget Form</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item">Budget</li>
            <li class="item">Add Budget</li>
        </ol>
    </div>

    @php
        // Demo data for testing
        $orders = [
            (object)['id'=>1,'display_name'=>'0000001','payment_mode'=>'LC','party_name'=>'Customer A','party_type'=>'Retail','styles'=>['All','STYLE001']],
            (object)['id'=>2,'display_name'=>'0000002','payment_mode'=>'TT','party_name'=>'Customer B','party_type'=>'Wholesale','styles'=>['All','T','4']],
        ];

        $styles = [
            (object)['name'=>'All'],
            (object)['name'=>'STYLE001'],
        ];

        $budget = $budget ?? null;

        $demoYarns = [
            (object)['fab_desc'=>'Cotton', 'supplier_name'=>'Supplier A', 'yarn_count'=>'30s', 'unit_price'=>10, 'consumption'=>2, 'w'=>5, 'total_qty'=>20, 'total_cost'=>200, 'pre_cost'=>180],
        ];

        $demoKnitting = [
            (object)['fab_desc'=>'Knitting Fabric', 'supplier_name'=>'Supplier K', 'yarn_count'=>'NA', 'unit_price'=>5, 'consumption'=>1, 'w'=>2, 'total_qty'=>50, 'total_cost'=>250, 'pre_cost'=>230],
        ];

        $demoAccessories = [
            (object)['accessories_des'=>'Button', 'supplier_name'=>'Acc Supplier', 'unit_price'=>0.5, 'unit_number'=>100, 'consumption'=>1, 'w'=>0, 'total_qty'=>100, 'total_cost'=>50, 'pre_cost'=>45],
        ];
    @endphp

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="" method="post" enctype="multipart/form-data" class="ajaxform_instant_reload" novalidate>
                @csrf

                {{-- Header: Order Info --}}
                <div class="row mb-4">
                    <div class="col-lg-4">
                        <label>Select Order</label>
                        <select name="order_id" id="orderSelect" class="form-control form-control-sm mb-3">
                            <option value="">Select an Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}"
                                    data-payment="{{ $order->payment_mode }}"
                                    data-party="{{ $order->party_name }}"
                                    data-type="{{ $order->party_type }}"
                                    data-styles="{{ implode(',', $order->styles) }}">
                                    {{ $order->display_name }}
                                </option>
                            @endforeach

                        </select>

                        <label>Select Style</label>
                        <select name="order_info[style]" id="styleSelect" class="form-control form-control-sm mb-3" disabled>
                            <option value="">Select a Style</option>
                        </select>

                        <table class="table table-bordered table-sm mt-4" style="margin-top: 3rem !important">
                            <tbody>
                                <tr><td style="vertical-align: middle; font-size:14px">Pre-costing Date</td><td class="p-0"><input type="date" name="pre_cost_date" class="form-control form-control-sm"></td></tr>
                                <tr><td style="vertical-align: middle; font-size:14px">Post-costing Date</td><td class="p-0"><input type="date" name="post_cost_date" class="form-control form-control-sm"></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4">

                    </div>
                    <div class="col-lg-4 text-center">
                        <table class="table table-sm table-bordered small-table clr-black form-table-sm mini-info-table">
                            <tbody>
                                <tr>
                                    <td class="">Payment Mode</td>
                                    <td  class="p-0"><input type="text" readonly="" id="payment" class="form-control form-control-sm" placeholder="Payment Mode"></td>
                                </tr>
                                <tr>
                                    <td  class="">Party Name</td>
                                    <td class="p-0"><input type="text" readonly="" id="party_name" class="form-control form-control-sm" placeholder="Party Name"></td>
                                </tr>
                                <tr>
                                    <td  class="">Type</td>
                                    <td class="p-0"><input type="text" readonly="" id="party_type" class="form-control form-control-sm" placeholder="Party Type"></td>
                                </tr>
                                <tr class="all-hide">
                                    <td  class="">Color</td>
                                    <td class="p-0"><input type="text" name="order_info[color]" readonly="" id="color" class="form-control form-control-sm clear" placeholder="Color"></td>
                                </tr>
                                <tr class="all-hide">
                                    <td  class="">Shipment Date</td>
                                    <td class="p-0"><input type="text" name="order_info[shipment_date]" readonly="" id="shipment_date" class="form-control form-control-sm datepicker clear" placeholder="Shipment Date"></td>
                                </tr>
                                <tr>
                                    <td  class="">Quantity</td>
                                    <td class="p-0"><input type="text" name="order_info[qty]" readonly="" id="quantity" value="0" class="form-control form-control-sm clear" placeholder="Quantity"></td>
                                </tr>
                                <tr>
                                    <td  class="">Unit Price</td>
                                    <td class="p-0"><input type="text" name="order_info[unit_price]" readonly="" value="0" id="unit_price" class="form-control form-control-sm clear" placeholder="Unit Price"></td>
                                </tr>
                                <tr>
                                    <td  class="">LC Value</td>
                                    <td class="p-0"><input type="text" name="order_info[lc]" readonly="" value="0" id="lc" class="form-control form-control-sm clear"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Fabric/Yarn Table --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Yarn / Fabric</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="yarnTable">
                                <thead>
                                    <tr>
                                        <th>Description</th><th>Supplier</th><th>Yarn Count</th>
                                        <th>Unit Price($)</th><th>Consumption(Kg/Dz)</th><th>Wastage%</th>
                                        <th>Total Qty</th><th>Total Cost</th><th>Pre-Cost%</th><th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="yarnRow">
                                        <td><input type="text" name="yarn_desc[fab_desc][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="yarn_desc[supplier_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="yarn_desc[yarn_count][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="yarn_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="yarn_desc[consumption][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="yarn_desc[w][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="yarn_desc[total_qty][]" class="form-control form-control-sm total_qty"></td>
                                        <td><input type="number" step="any" name="yarn_desc[total_cost][]" class="form-control form-control-sm total_cost" readonly></td>
                                        <td><input type="number" step="any" name="yarn_desc[pre_cost][]" class="form-control form-control-sm pre_cost" readonly></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow">-</button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10"><button type="button" class="btn btn-sm btn-primary" id="addYarnRow">+ Add Yarn</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Knitting Table (Same structure) --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Knitting</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="knittingTable">
                                <thead>
                                    <tr>
                                        <th>Description</th><th>Supplier</th><th>Yarn Count</th>
                                        <th>Unit Price($)</th><th>Consumption(Kg/Dz)</th><th>Wastage%</th>
                                        <th>Total Qty</th><th>Total Cost</th><th>Pre-Cost%</th><th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="knitRow">
                                        <td><input type="text" name="knitting_desc[fab_desc][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="knitting_desc[supplier_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="knitting_desc[yarn_count][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="knitting_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="knitting_desc[consumption][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="knitting_desc[w][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="knitting_desc[total_qty][]" class="form-control form-control-sm total_qty"></td>
                                        <td><input type="number" step="any" name="knitting_desc[total_cost][]" class="form-control form-control-sm total_cost" readonly></td>
                                        <td><input type="number" step="any" name="knitting_desc[pre_cost][]" class="form-control form-control-sm pre_cost" readonly></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow">-</button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10"><button type="button" class="btn btn-sm btn-primary" id="addKnitRow">+ Add Knitting</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Accessories Table --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Accessories</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="accessoriesTable">
                                <thead>
                                    <tr>
                                        <th>Description</th><th>Supplier</th><th>Unit Price($)</th>
                                        <th>Unit(In Number)</th><th>Consumption(Pc)</th><th>Wastage%</th>
                                        <th>Total Qty</th><th>Total Cost</th><th>Pre-Cost%</th><th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="accRow">
                                        <td><input type="text" name="accessories_desc[accessories_des][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="accessories_desc[supplier_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="accessories_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="accessories_desc[unit_number][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="accessories_desc[consumption][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="accessories_desc[w][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="accessories_desc[total_qty][]" class="form-control form-control-sm total_qty"></td>
                                        <td><input type="number" step="any" name="accessories_desc[total_cost][]" class="form-control form-control-sm total_cost" readonly></td>
                                        <td><input type="number" step="any" name="accessories_desc[pre_cost][]" class="form-control form-control-sm pre_cost" readonly></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow">-</button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10"><button type="button" class="btn btn-sm btn-primary" id="addAccRow">+ Add Accessories</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Grand Total --}}
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td colspan="9" class="text-right"><b>Grand Total:</b></td>
                                    <td>
                                        <b id="grandTotal">0</b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="row mb-3">
                    <div class="col-lg-12 text-center">
                        <button type="submit" class="btn btn-primary" disabled title="demo">Save</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>




@endsection



@push('js')
<script>
$(document).ready(function(){

    /* ============================
       DEMO / FUTURE BACKEND DATA
       ============================ */
    const demoData = {
        'STYLE001': {
            yarns: [
                { fab_desc: 'Cotton', supplier_name: 'Supplier A', yarn_count: '30s', unit_price: 10, consumption: 2, w: 5 },
                { fab_desc: 'Silk', supplier_name: 'Supplier B', yarn_count: '20s', unit_price: 15, consumption: 3, w: 1 },
            ],
            knitting: [
                { fab_desc: 'Knitting Fabric', supplier_name: 'Supplier K', yarn_count: 'NA', unit_price: 5, consumption: 1, w: 2 },
            ],
            accessories: [
                { accessories_des: 'Button', supplier_name: 'Acc Supplier', unit_price: 0.5, unit_number: 100, consumption: 1, w: 0 },
            ]
        },
        'All': {
            yarns: [],
            knitting: [],
            accessories: []
        }
    };


    /* ============================
       ORDER CHANGE
       ============================ */
    $('#orderSelect').on('change', function(){

        let selected = $(this).find('option:selected');

        $('#payment').val(selected.data('payment') || '');
        $('#party_name').val(selected.data('party') || '');
        $('#party_type').val(selected.data('type') || '');

        // Style dropdown reset
        let styles = (selected.data('styles') || '').toString().split(',');
        let $styleSelect = $('#styleSelect');
        $styleSelect.empty().append('<option value="">Select a Style</option>');

        if (selected.val()) {
            $.each(styles, function(i, s){
                $styleSelect.append('<option value="'+s+'">'+s+'</option>');
            });
            $styleSelect.prop('disabled', false);
        } else {
            $styleSelect.prop('disabled', true);
        }

        resetAllTables();
    });


    /* ============================
       STYLE CHANGE → FILL TABLES
       ============================ */
    $('#styleSelect').on('change', function(){

        let style = $(this).val();
        if (!style) return;

        let data = demoData[style] || { yarns:[], knitting:[], accessories:[] };

        fillTable('yarnTable', 'yarnRow', data.yarns,
            ['fab_desc','supplier_name','yarn_count','unit_price','consumption','w']
        );

        fillTable('knittingTable', 'knitRow', data.knitting,
            ['fab_desc','supplier_name','yarn_count','unit_price','consumption','w']
        );

        fillTable('accessoriesTable', 'accRow', data.accessories,
            ['accessories_des','supplier_name','unit_price','unit_number','consumption','w']
        );

        attachCalc();
        calculateTotals();
    });


    /* ============================
       FILL TABLE FUNCTION
       ============================ */
    function fillTable(tableId, rowClass, rows, fields) {

        let $tbody = $('#' + tableId + ' tbody');
        let $template = $tbody.find('tr.' + rowClass).first().clone();

        $tbody.empty();

        if (rows.length === 0) {
            $tbody.append($template);
            return;
        }

        $.each(rows, function(_, rowData){
            let $row = $template.clone();
            $.each(fields, function(_, field){
                $row.find('[name*="'+field+'"]').val(rowData[field] ?? '');
            });
            $tbody.append($row);
        });
    }


    /* ============================
       RESET ALL TABLES
       ============================ */
    function resetAllTables(){
        ['yarnTable','knittingTable','accessoriesTable'].forEach(function(id){
            let $tbody = $('#'+id+' tbody');
            let $template = $tbody.find('tr').first().clone();
            $tbody.empty().append($template);
        });
        calculateTotals();
    }


    /* ============================
       ADD ROW
       ============================ */
    function addRow(tableId, rowClass){
        let $tbody = $('#'+tableId+' tbody');
        let $newRow = $tbody.find('tr.'+rowClass).first().clone();
        $newRow.find('input').val('');
        $tbody.append($newRow);
        attachCalc();
    }

    $('#addYarnRow').click(()=>addRow('yarnTable','yarnRow'));
    $('#addKnitRow').click(()=>addRow('knittingTable','knitRow'));
    $('#addAccRow').click(()=>addRow('accessoriesTable','accRow'));


    /* ============================
       REMOVE ROW
       ============================ */
    $(document).on('click','.removeRow',function(){
        let $tbody = $(this).closest('tbody');
        if ($tbody.find('tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
        }
    });


    /* ============================
       CALCULATION
       ============================ */
    function attachCalc(){
        $('.calc').off('input').on('input', calculateTotals);
    }

    function calculateTotals(){

        let grandTotal = 0;

        ['yarnTable','knittingTable','accessoriesTable'].forEach(function(id){

            $('#'+id+' tbody tr').each(function(){

                let $row = $(this);

                let unit = parseFloat($row.find('[name$="[unit_price][]"]').val()) || 0;
                let cons = parseFloat($row.find('[name$="[consumption][]"]').val()) || 0;
                let w    = parseFloat($row.find('[name$="[w][]"]').val()) || 0;

                let totalQty  = cons + w;
                let totalCost = unit * totalQty;
                let preCost   = totalCost * 0.9;

                $row.find('.total_qty').val(totalQty.toFixed(2));
                $row.find('.total_cost').val(totalCost.toFixed(2));
                $row.find('.pre_cost').val(preCost.toFixed(2));

                grandTotal += totalCost;
            });
        });

        $('#grandTotal').text(grandTotal.toFixed(2));
    }


    /* ============================
       INIT
       ============================ */
    attachCalc();
    calculateTotals();

});
</script>
@endpush

@push('css')
 <style>
    .mini-info-table input{
        background: none !important;
        border: none;
    }
    .mini-info-table td{
        vertical-align: middle !important;
        font-size: 14px;
    }
 </style>
@endpush
