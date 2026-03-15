<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'from_date',
        'to_date',
        'remarks',
        'days',
        'status',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    /**
     * Check if a given date is a holiday
     */
    public static function isHoliday($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return self::where('status', 'active')
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->exists();
    }

    /**
     * Get the holiday for a given date
     */
    public static function getHoliday($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return self::where('status', 'active')
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->first();
    }
}
