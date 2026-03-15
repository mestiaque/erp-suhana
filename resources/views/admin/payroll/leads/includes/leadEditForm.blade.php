<form action="{{route('admin.leadsAction',['update',$lead->id])}}" method="post" enctype="multipart/form-data">
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
                    <div class="col-md-6 mb-3">
                        <label>Customer Name*</label>
                        <input type="text" name="owner_name" value="{{old('owner_name')?:$lead->name}}" class="form-control" placeholder="Enter Customer name" required="">
                        @if ($errors->has('owner_name'))
                        <span style="color: red; margin: 0;">{{ $errors->first('owner_name') }}</span>
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
                        <label>Mobile No*</label>
                        <input type="text" name="owner_mobile" value="{{old('owner_mobile')?:$lead->mobile}}" class="form-control mobileInput" data-url="{{route('admin.leadsAction',['mobile-doublicate-check',$lead->id])}}" placeholder="Enter Mobile no" required="">
                        @if ($errors->has('owner_mobile'))
                        <span style="color: red; margin: 0;">{{ $errors->first('owner_mobile') }}</span>
                        @endif
                        <span class="mobileDoubleErr" style="color:red;"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>E-mail</label>
                        <input type="text" name="owner_email" value="{{old('owner_email')?:$lead->email}}" class="form-control" placeholder="Enter Email Address">
                        @if ($errors->has('owner_email'))
                        <span style="color: red; margin: 0;">{{ $errors->first('owner_email') }}</span>
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
                                    @endforeach 
                                    <option value="0" {{0==$lead->city?'selected':''}}>Others</option>
                                    @endif
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
                    
                    <div class="col-md-4 mb-3">
                        <label>Customer Status *</label><br>
                        <label><input type="radio" class="customerStatusChenage" name="customer_status" value="Not Potential" {{ old('company_category', $lead->customer_status ?? '') == 'Not Potential' ? 'checked' : '' }} required="" > Not Potential</label>
                        <label><input type="radio" class="customerStatusChenage" name="customer_status" value="Potential" {{ old('company_category', $lead->customer_status ?? '') == 'Potential' ? 'checked' : '' }} required="" > Potential</label>
                        <label><input type="radio" class="customerStatusChenage" name="customer_status" value="Very Potential" {{ old('company_category', $lead->customer_status ?? '') == 'Very Potential' ? 'checked' : '' }} required="" > Very Potential</label>
                    </div> 
                    <div class="col-md-4 mb-3">
                        <label>Date of Next Call <span class="nextCallStar">{{($lead->customer_status  && $lead->customer_status!='Not Potential')?'*':''}}</span></label><br>
                        <input type="date" name="next_visit_day" value="{{ old('next_visit_day') ?: ($lead->next_visit_day ? \Carbon\Carbon::parse($lead->next_visit_day)->format('Y-m-d') : '') }}" class="form-control nextCall"
                        
                        @if($lead->customer_status  && $lead->customer_status!='Not Potential')
                        required
                        @endif
                        >
                        @if ($errors->has('next_visit_day'))
                        <span style="color: red; margin: 0;">{{ $errors->first('next_visit_day') }}</span>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Lead Type*</label><br>
                        <select class="form-control {{$errors->has('source')?'error':''}}" name="source" required="">
                            <option value="">Select Type</option>
                            <option value="Physical Visit" {{$lead->source=='Physical Visit'?'selected':''}} >Physical Visit</option>
                            <option value="Company Provided" {{$lead->source=='Company Provided'?'selected':''}} >Company Provided</option>
                            <option value="Phone call" {{$lead->source=='Phone call'?'selected':''}} >Phone call</option>
                            <option value="By Reference" {{$lead->source=='By Reference'?'selected':''}} >By Reference</option>
                            <option value="Social Media" {{$lead->source=='Social Media'?'selected':''}} >Social Media</option>
                            <option value="Others" {{$lead->source=='Others'?'selected':''}} >Others</option>
                        </select>
                    </div>
                    
                    
                    <div class="col-md-4 mb-3">
                        <label>Sister Concern*</label>
                        <select class="form-control" name="concern" required="">
                            <option value="">Select Concern</option>
                            <option value="MG Machineries Corporation" {{$lead->concern=='MG Machineries Corporation'?'selected':''}}  >MG Machineries Corporation (MMC)</option>
                            <option value="Embroidery Machine Corporation" {{$lead->concern=='Embroidery Machine Corporation'?'selected':''}} >Embroidery Machine Corporation (EMC)</option>
                            <option value="MG Training Centre Institute" {{$lead->concern=='MG Training Centre Institute'?'selected':''}} >MG Training Centre Institute (MTCI)</option>
                            <option value="Fiber Laser Cutting Division" {{$lead->concern=='Fiber Laser Cutting Division'?'selected':''}} >Fiber Laser Cutting Division (FLCD)</option>
                        </select> 
                        @if ($errors->has('concern'))
                        <span style="color: red; margin: 0;">{{ $errors->first('concern') }}</span>
                        @endif
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Interested Products*</label>
                        <select class="select2" multiple="multiple" data-placeholder="Select product" name="services[]" required="">
                            @foreach($services as $service)
                            <option value="{{$service->id}}" {{ in_array($service->id, old('services', $lead->servicesIDs())) ? 'selected' : '' }} >{{$service->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('services'))
            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('services') }}</p>
            		    @endif
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Remarks </label>
                        <textarea class="form-control" name="requirement"  rows="3" placeholder="Write Remarks" >{{old('requirement')?:$lead->requirement}}</textarea>
                        @if ($errors->has('requirement'))
                        <span style="color: red; margin: 0;">{{ $errors->first('requirement') }}</span>
                        @endif
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Created Date</label>
                        <input type="date" name="created_at" value="{{old('created_at')?:$lead->created_at->format('Y-m-d')}}" class="form-control" readonly="" >
                        @if ($errors->has('created_at'))
                        <span style="color: red; margin: 0;">{{ $errors->first('created_at') }}</span>
                        @endif
                    </div>  
                    <div class="col-md-4 mb-3">
                        <label>Status</label>
                        <select class="form-control" name="status" required="">
                            <option value="New" {{ old('status', $lead->status ?? '') == 'New' ? 'selected' : '' }}>New</option>
                            <option value="Contacted" {{ old('status', $lead->status ?? '') == 'Contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="Interested" {{ old('status', $lead->status ?? '') == 'Interested' ? 'selected' : '' }}>Interested</option>
                            <option value="Follow-up Scheduled" {{ old('status', $lead->status ?? '') == 'Follow-up Scheduled' ? 'selected' : '' }}>Follow-up Scheduled</option>
                            <option value="Meeting Done" {{ old('status', $lead->status ?? '') == 'Meeting Done' ? 'selected' : '' }}>Meeting Done</option>
                            <option value="Proposal Sent" {{ old('status', $lead->status ?? '') == 'Proposal Sent' ? 'selected' : '' }}>Proposal Sent</option>
                            <option value="Win" {{ old('status', $lead->status ?? '') == 'Win' ? 'selected' : '' }}>Win (Convert Customer)</option>
                            <option value="Cancelled" {{ old('status', $lead->status ?? '') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @if ($errors->has('status'))
                        <span style="color: red; margin: 0;">{{ $errors->first('status') }}</span>
                        @endif
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Assigned To*</label>
                        <select class="select23" data-placeholder="Select Assignee" name="assignee" required="">
                            <option ></option>
                            @if($lead->assinee_id==null)
                                <option value="{{Auth::id()}}" selected="" >{{Auth::user()->name}}</option>
                            @else
                            
                                @if(empty(json_decode(Auth::user()->permission->permission, true)['employees']['list']))
                                    <option value="{{Auth::id()}}" selected="">{{Auth::user()->name}}</option>
                                @else
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}" {{ old('assignee', $lead->assinee_id ?? '') == $user->id ? 'selected' : '' }} >{{$user->name}}</option>
                                    @endforeach
                                @endif

                            @endif
                        </select>
                        @if ($errors->has('assignee'))
            			<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('assignee') }}</p>
            		    @endif
                    </div>  
               </div>
               <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </div>
        <div class="tab-pane" id="other" >
            <div class="card" style="padding: 5px;">
                <div class="row">
                    <!--<div class="col-md-4 mb-3">-->
                    <!--    <label>Designation</label>-->
                    <!--    <input type="text" name="owner_designation" value="{{old('owner_designation')?:$lead->designation}}" class="form-control" placeholder="Enter Designation">-->
                    <!--    @if ($errors->has('owner_designation'))-->
                    <!--    <span style="color: red; margin: 0;">{{ $errors->first('owner_designation') }}</span>-->
                    <!--    @endif-->
                    <!--</div>-->
                    
                     
                    <div class="col-md-6 mb-3">
                        <label>Company Category</label><br>
                        <label><input type="radio" name="company_category" value="Small" {{ old('company_category', $lead->company_category ?? '') == 'Small' ? 'checked' : '' }} > Small</label>
                        <label><input type="radio" name="company_category" value="Medium" {{ old('company_category', $lead->company_category ?? '') == 'Medium' ? 'checked' : '' }} > Medium</label>
                        <label><input type="radio" name="company_category" value="Large" {{ old('company_category', $lead->company_category ?? '') == 'Large' ? 'checked' : '' }} > Large</label>
                    </div>  
                    <div class="col-md-6 mb-3">
                        <label>Company Status</label><br>
                        <label><input type="radio" name="company_status" value="Risky" {{ old('company_status', $lead->company_status ?? '') == 'Risky' ? 'checked' : '' }}  > Risky</label>
                        <label><input type="radio" name="company_status" value="Stable" {{ old('company_status', $lead->company_status ?? '') == 'Stable' ? 'checked' : '' }} > Stable</label>
                        <label><input type="radio" name="company_status" value="Growing" {{ old('company_status', $lead->company_status ?? '') == 'Growing' ? 'checked' : '' }} > Growing</label>
                        <label><input type="radio" name="company_status" value="Booming" {{ old('company_status', $lead->company_status ?? '') == 'Booming' ? 'checked' : '' }} > Booming</label>
                    </div>
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
                     
                    <div class="col-md-12 mb-1 mt-3">
                        <h4 class="text-danger">Partner Information</h4>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label>Partner Name</label>
                        <input type="text" name="partner_name" value="{{old('partner_name')?:$lead->partner_name}}" class="form-control" placeholder="Enter partner name" >
                        @if ($errors->has('partner_name'))
                        <span style="color: red; margin: 0;">{{ $errors->first('partner_name') }}</span>
                        @endif
                    </div>  
                    <div class="col-md-4 mb-3">
                        <label>Designation</label>
                        <input type="text" name="partner_designation" value="{{old('partner_designation')?:$lead->partner_designation}}" class="form-control" placeholder="Enter Designation" >
                        @if ($errors->has('partner_designation'))
                        <span style="color: red; margin: 0;">{{ $errors->first('partner_designation') }}</span>
                        @endif
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Partner Details</label>
                        <input type="text" name="partner_details" value="{{old('partner_details')?:$lead->partner_details}}" class="form-control" placeholder="Enter details" >
                        @if ($errors->has('partner_details'))
                        <span style="color: red; margin: 0;">{{ $errors->first('partner_details') }}</span>
                        @endif
                    </div>
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
                    --}}
            
                    <div class="col-md-12 mb-1 mt-3">
                        <h4 class="text-danger">Operator Information</h4>
                    </div>
                    
                    <div class="col-md-12 mb-1 mt-3">
                        <div class="table-responsive personList_0">
                            @include(adminTheme().'leads.includes.personList', ['type' => 0])
                        </div>
                    </div>
                    
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
    <!--<br>-->
    
    <!--<div class="row">-->
         
    <!--    <div class="col-md-6 mb-3">-->
    <!--        <label>Google Map</label>-->
    <!--        <input type="text" name="google_map" value="{{old('google_map')?:$lead->google_map}}" class="form-control" placeholder="Enter Google map">-->
    <!--        @if ($errors->has('google_map'))-->
    <!--        <span style="color: red; margin: 0;">{{ $errors->first('google_map') }}</span>-->
    <!--        @endif-->
    <!--    </div> -->
        
        
    <!--    <div class="col-md-12 mb-1 mt-3">-->
    <!--        <h4 class="text-danger">Company Status Information</h4>-->
    <!--    </div>-->
        
    <!--    <div class="col-md-3 mb-3">-->
    <!--        <label>Number of Employee</label>-->
    <!--        <input type="number" name="number_of_employee" value="{{old('number_of_employee')?:$lead->number_of_employee}}" class="form-control" placeholder="Employee">-->
    <!--        @if ($errors->has('number_of_employee'))-->
    <!--        <span style="color: red; margin: 0;">{{ $errors->first('number_of_employee') }}</span>-->
    <!--        @endif-->
    <!--    </div>  -->
    <!--    <div class="col-md-12 mb-1 mt-3">-->
    <!--        <h4 class="text-danger">Company Note:</h4>-->
    <!--    </div>-->
    <!--    <div class="col-md-12 mb-3">-->
    <!--        <label>Remarks</label>-->
    <!--        <textarea class="form-control" name="remarks" placeholder="Write remarks">{{old('remarks')?:$lead->notes}}</textarea>-->
    <!--        @if ($errors->has('remarks'))-->
    <!--        <span style="color: red; margin: 0;">{{ $errors->first('remarks') }}</span>-->
    <!--        @endif-->
    <!--    </div>  -->
        
    <!--</div>-->
    
    </form>