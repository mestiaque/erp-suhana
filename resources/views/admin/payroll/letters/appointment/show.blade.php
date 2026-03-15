@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Leaves List') }}</title>
@endsection

@section('contents')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Appointment Letter</h4>
                <div class="btn-group">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="feather icon-printer"></i> Print
                    </button>
                    <a href="{{ route('admin.letters.appointment.index') }}" class="btn btn-secondary">
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
                            <h3>Appointment Letter</h3>
                        </div>

                        <p>Dear {{ $letter->user->name }},</p>

                        <p>We are pleased to offer you the position of <strong>{{ $letter->position }}</strong>
                        @if($letter->department)
                        in the <strong>{{ $letter->department }}</strong> department
                        @endif
                        .</p>

                        <p><strong>Terms of Appointment:</strong></p>
                        <ul>
                            <li><strong>Salary:</strong> Tk. {{ number_format($letter->salary, 2) }} per month</li>
                            <li><strong>Joining Date:</strong> {{ $letter->joining_date->format('d F Y') }}</li>
                            @if($letter->department)
                            <li><strong>Department:</strong> {{ $letter->department }}</li>
                            @endif
                        </ul>

                        @if($letter->terms)
                        <p><strong>Terms & Conditions:</strong></p>
                        <p>{!! nl2br($letter->terms) !!}</p>
                        @endif

                        <div style="margin-top: 50px;">
                            <p>We are delighted to have you join our team and look forward to a long and successful association with you.</p>
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
