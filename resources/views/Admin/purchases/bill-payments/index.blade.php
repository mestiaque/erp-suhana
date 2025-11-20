@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Order List') }}</title>
@endsection

@section('contents')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Purchase Order List</h3>
            <div class="dropdown">
                <a href="{{ route('admin.billPaymentAction','create') }}" class="btn-custom primary" style="padding:5px 15px;">
                    <i class="bx bx-plus"></i> Add Purchase Order
                </a>
                <a href="{{ route('admin.billPayment') }}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
    </div>

    <div class="card-body">
        @include(adminTheme().'alerts')

                    <!-- Search Form -->
            <form action="{{ route('admin.billPayment') }}">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Order No, Company" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>

            <form action="{{ route('admin.billPayment') }}">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Order No</th>
                                <th>Supplier</th>
                                <th>Grand Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($purchases as $i=>$p)
                            <tr>
                                <td>
                                    <span style="margin:0 5px;">{{$purchases->currentpage()==1?$i+1:$i+($purchases->perpage()*($purchases->currentpage() - 1))+1}}</span>
                                    @if($p->can_pay == 1)
                                        <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                            <i class="bx bx-check-circle"></i>
                                        </span>
                                        @else
                                        <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                            <i class="bx bx-analyse"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.purchasesOrdersAction',['pay',$p->id]) }}">
                                        {{ $p->order_no }}
                                    </a>
                                </td>
                                <td class="">{{ $p->supplier_name }}</td>
                                <td class="text-right">{{ number_format($p->grand_total,2) }}</td>
                                <td class="text-right">{{ number_format(($p->paid_amount),2) }}</td>
                                <td class="text-right">{{ number_format($p->due_amount,2) }}</td>
                                <td class="text-center">
                                    @php
                                        $status = $p->payment_status;

                                        $badgeColor = match($status) {
                                            'due'     => 'danger',
                                            'partial' => 'warning',
                                            'paid'    => 'success',
                                            default   => 'secondary'
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $badgeColor }} px-2" >
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if($p->can_pay == 1)
                                        <a href="{{ route('admin.billPaymentAction',['pay',$p->id]) }}" class="btn-custom success">
                                            <i class="bx bx-money"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn-custom info-btn" data-id="{{ $p->id }}">
                                            <i class="bx bx-info-circle"></i>
                                        </button>

                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

    </div>
</div>


<div class="modal fade text-left" id="canPayInfoModal" tabindex="-1">
<div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title"><i class="bx bx-info-circle"></i> Payment Disabled Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times; </span>
        </button>
    </div>
    <div class="modal-body" id="canPayInfoBody">

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
    </div>
</div>
</div>




@endsection

@push('css')
    <style>
    .btn-custom.disabled,
    .btn-custom.disabled:hover,
    .btn-custom[disabled],
    .btn-custom[disabled]:hover,
    .btn-custom.readonly,
    .btn-custom.readonly:hover {
        pointer-events: none;     /* Click বন্ধ করবে */
        opacity: 0.5;             /* হালকা দেখাবে */
        cursor: not-allowed;      /* মাউস pointer পরিবর্তন হবে */
        background-color: #6c757d !important; /* optional: gray background */
        color: #fff !important;  /* text color */
    }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function(){
            $('.info-btn').click(function(e){
                e.preventDefault();

                let id = $(this).data('id');
                $('#canPayInfoBody').html('Loading...');

                $.ajax({
                    url: '/admin/bill-payments/can-pay-info/' + id,
                    type: 'GET',
                    success: function(res){
                        $('#canPayInfoBody').html(res);

                        // Bootstrap 5 modal show
                        let modalEl = document.getElementById('canPayInfoModal');
                        let modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    },
                    error: function(){
                        $('#canPayInfoBody').html('<p class="text-danger">Error loading data</p>');
                    }
                });
            });
        });

    </script>
@endpush


