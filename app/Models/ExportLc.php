<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class ExportLc extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'lc_no', 'buyer_id', 'buyer_name', 'buyer_address', 'buyer_contact',
        'lc_open_date', 'lc_expiry_date', 'shipment_date', 'issuing_bank', 'issuing_bank_branch',
        'negotiating_bank', 'lc_value', 'realized_value', 'pending_value', 'currency',
        'status', 'remarks', 'created_by', 'edited_by'
    ];

    protected $casts = [
        'lc_open_date' => 'date', 'lc_expiry_date' => 'date', 'shipment_date' => 'date',
        'lc_value' => 'decimal:2', 'realized_value' => 'decimal:2', 'pending_value' => 'decimal:2'
    ];

    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'edited_by'); }
    public function realizations() { return $this->hasMany(ExportRealization::class, 'export_lc_id'); }

    public static function generateLcNo() {
        $prefix = 'EXP-LC-' . date('Ymd');
        $last = self::where('lc_no', 'like', $prefix . '%')->orderBy('lc_no', 'desc')->first();
        $num = $last ? (int)substr($last->lc_no, -4) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute() {
        return [1 => 'Pending', 2 => 'Partially Realized', 3 => 'Fully Realized', 4 => 'Expired'][$this->status] ?? 'Unknown';
    }
}
