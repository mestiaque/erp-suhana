@php
$advisingBank =
    'Beneficiary Bank :' . "\n" .
    'MODHUMOTI BANK PLC' . "\n" .
    'Uttara Branch' . "\n" .
    'Siaam Tower (Level-3)' . "\n" .
    'Plot : 15, Road : 02, Sector : 03' . "\n" .
    'Uttara, Dhaka-1230' . "\n" .
    'Bangladesh' . "\n" .
    'SWIFT CODE : MODHBDDHUT' . "\n" .
    'A/C NO. : 111011100000878';

@endphp


<div class="col-md-4 mb-3">
    <label>Proforma Invoice No</label>
    <input type="text" class="form-control " value="{{ $pi->pi_no ?? '' }}" placeholder="Proforma Invoice No" name="pi_no" >
</div>

<div class="col-md-4 mb-3">
    <label>Proforma Invoice Date</label>
    <input type="date" class="form-control " value="{{ $pi->created_at->format('Y-m-d') ?? '' }}" placeholder="Proforma Invoice No" name="created_at">
</div>

<div class="col-md-4 mb-3">
    <label>Order Date</label>
    <input type="date" class="form-control " value="{{ $pi?->order_date?->format('Y-m-d') ?? '' }}" placeholder="Proforma Invoice No" name="order_date">
</div>

<div class="col-md-4 mb-3">
    <label>Remarks</label>
    {{-- <input type="text" name="remarks" class="form-control remarks" value="{{ $pi->remarks ?? '' }}"> --}}
    <textarea name="remarks" class="form-control remarks" rows="1" placeholder="Remarks">{{ $pi->remarks ?? '' }}</textarea>
</div>

<div class="col-md-4 mb-3">
    <label>Created By*</label>
    <input type="text" readonly class="form-control" value="{{ $pi->user?->name ?? '' }}">
</div>

<div class="col-md-4 mb-3">
    <label>Status</label>
    <select name="status" class="form-control" required>
        <option value="pending" {{ $pi->status=='pending'?'selected':'' }}>Pending</option>
        <option value="confirmed" {{ $pi->status=='confirmed'?'selected':'' }}>Confirmed</option>
        <option value="approved" {{ $pi->status=='approved'?'selected':'' }}>Approved</option>
        <option value="cancel" {{ $pi->status=='cancel'?'selected':'' }}>Cancel</option>
    </select>
</div>

<div class="col-md-12 mb-3 d-none">

    <label>Advising Bank</label>
    <textarea name="advising_bank" class="form-control advising_bank" rows="9" placeholder="Advising Bank readonly">{{ $pi->advising_bank ?? $advisingBank }}</textarea>
</div>

<div class="col-md-6 mb-3">
    <label>Applicant</label>
    <textarea name="applicant" class="form-control" rows="3" placeholder="Applicant">{{ $pi->applicant ?? '' }}</textarea>
</div>

<div class="col-md-6 mb-3">
    <label>Applicant Bank</label>
    <textarea name="applicant_bank" class="form-control" rows="3" placeholder="Applicant Bank">{{ $pi->applicant_bank ?? '' }}</textarea>
</div>

<div class="col-md-6 mb-3">
    <label>1st Beneficiary</label>
    <textarea name="first_beneficiary" class="form-control" rows="3" placeholder="1st Beneficiary">{{ $pi->first_beneficiary ?? '' }}</textarea>
</div>

<div class="col-md-6 mb-3">
    <label>1st Beneficiary Bank</label>
    <textarea name="first_beneficiary_bank" class="form-control" rows="3" placeholder="1st Beneficiary Bank">{{ $pi->first_beneficiary_bank ?? '' }}</textarea>
</div>

<div class="col-md-6 mb-3">
    <label>2nd Beneficiary</label>
    <textarea name="second_beneficiary" class="form-control" rows="3" placeholder="2nd Beneficiary">{{ $pi->second_beneficiary ?? '' }}</textarea>
</div>

<div class="col-md-6 mb-3">
    <label>2nd Beneficiary Bank</label>
    <textarea name="second_beneficiary_bank" class="form-control" rows="3" placeholder="2nd Beneficiary Bank">{{ $pi->second_beneficiary_bank ?? '' }}</textarea>
</div>

<div class="col-md-6 mb-3">
    <label>Notify Party</label>
    <textarea name="notify_party" class="form-control" rows="3" placeholder="Notify Party">{{ $pi->notify_party ?? '' }}</textarea>
</div>
