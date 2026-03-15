@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Company View')}}</title>
@endsection 
@push('css')

<style type="text/css">
    
</style>
@endpush 
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-8 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Company View</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-4 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary" href="{{route('admin.companies')}}">BACK</a>
            <a class="btn btn-outline-primary" href="{{route('admin.companiesAction',['edit',$company->id])}}" >Edit</a>
            <a class="btn btn-outline-primary" href="{{route('admin.companiesAction',['view',$company->id])}}">
                <i class='bx bx-loader-circle' ></i>
            </a>
        </div>
    </div>
</div>


<section class="flex-grow-1">
@include(adminTheme().'alerts')
    
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Company View</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            sdfsd
                        </div>
                    </div>
                </div>

                
            </div>
            
            <div class="col-md-4">

            </div>
            
            
        </div>
    
</section>
<!-- Basic Inputs end -->


@endsection 
@push('js')
<script>
    $(document).ready(function(){
        $('#PrintAction22').on("click", function () {
            $('.PrintAreaContact').printThis({
              	importCSS: false,
              	loadCSS: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap-grid.min.css",
            });
        });
    });
</script>
@endpush
