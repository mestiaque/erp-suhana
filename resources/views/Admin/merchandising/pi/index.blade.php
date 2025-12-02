@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('PI List') }}</title>
@endsection

@push('css')
<style type="text/css">

</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>PI List</h3>
             <div class="dropdown">
                @can('samples.add')
                 <a href="{{ route('admin.proformaInvoiceAction','create') }}" class="btn-custom primary" style="padding:5px 15px;">
                     <i class="bx bx-plus"></i> Add PI
                 </a>
                 @endcan
                 <a href="{{ route('admin.samples') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{ route('admin.samples') }}">
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Order, Buyer, Style, Merchant" class="form-control" />
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
                        <li><a href="{{ route('admin.proformaInvoice',['pi_status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['pi_status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['pi_status'=>'completed']) }}">Approved ({{ $totals->completed }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['pi_status'=>'cancel']) }}">Cancelled ({{ $totals->cancel }})</a></li>
                    </ul>
                </div>
            </div>

            <!-- Samples Table -->
            <div class="table-responsive">
                <table class="table table-striped table-borderd">
                    <thead>
                        <tr>
                            <th style="width: 80px">Order No.</th>
                            <th style="width: 150px">Buyer</th>
                            <th style="width: 120px">Merchent</th>
                            <th style="width: 100px">Style</th>
                            <th style="width: 100px">Items | Qty</th>
                            <th style="width: 100px">Create Date</th>
                            <th style="width: 110px">Received Date</th>
                            <th style="width: 100px">Delivery Date</th>
                            <th style="width: 100px">Status</th>
                            <th style="width: 150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($samples as $i => $sample)
                        <tr>
                            <td>{{$sample->getOrderNumber()}}</td>
                            <td>
                                {{
                                    collect([
                                        $sample->buyer_name,
                                        $sample?->buyer?->company_name,
                                        $sample?->buyer?->country
                                    ])->filter()->implode(' | ')
                                }}
                            </td>
                            <td>{{ $sample->merchant_name ?? '--' }}</td>
                            <td>{{ $sample->style ?? '--' }}</td>
                            <td>{{ $sample->items()->count() }} Items | {{ number_format($sample->total_qty) ?? 0 }}</td>
                            <td>{{ $sample->created_at->format('d.m.Y') }}</td>
                            <td>{{ $sample?->received_at?->format('d.m.Y') ?? '--' }}</td>
                            <td>{{ $sample?->delivery_at?->format('d.m.Y') ?? '--' }}</td>
                            <td>
                                @if($sample->pi_status=='temp')
                                    <span class="badge badge-secondary">Temp</span>
                                @elseif($sample->pi_status=='pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($sample->pi_status=='confirmed')
                                    <span class="badge badge-info">Confirmed</span>
                                @elseif($sample->pi_status=='approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($sample->pi_status=='canceled')
                                    <span class="badge badge-danger">Cancelled</span>
                                @endif
                            </td>
                            
                            <td class="text-center">
                                @if(can('samples.view') || can('samples.view') || can('samples.view'))
                                    @can('samples.view')
                                    <a href="{{ route('admin.proformaInvoiceAction',['view',$sample->id]) }}" class="btn-custom yellow mr-1"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @if(in_array($sample->pi_status, ['pending']))
                                        @can('samples.edit')
                                        <a href="{{ route('admin.proformaInvoiceAction',['edit',$sample->id]) }}" class="btn-custom success mr-1"><i class="bx bx-edit"></i></a>
                                        @endcan
                                        @can('samples.delete')
                                        <a href="{{ route('admin.proformaInvoiceAction',['delete',$sample->id]) }}" onclick="return confirm('Are You Sure To Delete?')" class="btn-custom danger"><i class="bx bx-trash"></i></a>
                                        @endcan
                                    @endif
                                @else -- @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No PI Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $samples->links('pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
