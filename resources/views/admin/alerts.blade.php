
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

@if(session('errors'))
<div class="alert alert-danger alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <strong>Oops! </strong> Need to Validation.
</div>
@endif