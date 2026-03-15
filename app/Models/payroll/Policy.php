<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'value',
        'unit',
        'description',
        'status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    /**
     * Policy Types:
     * - late_deduction_per_minute: Deduction per minute of late
     * - late_deduction_fixed: Fixed deduction per late occurrence
     * - absent_deduction_percentage: Deduction percentage of daily salary per absent
     * - late_count_for_absent: Number of lates that count as 1 absent (e.g., 3)
     * - grace_time_minutes: Grace period before counting as late
     * - overtime_rate_general: Rate per hour for general overtime
     * - overtime_rate_special: Rate per hour for special overtime
     * - provident_fund_percentage: PF percentage
     * - tax_exempt_limit: Tax exemption limit
     * - working_hours_per_day: Standard working hours per day
     * - late_threshold_minutes: Minutes after which considered late
     */

    /**
     * Get policy value by type
     */
    public static function getValue($type, $default = 0)
    {
        $policy = self::where('type', $type)->where('status', 'active')->first();
        return $policy ? $policy->value : $default;
    }

    /**
     * Get late deduction per occurrence
     * Returns fixed amount or per-minute calculation
     */
    public static function getLateDeduction($lateMinutes = 0)
    {
        // Check for per-minute deduction first
        $perMinute = self::getValue('late_deduction_per_minute', 0);
        if ($perMinute > 0) {
            return $lateMinutes * $perMinute;
        }

        // Otherwise use fixed deduction
        return self::getValue('late_deduction_fixed', 0);
    }

    /**
     * Get absent deduction (percentage of daily salary)
     */
    public static function getAbsentDeductionPercentage()
    {
        return self::getValue('absent_deduction_percentage', 100); // Default 100% of daily salary
    }

    /**
     * Get number of lates that count as 1 absent
     */
    public static function getLateCountForAbsent()
    {
        return (int) self::getValue('late_count_for_absent', 3); // Default 3 lates = 1 absent
    }

    /**
     * Get grace time in minutes
     */
    public static function getGraceTimeMinutes()
    {
        return (int) self::getValue('grace_time_minutes', 10); // Default 10 minutes
    }

    /**
     * Get general overtime rate
     */
    public static function getOvertimeRateGeneral()
    {
        return self::getValue('overtime_rate_general', 100);
    }

    /**
     * Get special overtime rate
     */
    public static function getOvertimeRateSpecial()
    {
        return self::getValue('overtime_rate_special', 200);
    }

    /**
     * Get provident fund percentage
     */
    public static function getProvidentFundPercentage()
    {
        return self::getValue('provident_fund_percentage', 5); // Default 5%
    }

    /**
     * Get working hours per day
     */
    public static function getWorkingHoursPerDay()
    {
        return self::getValue('working_hours_per_day', 8);
    }

    /**
     * Get late threshold minutes
     */
    public static function getLateThresholdMinutes()
    {
        return (int) self::getValue('late_threshold_minutes', 15); // Default 15 minutes
    }
}
