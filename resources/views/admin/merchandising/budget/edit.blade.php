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

            <form action="{{ route('admin.budgetAction', ['store']) }}" method="post" enctype="multipart/form-data" class="">
                @csrf

                {{-- Header: Order Info --}}
                {{-- ===================== COST SHEET HEADER TABLE ===================== --}}
                <div class="card mb-4">
                    <div class="card-body">

                        <h4 class="text-center fw-bold mb-3">COST SHEET</h4>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle headerTable">
                                <tbody>
{{-- @dd($budget->pi_no) --}}
                                    <tr>
                                        <th width="10%">P.I. No.</th>
                                        <td width="30%">
                                        @if($budget && $budget->pi_no)
                                            <input type="hidden" name="budget[pi_no]" value="{{ $budget->pi_no }}">
                                            <input type="text" class="form-control form-control-sm" value="{{ $budget->pi_no ?? '' }}" readonly>
                                        @else
                                            <select name="budget[pi_no]" id="pi_id" class="form-control form-control-sm">
                                                <option value=""> -- Select PI -- </option>
                                                @foreach ($pis as $pi)
                                                    <option value="{{ $pi['id'] }}"
                                                            data-pi-json='@json($pi, JSON_HEX_APOS | JSON_HEX_QUOT)'>
                                                        {{ $pi['pi_no'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                        </td>
                                        <th width="10%">BUYER</th>
                                        <td width="30%">
                                            <input type="text" name="budget[buyer]" class="form-control form-control-sm buyer_name" value="{{ $budget->buyer ?? '' }}" readonly>
                                        </td>

                                        <th width="10%" class="text-center">GMTS PICTURE</th>
                                    </tr>

                                    <tr>
                                        <th>TOTAL STYLES</th>
                                        <td>
                                            <input type="text" name="budget[total_styles]" class="form-control form-control-sm style_count" value="{{ $budget->total_styles ?? '' }}" readonly>
                                        </td>

                                        <th>TOTAL POs</th>
                                        <td>
                                            <input type="text" name="budget[total_pos]" class="form-control form-control-sm order_count" value="{{ $budget->total_pos ?? '' }}" readonly>
                                        </td>

                                        <td width="10%" rowspan="5">
                                            {{-- <img src="" alt="Attachment"> --}}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>ITEM</th>
                                        <td>
                                            <input type="text" name="budget[item]" class="form-control form-control-sm" value="" readonly>
                                        </td>
                                        <th></th>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <th>L/C NO.</th>
                                        <td>
                                            <input type="text" name="budget[lc_no]" class="form-control form-control-sm" readonly>
                                        </td>

                                        <th>L/C DT.</th>
                                        <td>
                                            <input type="date" name="budget[lc_date]" class="form-control form-control-sm" readonly>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>L/C VALUE</th>
                                        <td>
                                            <input type="text" name="budget[lc_value]" class="form-control form-control-sm" value="" readonly>
                                        </td>

                                        <th>SHIP DATE</th>
                                        <td>
                                            <input type="date" name="budget[ship_date]" class="form-control form-control-sm" value="" readonly>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>P.I. VALUE</th>
                                        <td>
                                            <input type="number" name="budget[pi_value]" class="form-control form-control-sm pi_value" id="pi-value" value="{{ $budget->pi_value ?? 0.00 }}" readonly>
                                        </td>

                                        <th>TOTAL QTY (PCS)</th>
                                        <td>
                                            <input type="number" name="budget[total_qty]" class="form-control form-control-sm total_qty" value="{{ $budget->total_qty ?? 0 }}" readonly>
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
                                        <td><input type="number" step="any" name="yarn_desc[percent][]" class="form-control form-control-sm" readonly></td>
                                        <td><input type="text" name="yarn_desc[company_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" step="any" name="yarn_desc[payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
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
                                        <td><input type="number" step="any" name="knitting_desc[percent][]" class="form-control form-control-sm" readonly></td>
                                        <td><input type="text" name="knitting_desc[company_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" step="any" name="knitting_desc[payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
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
                                     readonly            class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="dyeing_desc[company_name][]"
                                                class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="text" step="any"
                                                name="dyeing_desc[payment_value][]"
                                                class="form-control form-control-sm">
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <button type="button" class="btn btn-sm btn-primary" id="addDyeingRow"> + Add Dyeing </button>
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
                                                        readonly                class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    name="accessories_desc[company_name][]"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <input type="text" step="any"
                                                    name="accessories_desc[payment_value][]"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                        class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <button type="button" class="btn btn-sm btn-primary" id="addAccessoriesRow"> + Add Accessories </button>
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
                                        <td><input type="number" step="any" name="print_emb_desc[percent][]" class="form-control form-control-sm" readonly></td>
                                        <td><input type="text" name="print_emb_desc[company_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" step="any" name="print_emb_desc[payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button></td>
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
                                        <td><input type="number" step="any" name="cm_desc[percent][]" class="form-control form-control-sm" readonly></td>
                                        <td><input type="text" name="cm_desc[company_name][]" class="form-control form-control-sm"></td>
                                        <td><input type="text" step="any" name="cm_desc[payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-times"></i></button></td>
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
                                        <td><input type="number" step="any" name="test_desc[test_percent][]" class="form-control form-control-sm testPer"  readonly></td>
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
                                        <td><input type="number" step="any" name="test_desc[buying_commission_percent][]" class="form-control form-control-sm testPer" readonly></td>
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
                                        <td><input type="number" step="any" name="test_desc[local_transportation_percent][]" class="form-control form-control-sm testPer" readonly></td>
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
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_percent][]" class="form-control form-control-sm testPer" readonly></td>
                                        <td><input type="text" name="test_desc[bank_commercial_company][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[bank_commercial_payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRows d-none">-</button></td>
                                    </tr>

                                    <!-- 5. Commission % -->
                                    <tr class="testRow">
                                        <td colspan="10">COMMISSION %</td>
                                    </tr>
                                    <tr class="testRow">
                                        <td><input type="number" step="any" name="test_desc[commission_percent][]" class="form-control form-control-sm  readonlycalc"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_supplier][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_qty][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_unit_price][]" class="form-control form-control-sm calc"></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_ttl_usd][]" class="form-control form-control-sm ttl_usd" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_item_total][]" class="form-control form-control-sm item_total" readonly></td>
                                        <td><input type="number" step="any" name="test_desc[commission_percent_percent][]" class="form-control form-control-sm testPer" readonly></td>
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
                                        <td><input type="number" step="any" name="test_desc[freight_percent][]" class="form-control form-control-sm    testPer " readonly></td>
                                        <td><input type="text" name="test_desc[freight_company][]" class="form-control form-control-sm"></td>
                                        <td><input type="number" step="any" name="test_desc[freight_payment_value][]" class="form-control form-control-sm"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRows d-none">-</button></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h5>Summary / Total Cost & Payment</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="summaryTable">
                                <tbody>

                                    <!-- 1. Total Value of Order -->
                                    <tr>
                                        <th>TOTAL VALUE OF ORDER</th>
                                        <td>
                                            <input type="number" step="any" name="summary[pi_value]" class="form-control form-control-sm summary_pi_value" value="0.00" readonly comment="User input for PI value">
                                        </td>

                                        <th class="text-right">Total Expenditure</th>
                                        <td>
                                            <input type="number" step="any" name="summary[total_expenditure]" class="form-control form-control-sm summary_total_expenditure" value="0" readonly comment="Sum of all Item Wise Total Values, calculated by JS">
                                        </td>

                                        <td>
                                            <input type="text" name="summary[expenditure_percent]" class="form-control form-control-sm summary_expenditure_percent" value="0%" readonly comment="Percentage of PI value calculated from total expenditure">
                                        </td>
                                    </tr>

                                    <!-- 2. Reservation -->
                                    <tr>
                                        <th colspan="3" class="text-right">Reservation</th>
                                        <td colspan="2">
                                            <input type="number" step="any" name="summary[reservation]" class="form-control form-control-sm summary_reservation" value="0" readonly comment="PI Value minus Total Expenditure, calculated by JS">
                                        </td>
                                    </tr>

                                    <!-- 3. BTB -->
                                    <tr>
                                        <td>BTB</td>
                                        <td class="btb_percent_cell d-flex" style="vertical-align: middle" comment="User input for % of PI value allocated to BTB">
                                            <input type="number" step="any" name="summary[btb_percent]" class="form-control form-control-sm btb_percent_input w-50" value="">
                                            <span type="text" readonly class="form-control form-control-sm" style="width:3rem;background: none; border: none;"> % = </span>
                                            <input type="number" step="any" name="summary[btb_value]" class="form-control form-control-sm btb_value w-50" value="0" readonly>
                                        </td>
                                        <th rowspan="2" class="text-right">BBLC</th>
                                        <td rowspan="4" colspan="2" class="bbcl_detail_cell">
                                            <input type="text" name="summary[bbcl_yarn_dyeing_print_access]" class="form-control form-control-sm bbcl_yarn_dyeing_print_access mb-1" value="0 Yarn, Dyeing, Print & Acces" readonly>
                                            <input type="text" name="summary[bbcl_knitting]" class="form-control form-control-sm bbcl_knitting" value="0 Knitting" readonly>
                                        </td>
                                    </tr>

                                    <!-- 4. CASH -->
                                    <tr>
                                        <td>CASH</td>
                                        <td class="cash_percent_cell d-flex" comment="User input for % of PI value allocated to CASH">
                                            <input type="number" step="any" name="summary[cash_percent]" class="form-control form-control-sm cash_percent_input w-50" value="">
                                            <span type="text" readonly class="form-control form-control-sm" style="width:3rem;background: none; border: none;"> % = </span>
                                            <input type="number" step="any" name="summary[cash_value]" class="form-control form-control-sm cash_value w-50" value="0" readonly>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- production Cost  --}}
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
                                        <td><input type="text" name="prod_cost[item][]" class="form-control form-control-sm" value=""></td>
                                        <td><input type="number" step="any" name="prod_cost[machine_use][]" class="form-control form-control-sm" value=""></td>
                                        <td><input type="number" step="any" name="prod_cost[ocost][]" class="form-control form-control-sm" value=""></td>
                                        <td><input type="number" step="any" name="prod_cost[total_cost][]" class="form-control form-control-sm" value="" ></td>
                                        <td><input type="text" name="prod_cost[product_day][]" class="form-control form-control-sm" value=""></td>
                                        <td><input type="number" step="any" name="prod_cost[cm_doz][]" class="form-control form-control-sm" value="" ></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="row mb-3">
                    <div class="col-lg-12 text-center">
                        <button type="submit" class="btn btn-primary" title="demo">Save</button>
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
    ADD ROW FUNCTION
    ============================ */
    function addRow(tableId, rowClass){
        let $tbody = $('#' + tableId + ' tbody');
        let $newRow = $tbody.find('tr.' + rowClass).first().clone();

        // Clear all inputs
        $newRow.find('input').val('');

        // Hide % / Company / Payment in new row (except testTable)
        if(tableId !== '#testTable'){
            $newRow.find('input[name*="[percent]"], input[name*="[company_name]"], input[name*="[payment_value]"]').val('').hide();
        }

        $tbody.append($newRow);
        calculateAllTables();
    }

    /* ============================
    REMOVE ROW FUNCTION
    ============================ */
    $(document).on('click', '.removeRow, .removeRows', function(){
        let $tbody = $(this).closest('tbody');
        if($tbody.find('tr').length > 1){
            $(this).closest('tr').remove();
            calculateAllTables();
        }
    });

    /* ============================
    CALCULATION FOR SINGLE TABLE
    ============================ */
    function calculateTable(tableId){
        let table = $(tableId);
        let piValue = parseFloat($('#pi-value').val()) || 0;
        let total = 0;

        let $rows = table.find('tbody tr');

        $rows.each(function(){
            let $row = $(this);
            let qty = parseFloat($row.find('input[name*="[qty]"], input[name*="_qty]"]').val()) || 0;
            let unit = parseFloat($row.find('input[name*="[unit_price]"], input[name*="_unit_price]"]').val()) || 0;
            let ttlInput = $row.find('input.ttl_usd');
            let itemTotalInput = $row.find('input.item_total');
            let percentInput = $row.find('input[name*="[percent]"]');

            let ttl = qty * unit;
            if(ttlInput.length) ttlInput.val(ttl.toFixed(2));

            // If this is testTable, calculate per row
            if(tableId === '#testTable'){
                if(itemTotalInput.length) itemTotalInput.val(ttl.toFixed(2));
                if(percentInput.length){
                    let percent = piValue ? (ttl / piValue * 100) : 0;
                }
                let percentInputx = $row.find('.testPer');
                if(percentInputx.length){
                    let percent = piValue ? (ttl / piValue * 100) : 0;
                    percentInputx.val(percent.toFixed(2));
                }

            } else {
                total += ttl;
            }
        });

        // Normal tables: show last row item_total & percent
        if(tableId !== '#testTable'){
            $rows.find('input.item_total, input[name*="[percent]"], input[name*="[company_name]"], input[name*="[payment_value]"]').hide().val('');
            let $lastRow = $rows.last();
            $lastRow.find('input.item_total').show().val(total.toFixed(2));
            let percentInput = $lastRow.find('input[name*="[percent]"]');
            if(percentInput.length){
                let percent = piValue ? (total / piValue * 100) : 0;
                percentInput.show().val(percent.toFixed(2));
            }
            $lastRow.find('input[name*="[company_name]"], input[name*="[payment_value]"]').show();
        } else {
            // For testTable: sum all item_total for summary
            $rows.find('input.item_total').each(function(){
                total += parseFloat($(this).val()) || 0;
            });
        }

        return total;
    }

    function calculateTestTable(){
        let piValue = parseFloat($('#pi-value').val()) || 0;
        let $rows = $('#testTable tbody tr.testRow');

        $rows.each(function(){
            let $row = $(this);

            // Only process rows with actual inputs (skip header/label rows)
            let $qtyInput = $row.find('input[name*="[qty]"]');
            let $unitInput = $row.find('input[name*="[unit_price]"]');
            let $ttlInput = $row.find('input.ttl_usd');
            let $itemTotalInput = $row.find('input.item_total');
            let $percentInput = $row.find('.testPer');

            if($qtyInput.length && $unitInput.length){
                let qty = parseFloat($qtyInput.val()) || 0;
                let unit = parseFloat($unitInput.val()) || 0;
                let ttl = qty * unit;

                // Set TTL and Item Total
                if($ttlInput.length) $ttlInput.val(ttl.toFixed(2));
                if($itemTotalInput.length) $itemTotalInput.val(ttl.toFixed(2));

                // Set %
                if($percentInput.length){
                    let percent = piValue ? (ttl / piValue * 100) : 0;
                    $percentInput.val(percent.toFixed(2));
                }
            }
        });
    }

    /* ============================
    CALCULATE ALL TABLES + SUMMARY
    ============================ */
    function calculateAllTables(){
        let piValue = parseFloat($('#pi-value').val()) || 0;
        let grandTotal = 0;

        let tableIds = ['#yarnTable', '#knittingTable', '#dyeingTable', '#accessoriesTable', '#printEmbroideryTable', '#cmTable', '#testTable'];

        tableIds.forEach(function(id){
            if($(id).length){
                let total = calculateTable(id);
                grandTotal += total;
            }
        });

        // ==============================
        // Update Summary Table
        // ==============================
        $('.summary_total_expenditure').val(grandTotal.toFixed(2));
        $('.summary_expenditure_percent').val(piValue ? ((grandTotal/piValue)*100).toFixed(2)+'%' : '0%');
        $('.summary_reservation').val((piValue - grandTotal).toFixed(2));

        // BTB & CASH
        let btbPercent = parseFloat($('.btb_percent_input').val()) || 0;
        let cashPercent = parseFloat($('.cash_percent_input').val()) || 0;

        let btbValue = piValue * btbPercent / 100;
        let cashValue = piValue * cashPercent / 100;

        $('.btb_value').val(btbValue.toFixed(2));
        $('.cash_value').val(cashValue.toFixed(2));

        // BBLC details
        let bbclYarnDyeingPrint = 0;
        let bbclKnitting = 0;

        bbclYarnDyeingPrint += parseFloat($('#yarnTable tbody tr:last input.item_total').val()) || 0;
        bbclYarnDyeingPrint += parseFloat($('#dyeingTable tbody tr:last input.item_total').val()) || 0;
        bbclYarnDyeingPrint += parseFloat($('#accessoriesTable tbody tr:last input.item_total').val()) || 0;
        bbclYarnDyeingPrint += parseFloat($('#printEmbroideryTable tbody tr:last input.item_total').val()) || 0;

        bbclKnitting += parseFloat($('#knittingTable tbody tr:last input.item_total').val()) || 0;

        $('.bbcl_yarn_dyeing_print_access').val(bbclYarnDyeingPrint.toFixed(2) + ' Yarn, Dyeing, Print & Acces');
        $('.bbcl_knitting').val(bbclKnitting.toFixed(2) + ' Knitting');
    }

    // Trigger calculation on input
    $(document).on('keyup input change paste cut drop blur', '#pi-value, #pi_id, input.calc, .btb_percent_input, .cash_percent_input', function(){
        calculateAllTables();
        calculateTestTable();
    });

    // Initial calculation
    calculateAllTables();

    /* ============================
    BIND ADD ROW BUTTONS
    ============================ */
    $('#addYarnRow').click(()=>addRow('yarnTable','yarnRow'));
    $('#addKnitRow').click(()=>addRow('knittingTable','knitRow'));
    $('#addDyeingRow').click(()=>addRow('dyeingTable','dyeingRow'));
    $('#addAccessoriesRow').click(()=>addRow('accessoriesTable','accessoryRow'));
    $('#addPrintEmbRow').click(()=>addRow('printEmbroideryTable','printEmbRow'));
    $('#addCMRow').click(()=>addRow('cmTable','cmRow'));
    $('#addTestRow').click(()=>addRow('testTable','testRow'));

});
</script>


<script>
function getSelectedPiJson() {
    let option = $('#pi_id option:selected');
    let jsonStr = option.attr('data-pi-json'); // get raw string
    let jsonObj = JSON.parse(jsonStr); // parse to JS object
    return jsonObj;
}



// On change
$(document).on('change', '#pi_id', function () {
    let selectedJson = getSelectedPiJson();
    $('.buyer_name').val(selectedJson.buyer_name)
    $('.pi_value').val(parseFloat(selectedJson.total_bill).toFixed(2));
    $('.summary_pi_value').val(parseFloat(selectedJson.total_bill).toFixed(2));
    $('.total_qty').val(selectedJson.total_qty)
    $('.order_count').val(selectedJson.order_count)
    $('.style_count').val(selectedJson.style_count)
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
    td{
        vertical-align: middle !important;
    }
</style>
@endpush
