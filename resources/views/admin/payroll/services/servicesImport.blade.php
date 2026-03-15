@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Items of Goods Import')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Items of Goods Import</h3>
         <div class="dropdown">
             <a href="{{route('admin.services')}}" class="btn-custom yellow">
                 Back
             </a>
         </div>
    </div>
    <div class="card-body">
        
        <div style="text-align: center;margin: auto;max-width: 600px;padding: 35px 15px;border-radius: 10px;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
            @include(adminTheme().'alerts')
            <p class="mb-3">
                Upload a <strong>CSV</strong> or <strong>Excel</strong> file to import products.
                Download a  Exp:
                <a href="{{ asset('medies/Product_list.csv') }}"  class="text-primary" download>
                    Example CSV
                </a>
                <br>
                <span><b>Head:</b> Name, Price, Unit, Category</span>
            </p>

            <form id="importProductsForm" action="{{route('admin.servicesAction','import')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label for="productFile" class="form-label">Select CSV/Excel File</label>
                    <input type="file"
                           class="form-control"
                           id="productFile"
                           name="product_file"
                           accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                           style="padding: 3px;"
                           required>
                    <small class="form-text text-muted">
                        Allowed types: .csv, .xls, .xlsx
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-1"></i> Submit
                </button>
            </form>
        </div>
        
        
    </div>
</div>
</div>



@endsection @push('js') @endpush