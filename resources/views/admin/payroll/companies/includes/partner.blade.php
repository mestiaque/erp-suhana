<div class="partDiv_{{$partner->id}}">
    <hr>
    <h3 style="text-align: center;text-transform: uppercase;">Partner 
    <!--{{$i+1}}-->
    <span class="btn btn-sm btn-danger removePartner" data-id="{{$partner->id}}" data-url="{{route('admin.companiesAction',['delete-partners',$company->id,'partner_id'=>$partner->id])}}"><i class="bx bx-trash"></i></span>
    </h3>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Customer Name*</label>
            <input type="text" data-name="name" value="{{$partner->name}}" class="form-control partnerUpdate" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}" placeholder="Enter Customer name" required="">
        </div>    
           
        <div class="col-md-3 mb-3">
            <label>Mobile No*</label>
            <input type="text" data-name="mobile" value="{{$partner->mobile}}" class="form-control partnerUpdate" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}" placeholder="Enter Mobile no" required="">
        </div>
        <div class="col-md-3 mb-3">
            <label>Other Mobile No</label>
            <input type="text" data-name="mobile2" value="{{$partner->mobile2}}" class="form-control partnerUpdate" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}" placeholder="Enter other Mobile no" >
        </div>
        
        <div class="col-md-4 mb-3">
            <label>E-mail</label>
            <input type="text" data-name="email" value="{{$partner->email}}" class="form-control partnerUpdate" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}" placeholder="Enter Email Address">
        </div>
        
        <div class="col-md-4 mb-3">
            <label>Company Name</label>
            <input type="text" data-name="company_name" value="{{$partner->company_name}}" class="form-control partnerUpdate" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}" placeholder="Enter Company name"  >
        </div>
        
        <div class="col-md-4 mb-3">
            <label>Designation</label>
            <input type="text" data-name="designation" value="{{$partner->designation}}" class="form-control partnerUpdate" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}" placeholder="Enter Designation">
        </div>
        
        <div class="col-md-6 mb-3">
            <label>Company Address*</label>
            <div class="row" style="margin:0 -3px;">
                <div class="col-md-4" style="padding:3px;">
                    <select class="form-control division  {{$errors->has('division')?'error':''}} partnerUpdate"  data-name="division" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}"  required="">
                        <option value="">Select Division</option>
                        @foreach(App\Models\Country::where('type',2)->where('parent_id',1)->get() as $data)
                        <option value="{{$data->id}}" {{$data->id==$partner->division?'selected':''}}>{{$data->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4" style="padding:3px;">
                    <select class="form-control district {{$errors->has('district')?'error':''}} partnerUpdate" data-name="district" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}"  required="">
                        @if($partner->division==null)
                        <option value="">Select District</option>
                        @else
                        <option value="">Select District</option>
                        @foreach(App\Models\Country::where('type',3)->where('parent_id',$partner->division)->get() as $data)
                        <option value="{{$data->id}}" {{$data->id==$partner->district?'selected':''}}>{{$data->name}}</option>
                        @endforeach @endif
                    </select>
                </div>
                <div class="col-md-4" style="padding:3px;">
                    <select class="form-control city {{$errors->has('city')?'error':''}} partnerUpdate" data-name="city" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}"  required="">
                        @if($partner->district==null)
                        <option value="">Select Thana</option>
                        @else
                        <option value="">Select Thana</option>
                        @foreach(App\Models\Country::where('type',4)->where('parent_id',$partner->district)->get() as $data)
                        <option value="{{$data->id}}" {{$data->id==$partner->city?'selected':''}}>{{$data->name}}</option>
                        @endforeach @endif
                    </select>
                </div>
                <div class="col-md-12" style="padding:3px;">
                    <input type="text" data-name="company_address" value="{{$partner->company_address}}" class="form-control partnerUpdate" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}"  placeholder="Enter Company Address" required="">
                </div>
            </div>
        </div>  
        <div class="col-md-6 mb-3">
            <label>Customer Requirement</label>
            <textarea class="form-control partnerUpdate" data-name="description" data-url="{{route('admin.companiesAction',['update-partners',$company->id,'partner_id'=>$partner->id])}}"  rows="3" placeholder="Write Customer Requirement">{{$partner->description}}</textarea>
        </div>
    </div>
</div>