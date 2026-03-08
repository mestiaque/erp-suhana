<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class CommercialProformaInvoice extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'pi_no', 'buyer_id', 'buyer_name', 'buyer_address', 'buyer_contact',
        'pi_date', 'valid_until', 'payment_terms', 'delivery_terms',
        'shipment_from', 'shipment_to', 'total_qty', 'total_amount',
        'commission', 'net_amount', 'currency', 'status', 'remarks', 'created_by', 'edited_by'
    ];

    protected $casts = [
        'pi_date' => 'date', 'valid_until' => 'date',
        'total_qty' => 'decimal:2', 'total_amount' => 'decimal:2',
        'commission' => 'decimal:2', 'net_amount' => 'decimal:2'
    ];

    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'edited_by'); }

    public static function generatePiNo() {
        $prefix = 'CPI-' . date('Ymd');
        $last = self::where('pi_no', 'like', $prefix . '%')->orderBy('pi_no', 'desc')->first();
        $num = $last ? (int)substr($last->pi_no, -4) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute() {
        return [1 => 'Draft', 2 => 'Sent', 3 => 'Confirmed', 4 => 'Rejected', 5 => 'Expired'][$this->status] ?? 'Unknown';
    }
}
