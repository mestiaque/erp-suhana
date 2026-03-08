<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class CommercialInvoice extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'invoice_no',
        'buyer_id',
        'buyer_name',
        'buyer_address',
        'buyer_contact',
        'invoice_date',
        'shipment_date',
        'delivery_date',
        'lc_no',
        'lc_date',
        'pi_no',
        'shipment_from',
        'shipment_to',
        'country_of_origin',
        'destination_country',
        'carrier',
        'vessel_flight_no',
        'container_no',
        'seal_no',
        'marks_no',
        'description_of_goods',
        'total_qty',
        'total_amount',
        'discount',
        'tax',
        'shipping_cost',
        'insurance',
        'grand_total',
        'currency',
        'exchange_rate',
        'total_in_bdt',
        'status',
        'remarks',
        'created_by',
        'edited_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'shipment_date' => 'date',
        'delivery_date' => 'date',
        'lc_date' => 'date',
        'total_qty' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'insurance' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'exchange_rate' => 'decimal:2',
        'total_in_bdt' => 'decimal:2',
    ];

    // Relationships
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function items()
    {
        return $this->hasMany(CommercialInvoiceItem::class, 'invoice_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 1);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 2);
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 3);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 4);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 5);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [1, 2, 3, 4]);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            1 => 'Pending',
            2 => 'Approved',
            3 => 'Shipped',
            4 => 'Delivered',
            5 => 'Cancelled',
        ];
        return $labels[$this->status] ?? 'Unknown';
    }

    public function getStatusClassAttribute()
    {
        $classes = [
            1 => 'warning',
            2 => 'success',
            3 => 'info',
            4 => 'primary',
            5 => 'danger',
        ];
        return $classes[$this->status] ?? 'secondary';
    }

    // Generate invoice number
    public static function generateInvoiceNo()
    {
        $prefix = 'CI-' . date('Ymd');
        $lastInvoice = self::where('invoice_no', 'like', $prefix . '%')
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_no, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . '-' . $newNumber;
    }

    // Calculate grand total
    public function calculateGrandTotal()
    {
        $this->grand_total = $this->total_amount 
            - $this->discount 
            + $this->tax 
            + $this->shipping_cost 
            + $this->insurance;
        
        $this->total_in_bdt = $this->grand_total * $this->exchange_rate;
        
        return $this->grand_total;
    }
}
