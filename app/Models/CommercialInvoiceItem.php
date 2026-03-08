<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommercialInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_no',
        'description',
        'hs_code',
        'unit_id',
        'quantity',
        'unit_price',
        'total_price',
        'carton_qty',
        'carton_no',
        'net_weight',
        'gross_weight',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'net_weight' => 'decimal:2',
        'gross_weight' => 'decimal:2',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(CommercialInvoice::class, 'invoice_id');
    }

    public function unit()
    {
        return $this->belongsTo(Attribute::class, 'unit_id');
    }

    // Calculate total price
    public function calculateTotalPrice()
    {
        $this->total_price = $this->quantity * $this->unit_price;
        return $this->total_price;
    }
}
