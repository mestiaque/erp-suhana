@if($canInfo)
    <p><b>Disabled By:</b> {{ $canInfo->get('can_pay_by', 'N/A') }}</p>
    <p><b>Disabled At:</b> {{ $canInfo->get('can_pay_at') ? \Carbon\Carbon::parse($canInfo->get('can_pay_at'))->format('d-m-Y H:i') : 'N/A' }}</p>
    <p><b>Reason:</b> {{ $canInfo->get('can_pay_note', 'N/A') }}</p>
@else
    <p>No Information available</p>
@endif
