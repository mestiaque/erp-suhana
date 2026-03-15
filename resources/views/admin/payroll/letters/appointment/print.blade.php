@extends('layouts.print')

@section('title', 'Appointment Letter')

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
        <h3>Appointment Letter</h3>
    </div>

    <!-- Body -->
    <p>Dear {{ $letter->user->name }},</p>
    <br>
    <p>We are pleased to offer you the position of <strong>{{ $letter->position }}</strong></p>

    @if($letter->department)
    <p>in the <strong>{{ $letter->department }}</strong> department.</p>
    @endif

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

    <div class="mt-30">
        <p>We are delighted to have you join our team and look forward to a long and successful association with you.</p>
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
