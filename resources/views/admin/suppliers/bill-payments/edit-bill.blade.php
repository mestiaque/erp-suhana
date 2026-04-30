@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Edit Bill Entry') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Bill Entry</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">
                <a href="{{ route('admin.suppliers') }}">Creditor List</a>
            </li>
            <li class="item">
                <a href="{{ route('admin.suppliersAction', ['action' => 'bill-entry', 'id' => $bill->creditor_id]) }}">Bill Entry</a>
            </li>
            <li class="item">Edit Bill</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Edit Bill Entry</h3>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            
            <form action="{{ route('admin.suppliersAction', ['action' => 'bill-entry-update', 'id' => $bill->id]) }}" method="POST" class="p-3 border rounded">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Created Date</label>
                        <input type="date" name="created_at" class="form-control" value="{{ old('title', $bill->created_at->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Bill Title/Invoice No</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $bill->title) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Amount</label>
                        <input type="number" step="any" name="amount" class="form-control" value="{{ old('amount', $bill->amount) }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $bill->description) }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.suppliersAction', ['action' => 'bill-entry', 'id' => $bill->creditor_id]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Bill</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
