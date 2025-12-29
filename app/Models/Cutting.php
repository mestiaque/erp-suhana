<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cutting extends Model
{
    use HasFactory;

    protected $table = 'cuttings';

    protected $guarded = [];

    // তারিখগুলোকে অটোমেটিক কাস্ট করার জন্য (২০২৫ স্ট্যান্ডার্ড)
    protected $casts = [
        'cutting_date' => 'date',
        'cutting_qty' => 'integer',
        'reject_qty' => 'integer',
    ];

    public function pi()
    {
        return $this->belongsTo(ProformaInvoice::class, 'pi_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
