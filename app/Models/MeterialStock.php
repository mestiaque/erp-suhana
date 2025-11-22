<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterialStock extends Model
{
    
    public function branch()
    {
        return $this->belongsTo(Attribute::class, 'branch_id')
                    ->where('type', 0);
    }

}
