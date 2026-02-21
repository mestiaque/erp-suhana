<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Iron extends Model
{
    use HasFactory;

    protected $table = 'irons';

    protected $guarded = [];

    protected $casts = [
        'iron_date' => 'date',
        'iron_qty' => 'integer',
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
