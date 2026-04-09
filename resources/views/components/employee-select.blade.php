@props([
    'name'         => 'user_id',
    'id'           => null,
    'value'        => null,
    'selectedText' => null,
    'placeholder'  => 'Search Employee...',
    'required'     => false,
    'class'        => '',
    'multiple'     => false,
])

@php
    $selectId = $id ?: 'emp-select-' . uniqid();
@endphp

<select
    id="{{ $selectId }}"
    name="{{ $name }}"
    class="form-control employee-select {{ $class }}"
    @if($required) required @endif
    @if($multiple) multiple @endif
    style="width:100%;"
>
    <option value=""></option>
    @if($value && $selectedText)
        <option value="{{ $value }}" selected>{{ $selectedText }}</option>
    @elseif($value)
        <option value="{{ $value }}" selected>{{ $value }}</option>
    @endif
</select>

@push('js')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function () {
    $('#{{ $selectId }}').select2({
        placeholder: '{{ $placeholder }}',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: '{{ route('admin.ajax.employees') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term || '' };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        }
    });
})();
</script>
@endpush
