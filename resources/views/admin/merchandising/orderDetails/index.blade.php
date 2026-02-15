@extends(adminTheme().'layouts.app')

@section('title')
    <title>{{ websiteTitle('Order Details List') }}</title>
@endsection

@section('contents')
<div class="">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Order Details List</h3>
            <div class="d-flex gap-1">

                <form action="{{ route('admin.orderDetails') }}" method="GET" target="_blank" class="d-inline">
                    <input type="hidden" name="print" value="true">
                    @foreach(request()->except('print') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit" class="btn btn-info btn-sm mr-1">
                        <i class="fa fa-print"></i> Print
                    </button>
                </form>

                @can('order_details.add')
                    <a href="{{ route('admin.orderDetailsAction','create') }}" class="btn btn-primary btn-sm mr-1">
                        <i class="bx bx-plus"></i> Add Order Details
                    </a>
                @endcan

                <a href="{{ route('admin.orderDetails') }}" class="btn btn-warning btn-sm">
                    <i class="bx bx-rotate-left"></i> Reset
                </a>

            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search & Date Filter -->
            <form action="{{ route('admin.orderDetails') }}" class="mb-3 d-none">
                <div class="row g-2">
                    <div class="col-md-6 d-flex">
                        <input type="date" name="startDate" value="{{ request()->startDate }}" class="form-control me-1">
                        <input type="date" name="endDate" value="{{ request()->endDate }}" class="form-control">
                    </div>

                    <div class="col-md-4 d-flex">
                        <input type="text" name="search" value="{{ request()->search ?? '' }}" class="form-control me-1"
                               placeholder="Search Order, Buyer, Style, Merchant, Invoice, Order, Composition, Fabrication, PI No">
                        <button type="submit" class="btn btn-success btn-sm">Search</button>
                    </div>
                </div>
            </form>

            <form action="{{ route('admin.orderDetails') }}" method="GET" class="mb-3">

                <div class="row g-2 align-items-end">

                    {{-- Buyer --}}
                    <div class="col-md-1 pr-0" style="margin-left:5px">
                        <label class="form-label mb-0">Buyer</label>
                        <select name="buyer" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer }}"
                                    {{ request('buyer')==$buyer?'selected':'' }}>
                                    {{ $buyer }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Customer --}}
                    <div class="col-md-1 pr-0 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">Customer</label>
                        <select name="brand" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}"
                                    {{ request('brand')==$brand?'selected':'' }}>
                                    {{ $brand }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Style --}}
                    <div class="col-md-1 pr-0 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">Style</label>
                        <select name="style" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($styles as $style)
                                <option value="{{ $style }}"
                                    {{ request('style')==$style?'selected':'' }}>
                                    {{ $style }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Order No --}}
                    <div class="col-md-1 pr-0 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">Order No</label>
                        <select name="order_no" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($orderNos as $no)
                                <option value="{{ $no }}"
                                    {{ request('order_no')==$no?'selected':'' }}>
                                    {{ $no }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Shipment Date --}}
                    <div class="col-md-1 pr-0 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">Ship Date</label>
                        <select name="shipment_date" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($shipmentDates as $date)
                                <option value="{{ $date->format('Y-m-d') }}"
                                    {{ request('shipment_date')==$date->format('Y-m-d')?'selected':'' }}>
                                    {{ $date->format('d.m.Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Fabric --}}
                    <div class="col-md-1 pr-0 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">Fabric</label>
                        <select name="fabric" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($fabrics as $fabric)
                                <option value="{{ $fabric }}"
                                    {{ request('fabric')==$fabric?'selected':'' }}>
                                    {{ $fabric }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- PI Number --}}
                    <div class="col-md-1 pr-0 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">PI No</label>
                        <select name="pi" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($piNumbers as $pi)
                                <option value="{{ $pi }}"
                                    {{ request('pi')==$pi?'selected':'' }}>
                                    {{ $pi }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Status --}}
                    <div class="col-md-1 pr-0 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                            <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmed</option>
                            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-1 pr-0 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">Keyword</label>
                        <input type="text" name="search" value="{{ request()->search ?? '' }}" class="form-control form-control-sm"
                               placeholder="Search Order, Buyer, Style, Merchant, Invoice, Order, Composition, Fabrication, PI No">
                    </div>


                    {{-- Button --}}
                    <div class="col-md-2 pl-0" style="margin-left:5px">
                        <label class="form-label mb-0">&nbsp;</label>
                        <button type="submit" class="btn btn-success btn-sm w-10">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>

                </div>

            </form>



            <div class="row g-3" style="margin:0 -5px;"> <!-- g-3 adds gutter between cards -->

                <div class="col-12 col-md-2" style="padding:5px;">
                    <div class="custom-card" style="">
                        <!--<div style="font-size:30px;">👕</div>-->
                        <div style="font-weight:bold;">Total</div>
                        <div class="text-success">Qty: {{ number_format($totalOrderQty) }}</div>
                        <div class="text-danger">Balance: 0</div>
                    </div>
                </div>


                <div class="col-12 col-md-2" style="padding:5px;" >
                    <div class="custom-card" style="">
                        <!--<div style="font-size:30px;">✂️</div>-->
                        <div style="font-weight:bold;">Cutting</div>
                        <div class="text-success">Qty: {{ number_format($grandTotalCuttingOutput) }}</div>
                        <div class="text-danger">Balance: {{ number_format($totalOrderQty - $grandTotalCuttingOutput) }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-2" style="padding:5px;">
                    <div class="custom-card" style="">
                        <!--<div style="font-size:30px;">🎨</div>-->
                        <div style="font-weight:bold;">Print & Embroidery</div>
                        <div class="text-success">Qty: 0</div>
                        <div class="text-danger">Balance: 0</div>
                    </div>
                </div>

                <div class="col-12 col-md-2" style="padding:5px;">
                    <div class="custom-card" style="">
                        <!--<div style="font-size:30px;">🧵</div>-->
                        <div style="font-weight:bold;">Sewing Output</div>
                        <div class="text-success">Qty: {{ number_format($grandTotalSewingOutput) }}</div>
                        <div class="text-danger">Balance: {{ number_format($totalOrderQty - $grandTotalSewingOutput) }}</div>
                    </div>
                </div>

                <div class="col-12 col-md-2" style="padding:5px;">
                    <div class="custom-card" style="">
                        <!--<div style="font-size:30px;">📦</div>-->
                        <div style="font-weight:bold;">Packing</div>
                        <div class="text-success">Qty: 0</div>
                        <div class="text-danger">Balance: 0</div>
                    </div>
                </div>

                <div class="col-12 col-md-2" style="padding:5px;">
                    <div class="custom-card" style="">
                        <!--<div style="font-size:30px;">🚚</div>-->
                        <div style="font-weight:bold;">Shipped</div>
                        <div class="text-success">Qty: 0</div>
                        <div class="text-danger">Balance: 0</div>
                    </div>
                </div>

            </div>

            <div class="row mb-0">
                <div class="col-md-12">
                    <ul class="statuslist p-0 mb-0">
                        <li class=""><a class=" {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.orderDetails') }}">All ({{ $totals->total }})</a></li>
                        <li class=""><a class=" {{ request('status')=='pending' ? 'active' : '' }}" href="{{ route('admin.orderDetails',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li class=""><a class=" {{ request('status')=='confirmed' ? 'active' : '' }}" href="{{ route('admin.orderDetails',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                    </ul>
                </div>
            </div>


            <div class="table-responsive table-wrapper">
                <table class="table table-bordered table-stripeds table-hovers order-table">
                    <thead class=" sticky-top text-white">
                        {{-- Header --}}
                        <tr>
                            <th class="sticky-col col-sl">SL</th>
                            <th class="sticky-col col-buyer">Buyer</th>
                            <th class="sticky-col col-brand">Brand / Customer</th>
                            <th class="sticky-col col-style">Style No</th>
                            <th class="sticky-col col-order">Order / PO No</th>
                            <th class="sticky-col col-qty">Order Qty</th>

                            <!-- New output columns to the right of Order Qty -->
                            <th>Cutting</th>
                            <th>Print &amp; Emb</th>
                            <th>Sewing Output</th>
                            <th>Packing</th>
                            <th>Shipped</th>

                            <th>Shipment Date</th>
                            <th>Fabrication</th>
                            <th>Remarks</th>
                            <th>P.I No</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($orderDetails as $i => $order)
                        <tr>
                            {{-- SL + 3 DOT --}}
                            <td class="sticky-col col-sl">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ $orderDetails->firstItem() + $i }}</span>

                                    {{-- REPLACED: dropdown -> action button that opens a popup modal --}}
                                    <div class="no-collapse">
                                        <button type="button"
                                            class="text-dark btn btn-link p-0 action-trigger"
                                            data-view-modal="#viewModal_{{ $order->id }}"
                                            data-edit-url="{{ route('admin.orderDetailsAction',['edit',$order->id]) }}"
                                            data-delete-url="{{ route('admin.orderDetailsAction',['delete',$order->id]) }}"
                                            title="Actions">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td class="sticky-col col-buyer">{{ $order->buyer_name ?? '--' }}</td>
                            <td class="sticky-col col-brand">{{ $order->company_name ?? '--' }}</td>
                            <td class="sticky-col col-style">{{ $order->style_no ?? '--' }}</td>
                            <td class="sticky-col col-order">{{ $order->order_no ?? '--' }}</td>
                            <td class="sticky-col col-qty">{{ number_format($order->total_qty) }}</td>
                            <td>{{ number_format($order->getCutQty() ?? 0) }}</td>
                            <td>{{ number_format($order->print_emb_output ?? $order->print_emb ?? 0) }}</td>
                            <td>{{ number_format($order->getSewingQty() ?? 0) }}</td>
                            <td>{{ number_format($order->packing_output ?? $order->packing ?? 0) }}</td>
                            <td>{{ number_format($order->shipped_qty ?? $order->shipped ?? 0) }}</td>

                            <td>{{ $order->shipment_date?->format('d.m.Y') ?? '--' }}</td>
                            <td>{{ $order->fabrication ?? '--' }}</td>
                            <td>{{ $order->remarks ?? '--' }}</td>
                            <td>{{ $order?->piItem?->pi?->pi_no ?? '--' }}</td>
                            <td>
                                @php
                                    $statusClass = [
                                        'temp'=>'secondary',
                                        'pending'=>'warning',
                                        'confirmed'=>'info',
                                        'completed'=>'success',
                                        'canceled'=>'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusClass[$order->status] ?? 'secondary' }} text-white">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center text-muted">No order details found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{ $orderDetails->withQueryString()->links('pagination') }}
                </div>
            </div>

        </div>
    </div>
</div>

@include(adminTheme().'merchandising.orderDetails.details')

{{-- ADDED: centralized Action Modal (small popup) --}}
<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-2">
        <div class="list-group">
          <button type="button" class="list-group-item list-group-item-action" id="actionViewBtn">
            <i class="fa fa-eye"></i> View
          </button>
          <a href="#" class="list-group-item list-group-item-action" id="actionEditBtn">
            <i class="bx bx-edit"></i> Edit
          </a>
          <a href="#" class="list-group-item list-group-item-action text-danger" id="actionDeleteBtn">
            <i class="bx bx-trash"></i> Delete
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('css')
<style>
.main-content{
            min-height: 80vh !important;
            height: 100vh !important;
        }
    .custom-card {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 5px;
    text-align: center;
    transition: all 0.3s ease-in-out; /* অ্যানিমেশন স্মুথ করবে */
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* হোভার ইফেক্ট */
.custom-card:hover {
    transform: translateY(-8px); /* কার্ডটি উপরে উঠবে */
    box-shadow: 0 12px 20px rgba(0,0,0,0.1); /* শ্যাডো বাড়বে */
    border-color: #007bff; /* বর্ডারের রঙ নীল হবে */
}

/* আইকন হোভার করলে বড় হবে */
.custom-card:hover .card-icon {
    transform: scale(1.2);
    transition: transform 0.3s ease;
}

.table-wrapper{
       max-height: 58vh;
    overflow-x: auto;
    overflow-y: auto;
    position: relative;
    cursor: grab; /* shows draggable cursor */
}

.table-wrapper.active{
    cursor: grabbing;
}

/* small adjustment for modal action list */
#actionModal .list-group-item { cursor: pointer; }

/* THEAD FIX */
.order-table thead th{
    position: sticky;
    top: 0;
    /* z-index: 10; */
    /* background: #075aad; */
}


/* STICKY COLUMNS */
.sticky-col{
    position: sticky;
    left: 0;
    background: #ffffff;
    z-index: 5;
}

th.sticky-col{
    background: #7c7c7c !important;
    z-index: 115;    }

/* COLUMN WIDTH + LEFT POSITION */
/* adjusted smaller widths and updated left offsets */
.col-sl{ left:0;    min-width:60px;  }
.col-buyer{ left:60px;  min-width:100px; }
.col-brand{ left:160px; min-width:130px; }
.col-style{ left:290px; min-width:100px; }
.col-order{ left:390px; min-width:120px; }
.col-qty{ left:510px;   min-width:90px;  }

.filter-row th{
    /* background:#0ce61736 !important; */
}
/* table + td must allow overflow */
.order-table,
.order-table td,
.order-table th{
    overflow: visible !important;
}
.a-dropdown-menu{
    z-index: 99999 !important;
    position: relative;
    padding: 0px;
}


thead.sticky-top th{
    white-space: nowrap !important
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1) Action modal population + behaviors
    document.querySelectorAll('.action-trigger').forEach(function(btn){
        btn.addEventListener('click', function(){
            var viewModal = this.getAttribute('data-view-modal');
            var editUrl = this.getAttribute('data-edit-url');
            var deleteUrl = this.getAttribute('data-delete-url');

            // set edit link
            var editBtn = document.getElementById('actionEditBtn');
            editBtn.setAttribute('href', editUrl);

            // set delete link (use click handler to confirm)
            var deleteBtn = document.getElementById('actionDeleteBtn');
            deleteBtn.setAttribute('data-delete-url', deleteUrl);

            // store view modal selector on the view button
            var viewBtn = document.getElementById('actionViewBtn');
            viewBtn.setAttribute('data-view-modal', viewModal);

            // show action modal
            $('#actionModal').modal('show');
        });
    });

    // when "View" clicked: hide action modal and open the per-row view modal
    document.getElementById('actionViewBtn').addEventListener('click', function(){
        var vm = this.getAttribute('data-view-modal');
        $('#actionModal').modal('hide');
        if(vm){
            // small timeout to avoid modal stacking issues
            setTimeout(function(){ $(vm).modal('show'); }, 200);
        }
    });

    // when "Delete" clicked: confirm then navigate
    document.getElementById('actionDeleteBtn').addEventListener('click', function(e){
        var url = this.getAttribute('data-delete-url');
        if(!url) return;
        if(confirm('Are you sure?')){
            window.location.href = url;
        }
    });

    // 2) Drag-to-scroll for .table-wrapper (mouse + touch)
    var slider = document.querySelector('.table-wrapper');
    if(slider){
        var isDown = false;
        var startX, scrollLeft;

        slider.addEventListener('mousedown', function(e){
            isDown = true;
            slider.classList.add('active');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            e.preventDefault();
        });
        slider.addEventListener('mouseleave', function(){
            isDown = false;
            slider.classList.remove('active');
        });
        slider.addEventListener('mouseup', function(){
            isDown = false;
            slider.classList.remove('active');
        });
        slider.addEventListener('mousemove', function(e){
            if(!isDown) return;
            e.preventDefault();
            var x = e.pageX - slider.offsetLeft;
            var walk = (x - startX) * 1; // scroll-fast factor
            slider.scrollLeft = scrollLeft - walk;
        });

        // touch support
        slider.addEventListener('touchstart', function(e){
            startX = e.touches[0].pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        }, {passive: true});
        slider.addEventListener('touchmove', function(e){
            var x = e.touches[0].pageX - slider.offsetLeft;
            var walk = (x - startX) * 1;
            slider.scrollLeft = scrollLeft - walk;
        }, {passive: true});
    }
});
</script>
@endpush


