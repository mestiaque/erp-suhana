@extends('printMaster')
@section('title', 'Budget Details')
@section('contents')
        @include(adminTheme().'merchandising.budget.includeView.header')
        @include(adminTheme().'merchandising.budget.includeView.all')
        @include(adminTheme().'merchandising.budget.includeView.summary')
@endsection
@php
    $signatures = ['MANAGER (MM)', 'DIRECTOR', 'MANAGING DIRECTOR'];
@endphp
@push('js')
<script>
    var amount = Number($('#total_amount_input').val());
    console.log(amount);
    var words = toWords(amount);
    $('#total_amount_word').html(words + ' Taka Only');

</script>
@endpush

@push('css')
<style>
th {
    background: none !important;
}

#yarnTable thead th {
    background: #cfcfcf !important;
}
</style>
@endpush