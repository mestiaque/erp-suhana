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

            {{-- ======================
                Search Form
            ====================== --}}
            <form action="{{ route('admin.proformaInvoice') }}">
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control form-control-sm" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control form-control-sm" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search " class="form-control form-control-sm" />
                            <button type="submit" class="btn btn-sm btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- ======================
                Status Filter
            ====================== --}}
            <div class="row mb-2">
                <div class="col-md-12">
                    <ul class="statuslist p-0 mb-0">
                        <li><a href="{{ route('admin.proformaInvoice') }}">All ({{ $totals->total }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['status'=>'completed']) }}">Completed ({{ $totals->completed }})</a></li>
                        <li><a href="{{ route('admin.proformaInvoice',['status'=>'cancel']) }}">Cancelled ({{ $totals->canceled }})</a></li>
                    </ul>
                </div>
            </div>

            {{-- ======================
                Smart Table Component
            ====================== --}}
            @php
            $tableRows = [];
            foreach($pis as $i => $pi){
                $uniqueOrders = $pi->items->pluck('order_no')->unique()->count();
                $uniqueStyles = $pi->items->pluck('style_no')->unique()->count();
                $totalQty = $pi->items->sum('order_qty');
                $totalPrice = $pi->items->sum('total_price');

                $actions = [];

                $actions[] = [
                    'label'=>'<i class="fa-solid fa-chart-simple"></i> PI Status',
                    'href'=>route('admin.fabricStatus',$pi->id),
                    'tag'=>'a'
                ];

                // Permission-based actions
                if(can('proforma_invoice.view')){
                    $actions[] = [
                        'label'=>'<i class="fa fa-file"></i> Invoice',
                        'href'=>route('admin.proformaInvoiceAction',['invoice',$pi->id]),
                        'tag'=>'a'
                    ];
                }
                if(can('proforma_invoice.edit')){
                    $actions[] = [
                        'label'=>'<i class="bx bx-edit"></i> Edit',
                        'href'=>route('admin.proformaInvoiceAction',['edit',$pi->id]),
                        'tag'=>'a'
                    ];
                }
                if(can('proforma_invoice.delete')){
                    $actions[] = [
                        'label'=>'<i class="bx bx-trash"></i> Delete',
                        'href'=>route('admin.proformaInvoiceAction',['delete',$pi->id]),
                        'tag'=>'a'
                    ];
                }

                $tableRows[] = [
                    'sl'=>$pis->currentpage()==1?$i+1:$i+($pis->perpage()*($pis->currentpage() - 1))+1,
                    'pi_no'=>$pi->pi_no,
                    'buyer'=>$pi->buyer?->name ?? '--',
                    'total_orders'=>$uniqueOrders,
                    'total_styles'=>$uniqueStyles,
                    'total_qty'=>$totalQty,
                    'total_bill'=>$totalPrice,
                    'status'=>ucfirst($pi->status),
                    'pi_date'=>$pi->created_at->format('d.m.Y'),
                    'actions'=>$actions
                ];
            }

            $tableHeaders = [
                ['label'=>'PI No','key'=>'pi_no','freeze'=>true,'width'=>'120px'],
                ['label'=>'Buyer','key'=>'buyer','freeze'=>true,'width'=>'100px'],
                ['label'=>'Total Order','key'=>'total_orders','width'=>'100px'],
                ['label'=>'Total Style','key'=>'total_styles','width'=>'100px'],
                ['label'=>'Total Qnty','key'=>'total_qty','width'=>'100px'],
                ['label'=>'Total Bill','key'=>'total_bill','width'=>'120px'],
                ['label'=>'Status','key'=>'status','width'=>'100px'],
                ['label'=>'PI Date','key'=>'pi_date','width'=>'120px'],
            ];
            @endphp

            <x-smart-table :headers="$tableHeaders" :rows="$tableRows" />

            {{ $pis->links('pagination') }}

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
</style>
@endpush
