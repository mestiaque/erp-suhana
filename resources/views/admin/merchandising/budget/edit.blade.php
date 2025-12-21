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

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="" method="post" enctype="multipart/form-data" class="ajaxform_instant_reload" novalidate>
                @csrf

                {{-- Header: Order Info --}}
                {{-- ===================== COST SHEET HEADER TABLE ===================== --}}
                <div class="card mb-4">
                    <div class="card-body">

                        <h4 class="text-center fw-bold mb-3">COST SHEET</h4>

                        <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle headerTable">
                            <tbody>

                            <tr>
                                <th width="10%">BUYER</th>
                                <td width="30%">
                                    <input type="text" class="form-control form-control-sm" value="">
                                </td>

                                <th width="10%">P.I. No.</th>
                                <td width="30%">
                                    <input type="text" class="form-control form-control-sm" value="">
                                </td>
                                <th width="10%" class="text-center">GMTS PICTURE</th>
                            </tr>

                            <tr>
                                <th>TOTAL STYLES</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="">
                                </td>

                                <th>TOTAL POs</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="">
                                </td>
                                <td width="10%" rowspan="5">
                                    {{-- <img src="" alt="Attactment"> --}}
                                </td>
                            </tr>

                            <tr>
                                <th>ITEM</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="">
                                </td>

                                <th></th>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <th>L/C NO.</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm">
                                </td>

                                <th>L/C DT.</th>
                                <td>
                                    <input type="date" class="form-control form-control-sm">
                                </td>
                            </tr>

                            <tr>
                                <th>L/C VALUE</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="">
                                </td>

                                <th>SHIP DATE</th>
                                <td>
                                    <input type="date" class="form-control form-control-sm" value="">
                                </td>
                            </tr>

                            <tr>
                                <th>P.I. VALUE</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="">
                                </td>
                                <th>TOTAL QTY (PCS)</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="">
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        </div>

                    </div>
                </div>
                {{-- ===================== END HEADER TABLE ===================== --}}

                {{-- /Yarn Table --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Yarn</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="yarnTable">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Unit Price ($)</th>
                                        <th>TTL US $</th>
                                        <th>Item Wise Total Value</th>
                                        <th>%</th>
                                        <th>Company Name</th>
                                        <th>Payment Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="yarnRow">
                                        <td><input type="text" name="yarn_desc[description][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="yarn_desc[supplier][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="yarn_desc[qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="yarn_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="yarn_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="yarn_desc[item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="yarn_desc[percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="yarn_desc[company_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="yarn_desc[payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger removeRow">-</button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <button type="button" class="btn btn-sm btn-primary" id="addYarnRow">+ Add Yarn</button>
                                        </td>
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
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Unit Price ($)</th>
                                        <th>TTL US $</th>
                                        <th>Item Wise Total Value</th>
                                        <th>%</th>
                                        <th>Company Name</th>
                                        <th>Payment Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="knitRow">
                                        <td><input type="text" name="knitting_desc[description][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="knitting_desc[supplier][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="knitting_desc[qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="knitting_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="knitting_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="knitting_desc[item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="knitting_desc[percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="knitting_desc[company_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="knitting_desc[payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger removeRow">-</button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <button type="button" class="btn btn-sm btn-primary" id="addKnitRow">+ Add Knitting</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- dyeing Table (Same structure) --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Dyeing</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="dyeingTable">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Unit Price ($)</th>
                                        <th>TTL US $</th>
                                        <th>Item Wise Total Value</th>
                                        <th>%</th>
                                        <th>Company Name</th>
                                        <th>Payment Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="dyeingRow">
                                        <td>
                                            <input type="text"
                                                name="dyeing_desc[description][]"
                                                class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="dyeing_desc[supplier][]"
                                                class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                name="dyeing_desc[qty][]"
                                                class="form-control form-control-sm calc">
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                name="dyeing_desc[unit_price][]"
                                                class="form-control form-control-sm calc">
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                name="dyeing_desc[ttl_usd][]"
                                                class="form-control form-control-sm ttl_usd"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                name="dyeing_desc[item_total][]"
                                                class="form-control form-control-sm item_total"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                name="dyeing_desc[percent][]"
                                                class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="dyeing_desc[company_name][]"
                                                class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="number" step="any"
                                                name="dyeing_desc[payment_value][]"
                                                class="form-control form-control-sm">
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-danger removeRow">-</button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    id="addDyeingRow">
                                                + Add Dyeing
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- accessoris Table (Same structure) --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Accessories</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="accessoriesTable">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Unit Price ($)</th>
                                        <th>TTL US $</th>
                                        <th>Item Wise Total Value</th>
                                        <th>%</th>
                                        <th>Company Name</th>
                                        <th>Payment Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    {{-- Default Accessories --}}
                                    @php
                                        $accessories = [
                                            'BENGAL TAIMS',
                                            'Main Label',
                                            'Size Label',
                                            'Care Label',
                                            'Hangtag & Price Tag',
                                            'Sew/Thread',
                                            'Poly 8 Pcs/Bilster',
                                            'Woven Tap',
                                            'Woven Tap',
                                            'Woven Tap',
                                            'Woven Tap',
                                            'Woven Tap',
                                            '7PLY CTN 80Pcs',
                                            'Gum/Others',
                                        ];
                                    @endphp

                                    @foreach($accessories as $item)
                                        <tr class="accessoryRow">
                                            <td>
                                                <input type="text"
                                                    name="accessories_desc[description][]"
                                                    class="form-control form-control-sm"
                                                    value="{{ $item }}">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    name="accessories_desc[supplier][]"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input type="number" step="any"
                                                    name="accessories_desc[qty][]"
                                                    class="form-control form-control-sm calc">
                                            </td>
                                            <td>
                                                <input type="number" step="any"
                                                    name="accessories_desc[unit_price][]"
                                                    class="form-control form-control-sm calc">
                                            </td>
                                            <td>
                                                <input type="number" step="any"
                                                    name="accessories_desc[ttl_usd][]"
                                                    class="form-control form-control-sm ttl_usd"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="number" step="any"
                                                    name="accessories_desc[item_total][]"
                                                    class="form-control form-control-sm item_total"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="number" step="any"
                                                    name="accessories_desc[percent][]"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    name="accessories_desc[company_name][]"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input type="number" step="any"
                                                    name="accessories_desc[payment_value][]"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                        class="btn btn-sm btn-danger removeRow">-</button>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    id="addAccessoriesRow">
                                                + Add Accessories
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- print Table (Same structure) --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Print & Embroidery</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="printEmbroideryTable">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Unit Price ($)</th>
                                        <th>TTL US $</th>
                                        <th>Item Wise Total Value</th>
                                        <th>%</th>
                                        <th>Company Name</th>
                                        <th>Payment Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="printEmbRow">
                                        <td><input type="text" name="print_emb_desc[description][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="print_emb_desc[supplier][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="print_emb_desc[qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="print_emb_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="print_emb_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="print_emb_desc[item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="print_emb_desc[percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="print_emb_desc[company_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="print_emb_desc[payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow">-</button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <button type="button" class="btn btn-sm btn-primary" id="addPrintEmbRow">+ Add Row</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- cm Table (Same structure) --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>CM (Cutting & Making)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="cmTable">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Unit Price ($)</th>
                                        <th>TTL US $</th>
                                        <th>Item Wise Total Value</th>
                                        <th>%</th>
                                        <th>Company Name</th>
                                        <th>Payment Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="cmRow">
                                        <td><input type="text" name="cm_desc[description][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="cm_desc[supplier][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="cm_desc[qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="cm_desc[unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="cm_desc[ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="cm_desc[item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="cm_desc[percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="cm_desc[company_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="cm_desc[payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow">-</button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <button type="button" class="btn btn-sm btn-primary" id="addCMRow">+ Add Row</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- others Table (Same structure) --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Miscellaneous / Test</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="testTable">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Unit Price ($)</th>
                                        <th>TTL US $</th>
                                        <th>Item Wise Total Value</th>
                                        <th>%</th>
                                        <th>Company Name</th>
                                        <th>Payment Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <!-- 1. TEST -->
                                    <tr class="testRow">
                                        <td colspan="10">TEST</td>
                                    </tr>
                                    <tr class="testRow">
                                        <td><input type="text" name="test_desc[test][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="test_desc[test_supplier][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[test_qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[test_unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[test_ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[test_item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[test_percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="test_desc[test_company][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[test_payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRows d-none">-</button></td>
                                    </tr>

                                    <!-- 2. Buying Commission (Pcs) -->
                                    <tr class="testRow">
                                        <td colspan="10">Buying Commission (Pcs)</td>
                                    </tr>
                                    <tr class="testRow">
                                        <td><input type="number" step="any" name="test_desc[buying_commission][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[buying_commission_supplier][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[buying_commission_qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[buying_commission_unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[buying_commission_ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[buying_commission_item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[buying_commission_percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="test_desc[buying_commission_company][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[buying_commission_payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRows d-none">-</button></td>
                                    </tr>

                                    <!-- 3. Local Transportation -->
                                    <tr class="testRow">
                                        <td colspan="10">LOCAL TRANSPORTATION</td>
                                    </tr>
                                    <tr class="testRow">
                                        <td><input type="number" step="any" name="test_desc[local_transportation][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[local_transportation_supplier][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[local_transportation_qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[local_transportation_unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[local_transportation_ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[local_transportation_item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[local_transportation_percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="test_desc[local_transportation_company][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[local_transportation_payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRows d-none">-</button></td>
                                    </tr>

                                    <!-- 4. Bank & Commercial -->
                                    <tr class="testRow">
                                        <td colspan="10">BANK & COMMERCIAL</td>
                                    </tr>
                                    <tr class="testRow">
                                        <td><input type="number" step="any" name="test_desc[bank_commercial][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_supplier][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="test_desc[bank_commercial_company][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRows d-none">-</button></td>
                                    </tr>

                                    <!-- 5. Commission % -->
                                    <tr class="testRow">
                                        <td colspan="10">COMMISSION %</td>
                                    </tr>
                                    <tr class="testRow">
                                        <td><input type="number" step="any" name="test_desc[commission_percent][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_supplier][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="test_desc[commission_percent_company][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRows d-none">-</button></td>
                                    </tr>

                                    <!-- 6. Freight -->
                                    <tr class="testRow">
                                        <td colspan="10">FREIGHT</td>
                                    </tr>
                                    <tr class="testRow">
                                        <td><input type="number" step="any" name="test_desc[freight][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[freight_supplier][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[freight_qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[freight_unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[freight_ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[freight_item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[freight_percent][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="test_desc[freight_company][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[freight_payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRows d-none">-</button></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Summary / Total Cost & Payment</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="summaryTable">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Percentage / Note</th>
                                        <th>Amount (US$)</th>
                                        <th>Paid Amount (US$)</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <!-- 1. Total Cost / Profit -->
                                    <tr>
                                        <td>TOTAL COST / PROFIT</td>
                                        <td>90%</td>
                                        <td>68412.43</td>
                                        <td></td>
                                        <td>Total Expenditure</td>
                                    </tr>

                                    <!-- 2. Total Value of Order -->
                                    <tr>
                                        <td>TOTAL VALUE OF ORDER</td>
                                        <td></td>
                                        <td>76394.28</td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    <!-- 3. Reservation -->
                                    <tr>
                                        <td>Reservation</td>
                                        <td></td>
                                        <td>7981.85</td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    <!-- 4. BBLC / BTB -->
                                    <tr>
                                        <td>BTB</td>
                                        <td>75%</td>
                                        <td>57295.71</td>
                                        <td>47989.73</td>
                                        <td>Yarn, Dyeing, Print & Acces.</td>
                                    </tr>

                                    <!-- 5. CASH -->
                                    <tr>
                                        <td>CASH</td>
                                        <td>0%</td>
                                        <td>0.00</td>
                                        <td>2437.50</td>
                                        <td>Knitting</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Production Cost</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="productionCostTable">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Machine/Use</th>
                                        <th>O/Cost ($)</th>
                                        <th>Total Cost ($)</th>
                                        <th>Product/Day</th>
                                        <th>CM/Doz ($)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="prod_cost[item][]" class="form-control form-control-sm" value="T-Shirt"></td>
                                        <td><input type="number" step="any" name="prod_cost[machine_use][]" class="form-control form-control-sm" value="21"></td>
                                        <td><input type="number" step="any" name="prod_cost[ocost][]" class="form-control form-control-sm" value="25.00"></td>
                                        <td><input type="number" step="any" name="prod_cost[total_cost][]" class="form-control form-control-sm" value="525.00" readonly></td>
                                        <td><input type="text" name="prod_cost[product_day][]" class="form-control form-control-sm" value="2000 Pcs"></td>
                                        <td><input type="number" step="any" name="prod_cost[cm_doz][]" class="form-control form-control-sm" value="3.15" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
    .headerTable td, .headerTable th{
        vertical-align: middle !important;
    }
    .headerTable td{
        padding: 0px !important;
    }
</style>
@endpush
