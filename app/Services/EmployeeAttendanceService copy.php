<?php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use ME\Hr\Models\Leave;
use ME\Hr\Models\RegularToWeekend;

class EmployeeAttendanceService
{
    public static function getEmployeeAttendanceByDate($employeeId, $fromDate, $toDate,  $lang = 'en')
    {
        $employee = User::findOrFail($employeeId);
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);
        $dates = [];
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $dates[] = $date->copy();
        }
        // If not provided, fetch attendanceMap and holidays
        if (!$attendanceMap) {
            // You may want to optimize this for your app
            $attendanceMap = collect();
        }
        if (!$holidays) {
            $holidays = collect();
        }
        $allowOtHour = hr_factory('allow_ot_hour') ?? 2;
        $allowOtMin = $allowOtHour * 60;
        $getAtt = function($uid, $date) use ($attendanceMap) {
            return ($attendanceMap->get($uid . '_' . $date) ?? collect())->first();
        };
        $result = [];
        $empWeekend = strtolower($employee->otherInfo()['profile']['weekend'] ?? 'friday');
        foreach ($dates as $d) {
            $dateStr = $d->format('Y-m-d');
            $att = $getAtt($employee->id, $dateStr);
            // --- Status logic (from blade) ---
            $leave = Leave::where('employee_id', $employee->id)
                ->whereDate('start_date', '<=', $dateStr)
                ->whereDate('end_date', '>=', $dateStr)->first();
            $isHoliday = $holidays->contains(function($h) use ($dateStr) {
                return ($dateStr >= $h->from_date && $dateStr <= $h->to_date);
            });
            $dayOfWeek = strtolower($d->format('l'));
            $isRegularToWeekend = RegularToWeekend::where('section_id', $employee->section_id)
                ->where('date', $dateStr)
                ->where('type', 'weekend')
                ->where('is_active', 1)
                ->exists();
            $isWeekendToRegular = RegularToWeekend::where('section_id', $employee->section_id)
                ->where('date', $dateStr)
                ->where('type', 'regular')
                ->where('is_active', 1)
                ->exists();
            // Weekend logic for compliance OT:
            // For factory 1/2: even if weekend-to-regular, treat as weekend for compliance OT (do not count OT)
            // For factory null/0: treat as regular (count OT)
            $isWeekend = false;
            $isWeekendForCompliance = false;
            if ($dayOfWeek === $empWeekend && $isWeekendToRegular) {
                // Normally weekend, but set to regular
                $isWeekend = false;
                $isWeekendForCompliance = ($factoryNo == 1 || $factoryNo == 2) ? true : false;
            } elseif ($isRegularToWeekend || ($dayOfWeek === $empWeekend && !$isWeekendToRegular) || ($att && !empty($att->regular_to_weekend))) {
                $isWeekend = true;
                $isWeekendForCompliance = true;
            } else {
                $isWeekendForCompliance = false;
            }
            // Status
            $status = 'absent';
            $raw_status = null;
            $late = false;
            $early_exit = false;
            $punch_miss = false;
            $remarks = null;
            if ($leave) {
                $status = 'leave';
                $raw_status = 'L';
            } elseif ($isHoliday) {
                $status = 'holiday';
                $raw_status = 'GH';
            } elseif ($isWeekend) {
                $status = 'weekend';
                $raw_status = 'WO';
            } elseif ($att) {
                $raw_status = $att->status ?? null;
                $remarks = $att->remarks ?? null;
                if (!empty($att->punch_miss)) {
                    $status = 'punch_miss';
                    $punch_miss = true;
                } elseif (!empty($att->early_exit)) {
                    $status = 'early_exit';
                    $early_exit = true;
                } elseif (!empty($att->late)) {
                    $status = 'late';
                    $late = true;
                } elseif (!empty($att->status)) {
                    $status = strtolower($att->status);
                } else {
                    $status = 'present';
                }
            }
            // Shift info (if available)
            $shift = $att && isset($att->shift) ? $att->shift : null;
            $shiftName = $shift && isset($shift->name) ? $shift->name : null;
            // In/Out time
            $inTime = $att && $att->in_time ? $att->in_time : null;
            $outTime = $att && $att->out_time ? $att->out_time : null;
            // OT calculations
            $otMinRaw = $att ? (int)($att->overtime_minutes ?? 0) : 0;
            $actualOt = round($otMinRaw / 60, 2);
            $complianceOt = null;
            $extraOt = null;
            if ($factoryNo == 1) {
                // For factory 1, do not count OT on weekends (even if weekend-to-regular)
                if ($isWeekendForCompliance) {
                    $complianceOt = 0;
                } else {
                    $complianceOt = round(min($otMinRaw, $allowOtMin) / 60, 2);
                }
            } elseif ($factoryNo == 2) {
                // For factory 2, do not count OT on weekends (even if weekend-to-regular)
                if ($isWeekendForCompliance) {
                    $complianceOt = 0;
                    $extraOt = 0;
                } else {
                    $complianceOt = round(min($otMinRaw, $allowOtMin) / 60, 2);
                    $extraOt = $otMinRaw > $allowOtMin ? round(($otMinRaw - $allowOtMin) / 60, 2) : 0;
                }
            } else {
                // For factory null/0, always count OT (weekend-to-regular is regular)
                $complianceOt = $actualOt;
            }
            $result[] = [
                'date' => $d->format('d-m-Y'),
                'status' => $status,
                'raw_status' => $raw_status,
                'shift' => $shiftName,
                'day' => $d->format('l'),
                'in_time' => $inTime,
                'out_time' => $outTime,
                'actual_ot' => $actualOt,
                'compliance_ot' => $complianceOt,
                'extra_ot' => $extraOt,
                'late' => $late,
                'early_exit' => $early_exit,
                'punch_miss' => $punch_miss,
                'remarks' => $remarks,
            ];
        }
        return $result;
    }

    public static function getSectionWiseAttendance($employeeIds, $factoryNo, $fromDate, $toDate, $attendanceMap = null, $holidays = null)
    {
        $result = [];
        $employees = Employee::whereIn('id', $employeeIds)->get();
        foreach ($employees as $employee) {
            $sectionId = $employee->section_id;
            $result[$sectionId][$employee->id] = self::getEmployeeAttendanceByDate($employee->id, $factoryNo, $fromDate, $toDate, $attendanceMap, $holidays);
        }
        return $result;
    }
}
