@extends('app')
@section('title')
<title>{{websiteTitle('Page Not Found')}}</title>
@endsection

@push('css')
<style type="text/css">
    .errorPage {
        text-align: center;
        padding: 20% 0;
    }
    .errorPage h1{
        font-size: 120px;
        color: #f1594c;
    }
    .btn-Success {
        background: #009688;
        color: white;
    }
</style>
@endpush
@section('contents')


<div class="not-authorized-area">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="not-authorized-content">
                <a href="{{route('index')}}" class="logo" style="max-width: 200px;">
                    <img src="{{asset(general()->logo())}}" alt="{{general()->title}}">
                </a>
                <h2>404</h2>
                <p>Oppos! Not Found.</p>
                <a href="{{route('index')}}" class="default-btn">Back to Software</a>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')

@endpush