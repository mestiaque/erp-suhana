@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Leads Convert')}}</title>
@endsection @push('css')
<style type="text/css">
    .leadInfoTable tr th,.leadInfoTable tr td{
        padding:5px 10px;        
    }
</style>
@endpush 

@section('contents')

<div class="flex-grow-1">
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Leads Convert</h3>
             <div class="dropdown">
                 <a href="{{route('admin.leadsAction',['view',$lead->id])}}" class="btn-custom primary"  style="padding:5px 15px;">
                     Back Lead
                 </a>
                 <a href="{{route('admin.leadsAction',['convert',$lead->id])}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{route('admin.leadsAction',['convert',$lead->id])}}" method="post" enctype="multipart/form-data">
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
                                        <div class="col-md-3 mb-3">
                                            <label>Deed/CT Serial</label>
                                            <input type="text" name="deed_serial" value="{{old('deed_serial')}}" class="form-control" placeholder="Enter Deed/CT Serial">
                                            @if ($errors->has('deed_serial'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('deed_serial') }}</span>
                                            @endif
                                        </div>    
                                        <div class="col-md-9 mb-3">
                                            <label>Customer Name*</label>
                                            <input type="text" name="owner_name" value="{{old('owner_name')?:$lead->name}}" class="form-control" placeholder="Enter Customer name" required="">
                                            @if ($errors->has('owner_name'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('owner_name') }}</span>
                                            @endif
                                        </div>    
                                           
                                        <div class="col-md-6 mb-3">
                                            <label>Mobile No*</label>
                                            <input type="text" name="owner_mobile" value="{{old('owner_mobile')?:$lead->mobile}}" class="form-control" placeholder="Enter Mobile no" required="">
                                            @if ($errors->has('owner_mobile'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('owner_mobile') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>E-mail</label>
                                            <input type="text" name="owner_email" value="{{old('owner_email')?:$lead->email}}" class="form-control" placeholder="Enter Email Address">
                                            @if ($errors->has('owner_email'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('owner_email') }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label>Company Name</label>
                                            <input type="text" name="factory_name" value="{{old('factory_name')?:$lead->factory_name}}" class="form-control" placeholder="Enter Company name" >
                                            @if ($errors->has('factory_name'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('factory_name') }}</span>
                                            @endif
                                        </div>
                                         <div class="col-md-6 mb-3">
                                            <label>Designation</label>
                                            <input type="text" name="owner_designation" value="{{old('owner_designation')?:$lead->designation}}" class="form-control" placeholder="Enter Designation">
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
                                                        <option value="{{$data->id}}" {{$data->id==$lead->division?'selected':''}}>{{$data->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4" style="padding:3px;">
                                                    <select id="district" class="form-control {{$errors->has('district')?'error':''}}" name="district" required="">
                                                        @if($lead->division==null)
                                                        <option value="">No District</option>
                                                        @else
                                                        <option value="">Select District</option>
                                                        @foreach(App\Models\Country::where('type',3)->where('parent_id',$lead->division)->get() as $data)
                                                        <option value="{{$data->id}}" {{$data->id==$lead->district?'selected':''}}>{{$data->name}}</option>
                                                        @endforeach @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-4" style="padding:3px;">
                                                    <select id="city" class="form-control {{$errors->has('city')?'error':''}}" name="city" required="">
                                                        @if($lead->district==null)
                                                        <option value="">No Thana</option>
                                                        @else
                                                        <option value="">Select Thana</option>
                                                        @foreach(App\Models\Country::where('type',4)->where('parent_id',$lead->district)->get() as $data)
                                                        <option value="{{$data->id}}" {{$data->id==$lead->city?'selected':''}}>{{$data->name}}</option>
                                                        @endforeach @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-12" style="padding:3px;">
                                                    <input type="text" name="company_address" value="{{old('company_address')?:$lead->address}}" class="form-control" placeholder="Enter Company Address" required="">
                                                    @if ($errors->has('company_address'))
                                                    <span style="color: red; margin: 0;">{{ $errors->first('company_address') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Customer Requirement</label>
                                            <textarea class="form-control" name="requirement"  rows="3" placeholder="Write Customer Requirement">{{old('requirement')?:$lead->requirement}}</textarea>
                                            @if ($errors->has('requirement'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('requirement') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Created Date</label>
                                            <input type="date" name="created_at" value="{{old('created_at')?:Carbon\Carbon::now()->format('Y-m-d')}}" class="form-control" >
                                            @if ($errors->has('created_at'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('created_at') }}</span>
                                            @endif
                                        </div>  
                                         <div class="col-md-2 mb-3">
                                            <label>Status</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="status" name="status" checked=""/>
                                                <label class="custom-control-label" for="status">Active</label>
                                            </div>
                                            @if ($errors->has('status'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('status') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Sister Concern*</label>
                                            <select class="form-control" name="concern" required="">
                                                <option value="">Select Concern</option>
                                                <option value="MG Machineries Corporation" {{$lead->concern=='MG Machineries Corporation'?'selected':''}} >MG Machineries Corporation (MMC)</option>
                                                <option value="Embroidery Machine Corporation" {{$lead->concern=='Embroidery Machine Corporation'?'selected':''}} >Embroidery Machine Corporation (EMC)</option>
                                                <option value="MG Training Centre Institute" {{$lead->concern=='MG Training Centre Institute'?'selected':''}} >MG Training Centre Institute (MTCI)</option>
                                                <option value="Fiber Laser Cutting Division" {{$lead->concern=='Fiber Laser Cutting Division'?'selected':''}} >Fiber Laser Cutting Division (FLCD)</option>
                                            </select> 
                                            @if ($errors->has('concern'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('concern') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Created By</label>
                                            <input type="text" readonly=""  value="{{$lead->assineeUser?$lead->assineeUser->name:''}}" placeholder="Created By" class="form-control" >
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
                                            <input type="text" name="key_parson_name" value="{{old('key_parson_name')?:$lead->key_parson_name}}" class="form-control" placeholder="Enter Key parson name" >
                                            @if ($errors->has('key_parson_name'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('key_parson_name') }}</span>
                                            @endif
                                        </div>  
                                        <div class="col-md-3 mb-3">
                                            <label>Designation</label>
                                            <input type="text" name="key_parson_designation" value="{{old('key_parson_designation')?:$lead->key_parson_designation}}" class="form-control" placeholder="Enter Designation" >
                                            @if ($errors->has('key_parson_designation'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('key_parson_designation') }}</span>
                                            @endif
                                        </div>    
                                        <div class="col-md-3 mb-3">
                                            <label>Mobile No</label>
                                            <input type="text" name="key_parson_mobile" value="{{old('key_parson_mobile')?:$lead->key_parson_mobile}}" class="form-control" placeholder="Enter Mobile No" >
                                            @if ($errors->has('key_parson_mobile'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('key_parson_mobile') }}</span>
                                            @endif
                                        </div>    
                                        <div class="col-md-3 mb-3">
                                            <label>Whatsapps No</label>
                                            <input type="text" name="key_parson_whatsapp_mobile" value="{{old('key_parson_whatsapp_mobile')?:$lead->key_parson_whatsapp_mobile}}" class="form-control" placeholder="Enter Whatsapp Number">
                                            @if ($errors->has('key_parson_whatsapp_mobile'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('key_parson_whatsapp_mobile') }}</span>
                                            @endif
                                        </div>    
                                        <div class="col-md-3 mb-3">
                                            <label>E-mail</label>
                                            <input type="text" name="key_parson_email" value="{{old('key_parson_email')?:$lead->key_parson_email}}" class="form-control" placeholder="Enter Email Address">
                                            @if ($errors->has('key_parson_email'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('key_parson_email') }}</span>
                                            @endif
                                        </div>  
                                         
                                        <!--<div class="col-md-12 mb-1 mt-3">-->
                                        <!--    <h4 class="text-danger">Partner Information</h4>-->
                                        <!--</div>-->
                                        <!--<div class="col-md-8 mb-3">-->
                                        <!--    <label>Partner Name</label>-->
                                        <!--    <input type="text" name="partner_name" value="{{old('partner_name')?:$lead->partner_name}}" class="form-control" placeholder="Enter partner name" >-->
                                        <!--    @if ($errors->has('partner_name'))-->
                                        <!--    <span style="color: red; margin: 0;">{{ $errors->first('partner_name') }}</span>-->
                                        <!--    @endif-->
                                        <!--</div>  -->
                                        <!--<div class="col-md-4 mb-3">-->
                                        <!--    <label>Designation</label>-->
                                        <!--    <input type="text" name="partner_designation" value="{{old('partner_designation')?:$lead->partner_designation}}" class="form-control" placeholder="Enter Designation" >-->
                                        <!--    @if ($errors->has('partner_designation'))-->
                                        <!--    <span style="color: red; margin: 0;">{{ $errors->first('partner_designation') }}</span>-->
                                        <!--    @endif-->
                                        <!--</div>-->
                                        <!--<div class="col-md-12 mb-3">-->
                                        <!--    <label>Partner Details</label>-->
                                        <!--    <input type="text" name="partner_details" value="{{old('partner_details')?:$lead->partner_details}}" class="form-control" placeholder="Enter details" >-->
                                        <!--    @if ($errors->has('partner_details'))-->
                                        <!--    <span style="color: red; margin: 0;">{{ $errors->first('partner_details') }}</span>-->
                                        <!--    @endif-->
                                        <!--</div>-->
                                        <div class="col-md-12 mb-1 mt-3">
                                            <h4 class="text-danger">Manager Information</h4>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label>Manager Name</label>
                                            <input type="text" name="manager_name" value="{{old('manager_name')?:$lead->manager_name}}" class="form-control" placeholder="Enter Manager name" >
                                            @if ($errors->has('manager_name'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('manager_name') }}</span>
                                            @endif
                                        </div>  
                                        <div class="col-md-4 mb-3">
                                            <label>Designation</label>
                                            <input type="text" name="manager_designation" value="{{old('manager_designation')?:$lead->manager_designation}}" class="form-control" placeholder="Enter Designation" >
                                            @if ($errors->has('manager_designation'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('manager_designation') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label>Manager Details</label>
                                            <input type="text" name="manager_details" value="{{old('manager_details')?:$lead->manager_details}}" class="form-control" placeholder="Enter details" >
                                            @if ($errors->has('manager_details'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('manager_details') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-12 mb-1 mt-3">
                                            <h4 class="text-danger">PM Information</h4>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label>PM Name</label>
                                            <input type="text" name="pm_name" value="{{old('pm_name')?:$lead->pm_name}}" class="form-control" placeholder="Enter PM name" >
                                            @if ($errors->has('pm_name'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('pm_name') }}</span>
                                            @endif
                                        </div>  
                                        <div class="col-md-4 mb-3">
                                            <label>Designation</label>
                                            <input type="text" name="pm_designation" value="{{old('pm_designation')?:$lead->pm_designation}}" class="form-control" placeholder="Enter Designation" >
                                            @if ($errors->has('pm_designation'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('pm_designation') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label>PM Details</label>
                                            <input type="text" name="pm_details" value="{{old('pm_details')?:$lead->pm_details}}" class="form-control" placeholder="Enter details" >
                                            @if ($errors->has('pm_details'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('pm_details') }}</span>
                                            @endif
                                        </div>
                                        
                                        {{--
                                        <div class="col-md-12 mb-1 mt-3">
                                            <h4 class="text-danger">Operator Information</h4>
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <label>Operator Name (01)</label>
                                            <input type="text" name="operator_name" value="{{old('operator_name')?:$lead->operator_name}}" class="form-control" placeholder="Enter Operator name" >
                                            @if ($errors->has('operator_name'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('operator_name') }}</span>
                                            @endif
                                        </div>  
                                        <div class="col-md-7 mb-3">
                                            <label>Operator Details</label>
                                            <input type="text" name="operator_details" value="{{old('operator_details')?:$lead->operator_details}}" class="form-control" placeholder="Enter Details" >
                                            @if ($errors->has('operator_details'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('operator_details') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <label>Operator Name (02)</label>
                                            <input type="text" name="operator2_name" value="{{old('operator2_name')?:$lead->operator2_name}}" class="form-control" placeholder="Enter Operator name" >
                                            @if ($errors->has('operator2_name'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('operator2_name') }}</span>
                                            @endif
                                        </div>  
                                        <div class="col-md-7 mb-3">
                                            <label>Operator Details</label>
                                            <input type="text" name="operator2_details" value="{{old('operator2_details')?:$lead->operator2_details}}" class="form-control" placeholder="Enter Details" >
                                            @if ($errors->has('operator2_details'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('operator2_details') }}</span>
                                            @endif
                                        </div>
                                        
                                
                                        <div class="col-md-12 mb-1 mt-3">
                                            <h4 class="text-danger">Operator Information</h4>
                                        </div>
                                        
                                        <div class="col-md-12 mb-1 mt-3">
                                            <div class="table-responsive personList_0">
                                                @include(adminTheme().'leads.includes.personList', ['type' => 0])
                                            </div>
                                        </div>
                                        --}}
                                        <div class="col-md-12 mb-1 mt-3">
                                            <h4 class="text-danger">Engineer Information</h4>
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label>Engineer Name</label>
                                            <input type="text" name="engineer_name" value="{{old('engineer_name')?:$lead->engineer_name}}" class="form-control" placeholder="Enter Engineer name" >
                                            @if ($errors->has('engineer_name'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('engineer_name') }}</span>
                                            @endif
                                        </div>  
                                        <div class="col-md-4 mb-3">
                                            <label>Designation</label>
                                            <input type="text" name="engineer_designation" value="{{old('engineer_designation')?:$lead->engineer_designation}}" class="form-control" placeholder="Enter Designation" >
                                            @if ($errors->has('engineer_designation'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('engineer_designation') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label>Engineer Details</label>
                                            <input type="text" name="engineer_details" value="{{old('engineer_details')?:$lead->engineer_details}}" class="form-control" placeholder="Enter details" >
                                            @if ($errors->has('engineer_details'))
                                            <span style="color: red; margin: 0;">{{ $errors->first('engineer_details') }}</span>
                                            @endif
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

@endsection 
@push('js')
<script>
    $(document).ready(function(){
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
            var url ="{{route('admin.leadsAction',['update-person',$lead->id])}}";
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
    });
</script>
@endpush