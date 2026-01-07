<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommercialController extends Controller
{
    public function btbLc() { return view(adminTheme().'commercial.btb_lc'); }
    public function btbLcAction($action, $id = null) { return view(adminTheme().'commercial.btb_lc_action', compact('action', 'id')); }

    // 2. Export LC/Sales Contact
    public function exportLc() { return view(adminTheme().'commercial.export_lc'); }
    public function exportLcAction($action, $id = null) { return view(adminTheme().'commercial.export_lc_action', compact('action', 'id')); }

    // 3. Purchase Order (PO)
    public function purchaseOrders() { return view(adminTheme().'commercial.purchase_orders'); }
    public function purchaseOrdersAction($action, $id = null) { return view(adminTheme().'commercial.purchase_orders_action', compact('action', 'id')); }

    // 4. Proforma Invoice (PI)
    public function pi() { return view(adminTheme().'commercial.proforma_invoice'); }
    public function piAction($action, $id = null) { return view(adminTheme().'commercial.proforma_invoice_action', compact('action', 'id')); }

    // 5. Commercial Invoice
    public function invoice() { return view(adminTheme().'commercial.invoices'); }
    public function invoiceAction($action, $id = null) { return view(adminTheme().'commercial.invoices_action', compact('action', 'id')); }

    // 6. Packing List
    public function packingList() { return view(adminTheme().'commercial.packing_list'); }
    public function packingListAction($action, $id = null) { return view(adminTheme().'commercial.packing_list_action', compact('action', 'id')); }

    // 6. Packing List
    public function pricingList() { return view(adminTheme().'commercial.pricing_list'); }
    public function pricingListAction($action, $id = null) { return view(adminTheme().'commercial.pricing_list_action', compact('action', 'id')); }

    // 7. Shipping Bill/Docs
    public function shippingDocs() { return view(adminTheme().'commercial.shipping_docs'); }
    public function shippingDocsAction($action, $id = null) { return view(adminTheme().'commercial.shipping_docs_action', compact('action', 'id')); }

    // 8. Export Realization
    public function realization() { return view(adminTheme().'commercial.export_realization'); }
    public function realizationAction($action, $id = null) { return view(adminTheme().'commercial.export_realization_action', compact('action', 'id')); }

    // 9. Commercial Reports
    public function reports() { return view(adminTheme().'commercial.reports'); }
}
