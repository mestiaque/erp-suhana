<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class PackingList extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'packing_list_no', 'invoice_id', 'invoice_no', 'buyer_id', 'buyer_name',
        'packing_date', 'shipment_date', 'shipment_from', 'shipment_to',
        'vessel_flight_no', 'container_no', 'seal_no',
        'total_cartons', 'net_weight', 'gross_weight', 'total_volume',
        'status', 'remarks', 'created_by', 'edited_by'
    ];

    protected $casts = [
        'packing_date' => 'date', 'shipment_date' => 'date',
        'total_cartons' => 'integer', 'net_weight' => 'decimal:2', 
        'gross_weight' => 'decimal:2', 'total_volume' => 'decimal:4'
    ];

    public function invoice() { return $this->belongsTo(CommercialInvoice::class, 'invoice_id'); }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'edited_by'); }
    public function items() { return $this->hasMany(PackingListItem::class, 'packing_list_id'); }

    public static function generatePackingListNo() {
        $prefix = 'PKL-' . date('Ymd');
        $last = self::where('packing_list_no', 'like', $prefix . '%')->orderBy('packing_list_no', 'desc')->first();
        $num = $last ? (int)substr($last->packing_list_no, -4) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute() {
        return [1 => 'Draft', 2 => 'Packed', 3 => 'Shipped'][$this->status] ?? 'Unknown';
    }
}

class PackingListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'packing_list_id', 'item_description', 'style_no', 'color', 'size',
        'carton_qty', 'pcs_per_carton', 'total_pcs', 'unit_nw', 'unit_gw', 'carton_measurements', 'remarks'
    ];

    protected $casts = [
        'carton_qty' => 'integer', 'pcs_per_carton' => 'integer', 'total_pcs' => 'integer',
        'unit_nw' => 'decimal:2', 'unit_gw' => 'decimal:2', 'carton_measurements' => 'decimal:2'
    ];

    public function packingList() { return $this->belongsTo(PackingList::class, 'packing_list_id'); }
}
