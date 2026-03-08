<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggable;

class ShippingDocument extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggable;

    protected $fillable = [
        'doc_no', 'invoice_id', 'invoice_no', 'buyer_id', 'buyer_name',
        'issue_date', 'shipment_type', 'vessel_name', 'flight_no',
        'departure_date', 'arrival_date', 'port_of_loading', 'port_of_discharge',
        'country_of_origin', 'destination_country',
        'bl_awb_no', 'bl_awb_date', 'commercial_invoice_no', 'packing_list_no',
        'certificate_of_origin', 'gsp_form', 'inspection_certificate', 'insurance_policy',
        'status', 'remarks', 'created_by', 'edited_by'
    ];

    protected $casts = [
        'issue_date' => 'date', 'departure_date' => 'date', 'arrival_date' => 'date', 'bl_awb_date' => 'date'
    ];

    public function invoice() { return $this->belongsTo(CommercialInvoice::class, 'invoice_id'); }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'edited_by'); }

    public static function generateDocNo() {
        $prefix = 'SHIP-' . date('Ymd');
        $last = self::where('doc_no', 'like', $prefix . '%')->orderBy('doc_no', 'desc')->first();
        $num = $last ? (int)substr($last->doc_no, -4) + 1 : 1;
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute() {
        return [1 => 'Pending', 2 => 'Submitted', 3 => 'Approved', 4 => 'Rejected'][$this->status] ?? 'Unknown';
    }
}
