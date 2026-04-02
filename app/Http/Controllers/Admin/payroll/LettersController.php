<?php

namespace App\Http\Controllers\Admin\payroll;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AppointmentLetter;
use App\Models\JoiningLetter;
use App\Models\ConfirmationLetter;
use App\Models\EmployeeIncrement;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LettersController extends Controller
{
    // ============ Appointment Letter ============
    public function appointmentIndex()
    {
        $letters = AppointmentLetter::with(['user', 'creator'])->latest()->paginate(20);
        return view('admin.payroll.letters.appointment.index', compact('letters'));
    }

    public function appointmentCreate()
    {
        $employees = User::where('customer', 1)->where('employee_status', 'active')->filterByType('employee')->get();
        return view('admin.payroll.letters.appointment.create', compact('employees'));
    }

    public function appointmentStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'letter_date' => 'required|date',
            'position' => 'required',
            'salary' => 'required|numeric',
            'joining_date' => 'required|date',
        ]);

        AppointmentLetter::create([
            'user_id' => $request->user_id,
            'letter_date' => $request->letter_date,
            'position' => $request->position,
            'salary' => $request->salary,
            'joining_date' => $request->joining_date,
            'department' => $request->department,
            'terms' => $request->terms,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.letters.appointment.index')->with('success', 'Appointment Letter created!');
    }

    public function appointmentEdit($id)
    {
        $letter = AppointmentLetter::findOrFail($id);
        $employees = User::where('customer', 1)->where('employee_status', 'active')->filterByType('employee')->get();
        return view('admin.payroll.letters.appointment.edit', compact('letter', 'employees'));
    }

    public function appointmentUpdate(Request $request, $id)
    {
        $letter = AppointmentLetter::findOrFail($id);

        $request->validate([
            'user_id' => 'required',
            'letter_date' => 'required|date',
            'position' => 'required',
            'salary' => 'required|numeric',
            'joining_date' => 'required|date',
        ]);

        $letter->update([
            'user_id' => $request->user_id,
            'letter_date' => $request->letter_date,
            'position' => $request->position,
            'salary' => $request->salary,
            'joining_date' => $request->joining_date,
            'department' => $request->department,
            'terms' => $request->terms,
        ]);

        return redirect()->route('admin.letters.appointment.index')->with('success', 'Appointment Letter updated!');
    }

    public function appointmentShow($id)
    {
        $letter = AppointmentLetter::with('user')->findOrFail($id);
        return view('admin.payroll.letters.appointment.show', compact('letter'));
    }

    public function appointmentDestroy($id)
    {
        AppointmentLetter::findOrFail($id)->delete();
        return back()->with('success', 'Appointment Letter deleted!');
    }

    // ============ Joining Letter ============
    public function joiningIndex()
    {
        $letters = JoiningLetter::with(['user', 'creator'])->latest()->paginate(20);
        return view('admin.payroll.letters.joining.index', compact('letters'));
    }

    public function joiningCreate()
    {
        $employees = User::where('customer', 1)->where('employee_status', 'active')->filterByType('employee')->get();
        return view('admin.payroll.letters.joining.create', compact('employees'));
    }

    public function joiningStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'letter_date' => 'required|date',
            'joining_date' => 'required|date',
        ]);

        JoiningLetter::create([
            'user_id' => $request->user_id,
            'letter_date' => $request->letter_date,
            'joining_date' => $request->joining_date,
            'department' => $request->department,
            'designation' => $request->designation,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.letters.joining.index')->with('success', 'Joining Letter created!');
    }

    public function joiningEdit($id)
    {
        $letter = JoiningLetter::findOrFail($id);
        $employees = User::where('customer', 1)->where('employee_status', 'active')->filterByType('employee')->get();
        return view('admin.payroll.letters.joining.edit', compact('letter', 'employees'));
    }

    public function joiningUpdate(Request $request, $id)
    {
        $letter = JoiningLetter::findOrFail($id);

        $request->validate([
            'user_id' => 'required',
            'letter_date' => 'required|date',
            'joining_date' => 'required|date',
        ]);

        $letter->update([
            'user_id' => $request->user_id,
            'letter_date' => $request->letter_date,
            'joining_date' => $request->joining_date,
            'department' => $request->department,
            'designation' => $request->designation,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.letters.joining.index')->with('success', 'Joining Letter updated!');
    }

    public function joiningShow($id)
    {
        $letter = JoiningLetter::with('user')->findOrFail($id);
        return view('admin.payroll.letters.joining.show', compact('letter'));
    }

    public function joiningDestroy($id)
    {
        JoiningLetter::findOrFail($id)->delete();
        return back()->with('success', 'Joining Letter deleted!');
    }

    // ============ Confirmation Letter ============
    public function confirmationIndex()
    {
        $letters = ConfirmationLetter::with(['user', 'creator'])->latest()->paginate(20);
        return view('admin.payroll.letters.confirmation.index', compact('letters'));
    }

    public function confirmationCreate()
    {
        $employees = User::where('customer', 1)->where('employee_status', 'active')->filterByType('employee')->get();
        return view('admin.payroll.letters.confirmation.create', compact('employees'));
    }

    public function confirmationStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'letter_date' => 'required|date',
            'confirmation_date' => 'required|date',
        ]);

        ConfirmationLetter::create([
            'user_id' => $request->user_id,
            'letter_date' => $request->letter_date,
            'confirmation_date' => $request->confirmation_date,
            'performance_remarks' => $request->performance_remarks,
            'status' => $request->status ?? 'pending',
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.letters.confirmation.index')->with('success', 'Confirmation Letter created!');
    }

    public function confirmationEdit($id)
    {
        $letter = ConfirmationLetter::findOrFail($id);
        $employees = User::where('customer', 1)->where('employee_status', 'active')->filterByType('employee')->get();
        return view('admin.payroll.letters.confirmation.edit', compact('letter', 'employees'));
    }

    public function confirmationUpdate(Request $request, $id)
    {
        $letter = ConfirmationLetter::findOrFail($id);

        $request->validate([
            'user_id' => 'required',
            'letter_date' => 'required|date',
            'confirmation_date' => 'required|date',
        ]);

        $letter->update([
            'user_id' => $request->user_id,
            'letter_date' => $request->letter_date,
            'confirmation_date' => $request->confirmation_date,
            'performance_remarks' => $request->performance_remarks,
            'status' => $request->status ?? 'pending',
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.letters.confirmation.index')->with('success', 'Confirmation Letter updated!');
    }

    public function confirmationShow($id)
    {
        $letter = ConfirmationLetter::with('user')->findOrFail($id);
        return view('admin.payroll.letters.confirmation.show', compact('letter'));
    }

    public function confirmationDestroy($id)
    {
        ConfirmationLetter::findOrFail($id)->delete();
        return back()->with('success', 'Confirmation Letter deleted!');
    }

    // ============ Print Methods ============
    public function appointmentPrint($id)
    {
        $letter = AppointmentLetter::with('user')->findOrFail($id);
        return view('admin.payroll.letters.appointment.print', compact('letter'));
    }

    public function joiningPrint($id)
    {
        $letter = JoiningLetter::with('user')->findOrFail($id);
        return view('admin.payroll.letters.joining.print', compact('letter'));
    }

    public function confirmationPrint($id)
    {
        $letter = ConfirmationLetter::with('user')->findOrFail($id);
        return view('admin.payroll.letters.confirmation.print', compact('letter'));
    }

    public function incrementPrint($id)
    {
        $increment = EmployeeIncrement::with('user')->findOrFail($id);
        return view('admin.payroll.letters.increment.print', compact('increment'));
    }

    // ============ Increment ============
    public function incrementIndex()
    {
        $increments = EmployeeIncrement::with(['user', 'approver'])->latest()->paginate(20);
        return view('admin.payroll.letters.increment.index', compact('increments'));
    }

    public function incrementCreate()
    {
        $employees = User::where('customer', 1)->where('employee_status', 'active')->filterByType('employee')->get();
        return view('admin.payroll.letters.increment.create', compact('employees'));
    }

    public function incrementStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'increment_date' => 'required|date',
            'previous_salary' => 'required|numeric',
            'increment_amount' => 'required|numeric',
        ]);

        $newSalary = $request->previous_salary + $request->increment_amount;
        $percentage = ($request->increment_amount / $request->previous_salary) * 100;

        EmployeeIncrement::create([
            'user_id' => $request->user_id,
            'increment_date' => $request->increment_date,
            'previous_salary' => $request->previous_salary,
            'increment_amount' => $request->increment_amount,
            'increment_percentage' => round($percentage, 2),
            'new_salary' => $newSalary,
            'remarks' => $request->remarks,
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('admin.letters.increment.index')->with('success', 'Increment created!');
    }

    public function incrementEdit($id)
    {
        $increment = EmployeeIncrement::findOrFail($id);
        $employees = User::where('customer', 1)->where('employee_status', 'active')->filterByType('employee')->get();
        return view('admin.payroll.letters.increment.edit', compact('increment', 'employees'));
    }

    public function incrementUpdate(Request $request, $id)
    {
        $increment = EmployeeIncrement::findOrFail($id);

        $request->validate([
            'user_id' => 'required',
            'increment_date' => 'required|date',
            'previous_salary' => 'required|numeric',
            'increment_amount' => 'required|numeric',
        ]);

        $newSalary = $request->previous_salary + $request->increment_amount;
        $percentage = ($request->increment_amount / $request->previous_salary) * 100;

        $increment->update([
            'user_id' => $request->user_id,
            'increment_date' => $request->increment_date,
            'previous_salary' => $request->previous_salary,
            'increment_amount' => $request->increment_amount,
            'increment_percentage' => round($percentage, 2),
            'new_salary' => $newSalary,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.letters.increment.index')->with('success', 'Increment updated!');
    }

    public function incrementShow($id)
    {
        $increment = EmployeeIncrement::with('user')->findOrFail($id);
        return view('admin.payroll.letters.increment.show', compact('increment'));
    }

    public function incrementDestroy($id)
    {
        EmployeeIncrement::findOrFail($id)->delete();
        return back()->with('success', 'Increment deleted!');
    }
}
