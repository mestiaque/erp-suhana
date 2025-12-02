<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sample extends Model
{
    use HasFactory;

    protected $table = 'sample';

    protected $guarded = [];

    protected $casts = [
        'received_at'       => 'date',
        'delivery_at'       => 'date',
        'invoice_at'        => 'date',
        'pi_pending_at'     => 'date',
        'pi_confirmed_at'   => 'date',
        'pi_approved_at'    => 'date',
        'pi_cancelled_at'   => 'date',
        'p_pending_at'      => 'date',
        'p_running_at'      => 'date',
        'p_shipping_at'     => 'date',
        'p_cancelled_at'    => 'date',
        'created_at'        => 'date',
        'updated_at'        => 'date',
        'created_date'      => 'date',
    ];

    public function getOrderNumber()
    {
        $length = 8; // ID থেকে কত digit number তৈরি হবে
        return str_pad($this->id, $length, '0', STR_PAD_LEFT);
    }

    public function items()
    {
        return $this->hasMany(SampleItem::class, 'sample_id');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }
}
