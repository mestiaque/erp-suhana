@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Budget Form') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Budget Form</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.budget') }}">Budget</a></li>
            <li class="item">Add Budget</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            @if(isset($budget))
                <form action="{{ route('admin.budgetAction', ['update', $budget->id]) }}" method="post" enctype="multipart/form-data" class="">
            @else
                <form action="{{ route('admin.budgetAction', ['store']) }}" method="post" enctype="multipart/form-data" class="">
            @endif
                @csrf

                @include(adminTheme().'merchandising.budget.include.header')

                @include(adminTheme().'merchandising.budget.include.yarn')

                @include(adminTheme().'merchandising.budget.include.knitting')

                @include(adminTheme().'merchandising.budget.include.dyeing')

                @include(adminTheme().'merchandising.budget.include.accessories')

                @include(adminTheme().'merchandising.budget.include.print')

                @include(adminTheme().'merchandising.budget.include.cm')

                @include(adminTheme().'merchandising.budget.include.test')

                @include(adminTheme().'merchandising.budget.include.summary')

                @include(adminTheme().'merchandising.budget.include.production')

                {{-- Submit --}}
                <div class="row mb-3">
                    <div class="col-lg-12 text-center">
                        <button type="submit" class="btn btn-success" styles="position: fixed; bottom:4rem">{{ isset($budget) ? 'Update' : 'Save' }} Budget</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>



@endsection

@include(adminTheme().'merchandising.budget.include.script')

@push('css')
 <style>
    .headerTable td, .headerTable th{
        vertical-align: middle !important;
    }
    .headerTable td{
        padding: 0px !important;
    }
    td{
        vertical-align: middle !important;
    }
    .vm{
        vertical-align: middle !important;
    }
</style>
@endpush
