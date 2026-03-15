@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Add Asset') }}</title>
@endsection

@section('contents')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Add Asset</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.assets.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Category</label>
                    <select name="category" class="form-control" required>
                        <option value="laptop">Laptop</option>
                        <option value="phone">Phone</option>
                        <option value="vehicle">Vehicle</option>
                        <option value="equipment">Equipment</option>
                        <option value="furniture">Furniture</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Serial Number</label>
                    <input type="text" name="serial_number" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Value</label>
                    <input type="number" name="value" class="form-control" step="0.01">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
