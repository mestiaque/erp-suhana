<?php

namespace App\Models\payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name_of_shift',
        'name_of_shift_bn',
        'shift_starting_time',
        'red_marking_on',
        'shift_closing_time',
        'shift_closing_time_next_day',
        'over_time_allowed_up_to',
        'over_time_allowed_up_to_next_day',
        'over_time_1_allowed_up_to',
        'over_time_1_allowed_up_to_next_day',
        'card_accept_from',
        'card_accept_to',
        'card_accept_to_next_day',
        'meal_option',
        'tiffin_allowance',
        'no_lunch_hour_holiday',
        'dinner_allowance',
        'dinner_count_option',
        'double_shift',
        'weekly_overtime_allowed',
        'weekly_ot_sat',
        'weekly_ot_sun',
        'weekly_ot_mon',
        'weekly_ot_tue',
        'weekly_ot_wed',
        'weekly_ot_thu',
        'status',
        'addedby_id',
    ];

    /**
     * Casts for proper types
     */
    protected $casts = [
        'shift_closing_time_next_day' => 'boolean',
        'over_time_allowed_up_to_next_day' => 'boolean',
        'over_time_1_allowed_up_to_next_day' => 'boolean',
        'card_accept_to_next_day' => 'boolean',
        'no_lunch_hour_holiday' => 'boolean',
        'dinner_allowance' => 'boolean',
        'double_shift' => 'boolean',
        'tiffin_allowance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Optional: Scope to get only active shifts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Optional: Scope to get shifts by meal option
     */
    public function scopeMealOption($query, $option)
    {
        return $query->where('meal_option', $option);
    }
}
