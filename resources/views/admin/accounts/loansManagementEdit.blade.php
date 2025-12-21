@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Loan Edit')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Loan Edit</h3>
         <div class="dropdown">
             <a href="{{route('admin.loansManagement')}}" class="btn-custom primary"  style="padding:5px 15px;">
                  Back
             </a>
             <a href="{{route('admin.loansManagementAction',['edit',$loan->id])}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <div class="row">
            <div class="col-md-4">
                <div class="userInfo">
                    @if($loan->user)
                    <div class="image">
                        <img src="{{asset($loan->user->image())}}" style="height:100px;width:100px;">
                    </div>
                    <p>
                        <b>Name:</b> {{$loan->user->name}}<br>
                        <b>Mobile:</b> {{$loan->user->mobile}}<br>
                        <b>Total Loan:</b> BDT 0 <br>
                        <b>Salary:</b> BDT 0<br>
                    </p>
                    @else
                    <h3>Employee Not Found</h3>
                    @endif
                </div>
            </div>
            <div class="col-md-8">
                <form action="{{route('admin.loansManagementAction',['update',$loan->id])}}" method="post">
                    @csrf
                    
                    <div class="form-group">
                        <label>Account Method*</label>
                        @if($loan->status=='success')
                        <select class="form-control" disabled="">
                            <option value="{{$loan->payment_method_id}}">{{$loan->account?$loan->account->name:''}}</option>
                        </select>
                        @else
                        <select class="form-control" name="account" required="">
                            <option value="">Select Account</option>
                            @foreach($accountMethods as $method)
                            <option value="{{$method->id}}" {{$loan->payment_method_id==$method->id?'selected':''}}>{{$method->name}} - BDT {{priceFormat($method->amount)}}</option>
                            @endforeach
                        </select>
                        @endif
                        
        				@if ($errors->has('payment'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment') }}</p>
        				@endif
                    </div>
                    <div class="form-group">
                        <label>Loan Amount*</label>
                        <input type="number" class="form-control" 
                        step="any"
                        placeholder="Amount" value="{{$loan->amount > 0?$loan->amount:''}}"
                        @if($loan->status=='success')
                        disabled=""
                        @else
                        name="amount"
                        required=""
                        @endif
                        >
                        @if ($errors->has('amount'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('amount') }}</p>
        				@endif
                    </div>
                    @if($loan->status=='success')
                    <div class="form-group">
                        <label style="color: #FF9800;font-weight: bold;">Paid Amount*</label>
                        <input type="number" readonly="" class="form-control"  step="any" placeholder="Amount" value="{{$loan->paid_balance}}" name="amount">
                    </div>
                    @endif
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="5" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{$loan->billing_note}}</textarea>
    					@if ($errors->has('description'))
    					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
    					@endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Publish Date*</label>
                                <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$loan->created_at->format('Y-m-d')}}" name="created_at" required="">
                                @if ($errors->has('created_at'))
            					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
            					@endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Submit Loan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>




@endsection @push('js') 


@endpush