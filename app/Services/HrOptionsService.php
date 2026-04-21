<?php

namespace App\Services;

class HrOptionsService
{
    public static function getOptions(): array
    {
        // Use Eloquent models directly for now; can be optimized/cached later
        $classifications = \App\Models\Attribute::where('type', 16)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name', 'bn_name']);
        $departments     = \App\Models\Attribute::where('type', 3)->where('status', '<>' ,'temp')->orderBy('name')->get(['id', 'name', 'bn_name']);
        $sections        = \App\Models\Attribute::where('type', 29)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name', 'bn_name']);
        $subSections     = \ME\Hr\Models\SubSection::orderBy('name')->get(['id', 'name', 'department_id', 'section_id', 'salary_type', 'approve_man_power', 'bn_name']);
        $designations    = \ME\Hr\Models\Designation::orderBy('name')->get(['id', 'name', 'bn_name']);
        $shifts          = \ME\Hr\Models\Shift::orderBy('name_of_shift')->get(['id', 'name_of_shift']);
        $workingPlaces   = \ME\Hr\Models\WorkingPlace::orderBy('name')->get(['id', 'name', 'bn_name']);
        $lines           = \App\Models\Attribute::where('type', 4)->where('status', '<>', 'temp')->orderBy('name')->get(['id', 'name', 'bn_name', 'slug']);
        return [
            'classifications' => $classifications,
            'departments'     => $departments,
            'sections'        => $sections,
            'subSections'     => $subSections,
            'designations'    => $designations,
            'shifts'          => $shifts,
            'workingPlaces'   => $workingPlaces,
            'lines'           => $lines,
        ];
    }

    public static function getOptionsForEmployee(): callable
    {
        $options = self::getOptions();
        return function ($employee, $request = null, $factory = null, $salaryKey = null) use ($options) {
            $language = data_get($request ?? null, 'language', 'bn');
            $isBangla = $language === 'bn';
            $t = fn (string $bn, string $en) => $isBangla ? $bn : $en;
            $na = $t('প্রযোজ্য নয়', 'N/A');

            $companyName = $isBangla
                ? (hr_factory('bn_name') ?? hr_factory('name') ?? general()->name ?? $na)
                : (hr_factory('name') ?? general()->name ?? hr_factory('bn_name') ?? $na);
            $companyAddress = $isBangla
                ? (hr_factory('bn_address') ?? hr_factory('address') ?? general()->address ?? $na)
                : (hr_factory('address') ?? general()->address ?? hr_factory('bn_address') ?? $na);

            $employeeName = $isBangla
                ? (data_get($employee, 'bn_name') ?? data_get($employee, 'name') ?? $na)
                : (data_get($employee, 'name') ?? data_get($employee, 'bn_name') ?? $na);
            $fatherName = $isBangla ? data_get($employee, 'father_name_bn', $na) : data_get($employee, 'father_name', $na);
            $motherName = $isBangla ? data_get($employee, 'mother_name_bn', $na) : data_get($employee, 'mother_name', $na);
            $spouseName = $isBangla ? data_get($employee, 'spouse_name_bn', $na) : data_get($employee, 'spouse_name', $na);
            $joiningDate = blank($employee->joining_date) ? $na : bn_date($employee->joining_date, 'd/m/Y');

            $designationModel = optional($employee->designation);
            $designationAttr = optional(\ME\Hr\Models\Designation::find($employee->designation_id));
            $grade = $designationAttr->grade ?? $designationModel->grade ?? data_get($employee, 'designation_grade') ?? $na;
            $designation = $isBangla
                ? ($designationModel->bn_name ?? data_get($designationAttr, 'bn_name') ?? $designationModel->name ?? data_get($designationAttr, 'name') ?? data_get($employee, 'designation_bn_name') ?? data_get($employee, 'designation_name') ?? $na)
                : ($designationModel->name ?? data_get($designationAttr, 'name') ?? data_get($employee, 'designation_name') ?? $designationModel->bn_name ?? data_get($designationAttr, 'bn_name') ?? data_get($employee, 'designation_bn_name') ?? $na);

            $sectionAttr = optional(\App\Models\Attribute::where('type', 29)->find($employee->section_id));
            $section = $isBangla
                ? (data_get($sectionAttr, 'bn_name') ?? data_get($sectionAttr, 'name') ?? data_get($employee, 'section_bn_name') ?? data_get($employee, 'section_name') ?? $na)
                : (data_get($sectionAttr, 'name') ?? data_get($employee, 'section_name') ?? data_get($sectionAttr, 'bn_name') ?? data_get($employee, 'section_bn_name') ?? $na);

            $masterData = $options;
            $employeeOthers = method_exists($employee, 'otherInfo') ? $employee->otherInfo() : [];

            $jobType = $isBangla
                ? optional($masterData['classifications']->where('id', $employee->employee_type)->first())->bn_name
                : optional($masterData['classifications']->where('id', $employee->employee_type)->first())->name;
            $employeeId = data_get($employee, 'employee_id', $na);

            $presentAddress = collect([
                data_get($employee, 'present_address_bn'),
                data_get($employee, 'present_village_bn'),
                data_get($employee, 'present_post_office_bn'),
                data_get($employee, 'present_upazila_bn'),
                data_get($employee, 'present_district_bn'),
            ])->filter(fn ($v) => filled($v))->implode(', ');
            $permanentAddress = collect([
                data_get($employee, 'permanent_address_bn'),
                data_get($employee, 'permanent_village_bn'),
                data_get($employee, 'permanent_post_office_bn'),
                data_get($employee, 'permanent_upazila_bn'),
                data_get($employee, 'permanent_district_bn'),
            ])->filter(fn ($v) => filled($v))->implode(', ');
            $presentAddress = $presentAddress ?: data_get($employee, 'address', $na);
            $permanentAddress = $permanentAddress ?: data_get($employee, 'address', $na);

            // Salary breakdown
            $sal        = function_exists('hr_employee_salary') ? hr_employee_salary($employee, $factory ?? null, $salaryKey ?? null) : [];
            $gross      = $sal['gross'] ?? null;
            $basic      = $sal['basic'] ?? null;
            $house      = $sal['house'] ?? null;
            $medical    = $sal['medical'] ?? null;
            $transport  = $sal['transport'] ?? null;
            $food       = $sal['food'] ?? null;
            $otRate     = ($basic ?? 0) > 0 ? round(($basic / 208) * 2, 2) : 0;
            $deductFrom = $sal['deduct_from'] ?? null;

            return [
                'company_name'        => $companyName,
                'company_address'     => $companyAddress,
                'employee_name'       => $employeeName,
                'father_name'         => $fatherName,
                'mother_name'         => $motherName,
                'spouse_name'         => $spouseName,
                'joining_date'        => $joiningDate,
                'designation'         => $designation,
                'grade'               => $grade,
                'section'             => $section,
                'job_type'            => $jobType,
                'employee_id'         => $employeeId,
                'present_address'     => $presentAddress,
                'permanent_address'   => $permanentAddress,
                'salary'              => [
                    'gross'      => $gross,
                    'basic'      => $basic,
                    'house'      => $house,
                    'medical'    => $medical,
                    'transport'  => $transport,
                    'food'       => $food,
                    'ot_rate'    => $otRate,
                    'deduct_from'=> $deductFrom,
                ],
                'others'              => $employeeOthers,
                // Optionally add more fields as needed
            ];
        };
    }
}
