{{--
    Bell 🔔 "send reminder" button for anything that goes through the central
    approval system. Renders only while the record's approval is still pending,
    so it disappears by itself once it is approved or rejected.

    Usage:  <x-approval-remind :model="$expense" />      (any approvable model)
            <x-approval-remind :approval="$approval" />  (an Approval row itself)
--}}
@props(['model' => null, 'approval' => null])
@php
    $approval ??= $model
        ? \App\Models\Approval::query()->pending()
            ->where('approvable_type', get_class($model))
            ->where('approvable_id', $model->getKey())
            ->latest('id')->first()
        : null;
    $reminders = $approval?->meta['reminders'] ?? null;
    $tip = 'Send approval reminder email'
        . ($reminders ? " — sent {$reminders['count']} time(s), last " . \Carbon\Carbon::parse($reminders['last_at'])->format('d M h:i A') . ($reminders['last_by'] ? " by {$reminders['last_by']}" : '') : '');
@endphp
@if($approval && $approval->isPending())
    <form method="POST" action="{{ route('admin.approvals.remind', $approval) }}" class="d-inline"
          onsubmit="return confirm('Send the approval email to the approvers again?');">
        @csrf
        <button type="submit" {{ $attributes->merge(['class' => 'btn btn-sm btn-outline-warning position-relative', 'style' => 'padding:2px 7px;']) }} title="{{ $tip }}">
            <i class="fa-solid fa-bell"></i>
            @if($reminders)
                <span class="badge badge-warning" style="font-size:9px;position:absolute;top:-6px;right:-6px;">{{ $reminders['count'] }}</span>
            @endif
        </button>
    </form>
@endif
