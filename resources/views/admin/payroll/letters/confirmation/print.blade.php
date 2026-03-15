@extends('layouts.print')

@section('title', 'Confirmation Letter')

@section('contents')
<div class="container">
    <!-- Company Header -->
    <div class="text-center mb-30">
        <h2>{{ general()->company_name }}</h2>
        <p>{{ general()->address }}</p>
    </div>

    <!-- Date -->
    <div class="text-right mb-30">
        <p>Date: {{ $letter->letter_date->format('d F Y') }}</p>
    </div>

    <!-- Employee Address -->
    <div class="mb-20">
        <p><strong>To,</strong></p>
        <p>{{ $letter->user->name }}</p>
        <p>Employee ID: {{ $letter->user->employee_id }}</p>
    </div>

    <!-- Subject -->
    <div class="text-center mb-30">
        <h3>Confirmation Letter</h3>
    </div>

    <!-- Body -->
    <p>Dear {{ $letter->user->name }},</p>
    <br>
    <p>We are pleased to inform you that based on your performance during the probation period, you have been <strong>confirmed</strong> as a regular employee of the organization effective from <strong>{{ $letter->confirmation_date->format('d F Y') }}</strong>.</p>

    @if($letter->performance_remarks)
    <p><strong>Performance Remarks:</strong></p>
    <p>{!! nl2br($letter->performance_remarks) !!}</p>
    @endif

    @if($letter->remarks)
    <p><strong>Additional Remarks:</strong></p>
    <p>{!! nl2br($letter->remarks) !!}</p>
    @endif

    <div class="mt-30">
        <p>Congratulations on your confirmation and we look forward to your continued contributions to the organization.</p>
    </div>

    <!-- Signature Area -->
    <div class="signature-area">
        <div class="signature-box">
            <div class="signature-line">Employee Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Authorized Signature</div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print text-center mt-30">
        <button onclick="window.print()" style="padding: 10px 30px; background: #007bff; color: white; border: none; cursor: pointer;">Print Letter</button>
    </div>
</div>
@endsection
