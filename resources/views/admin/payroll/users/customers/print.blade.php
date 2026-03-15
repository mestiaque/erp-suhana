@extends('layouts.print')
@section('title')
    <title>Employee List - Print</title>
@endsection
@section('styles')
<style>
    .company-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .company-header h1 {
        font-size: 24px;
        margin-bottom: 5px;
    }
    .company-header p {
        font-size: 14px;
        color: #666;
    }
    .print-title {
        text-align: center;
        margin-bottom: 20px;
        padding: 10px;
        background: #f5f5f5;
    }
    .print-title h2 {
        margin: 0;
        font-size: 18px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    table th, table td {
        border: 1px solid #333;
        padding: 6px;
        text-align: left;
        font-size: 12px;
    }
    table th {
        background: #f5f5f5;
        font-weight: bold;
    }
    .employee-photo {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }
    .employee-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 14px;
    }
    .employee-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .print-date {
        text-align: right;
        margin-bottom: 15px;
        font-size: 11px;
        color: #666;
    }
    .signature-area {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        padding: 0 50px;
    }
    .signature-box {
        text-align: center;
        width: 180px;
    }
    .signature-line {
        border-top: 1px solid #333;
        margin-top: 35px;
        padding-top: 5px;
        font-size: 12px;
    }
    .page-break {
        page-break-after: always;
    }
    @media print {
        body { 
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 10px;
        }
    }
</style>
@endsection
@section('contents')
<div class="container">
    <div class="company-header">
        <h1>{{ general()->title ?? 'Company Name' }}</h1>
        <p>{{ general()->subtitle ?? '' }}</p>
    </div>
    
    <div class="print-title">
        <h2>Employee List</h2>
    </div>
    
    <div class="print-date">
        Print Date: {{ Carbon\Carbon::now()->format('d M Y, h:i A') }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">SL</th>
                <th style="width: 50px;">Photo</th>
                <th>Name</th>
                <th>ID</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Section</th>
                <th>Line</th>
                <th>Join Date</th>
                <th>Mobile</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $i => $user)
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td style="text-align: center;">
                    @if($user->photo)
                        <img src="{{ public_path('uploads/user_photo/' . $user->photo) }}" alt="{{ $user->name }}" class="employee-photo">
                    @else
                        <div class="employee-avatar" style="background-color: {{ random_color($user->id ?? 0) }};">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                </td>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->employee_id ?? 'N/A' }}</td>
                <td>{{ $user->designation->name ?? 'N/A' }}</td>
                <td>{{ $user->department->name ?? 'N/A' }}</td>
                <td>{{ $user->section->name ?? 'N/A' }}</td>
                <td>{{ $user->line->name ?? 'N/A' }}</td>
                <td>{{ $user->joining_date ? Carbon\Carbon::parse($user->joining_date)->format('d M Y') : 'N/A' }}</td>
                <td>{{ $user->mobile ?? 'N/A' }}</td>
                <td style="text-align: center;">
                    @if($user->status == 1)
                        <span>Active</span>
                    @else
                        <span>Inactive</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="signature-area">
        <div class="signature-box">
            <div class="signature-line">Authorized Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">HR Manager</div>
        </div>
    </div>
</div>
@endsection
