<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class BankBtbLc extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'lc_no', 'supplier_id', 'supplier_name', 'supplier_address', 'supplier_contact',
        'lc_open_date', 'lc_expiry_date', 'shipment_date', 'delivery_date',
        'bank_id', 'bank_name', 'branch_name', 'lc_value', 'used_value', 'remaining_value',
        'currency', 'exchange_rate', 'lc_value_bdt', 'status', 'remarks', 'created_by', 'edited_by'
    ];

    protected $casts = [
        'lc_open_date' => 'date', 'lc_expiry_date' => 'date', 'shipment_date' => 'date', 'delivery_date' => 'date',
        'lc_value' => 'decimal:2', 'used_value' => 'decimal:2', 'remaining_value' => 'decimal:2',
        'exchange_rate' => 'decimal:2', 'lc_value_bdt' => 'decimal:2'
    ];

    public function supplier() { return $this->belongsTo(User::class, 'supplier_id'); }
    public function bank() { return $this->belongsTo(User::class, 'bank_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'edited_by'); }

    public static function generateLcNo() {
        $prefix = 'BTB-LC-' . date('Ymd');
        $last = self::where('lc_no', 'like', $prefix . '%')->orderBy('lc_no', 'desc')->first();
        $num = $last ? (int)substr($last->lc_no, -4) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute() {
        return [1 => 'Pending', 2 => 'Active', 3 => 'Closed', 4 => 'Cancelled'][$this->status] ?? 'Unknown';
    }
}
