@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Leads Convert Success')}}</title>
@endsection @push('css')
<style type="text/css">
    .leadInfoTable tr th,.leadInfoTable tr td{
        padding:5px 10px;        
    }
    .leadSuccess {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .winSuccess .icon {
        font-size: 30px;
        width: 50px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
    }
    
    .companyProfile {
        background-color: #fff;
        border-radius: 8px;
        margin-top: 20px;
    }
    
    .companyProfile h3 {
        font-size: 20px;
        color: #343a40;
    }

    .companyProfile p {
        font-size: 14px;
        color: #555;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>
@endpush 

@section('contents')

<div class="flex-grow-1">
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Leads Convert Success</h3>
             <div class="dropdown">
                 <a href="{{route('admin.leads')}}" class="btn-custom primary"  style="padding:5px 15px;">
                      Back
                 </a>
                 @if($lead->status=='temp')
                 <a href="{{route('admin.leadsAction',['edit',$lead->id])}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
                 @else
                 <a href="{{route('admin.leadsAction',['view',$lead->id])}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
                 @endif
             </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="leadSuccess">
                        <div class="winSuccess d-flex align-items-center mb-4">
                            <div class="icon bg-success text-white p-3 rounded-circle">
                                <i class="bx bx-check"></i>
                            </div>
                            <div class="ms-3 ml-3">
                                <h4 class="text-success">Success</h4>
                                <p class="text-muted mb-0">Woo! You have successfully won this lead.</p>
                            </div>
                        </div>
                        
                        @if($company)
                        <!-- Company Profile -->
                        <div class="companyProfile bg-light p-4 rounded shadow-sm">
                            <h3 class="text-primary">Company: <span class="text-dark">{{$company->factory_name}}</span></h3>
                            <div class="mb-3">
                                <p><b>Email:</b> {{$company->owner_email}}</br>
                                <b>Phone:</b> {{$company->owner_mobile}}</p>
                            </div>
                            <a href="{{route('admin.companiesAction',['view',$company->id])}}" target="_blank" class="btn btn-success btn-sm">View Company</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 
@push('js')
<script>
    $(document).ready(function(){
        
    });
</script>
@endpush