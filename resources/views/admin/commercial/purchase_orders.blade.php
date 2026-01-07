@extends(adminTheme().'layouts.app')

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Purchase Orders List</h3>
            <div class="dropdown">
                <a href="#" class="btn-custom primary" style="padding:5px 15px;">
                    <i class="bx bx-plus"></i> Add New
                </a>
            </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Reference No</th>
                            <th>Details</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>DEMO-2026-001</td>
                            <td>Sample Data for Purchase Orders</td>
                            <td>$ 0.00</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>07.01.2026</td>
                            <td>
                                <a href="#" class="btn-custom success"><i class="bx bx-edit"></i></a>
                                <a href="#" class="btn-custom danger"><i class="bx bx-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection