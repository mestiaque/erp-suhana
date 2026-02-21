@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('P.I Fabric Status') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>P.I Fabric Status</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.proformaInvoice') }}">Proforma Invoice</a></li>
            <li class="item">Fabric Status</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="w-100 text-right mb-1">
                <a href="{{ route('admin.fabricStatus', ['id' => $pi->id, 'print' => true]) }}" class="btn-custom primary">
                    <i class="fa-solid fa-print"></i> PRINT
                </a>
            </div>
            <div class="table-responsive">
                @include(adminTheme().'productions.fabric-status.table')
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
@push('css')
@endpush
