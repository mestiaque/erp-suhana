@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Purchases Stock')}}</title>
@endsection

@push('css')
<style type="text/css">
    .brandSpan{
        display: inline-block;
        border: 1px solid #dddada;
        padding: 1px 15px;
        border-radius: 3px;
        margin: 1px 3px;
    }
</style>
@endpush

@section('contents')

<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Purchases Stock</h3>
             <div class="dropdown">
                 <a href="{{route('admin.purchasesStocks')}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')

            <!-- Search Form -->
            <form action="{{route('admin.purchasesStocks')}}">
                <div class="row">
                    <div class="col-md-3 mb-1">
                        <select class="form-control" name="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{$category->id}}" {{request()->category_id==$category->id?'selected':''}}>
                                    {{$category->name}}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-3 mb-1">
                        <select class="form-control" name="branch_id">
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{$branch->id}}" {{request()->branch_id==$branch->id?'selected':''}}>
                                    {{$branch->name}}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search name, Barcode, ID" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <br>

                <!-- Requisition Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="min-width: 100px;">SL</th>
                                <th style="min-width: 150px;">Item name</th>
                                <th style="min-width: 150px;">Stock</th>
                                <th style="min-width: 200px;">Branchs</th>
                                <th style="min-width: 150px;">Last Purchases</th>
                                <th style="min-width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach($goodsItems as $key=>$item)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $item->name }} </td>
                                    <td>{{numberFormat($item->materialStockQty(request()->branch_id),1) }}</td>
                                    <td>
                                        @foreach($item->materialStock()->where('quantity','>',0)->get() as $brh)
                                        <span class="brandSpan">{{$brh->branch?->name ?? N/A}} ({{numberFormat($brh->quantity,1)}})</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        {{$item->materialLastPurchase?$item->materialLastPurchase->order->created_at->format('d.m.Y'):'N/A'}}
                                    </td>
                                    <td>
                                        {{ucfirst($item->status)}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>

@endsection

@push('js')
@endpush

