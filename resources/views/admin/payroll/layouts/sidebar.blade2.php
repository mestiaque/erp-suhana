<!-- Start Sidemenu Area -->
        <div class="sidemenu-area">
            <div class="sidemenu-header">
                <a href="{{route('admin.dashboard')}}" class="navbar-brand d-flex align-items-center">
                    <img src="{{asset(general()->logo())}}" alt="logo" />
                </a>
                <div class="burger-menu d-none d-lg-block">
                    <span class="top-bar"></span>
                    <span class="middle-bar"></span>
                    <span class="bottom-bar"></span>
                </div>
                <div class="responsive-burger-menu d-block d-lg-none">
                    <span class="top-bar"></span>
                    <span class="middle-bar"></span>
                    <span class="bottom-bar"></span>
                </div>
            </div>
            <div class="sidemenu-body">
                <ul class="sidemenu-nav metisMenu h-100" id="sidemenu-nav" data-simplebar="">
                    <li class="nav-item-title">
                        Main
                    </li>
                    <li class="nav-item {{Request::is('admin/dashboard')? 'mm-active' : ''}}">
                        <a href="{{route('admin.dashboard')}}" class="nav-link">
                            <span class="icon"><i class='bx bxs-dashboard'></i></span>
                            <span class="menu-title">Dashboard </span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('admin/my-profile')? 'mm-active' : ''}}">
                        <a href="{{route('admin.myProfile')}}" class="nav-link">
                            <span class="icon"><i class="bx bx-user"></i></span>
                            <span class="menu-title">My Profile </span>
                        </a>
                    </li>


                    <li class="nav-item-title">
                        Employee Management
                    </li>
                    <li class="nav-item {{Request::is('admin/users/customer*')? 'mm-active' : ''}}">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-user-circle"></i></span>
                            <span class="menu-title">Employee Management</span>
                        </a>
                        <ul class="sidemenu-nav-second-level">
                            <li class="nav-item {{Request::is('admin/users/customer') && !Request::get('status')? 'mm-active' : ''}}">
                                <a href="{{route('admin.usersCustomer')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">All Employees</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/users/customer/create')? 'mm-active' : ''}}">
                                <a href="{{route('admin.usersCustomerAction', 'create')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Add New Employee</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.usersCustomer', ['status' => 1])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Active Employees</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.usersCustomer', ['status' => 0])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Inactive Employees</span>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <!-- Employee Portal Section -->
                    @if(true)
                    <li class="nav-item-title">
                        Employee Portal
                    </li>

                    <li class="nav-item {{Request::is('employee/dashboard')? 'mm-active' : ''}}">
                        <a href="{{route('customer.dashboard')}}" class="nav-link">
                            <span class="icon"><i class="bx bx-home"></i></span>
                            <span class="menu-title">Portal Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('employee/attendance')? 'mm-active' : ''}}">
                        <a href="{{route('customer.attendance')}}" class="nav-link">
                            <span class="icon"><i class="bx bx-check-square"></i></span>
                            <span class="menu-title">My Attendance</span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('employee/profile')? 'mm-active' : ''}}">
                        <a href="{{route('customer.myProfile')}}" class="nav-link">
                            <span class="icon"><i class="bx bx-user"></i></span>
                            <span class="menu-title">My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('employee/dashboard')? 'mm-active' : ''}}">
                        <a href="{{route('customer.dashboard')}}" class="nav-link">
                            <span class="icon"><i class="bx bx-home"></i></span>
                            <span class="menu-title">Portal Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('employee/attendance')? 'mm-active' : ''}}">
                        <a href="{{route('customer.attendance')}}" class="nav-link">
                            <span class="icon"><i class="bx bx-check-square"></i></span>
                            <span class="menu-title">My Attendance</span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('employee/profile')? 'mm-active' : ''}}">
                        <a href="{{route('customer.myProfile')}}" class="nav-link">
                            <span class="icon"><i class="bx bx-user"></i></span>
                            <span class="menu-title">My Profile</span>
                        </a>
                    </li>

                    <!-- Attendance Management Section -->
                    <li class="nav-item-title">
                        Attendance Management
                    </li>

                    <!-- Roaster Management -->
                    <li class="nav-item {{Request::is('admin/attendance/roaster*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.attendance.roaster.index')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-time-five'></i></span>
                            <span class="menu-title">Roaster Management</span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('admin/attendance/roaster*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.attendance.roaster.index')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-time-five'></i></span>
                            <span class="menu-title">Roaster Management</span>
                        </a>
                    </li>

                    <!-- Process Attendance -->
                    <li class="nav-item {{Request::is('admin/attendance/process')? 'mm-active' : ''}}">
                        <a href="{{route('admin.attendance.process')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-refresh'></i></span>
                            <span class="menu-title">Process Attendance</span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('admin/attendance/process')? 'mm-active' : ''}}">
                        <a href="{{route('admin.attendance.process')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-refresh'></i></span>
                            <span class="menu-title">Process Attendance</span>
                        </a>
                    </li>

                    <!-- Daily Attendance Reports -->
                    <li class="nav-item {{Request::is('admin/attendance/daily-report*')? 'mm-active' : ''}}">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-file-find"></i></span>
                            <span class="menu-title">Daily Attendance Report</span>
                        </a>
                        <ul class="sidemenu-nav-second-level">
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">All Attendance</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'present'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Present</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'late'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Late</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'leave'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Leave</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'absent'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Absent</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'tour'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Tour</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'weekly_off'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Weekly Off</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'holiday'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Holiday Present</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{Request::is('admin/attendance/daily-report*')? 'mm-active' : ''}}">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-file-find"></i></span>
                            <span class="menu-title">Daily Attendance Report</span>
                        </a>
                        <ul class="sidemenu-nav-second-level">
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">All Attendance</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'present'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Present</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'late'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Late</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'leave'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Leave</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'absent'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Absent</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'tour'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Tour</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'weekly_off'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Weekly Off</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.attendance.daily.report', ['status' => 'holiday'])}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Holiday Present</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{Request::is('admin/attendance/absent-report*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.attendance.absent.report')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-user-x'></i></span>
                            <span class="menu-title">Last 7/10 Days Absent</span>
                        </a>
                    </li>

                    <li class="nav-item {{Request::is('admin/attendance/invalid-report*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.attendance.invalid.report')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-error-circle'></i></span>
                            <span class="menu-title">Invalid Attendance</span>
                        </a>
                    </li>

                    <li class="nav-item {{Request::is('admin/attendance/summary*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.attendance.summary')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-bar-chart-alt-2'></i></span>
                            <span class="menu-title">Attendance Summary</span>
                        </a>
                    </li>

                    <li class="nav-item {{Request::is('admin/attendance/monthly-report*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.attendance.monthly.report')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-calendar-event'></i></span>
                            <span class="menu-title">Monthly Attendance Report</span>
                        </a>
                    </li>
                    @endisset

                    <!-- Leave Management -->
                    <li class="nav-item-title">
                        Leave Management
                    </li>


                    <li class="nav-item {{Request::is('admin/leaves*')? 'mm-active' : ''}}">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-calendar-minus"></i></span>
                            <span class="menu-title">Leave Management</span>
                        </a>
                        <ul class="sidemenu-nav-second-level">
                            <li class="nav-item {{Request::is('admin/leaves') && !Request::is('admin/leaves/create')? 'mm-active' : ''}}">
                                <a href="{{route('admin.leaves.index')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Leave Applications</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/leaves/create')? 'mm-active' : ''}}">
                                <a href="{{route('admin.leaves.create')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Apply Leave</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.leaveReport')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Leave Reports</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Holiday Management -->
                    <li class="nav-item-title">
                        Holiday Management
                    </li>


                    <li class="nav-item {{Request::is('admin/holidays*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.holiday.index')}}" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-calendar-star"></i></span>
                            <span class="menu-title">Holidays</span>
                        </a>
                    </li>

                    <li class="nav-item {{Request::is('admin/offday*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.offday.index')}}" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-calendar-exclude"></i></span>
                            <span class="menu-title">Weekly Offday</span>
                        </a>
                    </li>

                    <li class="nav-item {{Request::is('admin/jobcard*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.jobcard.index')}}" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-id-card"></i></span>
                            <span class="menu-title">Job Card</span>
                        </a>
                    </li>

                    <li class="nav-item {{Request::is('admin/idcard*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.idcard.index')}}" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-id-card"></i></span>
                            <span class="menu-title">ID Card</span>
                        </a>
                    </li>

                    <!-- Payroll Management -->
                    <li class="nav-item-title">
                        Payroll Management
                    </li>


                    <li class="nav-item {{Request::is('admin/payroll*')? 'mm-active' : ''}}">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-wallet"></i></span>
                            <span class="menu-title">Payroll Management</span>
                        </a>
                        <ul class="sidemenu-nav-second-level">
                            <li class="nav-item {{Request::is('admin/payroll') && !Request::is('admin/payroll/*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.payroll.index')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Payroll Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/payroll/process')? 'mm-active' : ''}}">
                                <a href="{{route('admin.payroll.process')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Process Payroll</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/payroll/daily-salary-sheet')? 'mm-active' : ''}}">
                                <a href="{{route('admin.payroll.dailySalarySheet')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Daily Salary Sheet</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/payroll/salary-sheet')? 'mm-active' : ''}}">
                                <a href="{{route('admin.payroll.salarySheet')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Monthly Salary Sheet</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/payroll/salary-summary')? 'mm-active' : ''}}">
                                <a href="{{route('admin.payroll.salarySummary')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Salary Summary</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Employee Reports -->
                    <li class="nav-item-title">
                        Employee Reports
                    </li>


                    <li class="nav-item {{Request::is('admin/reports/employees*')? 'mm-active' : ''}}">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-bar-chart-square"></i></span>
                            <span class="menu-title">Employee Reports</span>
                        </a>
                        <ul class="sidemenu-nav-second-level">
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.genderWise')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Gender-wise Report</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.statusWise')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Status-wise Report</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.newlyJoined')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Newly Joined Employees</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.retired')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Retired Employees</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.increment')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Month-wise Increment</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.serviceCompleted')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Service Completed</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.bengaliList')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Bengali Employee List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.reports.employees.departmentWise')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Department/Division Reports</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- System Generated Documents -->
                    <li class="nav-item-title">
                        System Documents
                    </li>


                    <li class="nav-item {{Request::is('admin/documents*')? 'mm-active' : ''}}">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bx-file"></i></span>
                            <span class="menu-title">System Documents</span>
                        </a>
                        <ul class="sidemenu-nav-second-level">
                            <li class="nav-item">
                                <a href="{{route('admin.documents.idCard')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">ID Cards</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.documents.personalInfo')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Personal Information</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.documents.paySlip')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Pay Slip</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.documents.appointmentLetter')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Appointment Letter</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.documents.joiningLetter')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Joining Letter</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.documents.incrementLetter')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Increment Letter</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('admin.documents.confirmationLetter')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Service Confirmation</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item-title">
                        HR Management
                    </li>


                    <li class="nav-item {{Request::is('admin/hr*')? 'mm-active' : ''}}">
                        <a href="#" class="collapsed-nav-link nav-link" aria-expanded="false">
                            <span class="icon"><i class="bx bxs-brightness"></i></span>
                            <span class="menu-title">HR Setup </span>
                        </a>
                        <ul class="sidemenu-nav-second-level">
                            <li class="nav-item {{Request::is('admin/hr/departments*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.departments')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Department </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/hr/designations*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.designations')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Designation </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/hr/divisions*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.divisions')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Division </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/hr/grades*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.grades')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Grade </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/hr/line-numbers*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.line_numbers')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Line Number </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/hr/sections*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.sections')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Section </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/hr/shifts*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.shifts')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Shift </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/hr/employee-types*')? 'mm-active' : ''}}">
                                <a href="{{route('admin.employeeType')}}" class="nav-link">
                                    <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                                    <span class="menu-title">Employee Type </span>
                                </a>
                            </li>

                        </ul>
                    </li>


                    <li class="nav-item {{Request::is('admin/users/customer*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.usersCustomer')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-group'></i></span>
                            <span class="menu-title">Employee Users</span>
                        </a>
                    </li>

                    <li class="nav-item {{Request::is('admin/users/admin*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.usersAdmin')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-user'></i></span>
                            <span class="menu-title">Supper Admin</span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('admin/users/role*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.userRoles')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-joystick' ></i></span>
                            <span class="menu-title">Roles Setup</span>
                        </a>
                    </li>

                    <li class="nav-item {{Request::is('admin/zkteco-data-import*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.importZkteco')}}" class="nav-link">
                            <span class="icon"><i class="bx bx-radio-circle-marked"></i></span>
                            <span class="menu-title">Atten. Import </span>
                        </a>
                    </li>

                    <li class="nav-item-title">
                       Software Setting
                    </li>

                    <li class="nav-item {{Request::is('admin/setting/general*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.setting','general')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-cog'></i></span>
                            <span class="menu-title">General Setting </span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('admin/setting/mail*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.setting','mail')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-envelope'></i></span>
                            <span class="menu-title">Mail Setting </span>
                        </a>
                    </li>
                    <li class="nav-item {{Request::is('admin/setting/sms*')? 'mm-active' : ''}}">
                        <a href="{{route('admin.setting','sms')}}" class="nav-link">
                            <span class="icon"><i class='bx bx-message'></i></span>
                            <span class="menu-title">SMS Setting </span>
                        </a>
                    </li>


                    <li class="nav-item" style="margin:100px 0"></li>
                </ul>

            </div>
        </div>
        <!-- End Sidemenu Area -->
