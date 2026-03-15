@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Leaves List') }}</title>
@endsection

@section('contents')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Joining Letter</h4>
                <div class="btn-group">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="feather icon-printer"></i> Print
                    </button>
                    <a href="{{ route('admin.letters.joining.index') }}" class="btn btn-secondary">
                        <i class="feather icon-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="letter-container" style="max-width: 800px; margin: 0 auto; padding: 40px; border: 1px solid #ddd;">
                        <div style="text-align: center; margin-bottom: 30px;">
                            <h2>{{ general()->company_name }}</h2>
                            <p>{{ general()->address }}</p>
                        </div>

                        <div style="text-align: right; margin-bottom: 30px;">
                            <p>Date: {{ $letter->letter_date->format('d F Y') }}</p>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <p><strong>To,</strong></p>
                            <p>{{ $letter->user->name }}</p>
                            <p>Employee ID: {{ $letter->user->employee_id }}</p>
                        </div>

                        <div style="text-align: center; margin: 30px 0;">
                            <h3>Joining Letter</h3>
                        </div>

                        <p>Dear {{ $letter->user->name }},</p>

                        <p>We are pleased to confirm that you have successfully all completed joining formalities and have joined as <strong>{{ $letter->designation ?? 'N/A' }}</strong>
                        @if($letter->department)
                        in the <strong>{{ $letter->department }}</strong> department
                        @endif
                        effective from <strong>{{ $letter->joining_date->format('d F Y') }}</strong>.</p>

                        @if($letter->remarks)
                        <p><strong>Remarks:</strong></p>
                        <p>{!! nl2br($letter->remarks) !!}</p>
                        @endif

                        <div style="margin-top: 50px;">
                            <p>We welcome you to the organization and wish you all the best in your new role.</p>
                        </div>

                        <div style="margin-top: 60px; display: flex; justify-content: space-between;">
                            <div>
                                <p>_________________________</p>
                                <p>Employee Signature</p>
                            </div>
                            <div>
                                <p>_________________________</p>
                                <p>Authorized Signature</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .card-header, .btn-group {
        display: none !important;
    }
    .letter-container {
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection
