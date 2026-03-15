@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Due Bill Collections')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush

@section('contents')

<div class="flex-grow-1">

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Due Bill Collections</h3>
         <div class="dropdown">

             <a href="{{ route('admin.billDueCollection', [
    'export' => 'report',
    'startDate' => request()->startDate,
    'endDate' => request()->endDate,
    'due_type' => request()->due_type,
    'search' => request()->search]) }}" class="btn-custom yellow">
                 <i class="bx bx-export"></i> Export
             </a>
             <a href="{{route('admin.billDueCollection')}}" class="btn-custom yellow">
                 <i class="bx bx-rotate-left"></i>
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.billDueCollection')}}">
            <div class="row">
                <div class="col-md-5 mb-1">
                    <div class="input-group">
                        <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                        <input type="date" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                    </div>
                </div>
                <div class="col-md-2 mb-1">
                    <select name="due_type" class="form-control" >
                        <option value="">All Due</option>
                        <option value="over" {{request()->due_type=='over'?'selected':''}} >Over Due</option>
                        <option value="next" {{request()->due_type=='next'?'selected':''}} >Next Due</option>
                    </select>
                </div>
                <div class="col-md-5 mb-1">
                    <div class="input-group">
                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Invoice, billing Name" class="form-control {{$errors->has('search')?'error':''}}" />
                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="{{route('admin.billDueCollection')}}">
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-8">
                    <ul class="statuslist mb-0">
                        <li><a href="{{route('admin.billDueCollection',['status'=>'all'])}}">All ({{$billcollections->total()}})</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="min-width: 100px;width: 100px;padding-right:0;">
                                SL
                            </th>
                            <th style="min-width: 100px;">Inv No</th>
                            <th style="min-width: 150px;">Billing</th>
                            <th style="min-width: 150px;">Title</th>
                            <th style="min-width: 100px;">Total</th>
                            <th style="min-width: 100px;">Due Date</th>
                            <th style="min-width: 100px;width:100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($billcollections as $i=>$invoice)
                        <tr>
                            <td>
                                <span style="margin:0 5px;">{{$billcollections->currentpage()==1?$i+1:$i+($billcollections->perpage()*($billcollections->currentpage() - 1))+1}}</span>
                            </td>
                            <td>
                                <a href="{{route('admin.billCollectionAction',['edit',$invoice->sale->id])}}" target="_blank">{{$invoice->sale->invoice}}</a>
                            </td>
                            <td>
                                @if($invoice->company)
                                    <a href="{{route('admin.companiesAction',['sales',$invoice->company->id])}}" >{{$invoice->billing_name}}</a>
                                @else
                                    {{$invoice->billing_name}}
                                @endif
                            </td>
                            <td>
                                @if($invoice->sale->emi_status)
                                    @if(str_contains($invoice->billing_reason, 'Installment'))
                                        EMI Installment
                                    @else
                                        EMI Down payment
                                    @endif
                                @else
                                {{$invoice->billing_reason}}
                                @endif
                            </td>
                            <td>{{$invoice->currency}} {{number_format($invoice->amount,2)}}</td>
                            <td>
                                @if($invoice->created_at->isPast())
                                    <span class="text-danger">{{ $invoice->created_at->format('d M Y') }}</span>
                                @else
                                    <span>{{ $invoice->created_at->format('d M Y') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('admin.billCollectionAction',['edit',$invoice->sale->id])}}" class="btn-custom"><i class="bx bx-edit"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>BDT {{number_format($billcollections->sum('amount'),2)}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                {{$billcollections->links('pagination')}}
            </div>
        </form>
    </div>
</div>
</div>

@endsection
@push('js')
@endpush
