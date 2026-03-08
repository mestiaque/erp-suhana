<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class ExportRealization extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'realization_no', 'export_lc_id', 'lc_no', 'buyer_id', 'buyer_name',
        'submission_date', 'realization_date', 'bank_name', 'bank_branch',
        'invoice_value', 'realized_value', 'discount', 'bank_charges', 'net_realized',
        'currency', 'exchange_rate', 'realized_in_bdt',
        'status', 'remarks', 'created_by', 'edited_by'
    ];

    protected $casts = [
        'submission_date' => 'date', 'realization_date' => 'date',
        'invoice_value' => 'decimal:2', 'realized_value' => 'decimal:2',
        'discount' => 'decimal:2', 'bank_charges' => 'decimal:2', 'net_realized' => 'decimal:2',
        'exchange_rate' => 'decimal:2', 'realized_in_bdt' => 'decimal:2'
    ];

    public function exportLc() { return $this->belongsTo(ExportLc::class, 'export_lc_id'); }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'edited_by'); }

    public static function generateRealizationNo() {
        $prefix = 'REAL-' . date('Ymd');
        $last = self::where('realization_no', 'like', $prefix . '%')->orderBy('realization_no', 'desc')->first();
        $num = $last ? (int)substr($last->realization_no, -4) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute() {
        return [1 => 'Pending', 2 => 'Partially Realized', 3 => 'Fully Realized'][$this->status] ?? 'Unknown';
    }
}
