@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('My Profile')}}</title>
@endsection
@push('css')
<style type="text/css">
    .ProfileImage {
        width: 150px;
        height: 150px;
        min-width: 140px;
        min-height: 140px;
        border-radius: 50%;
        /* object-fit: fill; */
        border: 4px solid rgba(255,255,255,0.5);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 10px 40px;
        border-radius: 15px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }
    .profile-header h2 {
        font-size: 28px;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .profile-header p {
        font-size: 14px;
        opacity: 0.95;
    }
    .profile-header .badge {
        font-size: 12px;
        padding: 5px 12px;
    }
    .profile-header .btn-light {
        color: #667eea;
        font-weight: 600;
        border-radius: 25px;
        padding: 10px 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    .profile-header .btn-light:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        color: #764ba2;
    }
    .nav-tabs .nav-link.active {
        background-color: #667eea;
        color: white;
        border-color: #667eea;
    }
    .nav-tabs .nav-link {
        color: #333;
        font-weight: 500;
        border-radius: 8px 8px 0 0;
        margin-right: 3px;
    }
    .nav-tabs .nav-link:hover {
        border-color: #667eea;
        color: #667eea;
    }

    .nav-tabs .nav-link.active:hover {
        background-color: #ffffff;
        color: #667eea !important;
    }
    .info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 25px;
        margin-bottom: 20px;
        border: 1px solid #f0f0f0;
    }
    .info-label {
        font-weight: 600;
        color: #666;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        color: #333;
        font-size: 14px;
        margin-top: 2px;
        font-weight: 500;
    }
    .section-title {
        color: #667eea;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #667eea;
        font-size: 16px;
    }
    .tab-content {
        background: white;
        border-radius: 0 0 12px 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        border-top: none;
    }
    .nav-tabs {
        border-bottom: none;
        background: #f8f9fa;
        padding: 10px 10px 0 10px;
        border-radius: 12px 12px 0 0;
    }
</style>
@endpush
@section('contents')

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    <!-- Profile Header -->
    <div class="row">
        <div class="col-md-12">
            <div class="profile-header">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <div class="position-relative">
                            <img src="{{asset($user->image())}}" class="ProfileImage" alt="profile image" />
                            {{-- @if($user->status)
                            <span class="position-absolute bottom-0 start-50 translate-middle-x badge badge-success" style="font-size: 10px;">
                                <i class='bx bx-check-circle'></i> Active
                            </span>
                            @else
                            <span class="position-absolute bottom-0 start-50 translate-middle-x badge badge-warning" style="font-size: 10px;">
                                <i class='bx bx-time-five'></i> Inactive
                            </span>
                            @endif --}}
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h2 class="mb-2 text-white">{{$user->name}}</h2>
                        @if($user->designation)
                        <p class="mb-1"><i class="bx bx-briefcase mr-2"></i> {{$user->designation->name}}</p>
                        @endif
                        <p class="mb-1"><i class="bx bx-id-card mr-2"></i> Employee ID: <strong>{{$user->employee_id}}</strong></p>
                        @if($user->department)
                        <p class="mb-1"><i class="bx bx-buildings mr-2"></i> {{$user->department->name}}</p>
                        @endif
                        <p class="mb-1"><i class="bx bx-calendar mr-2"></i> Join Date: {{$user->created_at->format('d M Y')}}</p>
                        <div class="mt-2">
                            @if($user->permission)
                            <span class="badge badge-light mr-1">{{$user->permission->name}}</span>
                            @endif
                            <span class="badge {{$user->status ? 'badge-success' : 'badge-warning'}}">
                                {{$user->status ? 'Active' : 'Inactive'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 text-right">
                        <a href="{{route('admin.editProfile')}}" class="btn btn-light btn-md">
                            <i class="bx bx-edit"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                        <i class="bx bx-user"></i> Basic Info
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="personal-tab" data-toggle="tab" href="#personal" role="tab">
                        <i class="bx bx-heart"></i> Personal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab">
                        <i class="bx bx-phone"></i> Contact
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="job-tab" data-toggle="tab" href="#job" role="tab">
                        <i class="bx bx-briefcase"></i> Job Info
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="salary-tab" data-toggle="tab" href="#salary" role="tab">
                        <i class="bx bx-money"></i> Salary
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab">
                        <i class="bx bx-file"></i> Documents
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="profileTabsContent">

                <!-- Basic Information Tab -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Basic Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Employee ID</div>
                                        <div class="info-value">{{$user->employee_id}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Employee Name</div>
                                        <div class="info-value">{{$user->name}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Name (Bangla)</div>
                                        <div class="info-value">{{$user->bn_name ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Gender</div>
                                        <div class="info-value">{{$user->gender ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Date of Birth</div>
                                        <div class="info-value">{{$user->dob ? Carbon\Carbon::parse($user->dob)->format('d M Y') : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Blood Group</div>
                                        <div class="info-value">{{$user->blood_group ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Religion</div>
                                        <div class="info-value">{{$user->religion ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Marital Status</div>
                                        <div class="info-value">{{ucfirst($user->marital_status ?? 'N/A')}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Mobile Number</div>
                                        <div class="info-value">{{$user->mobile ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Email Address</div>
                                        <div class="info-value">{{$user->email ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Nationality</div>
                                        <div class="info-value">{{$user->nationality ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Home District</div>
                                        <div class="info-value">{{$user->home_district ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Report To</div>
                                        <div class="info-value">{{$user->report_to ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Physical Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Height (Cm)</div>
                                        <div class="info-value">{{$user->height ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Weight (KG)</div>
                                        <div class="info-value">{{$user->weight ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Distinguished Mark</div>
                                        <div class="info-value">{{$user->distinguished_mark ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-card">
                                <h5 class="section-title">Education</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Education Qualification</div>
                                        <div class="info-value">{{$user->education ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Type of Work</div>
                                        <div class="info-value">{{$user->work_type ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Tab -->
                <div class="tab-pane fade" id="personal" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Family Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Father's Name</div>
                                        <div class="info-value">{{$user->father_name ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Father's Name (Bangla)</div>
                                        <div class="info-value">{{$user->father_name_bn ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Mother's Name</div>
                                        <div class="info-value">{{$user->mother_name ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Mother's Name (Bangla)</div>
                                        <div class="info-value">{{$user->mother_name_bn ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Spouse Name</div>
                                        <div class="info-value">{{$user->spouse_name ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Spouse Name (Bangla)</div>
                                        <div class="info-value">{{$user->spouse_name_bn ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="info-label">No of Boys</div>
                                        <div class="info-value">{{$user->boys ?? '0'}}</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="info-label">No of Girls</div>
                                        <div class="info-value">{{$user->girls ?? '0'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Nominee Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Nominee Name</div>
                                        <div class="info-value">{{$user->nominee ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Nominee Name (Bangla)</div>
                                        <div class="info-value">{{$user->nominee_bn ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Relation</div>
                                        <div class="info-value">{{$user->nominee_relation ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Nominee Age</div>
                                        <div class="info-value">{{$user->nominee_age ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-card">
                                <h5 class="section-title">Other Information</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Reference - 1</div>
                                        <div class="info-value">{{$user->reference_1 ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Reference - 2</div>
                                        <div class="info-value">{{$user->reference_2 ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Other Information</div>
                                        <div class="info-value">{{$user->other_information ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Tab -->
                <div class="tab-pane fade" id="contact" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Present Address</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Address</div>
                                        <div class="info-value">{{$user->present_address ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Address (Bangla)</div>
                                        <div class="info-value">{{$user->present_address_bn ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Permanent Address</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Address</div>
                                        <div class="info-value">{{$user->permanent_address ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Address (Bangla)</div>
                                        <div class="info-value">{{$user->permanent_address_bn ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Emergency Contact</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Emergency Mobile</div>
                                        <div class="info-value">{{$user->emergency_mobile ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Emergency Contact Relation</div>
                                        <div class="info-value">{{$user->emergency_relation ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Information Tab -->
                <div class="tab-pane fade" id="job" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Job Details</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Designation</div>
                                        <div class="info-value">{{$user->designation ? $user->designation->name : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Department</div>
                                        <div class="info-value">{{$user->department ? $user->department->name : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Division</div>
                                        <div class="info-value">{{$user->division ? $user->division->name : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Section</div>
                                        <div class="info-value">{{$user->section ? $user->section->name : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Line Number</div>
                                        <div class="info-value">{{$user->line ? $user->line->name : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Shift</div>
                                        <div class="info-value">{{$user->shift ? $user->shift->name_of_shift : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Employee Type</div>
                                        <div class="info-value">{{$user->employeeType ? $user->employeeType->name : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Placement/Location</div>
                                        <div class="info-value">{{$user->location ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Joining Date</div>
                                        <div class="info-value">{{$user->created_at ? $user->created_at->format('d M Y') : 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Account Status</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">User Status</div>
                                        <div class="info-value">
                                            @if($user->status)
                                            <span class="badge badge-success">Active</span>
                                            @else
                                            <span class="badge badge-warning">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Employment Status</div>
                                        <div class="info-value">{{$user->employment_status ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Salary Tab -->
                <div class="tab-pane fade" id="salary" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Salary Information</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Salary Type</div>
                                        <div class="info-value">{{$user->salary_type ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Gross Salary</div>
                                        <div class="info-value">{{$user->gross_salary ? number_format($user->gross_salary, 2) : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Basic Salary</div>
                                        <div class="info-value">{{$user->basic_salary ? number_format($user->basic_salary, 2) : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">House Rent</div>
                                        <div class="info-value">{{$user->house_rent ? number_format($user->house_rent, 2) : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Medical Allowance</div>
                                        <div class="info-value">{{$user->medical_allowance ? number_format($user->medical_allowance, 2) : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Transport Allowance</div>
                                        <div class="info-value">{{$user->transport_allowance ? number_format($user->transport_allowance, 2) : 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Food Allowance</div>
                                        <div class="info-value">{{$user->food_allowance ? number_format($user->food_allowance, 2) : 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Recent Salary History</h5>
                                @if($user->salarySheets->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Year</th>
                                                <th>Gross</th>
                                                <th>Net</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->salarySheets()->orderBy('year', 'desc')->orderBy('month', 'desc')->limit(6)->get() as $salary)
                                            <tr>
                                                <td>{{$salary->month}}</td>
                                                <td>{{$salary->year}}</td>
                                                <td>{{number_format($salary->gross_salary, 2)}}</td>
                                                <td>{{number_format($salary->net_salary, 2)}}</td>
                                                <td>
                                                    @if($salary->payment_status == 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                    @else
                                                    <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <p class="text-muted">No salary history available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5 class="section-title">Identity Documents</h5>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">National ID Card</div>
                                        <div class="info-value">{{$user->nid_number ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Birth Registration No</div>
                                        <div class="info-value">{{$user->birth_registration ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Passport No</div>
                                        <div class="info-value">{{$user->passport_no ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Driving License</div>
                                        <div class="info-value">{{$user->driving_license ?? 'N/A'}}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">e-TIN</div>
                                        <div class="info-value">{{$user->etin ?? 'N/A'}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('js')

@endpush
