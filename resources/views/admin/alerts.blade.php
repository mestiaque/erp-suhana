
@if(Session::has('success'))
<div class="alert alert-success alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <strong>Success! </strong> {{Session::get('success') }}.
</div>
@endif


@if(session('error'))
<div class="alert alert-danger alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <strong>Oops! </strong> {{Session::get('error') }}.
</div>
@endif

@if(session('info'))
<div class="alert alert-info alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <strong>Info! </strong> {{Session::get('info') }}.
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <strong>Oops! </strong>
    <ul style="margin: 6px 0 0 18px; padding: 0;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif