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
        $holidays         = \ME\Hr\Models\Holiday::orderBy('from_date')->get();
        return [
            'classifications' => $classifications,
            'departments'     => $departments,
            'sections'        => $sections,
            'subSections'     => $subSections,
            'designations'    => $designations,
            'shifts'          => $shifts,
            'workingPlaces'   => $workingPlaces,
            'lines'           => $lines,
            'holidays'        => $holidays,
        ];
    }

    public static function getOptionsForEmployee(): callable
    {
        $options = self::getOptions();
        return function ($employee, $request = null, $factory = null, $salaryKey = null, $profile = null, $nominee = null) use ($options) {
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
            $gender = $isBangla ? \ME\Hr\Models\Location::where('type', 'sex')->where('name', $employee->gender)->first()->bn_name : $employee->gender ?? $na;
            $designationModel = optional($employee->designation);
            $designationAttr = optional(\ME\Hr\Models\Designation::find($employee->designation_id));
            $grade = $designationAttr->grade_id ?? $designationModel->grade_id ?? data_get($employee, 'designation_grade') ?? $na;
            $designation = $isBangla
                ? ($designationModel->bn_name ?? data_get($designationAttr, 'bn_name') ?? $designationModel->name ?? data_get($designationAttr, 'name') ?? data_get($employee, 'designation_bn_name') ?? data_get($employee, 'designation_name') ?? $na)
                : ($designationModel->name ?? data_get($designationAttr, 'name') ?? data_get($employee, 'designation_name') ?? $designationModel->bn_name ?? data_get($designationAttr, 'bn_name') ?? data_get($employee, 'designation_bn_name') ?? $na);

            $sectionAttr = optional(\App\Models\Attribute::where('type', 29)->find($employee->section_id));
            $section = $isBangla
                ? (data_get($sectionAttr, 'bn_name') ?? data_get($sectionAttr, 'name') ?? data_get($employee, 'section_bn_name') ?? data_get($employee, 'section_name') ?? $na)
                : (data_get($sectionAttr, 'name') ?? data_get($employee, 'section_name') ?? data_get($sectionAttr, 'bn_name') ?? data_get($employee, 'section_bn_name') ?? $na);
            $subSections = $options['subSections'] ?? collect();
            $subSectionId = $employee->otherInfo()['profile']['sub_section_id'] ?? null;
            $subSection = $subSections->where('id', $subSectionId)->first();
            $subSection = $isBangla ? optional($subSection)->bn_name : optional($subSection)->name;
            $line = $isBangla
                ? optional($options['lines']->where('id', $employee->line_number)->first())->bn_name
                : optional($options['lines']->where('id', $employee->line_number)->first())->slug;
            $workingPlaces = $options['workingPlaces'] ?? collect();
            $workingPlace = $workingPlaces->where('id', $employee->otherInfo()['profile']['working_place_id'] ?? null)->first();
            $workingPlace = $isBangla
                ? optional($workingPlace)->bn_name
                : optional($workingPlace)->name;
            $masterData = $options;
            $employeeOthers = method_exists($employee, 'otherInfo') ? $employee->otherInfo() : [];
            $departments = $masterData['departments'] ?? collect();
            $department = $departments->where('id', $employee->department_id)->first();
            $department = $isBangla
                ? optional($department)->bn_name
                : optional($department)->name;
            $jobType = $isBangla
                ? optional($masterData['classifications']->where('id', $employee->employee_type)->first())->bn_name
                : optional($masterData['classifications']->where('id', $employee->employee_type)->first())->name;
            $employeeId = data_get($employee, 'employee_id', $na);

            $presentAddress = collect([
                data_get($employee, 'present_address'),
                data_get($employee, 'present_village'),
                data_get($employee, 'present_post_office'),
                data_get($employee, 'present_upazila'),
                data_get($employee, 'present_district'),
            ])->filter(fn ($v) => filled($v))->implode(', ');
            $permanentAddress = collect([
                data_get($employee, 'permanent_address'),
                data_get($employee, 'permanent_village'),
                data_get($employee, 'permanent_post_office'),
                data_get($employee, 'permanent_upazila'),
                data_get($employee, 'permanent_district'),
            ])->filter(fn ($v) => filled($v))->implode(', ');
            $presentAddressBn = collect([
                data_get($employee, 'present_address_bn'),
                data_get($employee, 'present_village_bn'),
                data_get($employee, 'present_post_office_bn'),
                data_get($employee, 'present_upazila_bn'),
                data_get($employee, 'present_district_bn'),
            ])->filter(fn ($v) => filled($v))->implode(', ');
            $permanentAddressBn = collect([
                data_get($employee, 'permanent_address_bn'),
                data_get($employee, 'permanent_village_bn'),
                data_get($employee, 'permanent_post_office_bn'),
                data_get($employee, 'permanent_upazila_bn'),
                data_get($employee, 'permanent_district_bn'),
            ])->filter(fn ($v) => filled($v))->implode(', ');
            $presentAddress = $presentAddress ?: data_get($employee, 'address', $na);
            $permanentAddress = $permanentAddress ?: data_get($employee, 'address', $na);
            // Present Address (EN)
            $presentAddressFull = collect([
                    'Address' => data_get($employee, 'present_address'),
                    'Village' => data_get($employee, 'present_village'),
                    'Post Office' => data_get($employee, 'present_post_office'),
                    'Upazila' => data_get($employee, 'present_upazila'),
                    'District' => data_get($employee, 'present_district'),
                ])
                ->filter(fn ($v) => filled($v))
                ->map(fn ($v, $key) => "{$key}: {$v}")
                ->implode(', ');

            // Permanent Address (EN)
            $permanentAddressFull = collect([
                    'Address' => data_get($employee, 'permanent_address'),
                    'Village' => data_get($employee, 'permanent_village'),
                    'Post Office' => data_get($employee, 'permanent_post_office'),
                    'Upazila' => data_get($employee, 'permanent_upazila'),
                    'District' => data_get($employee, 'permanent_district'),
                ])
                ->filter(fn ($v) => filled($v))
                ->map(fn ($v, $key) => "{$key}: {$v}")
                ->implode(', ');

            // Present Address (BN)
            $presentAddressBnFull = collect([
                    'ঠিকানা' => data_get($employee, 'present_address_bn'),
                    'গ্রাম' => data_get($employee, 'present_village_bn'),
                    'ডাকঘর' => data_get($employee, 'present_post_office_bn'),
                    'উপজেলা' => data_get($employee, 'present_upazila_bn'),
                    'জেলা' => data_get($employee, 'present_district_bn'),
                ])
                ->filter(fn ($v) => filled($v))
                ->map(fn ($v, $key) => "{$key}: {$v}")
                ->implode(', ');


            // Permanent Address (BN)
            $permanentAddressBnFull = collect([
                    'ঠিকানা' => data_get($employee, 'permanent_address_bn'),
                    'গ্রাম' => data_get($employee, 'permanent_village_bn'),
                    'ডাকঘর' => data_get($employee, 'permanent_post_office_bn'),
                    'উপজেলা' => data_get($employee, 'permanent_upazila_bn'),
                    'জেলা' => data_get($employee, 'permanent_district_bn'),
                ])
                ->filter(fn ($v) => filled($v))
                ->map(fn ($v, $key) => "{$key}: {$v}")
                ->implode(', ');

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

            $qualification = data_get($employee, 'qualification', data_get($profile, 'qualification', $na));
            $birthDate = data_get($employee, 'date_of_birth', data_get($employee, 'dob'));
            $employeeAge = '';
            if (filled($birthDate)) {
                try {
                    $employeeAge = \Illuminate\Support\Carbon::parse($birthDate)->age;
                } catch (\Throwable $e) {
                    $employeeAge = '';
                }
            }
            $employeePhoto = method_exists($employee, 'image') ? $employee->image() : null;

            $shifts = \ME\Hr\Models\Shift::orderBy('name_of_shift')->get();
            $shift = $shifts->where('id', $employee->shift_id)->first();


            // Nominee fields (EN & BN)
            $nomineeName = data_get($employee, 'nominee', data_get($nominee, 'nominee', $na));
            $nomineeNameBn = data_get($employee, 'nominee_bn_name', data_get($nominee, 'nominee_bn_name', $na));
            $nomineeRelation = data_get($employee, 'nominee_relation', data_get($nominee, 'nominee_relation', $na));
            $nomineeRelationBn = data_get($employee, 'nominee_relation_bn', data_get($nominee, 'nominee_relation_bn', $na));
            $nomineeAge = data_get($employee, 'nominee_age', data_get($nominee, 'nominee_age', ''));
            $nomineeVillage = data_get($nominee, 'nominee_village', $na);
            $nomineeVillageBn = data_get($nominee, 'nominee_village_bn', $na);
            $nomineePoStation = data_get($nominee, 'nominee_po_station', $na);
            $nomineePoStationBn = \App\Models\Country::where('name', $nomineePoStation)->where('type', 4)->first()->bn_name ?? $na;
            $nomineePostOffice = data_get($nominee, 'nominee_post_office', $na);
            $nomineePostOfficeBn = data_get($nominee, 'nominee_post_office_bn', $na);
            $nomineeDistrict = data_get($nominee, 'nominee_district', $na);
            $nomineeDistrictBn = \App\Models\Country::where('name', $nomineeDistrict)->where('type', 3)->first()->bn_name ?? $na;
            $nomineeNid = data_get($nominee, 'nominee_nid', $na);
            $nomineeMobile = data_get($nominee, 'nominee_mobile', $na);
            $nomineeImage = data_get($nominee, 'nominee_image', null);
            $nationality = data_get($nominee, 'nominee_nationality', data_get($employee, 'nationality', $t('বাংলাদেশী', 'Bangladeshi')));
            $permanentAddress = collect([
                data_get($employee, 'permanent_village'),
                data_get($employee, 'permanent_post_office'),
                data_get($employee, 'permanent_upazila'),
                data_get($employee, 'permanent_district'),
            ])->filter(fn ($value) => filled($value))->implode(', ');
            $presentAddress = collect([
                data_get($employee, 'present_village'),
                data_get($employee, 'present_post_office'),
                data_get($employee, 'present_upazila'),
                data_get($employee, 'present_district'),
            ])->filter(fn ($value) => filled($value))->implode(', ');
            $permanentAddress = $permanentAddress ?: data_get($employee, 'permanent_address', data_get($employee, 'address', $na));
            $presentAddress = $presentAddress ?: data_get($employee, 'present_address', data_get($employee, 'address', $na));

            // --- Salary/Earnings/Deductions/Leaves/Increments Logic ---
            $other = json_decode($employee->other_information ?? '{}', true);
            $earningsDeductions = data_get($other, 'earnings_deductions', []);
            $increments = data_get($other, 'increments', []);
            $leaves = data_get($other, 'leaves', []);

            // Earnings/Deductions summary logic (for a date range)
            $getEarningsDeductionsSummary = function($from = null, $to = null) use ($earningsDeductions) {
                $earnings    = 0.0;
                $deductions  = 0.0;
                $advanceIou  = 0.0;
                $otPlusHours = 0.0;
                $otMinusHours = 0.0;
                $dayPlus     = 0.0;
                $dayMinus    = 0.0;
                foreach ($earningsDeductions as $entry) {
                    $date = data_get($entry, 'date');
                    if ($from && $to && ($date < $from || $date > $to)) continue;
                    $earnings   += (float) data_get($entry, 'earnings',    0);
                    $deductions += (float) data_get($entry, 'deductions',  0);
                    $advanceIou += (float) data_get($entry, 'advance_iou', 0);
                    $otHours = (float) data_get($entry, 'ot', 0);
                    if ($otHours >= 0) {
                        $otPlusHours += $otHours;
                    } else {
                        $otMinusHours += abs($otHours);
                    }
                    $days = (float) data_get($entry, 'day', 0);
                    if ($days >= 0) {
                        $dayPlus += $days;
                    } else {
                        $dayMinus += abs($days);
                    }
                }
                return compact('earnings', 'deductions', 'advanceIou', 'otPlusHours', 'otMinusHours', 'dayPlus', 'dayMinus');
            };

            // Increments logic
            $getIncrements = function() use ($increments) {
                return $increments;
            };

            // Leaves logic
            $getLeaves = function() use ($leaves) {
                return $leaves;
            };

            // Salary report logic (aggregate salary, earnings, deductions, etc.)
            $getSalaryReport = function($from = null, $to = null) use ($employee, $getEarningsDeductionsSummary) {
                $sal = function_exists('hr_employee_salary') ? hr_employee_salary($employee) : [];
                $otRate = (float) ($sal['ot_rate'] ?? 0);
                $gross = (float) ($sal['gross'] ?? $employee->gross_salary ?? 0);
                $basic = (float) ($sal['basic'] ?? $employee->basic_salary ?? 0);
                $deductFrom = (string) ($sal['deduct_from'] ?? 'gross');
                $dayBase = $deductFrom === 'basic' ? $basic : $gross;
                $dayRate = $dayBase > 0 ? ($dayBase / 30) : 0;
                $extras = $getEarningsDeductionsSummary($from, $to);
                $otEarn   = $extras['otPlusHours'] * $otRate;
                $otDeduct = $extras['otMinusHours'] * $otRate;
                $dayEarn  = $extras['dayPlus'] * $dayRate;
                $dayDeduct = $extras['dayMinus'] * $dayRate;
                $extraEarningAmount = $extras['earnings'] + $extras['advanceIou'] + $otEarn + $dayEarn;
                $extraDeductionAmount = $extras['deductions'] + $otDeduct + $dayDeduct;
                $totalEarn   = $extraEarningAmount;
                $totalDeduct = $extraDeductionAmount;
                $net         = $gross + $totalEarn - $totalDeduct;
                return [
                    'gross'        => $gross,
                    'basic'        => $basic,
                    'total_earn'   => $totalEarn,
                    'total_deduct' => $totalDeduct,
                    'net'          => $net,
                    'ot'           => $otEarn - $otDeduct,
                ];
            };

            return [
                'company_name'              => $companyName,
                'company_address'           => $companyAddress,
                'employee_name'             => $employeeName,
                'father_name'               => $fatherName,
                'mother_name'               => $motherName,
                'spouse_name'               => $spouseName,
                'joining_date'              => $joiningDate,
                'designation'               => $designation,
                'designation_full'          => $designationAttr,
                'department'                => $department,
                'grade'                     => $grade,
                'section'                   => $section,
                'shift'                     => $shift,
                'line'                      => $line,
                'working_place'             => $workingPlace,
                'sub_section'               => $subSection,
                'job_type'                  => $jobType,
                'employee_id'               => $employeeId,
                'present_address'           => $presentAddress,
                'permanent_address'         => $permanentAddress,
                'present_address_bn'        => $presentAddressBn,
                'permanent_address_bn'      => $permanentAddressBn,
                'present_address_full'      => $presentAddressFull,
                'permanent_address_full'    => $permanentAddressFull,
                'present_address_bn_full'   => $presentAddressBnFull,
                'permanent_address_bn_full' => $permanentAddressBnFull,
                'qualification'             => $qualification,
                'birth_date'                => $birthDate,
                'employee_age'              => $employeeAge,
                'employee_photo'            => $employeePhoto,
                'nominee_name'              => $nomineeName,
                'nominee_name_bn'           => $nomineeNameBn,
                'nominee_relation'          => $nomineeRelation,
                'nominee_relation_bn'       => $nomineeRelationBn,
                'nominee_age'               => $nomineeAge,
                'nominee_village'           => $nomineeVillage,
                'nominee_village_bn'        => $nomineeVillageBn,
                'nominee_po_station'        => $nomineePoStation,
                'nominee_po_station_bn'     => $nomineePoStationBn,
                'nominee_post_office'       => $nomineePostOffice,
                'nominee_post_office_bn'    => $nomineePostOfficeBn,
                'nominee_district'          => $nomineeDistrict,
                'nominee_district_bn'       => $nomineeDistrictBn,
                'nominee_nid'               => $nomineeNid,
                'nominee_mobile'            => $nomineeMobile,
                'nominee_image'             => $nomineeImage,
                'nationality'               => $nationality,
                'salary'                    => [
                    'gross'       => $gross,
                    'basic'       => $basic,
                    'house'       => $house,
                    'medical'     => $medical,
                    'transport'   => $transport,
                    'food'        => $food,
                    'ot_rate'     => $otRate,
                    'deduct_from' => $deductFrom,
                ],
                'others' => $employeeOthers,
                  // Global helpers for reports and pages
                'getEarningsDeductionsSummary' => $getEarningsDeductionsSummary,
                'getIncrements'                => $getIncrements,
                'getLeaves'                    => $getLeaves,
                'getSalaryReport'              => $getSalaryReport,
                'gender'                       => $gender,
                  // Optionally add more fields as needed
            ];
        };
    }
}
