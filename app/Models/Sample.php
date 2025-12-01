<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sample extends Model
{
    use HasFactory;

    protected $table = 'sample';

    protected $fillable = [
        'buyer_id',
        'buyer_name',
        'style',
        'status',
        'type',
        'created_by'
    ];

    public function items()
    {
        return $this->hasMany(SampleItem::class, 'sample_id');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
