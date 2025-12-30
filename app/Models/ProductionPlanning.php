<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class ProductionPlanning extends Model
{
    use HasFactory;
    use ActivityLoggable;

    protected $guarded = [];

    protected $casts = [
        'created_date'     => 'date',
        'cutting_start'    => 'datetime',
        'cutting_end'      => 'datetime',
        'sewing_start'     => 'datetime',
        'sewing_end'       => 'datetime',
        'packing_start'    => 'datetime',
        'packing_end'      => 'datetime',
        'shippment_start'  => 'datetime',
        'shippment_end'    => 'datetime',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'addedby_id');
    }

    public function style()
    {
        return $this->belongsTo(OrderDetail::class,'style_no','style_no');
    }

    // ১. প্রোফর্মা ইনভয়েসের সাথে রিলেশন (যদি pi_id কলাম থাকে)
    public function pi()
    {
        return $this->belongsTo(ProformaInvoice::class, 'pi_id');
    }

    // ২. পিআই এবং স্টাইল অনুযায়ী ডিটেইলস নিয়ে আসার মেথড
    public function getPiStyle()
    {
        // এই প্ল্যানিং এর pi_id এবং style_no ব্যবহার করে প্রোফর্মা আইটেম এবং অর্ডার ডিটেইলস জয়েন করা হচ্ছে
        return \DB::table('proforma_invoice_items as pii')
            ->join('order_details as od', 'pii.style_no', '=', 'od.style_no')
            ->select(
                'od.buyer_name',
                'od.merchant_name',
                'od.company_name',
                'od.fabrication',
                \DB::raw('SUM(pii.order_qty) as pi_total_qty')
            )
            ->where('pii.proforma_invoice_id', $this->pi_id)
            ->where('pii.style_no', $this->style_no)
            ->groupBy('od.buyer_name', 'od.merchant_name', 'od.company_name', 'od.fabrication')
            ->first();
    }

    public function sewingLines()
    {
        return $this->hasMany(ProductionSewing::class, 'planning_id');
    }

    public function sewingOutputs()
    {
        return $this->hasMany(SewingOutput::class, 'planning_id');
    }

    public function floorLines(){
        return Attribute::whereIn(
            'slug',
            $this->sewingLines()->pluck('line_name')
        )->get();
    }



}
