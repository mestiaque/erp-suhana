<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SampleItem extends Model
{
    use HasFactory;

    protected $table = 'sample_items';

    protected $guarded = [];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'sample_id');
    }
}
