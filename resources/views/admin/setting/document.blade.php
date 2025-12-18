@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Apps Documents')}}</title>
@endsection

@push('css')
<style type="text/css">

</style>
@endpush
@section('contents')
<!-- Breadcrumb Area -->
<div class="breadcrumb-area">
    <h1>Documents</h1>
    <ol class="breadcrumb">
        <li class="item">
            <a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="item">Apps Documents</li>
    </ol>
</div>
<div class="flex-grow-1">
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Apps Documents</h3>
        </div>
        <div class="card-body">
            <h1>Apps Documents white...</h1>
        </div>
    </div>
</div>
@endsection
@push('js')

@endpush