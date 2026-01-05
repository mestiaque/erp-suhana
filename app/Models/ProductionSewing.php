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
        return $this->belongsTo(ProductionPlanning::class, 'planning_id', 'id');
    }

    // Example helper
    public function isBreakHour($hour) {
        $breakHours = [13]; // Example: 1 PM is break
        return in_array($hour, $breakHours);
    }

    public function outputs()
    {
        return $this->hasMany(SewingOutput::class, 'sewing_id');
    }

    public function getProductionHour($hour, $date = null)
    {
        $date = $date ?? date('Y-m-d');

        return $this->outputs()
            ->where('hour', $hour)
            ->where('date', $date)
            ->value('production') ?? 0;
    }


}
