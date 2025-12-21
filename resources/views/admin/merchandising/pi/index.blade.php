@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Proforma Invoice List') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Proforma Invoice List</h3>
             <div class="dropdown">
                @can('order_details.add')
                 <a href="{{ route('admin.proformaInvoiceAction','create') }}" class="btn-custom primary" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add Proforma Invoice
                 </a>
                 @endcan
                 <a href="{{ route('admin.proformaInvoice') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.proformaInvoice') }}">
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search " class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Status Filter -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <ul class="statuslist p-0">
                        <li><a href="{{ route('admin.proformaInvoice') }}">All ({{ $totals->total }})</a></li>
                        {{-- <li><a href="{{ route('admin.proformaInvoice',['status'=>'temp']) }}">Temp ({{ $totals->temp }})</a></li> --}}
                        <li><a href="{{ route('admin.proformaInvoice',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['status'=>'completed']) }}">Completed ({{ $totals->completed }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['status'=>'cancel']) }}">Cancelled ({{ $totals->canceled }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- proformaInvoice Table -->
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>PI No</th>
                                <th>Buyer</th>
                                <th>Total Order</th>
                                <th>Total Style</th>
                                <th>Total Qnty</th>
                                <th>Total Bill</th>
                                <th>Status</th>
                                <th>PI Date</th>
                                <th width="200">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pis as $i=>$pi)
                            <tr>
                                <td>{{$pis->currentpage()==1?$i+1:$i+($pis->perpage()*($pis->currentpage() - 1))+1}}</td>
                                <td>{{ $pi->pi_no }}</td>

                                <td>{{ $pi->buyer?->name ?? '--' }}</td>

                                @php
                                    $uniqueOrders = $pi->items->pluck('order_no')->unique()->count();
                                    $uniqueStyles = $pi->items->pluck('style_no')->unique()->count();
                                @endphp

                                <td>{{ $uniqueOrders }}</td>
                                <td>{{ $uniqueStyles }}</td>

                                <td>{{ number_format($pi->items->sum('order_qty')) }}</td>

                                <td>{{ number_format($pi->items->sum('total_price'),2) }}</td>

                                <td>
                                    <span class="badge
                                        @if($pi->status=='pending') badge-warning
                                        @elseif($pi->status=='confirmed') badge-info
                                        @elseif($pi->status=='approved') badge-success
                                        @elseif($pi->status=='cancel') badge-danger
                                        @endif">
                                        {{ ucfirst($pi->status) }}
                                    </span>
                                </td>

                                <td>{{ $pi->created_at->format('d.m.Y') }}</td>

                                <td class="text-center">
                                    @if(can('proforma_invoice.view') || can('proforma_invoice.view') || can('proforma_invoice.view'))
                                        @can('proforma_invoice.view')
                                        {{-- <a href="{{ route('admin.proformaInvoiceAction',['view',$pi->id]) }}" class="btn-custom yellow">
                                            <i class="fa fa-eye"></i>
                                        </a> --}}
                                        <a href="{{ route('admin.proformaInvoiceAction',['invoice',$pi->id]) }}" class="btn-custom info" style="background: #F44336;color: white">
                                            <i class="fa fa-file"></i>
                                        </a>
                                        @endcan

                                        @can('proforma_invoice.edit')
                                            @if($pi->status=='pending')
                                            <a href="{{ route('admin.proformaInvoiceAction',['edit',$pi->id]) }}" class="btn-custom success">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            @endif
                                            @can('proforma_invoice.delete')
                                                @if($pi->status=='pending')
                                                <a href="{{ route('admin.proformaInvoiceAction',['delete',$pi->id]) }}" onclick="return confirm('Are You Sure To Delete?')" class="btn-custom danger"><i class="bx bx-trash"></i></a>
                                                @endif
                                            @endcan
                                        @endcan
                                    @else -- @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">No Proforma Invoice Found</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                {{ $pis->links('pagination') }}
            </div>
        </div>
    </div>




</div>
@endsection

@push('js')
@endpush





































