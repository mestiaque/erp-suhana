<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class ProductionSewing extends Model
{
    use HasFactory;
    use ActivityLoggable;

    protected $guarded = [];

    protected $casts = [
        'created_date' => 'date',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }


    public function planning() {
        return $this->hasMany(ProductionPlanning::class, 'style_no', 'style_no');
    }

    // Example helper
    public function isBreakHour($hour) {
        $breakHours = [13]; // Example: 1 PM is break
        return in_array($hour, $breakHours);
    }

    public function getProductionHour($hour)
    {
        $date = date('Y-m-d'); // Today
        $data = $this->production ? json_decode($this->production, true) : [];

        return isset($data[$date][$hour]) ? $data[$date][$hour] : 0;
    }


}
