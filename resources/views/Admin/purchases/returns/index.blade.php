@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Damage/Return List') }}</title>
@endsection

@section('contents')
    <div class="flex-grow-1">
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Damage/Return List</h3>

                <div class="dropdown d-flex">
                    <a href="javascript:void(0)" class="btn-custom primary mr-1" style="padding:5px 15px;" data-toggle="modal" data-target="#createReceiveModal"  >
                        <i class="fa fa-plus"></i> Add Retun
                    </a>
                    <a href="{{route('admin.purchasesDamageReturn')}}" class="btn-custom yellow">
                        <i class="bx bx-rotate-left"></i>
                    </a>

                </div>
            </div>


            <div class="card-body">
                @include(adminTheme().'alerts')

                <!-- Search Form -->
                <form action="#">
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <div class="input-group">
                                <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control" />
                                <input type="date" name="endDate" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-6 mb-1">
                            <div class="input-group">
                                <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Material" class="form-control" />
                                <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
                <br>
                @include(adminTheme().'purchases.returns.loading')

            </div>
        </div>
    </div>



@endsection


