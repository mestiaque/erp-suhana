@extends('printMaster')

@section('title', 'Employee Information Sheet')

@push('css')
<style>
    .title {
        font-size: 20px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 8px;
    }

    .sub-title {
        font-size: 13px;
        text-align: center;
        margin-bottom: 16px;
    }

    .section {
        margin-bottom: 14px;
    }

    .section h4 {
        margin: 0 0 6px 0;
        font-size: 14px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 4px;
    }

    .label {
        width: 34%;
        font-weight: 600;
        background: #f8f9fa;
    }

    .value {
        width: 66%;
    }

    .head-table td {
        border: none !important;
        padding: 2px 6px;
    }

    .photo {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border: 1px solid #ddd;
    }
    @media print {
        .page-break-avoid {
            page-break-inside: avoid;
        }
    }
</style>
@endpush

@section('contents')

<table class="head-table" style="margin-bottom: 10px;">
    <tr>
        <td style="width:80%;">
            <p><strong>Name:</strong> {{ $user->name ?? 'N/A' }}</p>
            <p><strong>Employee ID:</strong> {{ $user->employee_id ?? 'N/A' }}</p>
            <p><strong>Designation:</strong> {{ $user->designation?->name ?? 'N/A' }}</p>
            <p><strong>Department:</strong> {{ $user->department?->name ?? 'N/A' }}</p>
        </td>
        <td style="width:20%; text-align:right; vertical-align:top;">
            <img src="{{ asset($user->image()) }}" class="photo" alt="Employee Photo">
        </td>
    </tr>
</table>

<div class="section">
    <h4>Basic Information</h4>
    <table>
        <tr><td class="label">Employee ID</td><td class="value">{{ $user->employee_id ?? 'N/A' }}</td></tr>
        <tr><td class="label">Employee Name</td><td class="value">{{ $user->name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Name (Bangla)</td><td class="value">{{ $user->bn_name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Gender</td><td class="value">{{ $user->gender ?? 'N/A' }}</td></tr>
        <tr><td class="label">Date of Birth</td><td class="value">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d M Y') : 'N/A' }}</td></tr>
        <tr><td class="label">Marital Status</td><td class="value">{{ $user->marital_status ? ucfirst($user->marital_status) : 'N/A' }}</td></tr>
        <tr><td class="label">Blood Group</td><td class="value">{{ $user->blood_group ?? 'N/A' }}</td></tr>
        <tr><td class="label">Religion</td><td class="value">{{ $user->religion ?? 'N/A' }}</td></tr>
        <tr><td class="label">Mobile Number</td><td class="value">{{ $user->mobile ?? 'N/A' }}</td></tr>
        <tr><td class="label">Email</td><td class="value">{{ $user->email ?? 'N/A' }}</td></tr>
        <tr><td class="label">Nationality</td><td class="value">{{ $user->nationality ?? 'N/A' }}</td></tr>
        <tr><td class="label">Home District</td><td class="value">{{ $user->home_district ?? 'N/A' }}</td></tr>
        <tr><td class="label">Report To</td><td class="value">{{ $user->report_to ?? 'N/A' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Family and Personal Details</h4>
    <table>
        <tr><td class="label">Father Name</td><td class="value">{{ $user->father_name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Father Name (Bangla)</td><td class="value">{{ $user->father_name_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">Mother Name</td><td class="value">{{ $user->mother_name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Mother Name (Bangla)</td><td class="value">{{ $user->mother_name_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">Spouse Name</td><td class="value">{{ $user->spouse_name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Spouse Name (Bangla)</td><td class="value">{{ $user->spouse_name_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">No of Boys</td><td class="value">{{ $user->boys ?? '0' }}</td></tr>
        <tr><td class="label">No of Girls</td><td class="value">{{ $user->girls ?? '0' }}</td></tr>
        <tr><td class="label">Height (Cm)</td><td class="value">{{ $user->height ?? 'N/A' }}</td></tr>
        <tr><td class="label">Weight (Kg)</td><td class="value">{{ $user->weight ?? 'N/A' }}</td></tr>
        <tr><td class="label">Identification Mark</td><td class="value">{{ $user->distinguished_mark ?? 'N/A' }}</td></tr>
        <tr><td class="label">Identification Mark (Bangla)</td><td class="value">{{ $user->distinguished_mark_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">Education</td><td class="value">{{ $user->education ?? 'N/A' }}</td></tr>
        <tr><td class="label">Type of Work</td><td class="value">{{ $user->work_type ?? 'N/A' }}</td></tr>
        <tr><td class="label">NID Number</td><td class="value">{{ $user->nid_number ?? 'N/A' }}</td></tr>
        <tr><td class="label">Birth Registration</td><td class="value">{{ $user->birth_registration ?? 'N/A' }}</td></tr>
        <tr><td class="label">Passport No</td><td class="value">{{ $user->passport_no ?? 'N/A' }}</td></tr>
        <tr><td class="label">Driving License</td><td class="value">{{ $user->driving_license ?? 'N/A' }}</td></tr>
        <tr><td class="label">e-TIN</td><td class="value">{{ $user->etin ?? 'N/A' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Job Information</h4>
    <table>
        <tr><td class="label">Designation</td><td class="value">{{ $user->designation?->name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Division</td><td class="value">{{ $user->divisions?->name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Department</td><td class="value">{{ $user->department?->name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Section</td><td class="value">{{ $user->section?->name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Line Number</td><td class="value">{{ $user->line?->name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Shift</td><td class="value">{{ $user->shift?->name_of_shift ?? 'N/A' }}</td></tr>
        <tr><td class="label">Employee Type</td><td class="value">{{ $user->employeeType?->name ?? 'N/A' }}</td></tr>
        <tr><td class="label">Placement/Location</td><td class="value">{{ $user->location ?? 'N/A' }}</td></tr>
        <tr><td class="label">Joining Date</td><td class="value">{{ $user->joining_date ? $user->joining_date->format('d M Y') : 'N/A' }}</td></tr>
        <tr><td class="label">Employment Status</td><td class="value">{{ $user->employment_status ?? 'N/A' }}</td></tr>
        <tr><td class="label">User Status</td><td class="value">{{ $user->status ? 'Active' : 'Inactive' }}</td></tr>
        <tr><td class="label">Role</td><td class="value">{{ $user->permission?->name ?? 'N/A' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Present Address</h4>
    <table>
        <tr><td class="label">Village Name</td><td class="value">{{ $user->present_village ?? 'N/A' }}</td></tr>
        <tr><td class="label">Village Name (Bangla)</td><td class="value">{{ $user->present_village_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">Post Office</td><td class="value">{{ $user->present_post_office ?? 'N/A' }}</td></tr>
        <tr><td class="label">Post Office (Bangla)</td><td class="value">{{ $user->present_post_office_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">Upazila/Police Station</td><td class="value">{{ $user->present_upazila ?? 'N/A' }}</td></tr>
        <tr><td class="label">Upazila/Police Station (Bangla)</td><td class="value">{{ $user->present_upazila_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">District</td><td class="value">{{ $user->present_district ?? 'N/A' }}</td></tr>
        <tr><td class="label">District (Bangla)</td><td class="value">{{ $user->present_district_bn ?? 'N/A' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Permanent Address</h4>
    <table>
        <tr><td class="label">Village Name</td><td class="value">{{ $user->permanent_village ?? 'N/A' }}</td></tr>
        <tr><td class="label">Village Name (Bangla)</td><td class="value">{{ $user->permanent_village_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">Post Office</td><td class="value">{{ $user->permanent_post_office ?? 'N/A' }}</td></tr>
        <tr><td class="label">Post Office (Bangla)</td><td class="value">{{ $user->permanent_post_office_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">Upazila/Police Station</td><td class="value">{{ $user->permanent_upazila ?? 'N/A' }}</td></tr>
        <tr><td class="label">Upazila/Police Station (Bangla)</td><td class="value">{{ $user->permanent_upazila_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">District</td><td class="value">{{ $user->permanent_district ?? 'N/A' }}</td></tr>
        <tr><td class="label">District (Bangla)</td><td class="value">{{ $user->permanent_district_bn ?? 'N/A' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Emergency Contact</h4>
    <table>
        <tr><td class="label">Emergency Contact Number</td><td class="value">{{ $user->emergency_mobile ?? 'N/A' }}</td></tr>
        <tr><td class="label">Emergency Contact Relation</td><td class="value">{{ $user->emergency_relation ?? 'N/A' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Nominee and References</h4>
    <table>
        <tr><td class="label">Nominee</td><td class="value">{{ $user->nominee ?? 'N/A' }}</td></tr>
        <tr><td class="label">Nominee (Bangla)</td><td class="value">{{ $user->nominee_bn ?? 'N/A' }}</td></tr>
        <tr><td class="label">Nominee Relation</td><td class="value">{{ $user->nominee_relation ?? 'N/A' }}</td></tr>
        <tr><td class="label">Nominee Age</td><td class="value">{{ $user->nominee_age ?? 'N/A' }}</td></tr>
        <tr><td class="label">Reference - 1</td><td class="value">{{ $user->reference_1 ?? 'N/A' }}</td></tr>
        <tr><td class="label">Reference - 2</td><td class="value">{{ $user->reference_2 ?? 'N/A' }}</td></tr>
        <tr><td class="label">Other Information</td><td class="value">{{ $user->other_information ?? 'N/A' }}</td></tr>
    </table>
</div>

<div class="section">
    <h4>Salary Information</h4>
    <table>
        <tr><td class="label">Salary Type</td><td class="value">{{ $user->salary_type ?? 'N/A' }}</td></tr>
        <tr><td class="label">Gross Salary</td><td class="value">{{ $user->gross_salary ? number_format($user->gross_salary, 2) : 'N/A' }}</td></tr>
        <tr><td class="label">Basic Salary</td><td class="value">{{ $user->basic_salary ? number_format($user->basic_salary, 2) : 'N/A' }}</td></tr>
        <tr><td class="label">House Rent</td><td class="value">{{ $user->house_rent ? number_format($user->house_rent, 2) : 'N/A' }}</td></tr>
        <tr><td class="label">Medical Allowance</td><td class="value">{{ $user->medical_allowance ? number_format($user->medical_allowance, 2) : 'N/A' }}</td></tr>
        <tr><td class="label">Transport Allowance</td><td class="value">{{ $user->transport_allowance ? number_format($user->transport_allowance, 2) : 'N/A' }}</td></tr>
        <tr><td class="label">Food Allowance</td><td class="value">{{ $user->food_allowance ? number_format($user->food_allowance, 2) : 'N/A' }}</td></tr>
        <tr><td class="label">Attendance Bonus</td><td class="value">{{ isset($user->attendance_bonus) ? number_format($user->attendance_bonus, 2) : 'N/A' }}</td></tr>
        <tr><td class="label">Other Allowance</td><td class="value">{{ isset($user->other_allowance) ? number_format($user->other_allowance, 2) : 'N/A' }}</td></tr>
    </table>
</div>

<div class="section page-break-avoid">
    <h4>Attachment Documents</h4>
    <table>
        @forelse($user->galleryFiles as $file)
        <tr>
            <td class="value">{{ $file->file_name ?: 'Document' }}</td>
        </tr>
        @empty
        <tr>
            <td class="value">N/A</td>
        </tr>
        @endforelse
    </table>
</div>
@endsection



