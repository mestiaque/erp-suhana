<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeEducation;
use App\Models\EmployeeTraining;
use App\Models\EmployeeExperience;
use App\Models\EmployeeBank;
use App\Models\EmployeeIncrement;
use App\Models\Attribute;
use App\Models\Shift;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EmployeeManagementController extends Controller
{
    /**
     * Display employee list
     */
    public function index(Request $request)
    {
        // Query employees directly from users table
        $query = User::query()->filterBy('employee');

        // Filter by employee status
        if ($request->employee_status) {
            $query->where('employee_status', $request->employee_status);
        }

        // Filter by gender
        if ($request->gender) {
            $query->where('gender', $request->gender);
        }

        // Filter by department
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by designation
        if ($request->designation_id) {
            $query->where('designation_id', $request->designation_id);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_id', 'like', '%' . $request->search . '%');
            });
        }

        $employees = $query->paginate(25);

        // Get filter data
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $designations = Attribute::where('type', 2)->where('status', 'active')->get();

        return view(adminTheme().'employees.index', compact('employees', 'departments', 'designations'));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $designations = Attribute::where('type', 2)->where('status', 'active')->get();
        $divisions = Attribute::where('type', 11)->where('status', 'active')->get();
        $sections = Attribute::where('type', 14)->where('status', 'active')->get();
        $grades = Attribute::where('type', 12)->where('status', 'active')->get();
        $shifts = Shift::where('status', 'active')->get();
        $employeeTypes = Attribute::where('type', 16)->where('status', 'active')->get();
        $lineNumbers = Attribute::where('type', 13)->where('status', 'active')->get();

        return view(adminTheme().'employees.create', compact(
            'departments', 'designations', 'divisions', 'sections',
            'grades', 'shifts', 'employeeTypes', 'lineNumbers'
        ));
    }

    /**
     * Store a newly created employee
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile',
            'password' => 'required|string|min:6',
            'employee_id' => 'nullable|unique:users,employee_id',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Create User
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->password = Hash::make($request->password);
            $user->password_show = $request->password;
            $user->customer = 1;
            $user->status = 1;
            $user->addedby_id = auth()->id();
            $user->save();

            // Create Employee Info
            $employeeInfo = new EmployeeInfo();
            $employeeInfo->user_id = $user->id;
            $employeeInfo->employee_id = $request->employee_id;
            $employeeInfo->card_no = $request->card_no;
            $employeeInfo->nid = $request->nid;
            $employeeInfo->birth_certificate = $request->birth_certificate;
            $employeeInfo->date_of_birth = $request->date_of_birth;
            $employeeInfo->gender = $request->gender;
            $employeeInfo->marital_status = $request->marital_status;
            $employeeInfo->blood_group = $request->blood_group;
            $employeeInfo->religion = $request->religion;
            $employeeInfo->nationality = $request->nationality ?? 'Bangladeshi';
            $employeeInfo->present_address = $request->present_address;
            $employeeInfo->permanent_address = $request->permanent_address;
            $employeeInfo->emergency_contact_name = $request->emergency_contact_name;
            $employeeInfo->emergency_contact_phone = $request->emergency_contact_phone;
            $employeeInfo->emergency_contact_relation = $request->emergency_contact_relation;
            $employeeInfo->father_name = $request->father_name;
            $employeeInfo->mother_name = $request->mother_name;
            $employeeInfo->spouse_name = $request->spouse_name;
            $employeeInfo->department_id = $request->department_id;
            $employeeInfo->designation_id = $request->designation_id;
            $employeeInfo->division_id = $request->division_id;
            $employeeInfo->section_id = $request->section_id;
            $employeeInfo->grade_id = $request->grade_id;
            $employeeInfo->shift_id = $request->shift_id;
            $employeeInfo->employee_type_id = $request->employee_type_id;
            $employeeInfo->line_number_id = $request->line_number_id;
            $employeeInfo->joining_date = $request->joining_date;
            $employeeInfo->confirmation_date = $request->confirmation_date;
            $employeeInfo->employee_status = $request->employee_status ?? 'active';
            $employeeInfo->service_confirmed = $request->service_confirmed ?? 'no';
            $employeeInfo->basic_salary = $request->basic_salary ?? 0;
            $employeeInfo->house_rent = $request->house_rent ?? 0;
            $employeeInfo->medical_allowance = $request->medical_allowance ?? 0;
            $employeeInfo->transport_allowance = $request->transport_allowance ?? 0;
            $employeeInfo->other_allowance = $request->other_allowance ?? 0;
            $employeeInfo->remarks = $request->remarks;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->store('employees/photos', 'public');
                $employeeInfo->photo = $photoPath;
            }

            // Handle signature upload
            if ($request->hasFile('signature')) {
                $signature = $request->file('signature');
                $signaturePath = $signature->store('employees/signatures', 'public');
                $employeeInfo->signature = $signaturePath;
            }

            $employeeInfo->save();

            DB::commit();

            return redirect()->route('admin.employees.show', $user->id)
                ->with('success', 'Employee created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating employee: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified employee
     */
    public function show($id)
    {
        $employee = User::with([
            'employeeInfo', 'employeeEducation', 'employeeTraining',
            'employeeExperience', 'employeeBankInfo', 'increments'
        ])->findOrFail($id);

        return view(adminTheme().'employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit($id)
    {
        $employee = User::with('employeeInfo')->findOrFail($id);

        $departments = Attribute::where('type', 3)->where('status', 'active')->get();
        $designations = Attribute::where('type', 2)->where('status', 'active')->get();
        $divisions = Attribute::where('type', 11)->where('status', 'active')->get();
        $sections = Attribute::where('type', 14)->where('status', 'active')->get();
        $grades = Attribute::where('type', 12)->where('status', 'active')->get();
        $shifts = Shift::where('status', 'active')->get();
        $employeeTypes = Attribute::where('type', 16)->where('status', 'active')->get();
        $lineNumbers = Attribute::where('type', 13)->where('status', 'active')->get();

        return view(adminTheme().'employees.edit', compact(
            'employee', 'departments', 'designations', 'divisions', 'sections',
            'grades', 'shifts', 'employeeTypes', 'lineNumbers'
        ));
    }

    /**
     * Update the specified employee
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'mobile' => 'required|string|unique:users,mobile,' . $id,
            'employee_id' => 'nullable|unique:employee_info,employee_id,' . $user->employeeInfo->id,
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // Update User
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;

            if ($request->password) {
                $user->password = Hash::make($request->password);
                $user->password_show = $request->password;
            }

            $user->save();

            // Update or Create Employee Info
            $employeeInfo = $user->employeeInfo ?? new EmployeeInfo(['user_id' => $user->id]);
            $employeeInfo->employee_id = $request->employee_id;
            $employeeInfo->card_no = $request->card_no;
            $employeeInfo->nid = $request->nid;
            $employeeInfo->birth_certificate = $request->birth_certificate;
            $employeeInfo->date_of_birth = $request->date_of_birth;
            $employeeInfo->gender = $request->gender;
            $employeeInfo->marital_status = $request->marital_status;
            $employeeInfo->blood_group = $request->blood_group;
            $employeeInfo->religion = $request->religion;
            $employeeInfo->nationality = $request->nationality ?? 'Bangladeshi';
            $employeeInfo->present_address = $request->present_address;
            $employeeInfo->permanent_address = $request->permanent_address;
            $employeeInfo->emergency_contact_name = $request->emergency_contact_name;
            $employeeInfo->emergency_contact_phone = $request->emergency_contact_phone;
            $employeeInfo->emergency_contact_relation = $request->emergency_contact_relation;
            $employeeInfo->father_name = $request->father_name;
            $employeeInfo->mother_name = $request->mother_name;
            $employeeInfo->spouse_name = $request->spouse_name;
            $employeeInfo->department_id = $request->department_id;
            $employeeInfo->designation_id = $request->designation_id;
            $employeeInfo->division_id = $request->division_id;
            $employeeInfo->section_id = $request->section_id;
            $employeeInfo->grade_id = $request->grade_id;
            $employeeInfo->shift_id = $request->shift_id;
            $employeeInfo->employee_type_id = $request->employee_type_id;
            $employeeInfo->line_number_id = $request->line_number_id;
            $employeeInfo->joining_date = $request->joining_date;
            $employeeInfo->confirmation_date = $request->confirmation_date;
            $employeeInfo->retirement_date = $request->retirement_date;
            $employeeInfo->resign_date = $request->resign_date;
            $employeeInfo->employee_status = $request->employee_status;
            $employeeInfo->service_confirmed = $request->service_confirmed;
            $employeeInfo->basic_salary = $request->basic_salary ?? 0;
            $employeeInfo->house_rent = $request->house_rent ?? 0;
            $employeeInfo->medical_allowance = $request->medical_allowance ?? 0;
            $employeeInfo->transport_allowance = $request->transport_allowance ?? 0;
            $employeeInfo->other_allowance = $request->other_allowance ?? 0;
            $employeeInfo->remarks = $request->remarks;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($employeeInfo->photo && Storage::disk('public')->exists($employeeInfo->photo)) {
                    Storage::disk('public')->delete($employeeInfo->photo);
                }
                $photo = $request->file('photo');
                $photoPath = $photo->store('employees/photos', 'public');
                $employeeInfo->photo = $photoPath;
            }

            // Handle signature upload
            if ($request->hasFile('signature')) {
                // Delete old signature
                if ($employeeInfo->signature && Storage::disk('public')->exists($employeeInfo->signature)) {
                    Storage::disk('public')->delete($employeeInfo->signature);
                }
                $signature = $request->file('signature');
                $signaturePath = $signature->store('employees/signatures', 'public');
                $employeeInfo->signature = $signaturePath;
            }

            $employeeInfo->save();

            DB::commit();

            return redirect()->route('admin.employees.show', $user->id)
                ->with('success', 'Employee updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified employee
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Delete related records (handled by cascade)
            $user->delete();

            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }

    /**
     * Employee Education Management
     */
    public function educationStore(Request $request, $userId)
    {
        $request->validate([
            'degree_name' => 'required|string',
            'institute_name' => 'required|string',
        ]);

        $education = new EmployeeEducation();
        $education->user_id = $userId;
        $education->degree_name = $request->degree_name;
        $education->institute_name = $request->institute_name;
        $education->subject = $request->subject;
        $education->result = $request->result;
        $education->passing_year = $request->passing_year;
        $education->remarks = $request->remarks;
        $education->save();

        return back()->with('success', 'Education record added successfully!');
    }

    public function educationUpdate(Request $request, $userId, $educationId)
    {
        $education = EmployeeEducation::where('user_id', $userId)->findOrFail($educationId);

        $education->degree_name = $request->degree_name;
        $education->institute_name = $request->institute_name;
        $education->subject = $request->subject;
        $education->result = $request->result;
        $education->passing_year = $request->passing_year;
        $education->remarks = $request->remarks;
        $education->save();

        return back()->with('success', 'Education record updated successfully!');
    }

    public function educationDestroy($userId, $educationId)
    {
        $education = EmployeeEducation::where('user_id', $userId)->findOrFail($educationId);
        $education->delete();

        return back()->with('success', 'Education record deleted successfully!');
    }

    /**
     * Employee Training Management
     */
    public function trainingStore(Request $request, $userId)
    {
        $request->validate([
            'training_title' => 'required|string',
        ]);

        $training = new EmployeeTraining();
        $training->user_id = $userId;
        $training->training_title = $request->training_title;
        $training->training_institute = $request->training_institute;
        $training->duration = $request->duration;
        $training->start_date = $request->start_date;
        $training->end_date = $request->end_date;
        $training->description = $request->description; $training->save();

        return back()->with('success', 'Training record added successfully!');
    }

    public function trainingUpdate(Request $request, $userId, $trainingId)
    {
        $training = EmployeeTraining::where('user_id', $userId)->findOrFail($trainingId);

        $training->training_title = $request->training_title;
        $training->training_institute = $request->training_institute;
        $training->duration = $request->duration;
        $training->start_date = $request->start_date;
        $training->end_date = $request->end_date;
        $training->description = $request->description;
        $training->save();

        return back()->with('success', 'Training record updated successfully!');
    }

    public function trainingDestroy($userId, $trainingId)
    {
        $training = EmployeeTraining::where('user_id', $userId)->findOrFail($trainingId);
        $training->delete();

        return back()->with('success', 'Training record deleted successfully!');
    }

    /**
     * Employee Experience Management
     */
    public function experienceStore(Request $request, $userId)
    {
        $request->validate([
            'company_name' => 'required|string',
            'designation' => 'required|string',
        ]);

        $experience = new EmployeeExperience();
        $experience->user_id = $userId;
        $experience->company_name = $request->company_name;
        $experience->designation = $request->designation;
        $experience->department = $request->department;
        $experience->from_date = $request->from_date;
        $experience->to_date = $request->to_date;
        $experience->responsibilities = $request->responsibilities;
        $experience->save();

        return back()->with('success', 'Experience record added successfully!');
    }

    public function experienceUpdate(Request $request, $userId, $experienceId)
    {
        $experience = EmployeeExperience::where('user_id', $userId)->findOrFail($experienceId);

        $experience->company_name = $request->company_name;
        $experience->designation = $request->designation;
        $experience->department = $request->department;
        $experience->from_date = $request->from_date;
        $experience->to_date = $request->to_date;
        $experience->responsibilities = $request->responsibilities;
        $experience->save();

        return back()->with('success', 'Experience record updated successfully!');
    }

    public function experienceDestroy($userId, $experienceId)
    {
        $experience = EmployeeExperience::where('user_id', $userId)->findOrFail($experienceId);
        $experience->delete();

        return back()->with('success', 'Experience record deleted successfully!');
    }

    /**
     * Employee Bank Information Management
     */
    public function bankStore(Request $request, $userId)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder_name' => 'required|string',
        ]);

        $bank = new EmployeeBank();
        $bank->user_id = $userId;
        $bank->bank_name = $request->bank_name;
        $bank->branch_name = $request->branch_name;
        $bank->account_number = $request->account_number;
        $bank->account_holder_name = $request->account_holder_name;
        $bank->routing_number = $request->routing_number;
        $bank->payment_method = $request->payment_method ?? 'bank';
        $bank->mobile_banking_number = $request->mobile_banking_number;
        $bank->is_primary = $request->is_primary ?? 'no';
        $bank->save();

        return back()->with('success', 'Bank information added successfully!');
    }

    public function bankUpdate(Request $request, $userId, $bankId)
    {
        $bank = EmployeeBank::where('user_id', $userId)->findOrFail($bankId);

        $bank->bank_name = $request->bank_name;
        $bank->branch_name = $request->branch_name;
        $bank->account_number = $request->account_number;
        $bank->account_holder_name = $request->account_holder_name;
        $bank->routing_number = $request->routing_number;
        $bank->payment_method = $request->payment_method;
        $bank->mobile_banking_number = $request->mobile_banking_number;
        $bank->is_primary = $request->is_primary;
        $bank->save();

        return back()->with('success', 'Bank information updated successfully!');
    }

    public function bankDestroy($userId, $bankId)
    {
        $bank = EmployeeBank::where('user_id', $userId)->findOrFail($bankId);
        $bank->delete();

        return back()->with('success', 'Bank information deleted successfully!');
    }

    /**
     * Service Confirmation
     */
    public function confirmService(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $employeeInfo = $user->employeeInfo;

        if ($employeeInfo) {
            $employeeInfo->service_confirmed = 'yes';
            $employeeInfo->confirmation_date = $request->confirmation_date ?? Carbon::now();
            $employeeInfo->save();

            return back()->with('success', 'Employee service confirmed successfully!');
        }

        return back()->with('error', 'Employee info not found!');
    }

    /**
     * Retirement Process
     */
    public function retire(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $employeeInfo = $user->employeeInfo;

        if ($employeeInfo) {
            $employeeInfo->employee_status = 'retired';
            $employeeInfo->retirement_date = $request->retirement_date ?? Carbon::now();
            $employeeInfo->save();

            return back()->with('success', 'Employee retired successfully!');
        }

        return back()->with('error', 'Employee info not found!');
    }

    /**
     * Mark as Inactive
     */
    public function markInactive(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $employeeInfo = $user->employeeInfo;

        if ($employeeInfo) {
            $employeeInfo->employee_status = 'inactive';
            $employeeInfo->save();

            // Also update user status
            $user->status = 0;
            $user->save();

            return back()->with('success', 'Employee marked as inactive!');
        }

        return back()->with('error', 'Employee info not found!');
    }

    /**
     * Mark as Active
     */
    public function markActive(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $employeeInfo = $user->employeeInfo;

        if ($employeeInfo) {
            $employeeInfo->employee_status = 'active';
            $employeeInfo->save();

            // Also update user status
            $user->status = 1;
            $user->save();

            return back()->with('success', 'Employee marked as active!');
        }

        return back()->with('error', 'Employee info not found!');
    }

    /**
     * Employee Increment
     */
    public function incrementStore(Request $request, $userId)
    {
        $request->validate([
            'increment_date' => 'required|date',
            'increment_amount' => 'required|numeric|min:0',
        ]);

        $user = User::with('employeeInfo')->findOrFail($userId);
        $employeeInfo = $user->employeeInfo;

        if (!$employeeInfo) {
            return back()->with('error', 'Employee info not found!');
        }

        $previousSalary = $employeeInfo->basic_salary;
        $incrementAmount = $request->increment_amount;
        $newSalary = $previousSalary + $incrementAmount;
        $incrementPercentage = ($incrementAmount / $previousSalary) * 100;

        // Create increment record
        $increment = new EmployeeIncrement();
        $increment->user_id = $userId;
        $increment->increment_date = $request->increment_date;
        $increment->previous_salary = $previousSalary;
        $increment->increment_amount = $incrementAmount;
        $increment->increment_percentage = $incrementPercentage;
        $increment->new_salary = $newSalary;
        $increment->remarks = $request->remarks;
        $increment->approved_by = auth()->id();
        $increment->save();

        // Update employee basic salary
        $employeeInfo->basic_salary = $newSalary;
        $employeeInfo->save();

        return back()->with('success', 'Salary increment added successfully!');
    }
}
