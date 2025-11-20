@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Supplier Ladgers')}}</title>
@endsection

@push('css')
<style type="text/css">
    .showPassword {
    right: 0 !important;
    cursor: pointer;
    }
    .ProfileImage{
        max-width: 64px;
        max-height: 64px;
    }
</style>
@endpush
@section('contents')

<div class="flex-grow-1">
    <!-- Breadcrumb Area -->
    <div class="breadcrumb-area">
        <h1>Supplier Ladgers</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="item">Supplier Ladgers</li>
        </ol>
    </div>

    @include(adminTheme().'alerts')

    
</div>
@endsection
@push('js')



@endpush