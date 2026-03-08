<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class CommercialPurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'po_no', 'supplier_id', 'supplier_name', 'supplier_address', 'supplier_contact',
        'po_date', 'delivery_date', 'pi_no', 'lc_no', 'buyer_id', 'buyer_name',
        'style_no', 'order_no', 'total_qty', 'unit_price', 'total_amount', 'currency',
        'status', 'remarks', 'created_by', 'edited_by'
    ];

    protected $casts = [
        'po_date' => 'date', 'delivery_date' => 'date',
        'total_qty' => 'decimal:2', 'unit_price' => 'decimal:2', 'total_amount' => 'decimal:2'
    ];

    public function supplier() { return $this->belongsTo(User::class, 'supplier_id'); }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'edited_by'); }

    public static function generatePoNo() {
        $prefix = 'PO-' . date('Ymd');
        $last = self::where('po_no', 'like', $prefix . '%')->orderBy('po_no', 'desc')->first();
        $num = $last ? (int)substr($last->po_no, -4) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute() {
        return [1 => 'Pending', 2 => 'Confirmed', 3 => 'Shipped', 4 => 'Received', 5 => 'Cancelled'][$this->status] ?? 'Unknown';
    }
}
