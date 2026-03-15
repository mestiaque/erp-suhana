@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Purchases Invoices')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Purchases Invoices</h3>
         <div class="dropdown">
             @isset(json_decode(Auth::user()->permission->permission, true)['purchases']['add'])
             <a href="{{route('admin.purchasesAction','create')}}" class="btn-custom primary" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i>  Purchase
             </a>
             @endisset
             <a href="{{route('admin.purchases')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.purchases')}}">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Purchases Number" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <hr>
        <form action="{{route('admin.purchases')}}">
            <div class="row">
                <div class="col-md-4">
                    @isset(json_decode(Auth::user()->permission->permission, true)['lc']['delete'])
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            <option value="2">Confirmed</option>
                            <option value="5">Delete</option>
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                    </div>
                    @endisset
                </div>
                <div class="col-md-8">
                    <ul class="statuslist p-0">
                        <li><a href="{{route('admin.purchases')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.purchases',['status'=>'confirmed'])}}">Confirmed ({{$totals->confirmed}})</a></li>
                        <li><a href="{{route('admin.purchases',['status'=>'trash'])}}">Trash ({{$totals->trash}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                @if(isset(json_decode(Auth::user()->permission->permission, true)['lc']['delete']))
                                <div class="checkbox mr-3">
                                 <input class="inp-cbx" id="checkall" type="checkbox" style="display: none;" />
                                 <label class="cbx" for="checkall">
                                     <span>
                                         <svg width="12px" height="10px" viewbox="0 0 12 10">
                                             <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                         </svg>
                                     </span>
                                     All <span class="checkCounter"></span> 
                                 </label>
                                </div>
                                @else
                                SL
                                @endif
                            </th>
                            <th style="min-width: 100px;">Invoice</th>
                            <th style="min-width: 250px;">Supplier Name</th>
                            <th style="min-width: 120px;padding: 10px 5px;">Total Amount</th>
                            <th style="min-width: 150px;">Paid Amount</th>
                            <th style="min-width: 150px;">Due Amount</th>
                            <th style="min-width: 120px;padding: 10px 5px;">Date</th>
                            <th style="min-width: 120px;padding: 10px 5px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $i=>$invoice)
                        <tr>
                            <td style="position: relative;">
                                @isset(json_decode(Auth::user()->permission->permission, true)['pi']['delete'])
                                <div class="checkbox">
                                     <input class="inp-cbx" id="cbx_{{$invoice->id}}" type="checkbox" name="checkid[]" value="{{$invoice->id}}" style="display: none;" />
                                     <label class="cbx" for="cbx_{{$invoice->id}}">
                                         <span>
                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                             </svg>
                                         </span>
                                     </label>
                                 </div>
                                 @endisset
                                <span style="margin:0 5px;">{{$invoices->currentpage()==1?$i+1:$i+($invoices->perpage()*($invoices->currentpage() - 1))+1}}</span>
                            </td>
                            <td><a href="{{route('admin.purchasesAction',['view',$invoice->id])}}" target="_blank">{{$invoice->invoice}}</a></td>
                            <td>{{$invoice->name}} - {{$invoice->mobile?:$invoice->email}}</td>
                            <td>{{priceFormat($invoice->grand_total)}}</td>
                            <td>{{priceFormat($invoice->paid_amount)}}</td>
                            <td>{{priceFormat($invoice->due_amount)}}</td>
                            <td>{{$invoice->created_at->format('d.m.Y')}}</td>
                            <td>
                                @isset(json_decode(Auth::user()->permission->permission, true)['purchases']['add'])
                                <a href="{{route('admin.purchasesAction',['edit',$invoice->id])}}" class="btn-custom"><i class="bx bx-edit"></i></a>
                                @endisset
                                @isset(json_decode(Auth::user()->permission->permission, true)['purchases']['delete'])
                                <a href="{{route('admin.purchasesAction',['delete',$invoice->id])}}" onclick="return confirm('Are You Want To Delete?')" class="btn-custom danger"><i class="bx bx-trash"></i></a>
                                @endisset
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$invoices->links('pagination')}}
            </div>
        </form>
        
        
    </div>
</div>
</div>



@endsection @push('js') @endpush