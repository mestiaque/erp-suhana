<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SampleItem extends Model
{
    use HasFactory;

    protected $table = 'sample_items';

    protected $fillable = [
        'sample_id',
        'composition',
        'gsm',
        'color',
        'size',
        'quantity',
        'comments'
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'sample_id');
    }
}
