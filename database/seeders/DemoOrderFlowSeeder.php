<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use ME\Commercial\Models\CommB2bLc;
use ME\Commercial\Models\CommBankNegotiation;
use ME\Commercial\Models\CommBuyer;
use ME\Commercial\Models\CommExportDoc;
use ME\Commercial\Models\CommLienBank;
use ME\Commercial\Models\CommMasterLc;
use ME\Commercial\Models\CommUdManagement;
use ME\Merchandising\Database\Seeders\MerchandisingLifecycleSeeder;
use ME\Merchandising\Models\Bom;
use ME\Merchandising\Models\Costing;
use ME\Merchandising\Models\Currency;
use ME\Merchandising\Models\Document;
use ME\Merchandising\Models\Incoterm;
use ME\Merchandising\Models\MaterialBooking;
use ME\Merchandising\Models\Order;
use ME\Merchandising\Models\PaymentTerm;
use ME\Merchandising\Models\Port;
use ME\Merchandising\Models\SalesContract;
use ME\Merchandising\Models\ShipmentPlan;
use ME\Merchandising\Models\Style;
use ME\Merchandising\Models\Supplier;
use ME\Merchandising\Models\TnaMilestone;
use ME\Merchandising\Models\Uom;
use ME\ProductionSfl\Database\Seeders\ProdCompleteOrderLifecycleSeeder;
use ME\ProductionSfl\Models\ProdShipment;

/**
 * Wipes every mer_, pro_ and com_ prefixed table and reseeds ONE coherent,
 * fully-linked demo order flow spanning all three modules:
 *
 *   Merchandising (buyer/style/sample/BOM/order + contract/costing/TNA/
 *   material booking/shipment plan/document)
 *        -> Production (MRP -> material issue -> cutting -> printing ->
 *           sewing line job w/ daily+hourly grid -> wash -> finishing ->
 *           QC -> packing -> shipment -> sub-contract chain)
 *        -> Commercial (buyer/lien bank/Master LC/B2B LC/export doc/
 *           bank negotiation/UD management) for the SAME order.
 *
 * Reuses the two existing, already-tested package seeders
 * (ME\Merchandising\Database\Seeders\MerchandisingLifecycleSeeder and
 * ME\ProductionSfl\Database\Seeders\ProdCompleteOrderLifecycleSeeder) as the
 * backbone for the Merchandising and Production legs, then layers on the
 * remaining Merchandising documents and the full Commercial paperwork on
 * top of the very same buyer/style/order/shipment.
 *
 * Run with: php artisan db:seed --class=Database\\Seeders\\DemoOrderFlowSeeder
 */
class DemoOrderFlowSeeder extends Seeder
{
    public function run(): void
    {
        $this->wipe();

        // The Commercial package's LC/export-doc/negotiation/UD models use
        // Spatie LogsActivity, which defaults to writing into an
        // `activity_log` table. This host app's own audit trail lives in a
        // differently-shaped `activity_logs` table instead (see
        // 2026_07_30_000001_create_activity_logs_table), so the vendor
        // table the trait expects was never created here. Disable activity
        // logging globally for the duration of this seeder run rather than
        // touching the app's activitylog config/migrations.
        app(\Spatie\Activitylog\ActivityLogStatus::class)->disable();

        DB::transaction(function () {
            $this->seedMerchandisingMasters();

            (new MerchandisingLifecycleSeeder())->run();

            $style = Style::where('style_no', 'DEMO-STY-01')->firstOrFail();
            $order = Order::where('style_id', $style->id)->latest('id')->firstOrFail();

            $this->linkBomUnits($style);
            $this->seedMerchandisingExtras($order);

            (new ProdCompleteOrderLifecycleSeeder())->run();

            $order->refresh();

            $this->seedCommercial($order);
        });
    }

    /**
     * Truncate every mer_/pro_/com_ table with FK checks disabled.
     * TRUNCATE causes an implicit commit in MySQL, so this intentionally
     * runs outside the DB::transaction() wrapping the actual seeding below.
     */
    private function wipe(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach (['mer_%', 'pro_%', 'com_%'] as $pattern) {
            foreach (DB::select("SHOW TABLES LIKE '{$pattern}'") as $row) {
                $table = array_values((array) $row)[0];
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedMerchandisingMasters(): void
    {
        Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1, 'is_active' => true]);
        Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.92, 'is_active' => true]);

        PaymentTerm::firstOrCreate(['code' => 'TT30'], ['name' => 'TT 30 Days', 'days' => 30, 'description' => 'Telegraphic Transfer, 30 days from B/L date', 'is_active' => true]);

        Incoterm::firstOrCreate(['code' => 'FOB'], ['name' => 'FOB', 'description' => 'Free on Board', 'is_active' => true]);

        Port::firstOrCreate(['code' => 'CGP'], ['name' => 'Chattogram Sea Port', 'country' => 'Bangladesh', 'port_type' => 'sea', 'is_active' => true]);

        Uom::firstOrCreate(['short_name' => 'YDS'], ['name' => 'Yard', 'is_active' => true]);
        Uom::firstOrCreate(['short_name' => 'PCS'], ['name' => 'Piece', 'is_active' => true]);
        Uom::firstOrCreate(['short_name' => 'KG'], ['name' => 'Kilogram', 'is_active' => true]);

        Supplier::firstOrCreate(
            ['code' => 'DEMO-SUP'],
            ['name' => 'Demo Fabric Mills Ltd', 'address' => 'Gazipur, Bangladesh', 'contact_person' => 'Karim Textile', 'phone' => '01711000000', 'email' => 'sales@demofabricmills.example', 'is_active' => true]
        );
    }

    /**
     * The BOM the lifecycle seeder created leaves unit_id null on every
     * line — attach the appropriate UOM now that the masters exist.
     */
    private function linkBomUnits(Style $style): void
    {
        $bom = Bom::where('style_id', $style->id)->with('items')->first();
        if (! $bom) {
            return;
        }

        $yard = Uom::where('short_name', 'YDS')->value('id');
        $pcs = Uom::where('short_name', 'PCS')->value('id');

        foreach ($bom->items as $item) {
            $item->update(['unit_id' => $item->item_type === 'fabric' ? $yard : $pcs]);
        }
    }

    private function seedMerchandisingExtras(Order $order): void
    {
        $buyer = $order->buyer;
        $creatorId = User::query()->value('id');
        $supplierId = Supplier::where('code', 'DEMO-SUP')->value('id');
        $yardUnitId = Uom::where('short_name', 'YDS')->value('id');

        // ---------- Sales Contract ----------
        SalesContract::create([
            'order_id'      => $order->id,
            'buyer_id'      => $buyer->id,
            'contract_date' => now()->subDays(24),
            'terms'         => 'FOB Chattogram. Payment: TT 30 days from B/L date. Partial shipment allowed.',
            'status'        => 'signed',
            'remarks'       => 'Sales contract signed against confirmed PO for ' . $order->style->style_no . '.',
            'created_by'    => $creatorId,
        ]);

        // ---------- Costing (pre-costing, approved) ----------
        Costing::create([
            'order_id'              => $order->id,
            'type'                  => 'pre',
            'fob_price'             => $order->price,
            'fabric_cost'           => 1.8500,
            'trim_cost'             => 0.3500,
            'wash_cost'             => 0.1500,
            'embroidery_print_cost' => 0.1000,
            'overhead_cost'         => 0.5500,
            'status'                => 'approved',
            'remarks'               => 'Pre-costing approved prior to order confirmation.',
            'created_by'            => $creatorId,
        ]);

        // ---------- TNA Milestones ----------
        // Fabric/trim/cutting/sewing already happened (see
        // ProdCompleteOrderLifecycleSeeder's now()->subDays(...) schedule)
        // so those are marked completed; Ex-Factory/Shipment are left as the
        // still-open PLANNED targets against the buyer's delivery date
        // (order shipped ahead of that plan), giving two upcoming
        // milestones.
        $milestones = [
            ['name' => 'Sample Approval', 'planned' => now()->subDays(25), 'actual' => now()->subDays(25), 'status' => 'completed'],
            ['name' => 'Fabric Booking', 'planned' => now()->subDays(22), 'actual' => now()->subDays(21), 'status' => 'completed'],
            ['name' => 'Trim Booking', 'planned' => now()->subDays(22), 'actual' => now()->subDays(23), 'status' => 'completed'],
            ['name' => 'Fabric In-house', 'planned' => now()->subDays(18), 'actual' => now()->subDays(17), 'status' => 'completed'],
            ['name' => 'Trim In-house', 'planned' => now()->subDays(18), 'actual' => now()->subDays(19), 'status' => 'completed'],
            ['name' => 'Cutting Start', 'planned' => now()->subDays(16), 'actual' => now()->subDays(16), 'status' => 'completed'],
            ['name' => 'Sewing Start', 'planned' => now()->subDays(15), 'actual' => now()->subDays(15), 'status' => 'completed'],
            ['name' => 'Ex-Factory', 'planned' => $order->delivery_date->copy()->subDays(5), 'actual' => null, 'status' => 'pending'],
            ['name' => 'Shipment', 'planned' => $order->delivery_date, 'actual' => null, 'status' => 'pending'],
        ];

        foreach ($milestones as $m) {
            TnaMilestone::create([
                'order_id'       => $order->id,
                'milestone_name' => $m['name'],
                'planned_date'   => $m['planned'],
                'actual_date'    => $m['actual'],
                'status'         => $m['status'],
                'is_escalated'   => false,
                'created_by'     => $creatorId,
            ]);
        }

        // ---------- Material Booking ----------
        MaterialBooking::create([
            'order_id'      => $order->id,
            'supplier_id'   => $supplierId,
            'material_type' => 'fabric',
            'material_name' => 'Demo Cotton Fabric',
            'qty'           => round($order->order_qty * 1.2 * 1.05, 4),
            'unit_id'       => $yardUnitId,
            'booking_date'  => now()->subDays(20),
            'expected_date' => now()->subDays(17),
            'status'        => 'received',
            'remarks'       => 'Booked against confirmed order; fully received into store.',
            'created_by'    => $creatorId,
        ]);

        // ---------- Shipment Plan ----------
        ShipmentPlan::create([
            'order_id'         => $order->id,
            'planned_date'     => $order->delivery_date,
            'actual_date'      => now()->subDay(),
            'planned_qty'      => $order->order_qty,
            'destination_port' => 'Chattogram Sea Port',
            'mode'             => 'sea',
            'status'           => 'shipped',
            'remarks'          => 'Shipped ahead of the buyer delivery target.',
            'created_by'       => $creatorId,
        ]);

        // ---------- Document ----------
        Document::create([
            'buyer_id'      => $buyer->id,
            'order_id'      => $order->id,
            'document_type' => 'po_attachment',
            'title'         => 'Buyer PO - ' . $order->po_number,
            'file_path'     => 'demo/po-' . strtolower($order->po_number) . '.pdf',
            'remarks'       => 'Original buyer purchase order attachment.',
            'created_by'    => $creatorId,
        ]);
    }

    private function seedCommercial(Order $order): void
    {
        $merchBuyer = $order->buyer;

        // ---------- Buyer (mirrors the Merchandising buyer identity) ----------
        $commBuyer = CommBuyer::create([
            'code'          => $merchBuyer->code,
            'name'          => $merchBuyer->name,
            'country'       => 'USA',
            'currency'      => 'USD',
            'payment_terms' => 'TT 30 Days',
            'address'       => $merchBuyer->address ?? 'New York, USA',
            'website'       => null,
            'status'        => 'active',
        ]);

        // ---------- Lien Bank ----------
        $lienBank = CommLienBank::create([
            'code'       => 'DEMO-BANK',
            'name'       => 'Demo Bank Ltd',
            'branch'     => 'Dhaka Main Branch',
            'swift'      => 'DEMOBDDH',
            'routing_no' => '123456789',
            'type'       => 'both',
            'status'     => 'active',
        ]);

        // Shipment actually happened yesterday (ProdCompleteOrderLifecycleSeeder).
        $shipmentDate = ProdShipment::where('order_id', $order->id)->value('shipment_date') ?? now()->subDay();
        $orderValue = round((float) $order->order_qty * (float) $order->price, 2);

        // ---------- Master LC ----------
        $masterLc = CommMasterLc::create([
            'lc_no'              => 'DEMO-MLC-0001',
            'contract_no'        => 'CTR-' . $order->po_number,
            'lc_type'            => 'sight',
            'buyer_id'           => $commBuyer->id,
            'lien_bank_id'       => $lienBank->id,
            'date'               => now()->subDays(45),
            'expiry_date'        => now()->addDays(30),
            'expiry_place'       => 'Dhaka, Bangladesh',
            'shipment_date'      => $shipmentDate,
            'port_of_loading'    => 'Chattogram',
            'port_of_discharge'  => 'New York',
            'total_amount'       => round($orderValue * 1.10, 2),
            'currency'           => 'USD',
            'max_b2b_percentage' => 75.00,
            'tolerance_pct'      => 5.00,
            'partial_shipment'   => true,
            'transhipment'       => true,
            'remarks'            => 'Master LC opened for PO ' . $order->po_number . ' (' . $order->style->style_no . ').',
            'status'             => 'active',
        ]);

        // ---------- B2B LC (back-to-back, used to import fabric for this order) ----------
        CommB2bLc::create([
            'master_lc_id'      => $masterLc->id,
            'b2b_lc_no'         => 'DEMO-B2B-0001',
            'pi_no'             => 'PI-DEMO-0001',
            'supplier_name'     => 'Demo Fabric Mills Ltd',
            'b2b_lc_type'       => 'sight',
            'amount'            => round($orderValue * 0.55, 2),
            'currency'          => 'USD',
            'tolerance_pct'     => 5.00,
            'partial_shipment'  => true,
            'port_of_loading'   => 'Shanghai',
            'port_of_discharge' => 'Chattogram',
            'shipment_date'     => now()->subDays(25),
            'expiry_date'       => now()->subDays(10),
            'acceptance_date'   => now()->subDays(24),
            'maturity_date'     => now()->addDays(35),
            'opening_bank_ref'  => 'DEMOBDDH-B2B-0001',
            'remarks'           => 'Back-to-back LC to import fabric for PO ' . $order->po_number . '.',
            'status'            => 'accepted',
        ]);

        // ---------- Export Doc ----------
        $exportDoc = CommExportDoc::create([
            'master_lc_id'      => $masterLc->id,
            'invoice_no'        => 'DEMO-INV-0001',
            'invoice_date'      => $shipmentDate,
            'invoice_amount'    => $orderValue,
            'invoice_currency'  => 'USD',
            'packing_list_no'   => 'DEMO-PL-0001',
            'exp_no'            => 'DEMO-EXP-0001',
            'shipping_bill_no'  => 'DEMO-SB-0001',
            'bl_no'             => 'DEMO-BL-0001',
            'bl_date'           => $shipmentDate,
            'forwarder'         => 'Demo Freight Forwarders Ltd',
            'shipping_line'     => 'Demo Shipping Line',
            'vessel_name'       => 'MV Demo Voyager',
            'port_of_loading'   => 'Chattogram',
            'port_of_discharge' => 'New York',
            'etd'               => $shipmentDate,
            'eta'               => now()->addDays(20),
            'container_no'      => 'DEMO-CONT-0001',
            'seal_no'           => 'DEMO-SEAL-0001',
            'remarks'           => 'Export documents for PO ' . $order->po_number . '.',
            'status'            => 'negotiated',
        ]);

        // ---------- Bank Negotiation ----------
        // Note: CommBankNegotiation's $fillable has since been corrected to
        // match the actual com_bank_negotiations columns; forceFill() is
        // kept here defensively but create() would now work too.
        (new CommBankNegotiation())->forceFill([
            'export_doc_id'           => $exportDoc->id,
            'negotiating_bank_id'     => $lienBank->id,
            'negotiation_date'        => now()->subHours(20),
            'original_docs_sent_date' => now()->subHours(12),
            'courier_name'            => 'DHL Express',
            'courier_tracking_no'     => 'DEMO-TRK-0001',
            'pad_no'                  => 'DEMO-PAD-0001',
            'lim_no'                  => 'DEMO-LIM-0001',
            'acceptance_date'         => now()->addDays(3),
            'maturity_date'           => now()->addDays(33),
            'realization_date'        => null,
            'realized_amount'         => null,
            'status'                  => 'submitted',
            'remarks'                 => 'Documents negotiated for PO ' . $order->po_number . '.',
        ])->save();

        // ---------- UD Management ----------
        // Same note as above: CommUdManagement's $fillable now matches the
        // real com_ud_managements columns (total_amount/utilized_amount/
        // valid_from/valid_to); forceFill() kept defensively.
        (new CommUdManagement())->forceFill([
            'ud_no'            => 'DEMO-UD-0001',
            'master_lc_id'     => $masterLc->id,
            'total_amount'     => $masterLc->total_amount,
            'utilized_amount'  => round($orderValue * 0.55, 2),
            'valid_from'       => now()->subDays(45),
            'valid_to'         => now()->addMonths(6),
            'remarks'          => 'Utilization Declaration against Master LC ' . $masterLc->lc_no . '.',
            'status'           => 'active',
        ])->save();
    }
}
