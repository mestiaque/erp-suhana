@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Leaves List') }}</title>
@endsection

@section('contents')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Salary Increment Letter</h4>
                <div class="btn-group">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="feather icon-printer"></i> Print
                    </button>
                    <a href="{{ route('admin.letters.increment.index') }}" class="btn btn-secondary">
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
                            <p>Date: {{ $increment->increment_date->format('d F Y') }}</p>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <p><strong>To,</strong></p>
                            <p>{{ $increment->user->name }}</p>
                            <p>Employee ID: {{ $increment->user->employee_id }}</p>
                        </div>

                        <div style="text-align: center; margin: 30px 0;">
                            <h3>Salary Increment Letter</h3>
                        </div>

                        <p>Dear {{ $increment->user->name }},</p>

                        <p>We are pleased to inform you that based on your performance and contribution to the organization, the management has approved a salary increment for you.</p>

                        <div style="margin: 30px 0; padding: 20px; background: #f5f5f5;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="padding: 10px;"><strong>Previous Salary:</strong></td>
                                    <td style="padding: 10px;">Tk. {{ number_format($increment->previous_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;"><strong>Increment Amount:</strong></td>
                                    <td style="padding: 10px;">Tk. {{ number_format($increment->increment_amount, 2) }} ({{ $increment->increment_percentage }}%)</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;"><strong>New Salary:</strong></td>
                                    <td style="padding: 10px;"><strong>Tk. {{ number_format($increment->new_salary, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px;"><strong>Effective From:</strong></td>
                                    <td style="padding: 10px;">{{ $increment->increment_date->format('d F Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        @if($increment->remarks)
                        <p><strong>Remarks:</strong></p>
                        <p>{!! nl2br($increment->remarks) !!}</p>
                        @endif

                        <div style="margin-top: 50px;">
                            <p>We appreciate your hard work and dedication and look forward to your continued contributions to the organization's success.</p>
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
