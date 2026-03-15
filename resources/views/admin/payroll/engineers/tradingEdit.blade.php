@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Trading Edit')}}</title>
@endsection @push('css')
<style type="text/css">
    .searchlist ul {
        list-style: none;
        padding: 5px;
    }
    
    .searchlist ul li {
        border-top: 1px solid #dbd6d6;
        padding: 5px 0;
    }
    .searchlist ul li img {
        width: 35px;
        height: 35px;
        border-radius: 100%;
        border: 1px solid #dbd6d6;
        padding: 2px;
        margin-right: 10px;
    }
</style>
@endpush @section('contents')

<div class="flex-grow-1" >
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Trading Edit</h3>
             <div class="dropdown">
             <a href="{{route('admin.supplierTrading')}}" class="btn-custom yellow">
                 <i class='bx bx-left-arrow-alt'></i> Back
             </a> 
         </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="row">
            <div class="col-md-4">
                <div class="userInfo">
                    @if($trading->supplier)
                    <div class="image">
                        <img src="{{asset($trading->supplier->image())}}" style="height:100px;width:100px;">
                    </div>
                    <p>
                        <b>Name:</b> {{$trading->supplier->name}}<br>
                        <b>Balance:</b> @if($trading->supplier->amount >=0) <span style="color:green;"> Advance @else <span style="color:red;"> Due @endif BDT {{priceFormat(abs($trading->supplier->amount))}}</span><br>
                        <b>Details:</b> <br>
                        {!!$trading->supplier->description!!}
                    </p>
                    @else
                    <h3>Employee Not Found</h3>
                    @endif
                </div>
            </div>
            <div class="col-md-8">
                @if($trading->type==1)
                <button class="btn-custom primary">Received Goods</button>
                @elseif($trading->type==2)
                <button class="btn-custom yellow">Pay Bill</button>
                @endif
                <form action="{{route('admin.supplierTradingAction',['update',$trading->id])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if($trading->type==2)
                    <div class="form-group">
                        <label for="name">Account Method *</label>
                        @if($trading->status=='active')
                        <select class="form-control" disabled="">
                            <option value="{{$trading->method_id}}">{{$trading->method?$trading->method->name:''}}</option>
                        </select>
                        @else
                        <select class="form-control" name="account" required="">
                            <option value="">Select Account</option>
                            @foreach($accountMethods as $method)
                            <option value="{{$method->id}}" {{$method->id==$trading->method_id?'selected':''}} >{{$method->name}} - BDT {{priceFormat($method->amount)}}</option>
                            @endforeach
                        </select>
                        @endif
        				@if ($errors->has('payment'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('payment') }}</p>
        				@endif
                    </div>
                    @endif
                    <div class="form-group">
                        <label>Ref/Title*</label>
                        <div class="searchRef">
                            <div class="input-group">
                                <input type="text" class="form-control {{$errors->has('title')?'error':''}}" value="{{$trading->title?:old('title')}}"
                                
                                name="title" placeholder="Enter title" required=""
                                {{$trading->title?'readonly':''}}
                                >
                                <div class="refActionBtn">
                                    @if($trading->title)
                                    <span class="remove" style="background: #ff6a6a;color: white;"><i class="bx bx-x"></i></span>
                                    @else
                                    <span><i class="bx bx-search"></i></span>
                                    @endif
                                </div>
                            </div>
                            <div class="reffSearchResult">
                               @include(adminTheme().'reffmembers.includes.reffSearchResult')
                            </div>
        			    </div>
                        <!--<input type="text" name="title" value="{{$trading->title}}" class="form-control" placeholder="Enter Ref/Title" required="">-->
        				@if ($errors->has('title'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
        				@endif
                    </div>
                    <div class="form-group">
                        <label>Amount*</label>
                        <input type="number" class="form-control" 
                        step="any"
                        placeholder="Amount" value="{{$trading->amount > 0?$trading->amount:''}}"
                       @if($trading->status=='active')
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
                    <div class="form-group">
                        <label>Attachment*</label>
                        <input type="file" name="attachment" class="form-control" style="padding: 3px;">
        				@if ($errors->has('attachment'))
        				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('attachment') }}</p>
        				@endif
                    </div>
                    @if($trading->imageFile)
                    <div class="form-group">
                        <a href="{{asset($trading->imageFile->file_url)}}" class="btn btn-danger" target="_blank"><i class="bx bx-file"></i> View Attachment</a> 
                    </div>
                    
                    @endif
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="5" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{{$trading->description}}</textarea>
    					@if ($errors->has('description'))
    					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
    					@endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Publish Date*</label>
                                <input type="date" class="form-control {{$errors->has('created_at')?'error':''}}" value="{{$trading->created_at->format('Y-m-d')}}" name="created_at" required="">
                                @if ($errors->has('created_at'))
            					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
            					@endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Submit</button>
                    </div>
                </form>
            </div>
            </div>
            
        </div>
    </div>
</div>

@endsection @push('js')


@endpush

