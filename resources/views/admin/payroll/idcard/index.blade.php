@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('ID Card') }}</title>
@endsection

@push('css')
<style>
    /* A4 Page Setup - Portrait */
    @page {
        size: A4 portrait;
        margin: 3mm;
    }
    
    @media print {
        .no-print { display: none !important; }
        body { 
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
        }
        .a4-page {
            page-break-after: always;
            page-break-inside: avoid;
        }
    }
    
    /* ID Card Size: Portrait - 53.98mm x 85.6mm (Credit Card Size) */
    .id-card {
        width: 53.98mm;
        height: 85.6mm;
        background: #fff;
        border: 1px solid #ddd;
        padding: 4mm;
        box-sizing: border-box;
        display: inline-block;
        margin: 1mm;
        page-break-inside: avoid;
        vertical-align: top;
        position: relative;
    }
    
    .card-header-text {
        font-size: 8px;
        font-weight: bold;
        margin-bottom: 2mm;
        text-align: center;
        color: #1e3c72;
    }
    
    .card-id-title { 
        font-size: 7px; 
        text-align: center; 
        margin: 2mm 10mm; 
        border: 1px solid #ddd; 
        padding: 1px; 
    }
    
    .card-divider { 
        margin-top: 2px; 
        border-bottom: 1px solid #eee; 
    }
    
    .card-profile-pic { 
        width: 28mm; 
        height: 32mm; 
        margin: 0 auto; 
        display: block; 
        border: 1px solid #ddd; 
        object-fit: cover;
        margin-bottom: 2mm;
    }
    
    .card-details-table { 
        width: 100%; 
        border-collapse: collapse; 
        font-size: 6px; 
    }
    
    .card-details-table td { 
        padding: 0.5px 0px; 
    }
    
    .card-details-label { 
        font-weight: 500; 
        color: #555; 
    }
    
    .card-details-value { 
        text-align: right; 
        font-weight: bold;
    }
    
    .card-signature-row { 
        text-align: center; 
        position: absolute; 
        bottom: 2mm; 
        left: 50%; 
        transform: translateX(-50%); 
        white-space: nowrap; 
        gap: 8px; 
        display: flex; 
        justify-content: space-between;  
        width: 90%;
    }
    
    .card-signature-box { 
        width: 40%; 
        text-align: center; 
    }
    
    .card-signature-line { 
        border-top: 1px solid #000; 
        margin-bottom: 0px; 
    }
    
    .card-signature-text { 
        font-size: 4px; 
    }
    
    .card-footer-logo { 
        font-size: 5px; 
        text-align: center; 
        position: absolute; 
        bottom: 1mm; 
        left: 50%; 
        transform: translateX(-50%); 
        white-space: nowrap; 
    }
    
    .card-contact-row { font-size: 6px; margin-bottom: 1px; color: #1e3c72; text-align:center;font-weight: bold;}
    .card-contact-row2 { font-size: 5px; margin-bottom: 1px; margin-top: 1px; color:black; text-align:center;}
    .card-warning-text { font-size: 5px; font-style: italic; margin-top: 2mm; border-top: 1px solid #eee; padding-top: 2px; text-align:center; }
    
    /* A4 Grid: 4 columns x 4 rows = 16 cards per page (portrait), each card has front + back */
    .a4-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        width: 100%;
    }
    
    .a4-page {
        width: 100%;
        min-height: 280mm;
        padding: 2mm;
        box-sizing: border-box;
    }
    
    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        max-height: 300px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #ddd;
    }
    
    .checkbox-item {
        display: flex;
        align-items: center;
        padding: 5px;
    }
    
    .checkbox-item input {
        margin-right: 8px;
    }
    
    .checkbox-item label {
        margin-bottom: 0;
        cursor: pointer;
        font-size: 13px;
    }
    
    .select-all {
        margin-bottom: 10px;
        padding: 10px;
        background: #f5f5f5;
        border-radius: 5px;
    }
    
    .btn-group {
        display: inline-flex;
        gap: 10px;
    }
</style>
@endpush

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1 no-print">

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">ID Card Generator</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.idcard.index') }}" id="idCardForm">
                <div class="select-all">
                    <div class="checkbox-item">
                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                        <label for="selectAll"><strong>Select All / Deselect All</strong></label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Select Employees (Check to select):</label>
                    <div class="checkbox-grid">
                        @foreach($users as $user)
                            <div class="checkbox-item">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" 
                                    {{ in_array($user->id, request()->user_ids ?? []) ? 'checked' : '' }}
                                    id="user_{{ $user->id }}">
                                <label for="user_{{ $user->id }}">{{ $user->name }} [{{ $user->employee_id ?? $user->id }}]</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-eye"></i> Preview
                    </button>
                    @if(count($selectedUsers) > 0)
                    <a href="{{ route('admin.idcard.print', ['user_ids' => request()->user_ids]) }}" class="btn btn-success" target="_blank">
                        <i class="fa fa-print"></i> Print ({{ count($selectedUsers) }})
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAll(source) {
    const checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}
</script>

@if(count($selectedUsers) > 0)
@php
$cardsPerPage = 16; // 4 columns x 4 rows (portrait)
$totalCards = count($selectedUsers);
$totalPages = ceil($totalCards / $cardsPerPage);
$companyName = general()->title ?? 'Company';
$companyAddress = general()->address_one ?? 'Address';
@endphp

@for($page = 0; $page < $totalPages; $page++)
<div class="a4-page">
    @if($page > 0)
    <div class="no-print" style="text-align:center;padding:5px;background:#f5f5f5;margin-bottom:5px;">
        Page {{ $page + 1 }} of {{ $totalPages }}
    </div>
    @endif
    
    <div class="a4-grid">
        @php
        $start = $page * $cardsPerPage;
        $end = min($start + $cardsPerPage, $totalCards);
        @endphp
        
        @for($i = $start; $i < $end; $i++)
        @php $user = $selectedUsers[$i]; @endphp
        <div class="card-container">
            <!-- Front Side -->
            <div class="id-card">
                <div class="card-header-text">{{ $companyName }}</div>
                <div class="card-id-title">ID CARD</div>
            
                <div class="card-profile-pic">
                    @php $userImage = $user->image(); @endphp
                    @if($userImage)
                    <img src="{{ asset($userImage) }}" alt="Profile Picture" style="width:100%;height:100%;object-fit:cover;">
                    @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#ddd;color:#666;font-size:5pt;">Photo</div>
                    @endif
                </div>
            
                <div class="card-divider"></div>
            
                <table class="card-details-table">
                    <tr>
                        <td class="card-details-label">Name</td><td>:</td>
                        <td class="card-details-value">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="card-details-label">Designation</td><td>:</td>
                        <td class="card-details-value">{{ optional($user->designation)->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="card-details-label">ID No</td><td>:</td>
                        <td class="card-details-value">{{ $user->employee_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="card-details-label">Department</td><td>:</td>
                        <td class="card-details-value">{{ optional($user->department)->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="card-details-label">Joined</td><td>:</td>
                        <td class="card-details-value">{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="card-details-label">Issued</td><td>:</td>
                        <td class="card-details-value">{{ now()->format('d/m/Y') }}</td>
                    </tr>
                </table>
                
                <div class="card-signature-row">
                    <div class="card-signature-box">
                        <div class="card-signature-line"></div>
                        <div class="card-signature-text">Holder Signature</div>
                    </div>
                    <div class="card-signature-box">
                        <div class="card-signature-line"></div>
                        <div class="card-signature-text">Authorized</div>
                    </div>
                </div>
            </div>
            
            <!-- Back Side -->
            <div class="id-card">
                <table class="card-details-table">
                    <tr>
                        <td class="card-details-label">Validity</td><td>:</td>
                        <td class="card-details-value">{{ $user->employment_status ?? 'Until Employment Ends' }}</td>
                    </tr>
                    <tr>
                        <td class="card-details-label">Blood Group</td><td>:</td>
                        <td class="card-details-value">{{ $user->blood_group ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="card-details-label">Emergency</td><td>:</td>
                        <td class="card-details-value">{{ $user->emergency_mobile ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="card-details-label">NID / Birth</td><td>:</td>
                        <td class="card-details-value">{{ $user->nid_number ?? '-' }}</td>
                    </tr>
                </table>
            
                <div class="card-divider" style="margin-bottom:2px;"></div>
            
                <div class="card-contact-row">{{ $companyName }}</div>
                <div class="card-contact-row2">{{ $companyAddress }}</div>
                <div class="card-contact-row2">{{ general()->phone ?? '' }}</div>
            
                <div class="card-warning-text">
                    If lost, inform management immediately.<br>
                    This card is issued under Bangladesh Labor Act 2015, Form 6.
                </div>
            
                <div class="card-footer-logo">
                    {{ $companyName }}
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
@endfor

@endif
@endsection
