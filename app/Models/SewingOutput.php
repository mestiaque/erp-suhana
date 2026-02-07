<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class SewingOutput extends Model
{
    use HasFactory;
    use ActivityLoggable;

    protected $guarded = [];

    // columns
    // id
    // planning_id
    // sewing_id
    // floor_name
    // line_name
    // style_no
    // capacity_hour
    // date
    // hour
    // production
    // addedby_id
    // editedby_id
    // created_at
    // updated_at

    protected $casts = [
        'created_date' => 'date',
    ];

    // Relationships

    public function style()
    {
        return $this->belongsTo(OrderDetail::class,'style_no','style_no');
    }

    public function addedby()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

}
