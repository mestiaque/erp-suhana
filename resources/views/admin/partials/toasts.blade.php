{{--
    Flash messages (success / error / warning / info / status) and validation errors as toastr
    toasts, for every page on the admin layout. Messages are escaped here, so toastr renders
    them as HTML only for the <br> between validation errors.
--}}
<script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        newestOnTop: true,
        preventDuplicates: true,
        positionClass: 'toast-top-right',
        timeOut: 4000,
        extendedTimeOut: 2000,
    };

    @foreach(['success' => 'success', 'error' => 'error', 'warning' => 'warning', 'info' => 'info', 'status' => 'info'] as $flashKey => $toastType)
        @if(is_string(session($flashKey)) && trim(session($flashKey)) !== '')
            toastr.{{ $toastType }}(@json(e(session($flashKey))), null, @json($toastType === 'error' ? ['timeOut' => $errorTimeout ?? 4000, 'extendedTimeOut' => $errorTimeout ?? 2000] : (object) []));
        @endif
    @endforeach

    @if(isset($errors) && $errors->any())
        toastr.error(@json(collect($errors->all())->map(fn ($message) => e($message))->implode('<br>')), null, { timeOut: {{ (int) ($errorTimeout ?? 8000) }}, extendedTimeOut: {{ (int) ($errorTimeout ?? 2000) }} });
    @endif
</script>
