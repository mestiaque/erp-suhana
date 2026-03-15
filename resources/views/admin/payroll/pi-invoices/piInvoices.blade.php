@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Invoices List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Invoices List</h3>
         <div class="dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['pi']['add'])
             <a href="{{route('admin.piInvoicesAction','create')}}" class="btn-custom primary" style="padding:5px 15px;">
                 <i class="bx bx-plus"></i> Add Invoice
             </a>
            @endisset
            
             <a href="{{route('admin.piInvoices')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.piInvoices')}}">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Invoice, Company Name" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.piInvoices')}}">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-1">
                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                            <option value="">Select Action</option>
                            <option value="1">Pending</option>
                            <option value="2">Confirmed</option>
                            <option value="4">Cancelled</option>
                            <option value="5">Delete</option>
                        </select>
                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                    </div>
                </div>
                <div class="col-md-8">
                    <ul class="statuslist p-0">
                        <li><a href="{{route('admin.piInvoices')}}">All ({{$totals->total}})</a></li>
                        <li><a href="{{route('admin.piInvoices',['status'=>'pending'])}}">Pending ({{$totals->pending}})</a></li>
                        <li><a href="{{route('admin.piInvoices',['status'=>'confirmed'])}}">Confirmed ({{$totals->confirmed}})</a></li>
                        <li><a href="{{route('admin.piInvoices',['status'=>'completed'])}}">Completed ({{$totals->completed}})</a></li>
                        <li><a href="{{route('admin.piInvoices',['status'=>'cancelled'])}}">Cancelled ({{$totals->cancelled}})</a></li>
                        <li><a href="{{route('admin.piInvoices',['status'=>'trash'])}}">Trash ({{$totals->trash}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                @if(isset(json_decode(Auth::user()->permission->permission, true)['pi']['delete']))
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
                            <th style="min-width: 150px;">Company</th>
                            <th style="min-width: 150px;">Items</th>
                            <th style="min-width: 100px;">Total</th>
                            <th style="min-width: 100px;">Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $i=>$invoice)
                        <tr>
                            <td>
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
                                @if($invoice->order_status=='confirmed')
                                <span style="color: #43d39e;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @elseif($invoice->order_status=='completed')
                                <span style="color: #3f51b5;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-check-circle"></i>
                                </span>
                                @elseif($invoice->order_status=='cancelled')
                                <span style="color: #f44336;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-x"></i>
                                </span>
                                @else
                                <span style="color: #FF9800;font-size: 20px;line-height: 20px;position:absolute;">
                                    <i class="bx bx-analyse"></i>
                                </span>
                                @endif
                            </td>
                            <td><a href="{{route('admin.piInvoicesAction',['view',$invoice->id])}}" target="_blank">{{$invoice->invoice}}</a>
                            </td>
                            <td>@if($invoice->company) {{$invoice->company->factory_name}} @endif</td>
                            <td>{{$invoice->items()->count()}} Items</td>
                            <td>$ {{$invoice->grand_total}}</td>
                            <td>{{$invoice->created_at->format('d.m.Y')}}</td>
                            <td>
                                @if($invoice->hasLcOrders->count() > 0)
                                @isset(json_decode(Auth::user()->permission->permission, true)['pi']['view'])
                                <a href="{{route('admin.piInvoicesAction',['view',$invoice->id])}}" class="btn-custom success"><i class="bx bx-show"></i></a>
                                @endisset
                                @else
                                @isset(json_decode(Auth::user()->permission->permission, true)['pi']['add'])
                                <a href="{{route('admin.piInvoicesAction',['edit',$invoice->id])}}" class="btn-custom"><i class="bx bx-edit"></i></a>
                                @endisset
                                @endif
                                @isset(json_decode(Auth::user()->permission->permission, true)['pi']['delete'])
                                <a href="{{route('admin.piInvoicesAction',['delete',$invoice->id])}}" onclick="return confirm('Are You Want To Delete?')" class="btn-custom danger"><i class="bx bx-trash"></i></a>
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