<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class ProformaInvoice extends Model
{
    use HasFactory;
    use ActivityLoggable;

    protected $guarded = [];

    protected $casts = [
        'created_date' => 'date',
        'updated_at' => 'date',
        'order_date' => 'date',

    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function order()
    {
        return $this->belongsTo(OrderDetail::class, 'order_no', 'order_no');
    }

    public function items()
    {
        return $this->hasMany(ProformaInvoiceItem::class, 'proforma_invoice_id');
    }

    public function yarnBookings()
    {
        return $this->hasMany(YarnBooking::class, 'pi_id', 'id');
    }

    public function yarnReceives()
    {
        return $this->hasMany(YarnReceive::class, 'pi_id', 'id');
    }

    public function dyeingBookings()
    {
        return $this->hasMany(DyeingBooking::class, 'pi_id', 'id');
    }

    public function dyeingReceives()
    {
        return $this->hasMany(DyeingReceive::class, 'pi_id', 'id');
    }

    public function knittingBookings()
    {
        return $this->hasMany(KnittingBooking::class, 'pi_id', 'id');
    }

    public function knittingReceives()
    {
        return $this->hasMany(KnittingReceive::class, 'pi_id', 'id');
    }

}
