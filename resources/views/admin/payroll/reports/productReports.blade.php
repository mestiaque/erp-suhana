@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Product Reports')}}</title>
@endsection @push('css')
<link rel="stylesheet" type="text/css" href="{{asset('public/app-assets/vendors/css/tables/datatable/datatables.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('public/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('public/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}" />
<style type="text/css">
    .dataTables_wrapper table {
        width: 100%;
        min-height: 0.01%;
        overflow-x: auto;
    }
    td.dataTables_empty {
        width: 1%;
    }
    
    .dataex-html5-export {
        width: 100% !important;
        table-layout: fixed;
    }
</style>
@endpush @section('contents')


<div class="flex-grow-1">
    <div class="content-body">
        <!-- Basic Elements start -->
        <section class="basic-elements">
            <div class="row">
                <div class="col-md-12">
                    @include(adminTheme().'alerts')
    
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3; padding: 1rem;">
                            <h4 class="card-title" style="padding: 5px;">Product Reports</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form action="{{route('admin.reports',$action)}}">
                                    <div class="row">
                                        <div class="col-md-6 mb-0">
                                            <div class="input-group">
                                                <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Product Name" class="form-control {{$errors->has('search')?'error':''}}" />
                                                <button type="submit" class="btn btn-success rounded-0">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <hr>
                                <table class="table table-striped table-bordered dataex-html5-export w-100">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;min-width: 40px;padding: 10px;">SL</th>
                                            <th style="width: 50px;min-width: 50px;padding: 10px;">Image</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th style="width: 100px;min-width: 100px;padding: 10px;">Quantity</th>
                                            <th style="width: 100px;min-width: 100px;padding: 10px;">Price</th>
                                            <th style="width: 120px;min-width: 120px;padding: 10px;">Asset Value</th>
                                            <th style="width: 120px;min-width: 120px;padding: 10px;">Total Sale</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($datas as $i=>$data)
                                           <tr>
                                               <td>{{$i+1}}</td>
                                               <td style="padding:2px;">
                                                   <img src="{{asset($data->image())}}" alt="{{$data->name}}" style="max-height: 35px;" />
                                               </td>
                                               <td>{{$data->name}}</td>
                                               <td>{{$data->category?$data->category->name:'No Category'}}</td>
                                               <td>{{$data->quantity}}</td>
                                               <td>{{$data->item_price}}</td>
                                               <td>{{$data->quantity*$data->item_price}}</td>
                                               <td>0</td>
                                           </tr>
                                       @endforeach
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>0</th>
                                        <th>Total</th>
                                        <th>0</th>
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection @push('js')

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('public/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('public/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('public/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/app-assets/vendors/js/tables/jszip.min.js')}}"></script>
<script src="{{asset('public/app-assets/vendors/js/tables/pdfmake.min.js')}}"></script>
<script src="{{asset('public/app-assets/vendors/js/tables/vfs_fonts.js')}}"></script>
<script src="{{asset('public/app-assets/vendors/js/tables/buttons.html5.min.js')}}"></script>
<script src="{{asset('public/app-assets/vendors/js/tables/buttons.print.min.js')}}"></script>
<script src="{{asset('public/app-assets/vendors/js/tables/buttons.colVis.min.js')}}"></script>

<!-- END: Page Vendor JS-->

<script type="text/javascript">
    $(document).ready(function () {
        
        
        $('.dataex-html5-export').DataTable({
            dom: "Bfrtip",
            buttons: [
                "copyHtml5",
                "excelHtml5",
                "csvHtml5",
                {
                    extend: "pdfHtml5",
                    orientation: "landscape",
                    pageSize: "LEGAL"
                },
                "print"
            ],
    
            pageLength: 100,
            lengthMenu: [25, 50, 100, 200, 500],
    
            autoWidth: false,
            responsive: false,
            scrollX: false
        });
        
    });
</script>

@endpush
