@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Company Profile')}}</title>
@endsection 
@push('css')

<style type="text/css">
    .mcTable tr th{
        padding:5px;
        background: #efefef;
    }
</style>
@endpush 
@section('contents')

<div class="flex-grow-1">


<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
    @include(adminTheme().'alerts')
        
            <div class="row">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header row" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title col-md-8 col-12">Company Information</h4>
                            <div class="content-header-right col-md-4 col-12 mb-2">
                                <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
                                    <a class="btn btn-outline-primary" href="{{route('admin.companies')}}">BACK</a>
                                    <a class="btn btn-outline-primary" href="{{route('admin.companiesAction','create')}}" onclick="return confirm('Are You Want To New Company?')">Add Company</a>
                                    <a class="btn btn-outline-primary" href="{{route('admin.companiesAction',['edit',$company->id])}}">
                                        <i class='bx bx-loader-circle' ></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form action="{{route('admin.companiesAction',['update',$company->id])}}" method="post" enctype="multipart/form-data">
                                @csrf
                                
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                  <li class="nav-item">
                                    <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">Information</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="other-tab" data-toggle="tab" href="#other" role="tab">Others</a>
                                  </li>
                                </ul>
                                
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="basic" >
                                        <div class="card" style="padding: 5px;">
                                          
                                            <div class="row">
   
                                                <div class="col-md-4 mb-3">
                                                    <label>Customer Name*</label>
                                                    <input type="text" name="owner_name" value="{{old('owner_name')?:$company->owner_name}}" class="form-control" placeholder="Enter Customer name" required="">
                                                    @if ($errors->has('owner_name'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('owner_name') }}</span>
                                                    @endif
                                                </div>    
                                                   
                                                <div class="col-md-4 mb-3">
                                                    <label>Mobile No*</label>
                                                    <input type="text" name="owner_mobile" value="{{old('owner_mobile')?:$company->owner_mobile}}" class="form-control" placeholder="Enter Mobile no" required="">
                                                    @if ($errors->has('owner_mobile'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('owner_mobile') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label>Other Mobile No</label>
                                                    <input type="text" name="owner_mobile2" value="{{old('owner_mobile2')?:$company->owner_mobile2}}" class="form-control" placeholder="Enter Other Mobile no" >
                                                    @if ($errors->has('owner_mobile2'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('owner_mobile2') }}</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-4 mb-3">
                                                    <label>E-mail</label>
                                                    <input type="text" name="owner_email" value="{{old('owner_email')?:$company->owner_email}}" class="form-control" placeholder="Enter Email Address">
                                                    @if ($errors->has('owner_email'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('owner_email') }}</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-4 mb-3">
                                                    <label>Company Name</label>
                                                    <input type="text" name="factory_name" value="{{old('factory_name')?:$company->factory_name}}" class="form-control" placeholder="Enter Company name"  >
                                                    @if ($errors->has('factory_name'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('factory_name') }}</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-4 mb-3">
                                                    <label>Designation</label>
                                                    <input type="text" name="owner_designation" value="{{old('owner_designation')?:$company->owner_designation}}" class="form-control" placeholder="Enter Designation">
                                                    @if ($errors->has('owner_designation'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('owner_designation') }}</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label>Company Address*</label>
                                                    <div class="row" style="margin:0 -3px;">
                                                        <div class="col-md-4" style="padding:3px;">
                                                            <select id="division" class="form-control {{$errors->has('division')?'error':''}}" name="division" required="">
                                                                <option value="">Select Division</option>
                                                                @foreach(App\Models\Country::where('type',2)->where('parent_id',1)->get() as $data)
                                                                <option value="{{$data->id}}" {{$data->id==$company->division?'selected':''}}>{{$data->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4" style="padding:3px;">
                                                            <select id="district" class="form-control {{$errors->has('district')?'error':''}}" name="district" required="">
                                                                @if($company->division==null)
                                                                <option value="">Select District</option>
                                                                @else
                                                                <option value="">Select District</option>
                                                                @foreach(App\Models\Country::where('type',3)->where('parent_id',$company->division)->get() as $data)
                                                                <option value="{{$data->id}}" {{$data->id==$company->district?'selected':''}}>{{$data->name}}</option>
                                                                @endforeach @endif
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4" style="padding:3px;">
                                                            <select id="city" class="form-control {{$errors->has('city')?'error':''}}" name="city" required="">
                                                                @if($company->district==null)
                                                                <option value="">Select Thana</option>
                                                                @else
                                                                <option value="">Select Thana</option>
                                                                @foreach(App\Models\Country::where('type',4)->where('parent_id',$company->district)->get() as $data)
                                                                <option value="{{$data->id}}" {{$data->id==$company->city?'selected':''}}>{{$data->name}}</option>
                                                                @endforeach 
                                                                <option value="0" {{0==$company->city?'selected':''}}>Others</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12" style="padding:3px;">
                                                            <input type="text" name="company_address" value="{{old('company_address')?:$company->company_address}}" class="form-control" placeholder="Enter Company Address" required="">
                                                            @if ($errors->has('company_address'))
                                                            <span style="color: red; margin: 0;">{{ $errors->first('company_address') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>  
                                                <div class="col-md-6 mb-3">
                                                    <label>Customer Requirement</label>
                                                    <textarea class="form-control" name="requirement" rows="3" placeholder="Write Customer Requirement">{{old('requirement')?:$company->requirement}}</textarea>
                                                    @if ($errors->has('requirement'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('requirement') }}</span>
                                                    @endif
                                                </div>

                                                <div class="col-md-12 moreAdd">
                                                    
                                                    <div class="partners">
                                                        @foreach($company->persons()->where('type',2)->get() as $i=>$partner)
                                                            @include(adminTheme().'companies.includes.partner')
                                                        @endforeach
                                                    </div>
                                                    <div class="text-center mb-3">
                                                        <span class="btn btn-sm btn-danger MorePartners" data-url="{{route('admin.companiesAction',['add-partners',$company->id])}}"><i class="bx bx-plus"></i> More Partners</span>
                                                    </div>
                                                    
                                                </div>

                                                <!--<div class="col-md-6 mb-3">-->
                                                <!--    <label>Google Map</label>-->
                                                <!--    <input type="text" name="google_map" value="{{old('google_map')?:$company->google_map}}" class="form-control" placeholder="Enter Google map">-->
                                                <!--    @if ($errors->has('google_map'))-->
                                                <!--    <span style="color: red; margin: 0;">{{ $errors->first('google_map') }}</span>-->
                                                <!--    @endif-->
                                                <!--</div>-->
                                                <div class="col-md-4 mb-3">
                                                    <label>Created Date*</label>
                                                    <input type="date" name="created_at" value="{{old('created_at')?:$company->created_at->format('Y-m-d')}}" class="form-control" required="" >
                                                    @if ($errors->has('created_at'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('created_at') }}</span>
                                                    @endif
                                                </div>  
                                                <div class="col-md-4 mb-3">
                                                    <label>Status</label>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="status" name="status" {{$company->status=='active'?'checked':''}}/>
                                                        <label class="custom-control-label" for="status">Active</label>
                                                    </div>
                                                    @if ($errors->has('status'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('status') }}</span>
                                                    @endif
                                                </div>
                                                <!--<div class="col-md-3 mb-3">-->
                                                <!--    <label>Sister Concern*</label>-->
                                                <!--    <select class="form-control" name="concern" required="">-->
                                                <!--        <option value="">Select Concern</option>-->
                                                <!--        <option value="MG Machineries Corporation" {{$company->concern=='MG Machineries Corporation'?'selected':''}}  >MG Machineries Corporation (MMC)</option>-->
                                                <!--        <option value="Embroidery Machine Corporation" {{$company->concern=='Embroidery Machine Corporation'?'selected':''}} >Embroidery Machine Corporation (EMC)</option>-->
                                                <!--        <option value="MG Training Centre Institute" {{$company->concern=='MG Training Centre Institute'?'selected':''}} >MG Training Centre Institute (MTCI)</option>-->
                                                <!--        <option value="Fiber Laser Cutting Division" {{$company->concern=='Fiber Laser Cutting Division'?'selected':''}} >Fiber Laser Cutting Division (FLCD)</option>-->
                                                <!--    </select> -->
                                                <!--    @if ($errors->has('concern'))-->
                                                <!--    <span style="color: red; margin: 0;">{{ $errors->first('concern') }}</span>-->
                                                <!--    @endif-->
                                                <!--</div>-->
                                                <div class="col-md-4 mb-3">
                                                    <label>Created By</label>
                                                    <input type="text" readonly=""  value="{{$company->user?$company->user->name:''}}" placeholder="Created By" class="form-control" >
                                                </div>  
                                            </div>
                                            <button type="submit" class="btn btn-success">Submit</button>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="other" >
                                        <div class="card" style="padding: 5px;">
                                            
                                            <div class="row">
                                                <div class="col-md-12 mb-1 mt-3">
                                                    <h4 class="text-danger">Key Person Information</h4>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label>Key Person Name</label>
                                                    <input type="text" name="key_parson_name" value="{{old('key_parson_name')?:$company->key_parson_name}}" class="form-control" placeholder="Enter Key parson name">
                                                    @if ($errors->has('key_parson_name'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('key_parson_name') }}</span>
                                                    @endif
                                                </div>  
                                                <div class="col-md-3 mb-3">
                                                    <label>Designation</label>
                                                    <input type="text" name="key_parson_designation" value="{{old('key_parson_designation')?:$company->key_parson_designation}}" class="form-control" placeholder="Enter Designation">
                                                    @if ($errors->has('key_parson_designation'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('key_parson_designation') }}</span>
                                                    @endif
                                                </div>    
                                                <div class="col-md-3 mb-3">
                                                    <label>Mobile No</label>
                                                    <input type="text" name="key_parson_mobile" value="{{old('key_parson_mobile')?:$company->key_parson_mobile}}" class="form-control" placeholder="Enter Mobile No">
                                                    @if ($errors->has('key_parson_mobile'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('key_parson_mobile') }}</span>
                                                    @endif
                                                </div>    
                                                <div class="col-md-3 mb-3">
                                                    <label>Whatsapps number</label>
                                                    <input type="text" name="key_parson_whatsapp_mobile" value="{{old('key_parson_whatsapp_mobile')?:$company->key_parson_whatsapp_mobile}}" class="form-control" placeholder="Enter Whatsapp Number">
                                                    @if ($errors->has('key_parson_whatsapp_mobile'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('key_parson_whatsapp_mobile') }}</span>
                                                    @endif
                                                </div>    
                                                <div class="col-md-3 mb-3">
                                                    <label>E-mail</label>
                                                    <input type="text" name="key_parson_email" value="{{old('key_parson_email')?:$company->key_parson_email}}" class="form-control" placeholder="Enter Email Address">
                                                    @if ($errors->has('key_parson_email'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('key_parson_email') }}</span>
                                                    @endif
                                                </div>  
                                                <!--<div class="col-md-12 mb-1 mt-3">-->
                                                <!--    <h4 class="text-danger">Partner Information</h4>-->
                                                <!--</div>-->
                                                <!--<div class="col-md-8 mb-3">-->
                                                <!--    <label>Partner Name</label>-->
                                                <!--    <input type="text" name="partner_name" value="{{old('partner_name')?:$company->partner_name}}" class="form-control" placeholder="Enter partner name" >-->
                                                <!--    @if ($errors->has('partner_name'))-->
                                                <!--    <span style="color: red; margin: 0;">{{ $errors->first('partner_name') }}</span>-->
                                                <!--    @endif-->
                                                <!--</div>  -->
                                                <!--<div class="col-md-4 mb-3">-->
                                                <!--    <label>Designation</label>-->
                                                <!--    <input type="text" name="partner_designation" value="{{old('partner_designation')?:$company->partner_designation}}" class="form-control" placeholder="Enter Designation" >-->
                                                <!--    @if ($errors->has('partner_designation'))-->
                                                <!--    <span style="color: red; margin: 0;">{{ $errors->first('partner_designation') }}</span>-->
                                                <!--    @endif-->
                                                <!--</div>-->
                                                <!--<div class="col-md-12 mb-3">-->
                                                <!--    <label>Partner Details</label>-->
                                                <!--    <input type="text" name="partner_details" value="{{old('partner_details')?:$company->partner_details}}" class="form-control" placeholder="Enter details" >-->
                                                <!--    @if ($errors->has('partner_details'))-->
                                                <!--    <span style="color: red; margin: 0;">{{ $errors->first('partner_details') }}</span>-->
                                                <!--    @endif-->
                                                <!--</div>-->
                                                <div class="col-md-12 mb-1 mt-3">
                                                    <h4 class="text-danger">Manager Information</h4>
                                                </div>
                                                <div class="col-md-8 mb-3">
                                                    <label>Manager Name</label>
                                                    <input type="text" name="manager_name" value="{{old('manager_name')?:$company->manager_name}}" class="form-control" placeholder="Enter manager name" >
                                                    @if ($errors->has('manager_name'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('manager_name') }}</span>
                                                    @endif
                                                </div>  
                                                <div class="col-md-4 mb-3">
                                                    <label>Designation</label>
                                                    <input type="text" name="manager_designation" value="{{old('manager_designation')?:$company->manager_designation}}" class="form-control" placeholder="Enter Designation" >
                                                    @if ($errors->has('manager_designation'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('manager_designation') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label>Manager Details</label>
                                                    <input type="text" name="manager_details" value="{{old('manager_details')?:$company->manager_details}}" class="form-control" placeholder="Enter details" >
                                                    @if ($errors->has('manager_details'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('manager_details') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12 mb-1 mt-3">
                                                    <h4 class="text-danger">PM Information</h4>
                                                </div>
                                                <div class="col-md-8 mb-3">
                                                    <label>PM Name</label>
                                                    <input type="text" name="pm_name" value="{{old('pm_name')?:$company->pm_name}}" class="form-control" placeholder="Enter PM name" >
                                                    @if ($errors->has('pm_name'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('pm_name') }}</span>
                                                    @endif
                                                </div>  
                                                <div class="col-md-4 mb-3">
                                                    <label>Designation</label>
                                                    <input type="text" name="pm_designation" value="{{old('pm_designation')?:$company->pm_designation}}" class="form-control" placeholder="Enter Designation" >
                                                    @if ($errors->has('pm_designation'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('pm_designation') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label>PM Details</label>
                                                    <input type="text" name="pm_details" value="{{old('pm_details')?:$company->pm_details}}" class="form-control" placeholder="Enter details" >
                                                    @if ($errors->has('pm_details'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('pm_details') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12 mb-1 mt-3">
                                                    <h4 class="text-danger">Operator Information</h4>
                                                </div>
                                
                                                <div class="col-md-12 mb-1 mt-3">
                                                    <div class="table-responsive personList_0">
                                                        @include(adminTheme().'companies.includes.personList', ['type' => 0])
                                                    </div>
                                                </div>
                                             
                                                
                                                <div class="col-md-12 mb-1 mt-3">
                                                    <h4 class="text-danger">Engineer Information</h4>
                                                </div>
                                                <div class="col-md-8 mb-3">
                                                    <label>Engineer Name</label>
                                                    <input type="text" name="engineer_name" value="{{old('engineer_name')?:$company->engineer_name}}" class="form-control" placeholder="Enter Engineer name" >
                                                    @if ($errors->has('engineer_name'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('engineer_name') }}</span>
                                                    @endif
                                                </div>  
                                                <div class="col-md-4 mb-3">
                                                    <label>Designation</label>
                                                    <input type="text" name="engineer_designation" value="{{old('engineer_designation')?:$company->engineer_designation}}" class="form-control" placeholder="Enter Designation" >
                                                    @if ($errors->has('engineer_designation'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('engineer_designation') }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label>Engineer Details</label>
                                                    <input type="text" name="engineer_details" value="{{old('engineer_details')?:$company->engineer_details}}" class="form-control" placeholder="Enter details" >
                                                    @if ($errors->has('engineer_details'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('engineer_details') }}</span>
                                                    @endif
                                                </div>
                                                
                                                
                                                <div class="col-md-12 mb-1 mt-3">
                                                    <h4 class="text-danger">Company Machineries</h4>
                                                    <div class="table-responsive machineList">
                                                        @include(adminTheme().'companies.includes.machineList')
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-success">Submit</button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
    </section>
    <!-- Basic Inputs end -->
</div>
</div>

@endsection 
@push('js')
<script>
    $(document).ready(function(){
        
        $(document).on('click', '.MorePartners', function () {
            var url = $(this).data('url');
            $.ajax({
                url: url,
                dataType: 'json',
                cache: false,
                success: function (data) {
                    $('.partners').append(data.view);
                },
                error: function () {
                    alert('error');
                }
            });
        });
        
        $(document).on('click', '.removePartner', function () {
            var url = $(this).data('url');
            var id = $(this).data('id');
            if(confirm('Are you want to delete?')){
                $.ajax({
                    url: url,
                    dataType: 'json',
                    cache: false,
                    success: function (data) {
                        if(data.success){
                            $('.partDiv_'+id).remove();
                        }else{
                            alert('Partner are not found!');
                        }
                    },
                    error: function () {
                        alert('error');
                    }
                });
            }
        });
        
        $(document).on('change', '.partnerUpdate', function () {
            var url = $(this).data('url');
            var name = $(this).data('name');
            var key = $(this).val();
            $.ajax({
                url: url,
                dataType: 'json',
                cache: false,
                data:{name:name,key:key},
                success: function (data) {
                    //Success
                },
                error: function () {
                    alert('error');
                }
            });

        });
        
        $(document).on('click', '.removeMachine, .addMachine', function () {
            var url = $(this).data('url');
            $.ajax({
                url: url,
                dataType: 'json',
                cache: false,
                success: function (data) {
                    $('.machineList').empty().append(data.view);
                },
                error: function () {
                    alert('error');
                }
            });
        });

        
        $(document).on('click','.addPerson, .removePerson',function(){
            var url =$(this).data('url');
            var type =$(this).data('type');
            $.ajax({
                url:url,
                dataType: 'json',
                cache: false,
                data: {'type':type},
                success : function(data){
                $('.personList_'+type).empty().append(data.view);
                },error: function () {
                  alert('error');
                }
            });
        });
        
        $(document).on('change','.updatePerson',function(){
            var url ="{{route('admin.companiesAction',['update-person',$company->id])}}";
            var person_id =$(this).data('id');
            var type =$(this).data('type');
            var column =$(this).data('column');
            var key_value =$(this).val();
            $.ajax({
                url:url,
                dataType: 'json',
                cache: false,
                data: {'type':type,'person_id':person_id,'column':column,'key_value':key_value},
                success : function(data){
                // $('.machineList').empty().append(data.view);
                },error: function () {
                  alert('error');
                }
            });
        });
        
        $(document).on('change','.updateMachine',function(){
            var url ="{{route('admin.companiesAction',['update-machine',$company->id])}}";
            var machine_id =$(this).data('id');
            var column =$(this).data('column');
            var key_value =$(this).val();
            $.ajax({
                url:url,
                dataType: 'json',
                cache: false,
                data: {'machine_id':machine_id,'column':column,'key_value':key_value},
                success : function(data){
                // $('.machineList').empty().append(data.view);
                },error: function () {
                  alert('error');
                }
            });
        });
        
        $(document).on("change", ".division", function() {
            var id = $(this).val();
            var parent = $(this).closest('.row'); // limit scope to this block
        
            var districtSelect = parent.find('.district');
            var citySelect = parent.find('.city');
        
            if (id == '') {
                districtSelect.empty().append('<option value="">Select District</option>');
                citySelect.empty().append('<option value="">Select Thana</option>');
                return;
            }
        
            var url = '{{ url("geo/filter") }}/' + id;
            $.get(url, function(data) {
                districtSelect.empty().append(data.geoData);
                citySelect.empty().append('<option value="">Select Thana</option>');
            });
        });
        
        $(document).on("change", ".district", function() {
            var id = $(this).val();
            var parent = $(this).closest('.row'); // limit scope to this block
        
            var citySelect = parent.find('.city');
        
            if (id == '') {
                citySelect.empty().append('<option value="">Select Thana</option>');
                return;
            }
        
            var url = '{{ url("geo/filter") }}/' + id;
            $.get(url, function(data) {
                citySelect.empty().append(data.geoData);
            });
        });

        
    });
</script>

@endpush
