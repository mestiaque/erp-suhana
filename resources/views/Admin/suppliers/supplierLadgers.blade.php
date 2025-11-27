@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Supplier Ledgers')}}</title>
@endsection

@push('css')
<style type="text/css">
    .ProfileImage{
        max-width: 64px;
        max-height: 64px;
    }
</style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb Area -->
    <div class="breadcrumb-area">
        <h1>Supplier Ledgers</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">Supplier Ledgers</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Ledger @if($supplier) of
                <span class="font-weight-bold">
                    <a href="{{route('admin.suppliersAction',['view',$supplier->id])}}">{{ $supplier?->name }}</a>
                </span> @endif
            </h3>
             <div class="d-flex align-items-center" style="gap:0.5rem;">
                <form id="supplierFilterForm" method="GET" action="{{ route('admin.suppliersLegers') }}">
                    <select name="supplier_id" id="supplier_id" class="form-control" style="width:20rem" required>
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ request()->supplier_id == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.suppliersLegers') }}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @if(isset($ledgerEntries) && !is_null($supplier))
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped table-hover">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Reference</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ledgerEntries as $i=>$entry)
                            <tr>
                                <td>{{ $entry['sl'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d-m-Y') }}</td>
                                <td>{{ $entry['supplier'] }}</td>
                                <td>{{ $entry['ref'] }}</td>
                                <td class="text-right text-success">{{ $entry['debit'] > 0 ? number_format($entry['debit'],2) : '-' }}</td>
                                <td class="text-right text-danger">{{ $entry['credit'] > 0 ? number_format($entry['credit'],2) : '-' }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($entry['balance'],2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No entries found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center display-4 text-muted p-4">
                    Please Select A Supplier
                </div>
            @endif
        </div>
    </div>



</div>
@endsection

@push('js')
<script>
    $(document).ready(function(){
        $('#supplier_id').change(function(){
            $('#supplierFilterForm').submit();
        });
    });
</script>
@endpush
