<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditorBill extends Model
{
    use HasFactory;

    // Table name (optional, Laravel auto-detects 'creditor_bills')
    protected $table = 'creditor_bills';

    // Mass assignable fields
    protected $fillable = [
        'title',
        'creditor_id', // relation to users table
        'amount',
        'description',
        'created_by',  // user id who created the bill
    ];

    // Optional: cast amount as float
    protected $casts = [
        'amount' => 'float',
    ];

    // Optional: define relationship to User (Creditor)
    public function creditor()
    {
        return $this->belongsTo(User::class, 'creditor_id');
    }

    // Optional: define relationship to creator
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

