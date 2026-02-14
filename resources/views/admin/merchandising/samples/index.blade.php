@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Sample List') }}</title>
@endsection

@section('contents')

<div class="flex-grow-1">

<div class="card mb-30">

{{-- ================= HEADER ================= --}}
<div class="card-header d-flex justify-content-between align-items-center">

<h3>Sample List</h3>

<div>

@can('samples.add')
<a href="{{ route('admin.samplesAction','create') }}"
   class="btn-custom primary mr-1"
   style="padding:5px 15px;">
<i class="bx bx-plus"></i> Add Sample
</a>
@endcan

<a href="{{ route('admin.samples') }}" class="btn-custom yellow">
<i class="bx bx-rotate-left"></i>
</a>

</div>

</div>


<div class="card-body">

@include(adminTheme().'alerts')

{{-- ================= SEARCH ================= --}}
<form action="{{ route('admin.samples') }}" class="mb-3">

<div class="row">

<div class="col-md-6 mb-1">
<div class="input-group">

<input type="date"
       name="startDate"
       value="{{ request()->startDate ? \Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}"
       class="form-control">

<input type="date"
       name="endDate"
       value="{{ request()->endDate ? \Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}"
       class="form-control">

</div>
</div>


<div class="col-md-6 mb-1">
<div class="input-group">

<input type="text"
       name="search"
       value="{{ request()->search ?? '' }}"
       placeholder="Search Order, Buyer, Style, Merchant"
       class="form-control">

<button type="submit"
        class="btn btn-success btn-sm rounded-0">
Search
</button>

</div>
</div>

</div>

</form>

{{-- ================= STATUS FILTER ================= --}}
<div class="row mb-2">

<div class="col-md-12">

<ul class="statuslist p-0">

<li><a href="{{ route('admin.samples') }}">All ({{ $totals->total }})</a></li>
<li><a href="{{ route('admin.samples',['status'=>'pending']) }}">Pending ({{ $totals->pending }})</a></li>
<li><a href="{{ route('admin.samples',['status'=>'confirmed']) }}">Confirmed ({{ $totals->confirmed }})</a></li>
<li><a href="{{ route('admin.samples',['status'=>'completed']) }}">Completed ({{ $totals->completed }})</a></li>
<li><a href="{{ route('admin.samples',['status'=>'cancel']) }}">Cancelled ({{ $totals->cancel }})</a></li>

</ul>

</div>

</div>

{{-- ================= BUILD ROWS INLINE ================= --}}
@php
$rows = $samples->map(function($s){

    // Inline Status Badge
    $statusHtml = '';
    switch($s->status){
        case 'temp': $statusHtml = '<span class="badge badge-secondary">Temp</span>'; break;
        case 'pending': $statusHtml = '<span class="badge badge-warning">Pending</span>'; break;
        case 'confirmed': $statusHtml = '<span class="badge badge-info">Confirmed</span>'; break;
        case 'completed': $statusHtml = '<span class="badge badge-success">Completed</span>'; break;
        case 'cancel': $statusHtml = '<span class="badge badge-danger">Cancelled</span>'; break;
        default: $statusHtml = '<span class="badge badge-secondary">--</span>';
    }

    return [
        'order' => $s->getOrderNumber(),

        'buyer' => collect([
            $s->buyer_name,
            $s?->buyer?->company_name,
            $s?->buyer?->country
        ])->filter()->implode(' | '),

        'merchant' => $s->merchant_name ?? '--',
        'style' => $s->style ?? '--',
        'items' => $s->items()->count().' | '.number_format($s->total_qty ?? 0),
        'created' => $s->created_at->format('d.m.Y'),
        'received' => $s?->received_at?->format('d.m.Y') ?? '--',
        'delivery' => $s?->delivery_at?->format('d.m.Y') ?? '--',
        'status' => $statusHtml,
        'view' => can('samples.view') ? route('admin.samplesAction',['view',$s->id]) : '',
        'edit' => can('samples.edit') ? route('admin.samplesAction',['edit',$s->id]) : '',
        'delete' => can('samples.delete') ? route('admin.samplesAction',['delete',$s->id]) : '',
    ];

});
@endphp

{{-- ================= SMART TABLE ================= --}}
<x-smart-table

    :headers="[
        ['label'=>'Order No','key'=>'order','freeze'=>true],
        ['label'=>'Buyer','key'=>'buyer','freeze'=>true],
        ['label'=>'Merchant','key'=>'merchant'],
        ['label'=>'Style','key'=>'style'],
        ['label'=>'Items | Qty','key'=>'items'],
        ['label'=>'Create Date','key'=>'created'],
        ['label'=>'Received Date','key'=>'received'],
        ['label'=>'Delivery Date','key'=>'delivery'],
        ['label'=>'Status','key'=>'status'],
    ]"

    :rows="$rows"

/>

{{-- ================= PAGINATION ================= --}}
<div class="d-flex justify-content-end mt-3">
    {{ $samples->links('pagination') }}
</div>

</div> {{-- card-body --}}
</div> {{-- card --}}
</div> {{-- flex-grow --}}
@endsection
