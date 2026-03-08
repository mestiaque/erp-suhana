<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class PricingList extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'price_list_no', 'buyer_id', 'buyer_name', 'effective_date', 'expiry_date',
        'season', 'year', 'status', 'remarks', 'created_by', 'edited_by'
    ];

    protected $casts = [
        'effective_date' => 'date', 'expiry_date' => 'date'
    ];

    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'edited_by'); }
    public function items() { return $this->hasMany(PricingListItem::class, 'pricing_list_id'); }

    public static function generatePriceListNo() {
        $prefix = 'PL-' . date('Ymd');
        $last = self::where('price_list_no', 'like', $prefix . '%')->orderBy('price_list_no', 'desc')->first();
        $num = $last ? (int)substr($last->price_list_no, -4) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute() {
        return [1 => 'Active', 2 => 'Expired', 3 => 'Cancelled'][$this->status] ?? 'Unknown';
    }
}

class PricingListItem extends Model
{
    use HasFactory;

    protected $fillable = ['pricing_list_id', 'item_name', 'item_code', 'description', 'unit_price', 'moq', 'currency', 'remarks'];

    protected $casts = ['unit_price' => 'decimal:2', 'moq' => 'decimal:2'];

    public function pricingList() { return $this->belongsTo(PricingList::class, 'pricing_list_id'); }
}
