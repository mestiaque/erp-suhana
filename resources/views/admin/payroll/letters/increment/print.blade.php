@extends('layouts.print')

@section('title', 'Salary Increment Letter')

@section('contents')
<div class="container">
    <!-- Company Header -->
    <div class="text-center mb-30">
        <h2>{{ general()->company_name }}</h2>
        <p>{{ general()->address }}</p>
    </div>

    <!-- Date -->
    <div class="text-right mb-30">
        <p>Date: {{ $increment->increment_date->format('d F Y') }}</p>
    </div>

    <!-- Employee Address -->
    <div class="mb-20">
        <p><strong>To,</strong></p>
        <p>{{ $increment->user->name }}</p>
        <p>Employee ID: {{ $increment->user->employee_id }}</p>
    </div>

    <!-- Subject -->
    <div class="text-center mb-30">
        <h3>Salary Increment Letter</h3>
    </div>

    <!-- Body -->
    <p>Dear {{ $increment->user->name }},</p>
    <br>
    <p>We are pleased to inform you that based on your performance and contribution to the organization, the management has approved a salary increment for you.</p>

    <!-- Salary Details -->
    <div class="gray-bg mb-20">
        <table>
            <tr>
                <td><strong>Previous Salary:</strong></td>
                <td>Tk. {{ number_format($increment->previous_salary, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Increment Amount:</strong></td>
                <td>Tk. {{ number_format($increment->increment_amount, 2) }} ({{ $increment->increment_percentage }}%)</td>
            </tr>
            <tr>
                <td><strong>New Salary:</strong></td>
                <td><strong>Tk. {{ number_format($increment->new_salary, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Effective From:</strong></td>
                <td>{{ $increment->increment_date->format('d F Y') }}</td>
            </tr>
        </table>
    </div>

    @if($increment->remarks)
    <p><strong>Remarks:</strong></p>
    <p>{!! nl2br($increment->remarks) !!}</p>
    @endif

    <div class="mt-30">
        <p>We appreciate your hard work and dedication and look forward to your continued contributions to the organization's success.</p>
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
