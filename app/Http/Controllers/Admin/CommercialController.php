<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommercialInvoice;
use App\Models\CommercialInvoiceItem;
use App\Models\BankBtbLc;
use App\Models\ExportLc;
use App\Models\CommercialPurchaseOrder;
use App\Models\CommercialProformaInvoice;
use App\Models\PricingList;
use App\Models\PackingList;
use App\Models\ShippingDocument;
use App\Models\ExportRealization;
use App\Models\User;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommercialController extends Controller
{
    // ================== Commercial Invoice ==================

    /**
     * Display a listing of commercial invoices.
     */
    public function invoice(Request $request)
    {
        $query = CommercialInvoice::with(['buyer', 'creator']);

        // Apply filters
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('invoice_date', [
                \Carbon\Carbon::parse($request->startDate)->format('Y-m-d'),
                \Carbon\Carbon::parse($request->endDate)->format('Y-m-d')
            ]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('buyer_name', 'like', "%{$search}%")
                  ->orWhere('lc_no', 'like', "%{$search}%")
                  ->orWhere('pi_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Sorting
        $sortBy = $request->sortBy ?? 'created_at';
        $sortDir = $request->sortDir ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        $invoices = $query->paginate(20);

        // Get buyers for filter
        $buyers = User::where('buyer', true)->orderBy('name')->get();

        // Status counts
        $statusCounts = [
            'total' => CommercialInvoice::count(),
            'pending' => CommercialInvoice::pending()->count(),
            'approved' => CommercialInvoice::approved()->count(),
            'shipped' => CommercialInvoice::shipped()->count(),
            'delivered' => CommercialInvoice::delivered()->count(),
            'cancelled' => CommercialInvoice::cancelled()->count(),
        ];

        return view(adminTheme() . 'commercial.invoices', compact('invoices', 'buyers', 'statusCounts'));
    }

    /**
     * Handle invoice actions (create, edit, view, delete)
     */
    public function invoiceAction(Request $request, $action = null, $id = null)
    {
        // dd($action, $id);
        // Handle store action
        if ($action === 'store') {
            return $this->storeInvoice($request);
        }

        switch ($action) {
            case 'create':
                return $this->createInvoice($request);
            case 'edit':
                return $this->editInvoice($request, $id);
            case 'view':
                return $this->viewInvoice($id);
            case 'delete':
                return $this->deleteInvoice($id);
            case 'print':
                return $this->printInvoice($id);
            case 'update':
                return $this->updateInvoice($request, $id);
            default:
                return redirect()->route('admin.commercial.invoice')->with('error', 'Invalid action');
        }
    }

    /**
     * Show create invoice form
     */
    public function createInvoice(Request $request)
    {
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        $units = Attribute::where('type', 1)->orderBy('name')->get(); // Unit type
        $invoiceNo = CommercialInvoice::generateInvoiceNo();

        $action = 'create';
        $route = route('admin.commercial.invoiceAction', ['store']);
        // dd($route);

        return view(adminTheme() . 'commercial.invoices_action', compact('buyers', 'units', 'invoiceNo', 'action', 'route'));
    }

    /**
     * Store a new invoice
     */
    private function storeInvoice(Request $request)
    {
        $request->validate([
            'buyer_id' => 'required',
            'invoice_date' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $invoice = new CommercialInvoice();
            $invoice->invoice_no = $request->invoice_no ?? CommercialInvoice::generateInvoiceNo();
            $invoice->buyer_id = $request->buyer_id;
            $invoice->buyer_name = $request->buyer_name;
            $invoice->buyer_address = $request->buyer_address;
            $invoice->buyer_contact = $request->buyer_contact;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->shipment_date = $request->shipment_date;
            $invoice->delivery_date = $request->delivery_date;
            $invoice->lc_no = $request->lc_no;
            $invoice->lc_date = $request->lc_date;
            $invoice->pi_no = $request->pi_no;
            $invoice->shipment_from = $request->shipment_from;
            $invoice->shipment_to = $request->shipment_to;
            $invoice->country_of_origin = $request->country_of_origin;
            $invoice->destination_country = $request->destination_country;
            $invoice->carrier = $request->carrier;
            $invoice->vessel_flight_no = $request->vessel_flight_no;
            $invoice->container_no = $request->container_no;
            $invoice->seal_no = $request->seal_no;
            $invoice->marks_no = $request->marks_no;
            $invoice->description_of_goods = $request->description_of_goods;
            $invoice->total_qty = $request->total_qty ?? 0;
            $invoice->total_amount = $request->total_amount ?? 0;
            $invoice->discount = $request->discount ?? 0;
            $invoice->tax = $request->tax ?? 0;
            $invoice->shipping_cost = $request->shipping_cost ?? 0;
            $invoice->insurance = $request->insurance ?? 0;
            $invoice->currency = $request->currency ?? 'USD';
            $invoice->exchange_rate = $request->exchange_rate ?? 1;
            $invoice->status = $request->status ?? 1;
            $invoice->remarks = $request->remarks;
            $invoice->created_by = Auth::id();

            $invoice->calculateGrandTotal();
            $invoice->save();

            // Save items
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (!empty($itemData['description'])) {
                        $item = new CommercialInvoiceItem();
                        $item->invoice_id = $invoice->id;
                        $item->item_no = $itemData['item_no'];
                        $item->description = $itemData['description'];
                        $item->hs_code = $itemData['hs_code'] ?? null;
                        $item->unit_id = $itemData['unit_id'] ?? null;
                        $item->quantity = $itemData['quantity'] ?? 0;
                        $item->unit_price = $itemData['unit_price'] ?? 0;
                        $item->carton_qty = $itemData['carton_qty'] ?? null;
                        $item->carton_no = $itemData['carton_no'] ?? null;
                        $item->net_weight = $itemData['net_weight'] ?? null;
                        $item->gross_weight = $itemData['gross_weight'] ?? null;
                        $item->remarks = $itemData['remarks'] ?? null;
                        $item->calculateTotalPrice();
                        $item->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.commercial.invoiceAction', ['view', $invoice->id])
                ->with('success', 'Commercial Invoice created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Invoice creation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating invoice: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show edit invoice form
     */
    private function editInvoice(Request $request, $id)
    {
        $invoice = CommercialInvoice::with('items')->findOrFail($id);
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        $units = Attribute::where('type', 1)->orderBy('name')->get();

        $action = 'edit';
        $route = route('admin.commercial.invoiceAction', ['update', $invoice->id]);

        return view(adminTheme() . 'commercial.invoices_action', compact('invoice', 'buyers', 'units', 'action', 'route'));
    }

    /**
     * Update an existing invoice
     */
    private function updateInvoice(Request $request, $id)
    {
        $request->validate([
            'buyer_id' => 'required',
            'invoice_date' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $invoice = CommercialInvoice::findOrFail($id);
            $invoice->buyer_id = $request->buyer_id;
            $invoice->buyer_name = $request->buyer_name;
            $invoice->buyer_address = $request->buyer_address;
            $invoice->buyer_contact = $request->buyer_contact;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->shipment_date = $request->shipment_date;
            $invoice->delivery_date = $request->delivery_date;
            $invoice->lc_no = $request->lc_no;
            $invoice->lc_date = $request->lc_date;
            $invoice->pi_no = $request->pi_no;
            $invoice->shipment_from = $request->shipment_from;
            $invoice->shipment_to = $request->shipment_to;
            $invoice->country_of_origin = $request->country_of_origin;
            $invoice->destination_country = $request->destination_country;
            $invoice->carrier = $request->carrier;
            $invoice->vessel_flight_no = $request->vessel_flight_no;
            $invoice->container_no = $request->container_no;
            $invoice->seal_no = $request->seal_no;
            $invoice->marks_no = $request->marks_no;
            $invoice->description_of_goods = $request->description_of_goods;
            $invoice->total_qty = $request->total_qty ?? 0;
            $invoice->total_amount = $request->total_amount ?? 0;
            $invoice->discount = $request->discount ?? 0;
            $invoice->tax = $request->tax ?? 0;
            $invoice->shipping_cost = $request->shipping_cost ?? 0;
            $invoice->insurance = $request->insurance ?? 0;
            $invoice->currency = $request->currency ?? 'USD';
            $invoice->exchange_rate = $request->exchange_rate ?? 1;
            $invoice->status = $request->status ?? 1;
            $invoice->remarks = $request->remarks;
            $invoice->edited_by = Auth::id();

            $invoice->calculateGrandTotal();
            $invoice->save();

            // Delete old items and create new ones
            $invoice->items()->delete();

            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (!empty($itemData['description'])) {
                        $item = new CommercialInvoiceItem();
                        $item->invoice_id = $invoice->id;
                        $item->item_no = $itemData['item_no'];
                        $item->description = $itemData['description'];
                        $item->hs_code = $itemData['hs_code'] ?? null;
                        $item->unit_id = $itemData['unit_id'] ?? null;
                        $item->quantity = $itemData['quantity'] ?? 0;
                        $item->unit_price = $itemData['unit_price'] ?? 0;
                        $item->carton_qty = $itemData['carton_qty'] ?? null;
                        $item->carton_no = $itemData['carton_no'] ?? null;
                        $item->net_weight = $itemData['net_weight'] ?? null;
                        $item->gross_weight = $itemData['gross_weight'] ?? null;
                        $item->remarks = $itemData['remarks'] ?? null;
                        $item->calculateTotalPrice();
                        $item->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.commercial.invoiceAction', ['view', $invoice->id])
                ->with('success', 'Commercial Invoice updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Invoice update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating invoice: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * View invoice details
     */
    private function viewInvoice($id)
    {
        $invoice = CommercialInvoice::with(['buyer', 'creator', 'editor', 'items.unit'])->findOrFail($id);

        return view(adminTheme() . 'commercial.invoices_view', compact('invoice'));
    }

    /**
     * Print invoice
     */
    private function printInvoice($id)
    {
        $invoice = CommercialInvoice::with(['buyer', 'creator', 'items.unit'])->findOrFail($id);

        return view(adminTheme() . 'commercial.invoices_print', compact('invoice'));
    }

    /**
     * Delete invoice
     */
    private function deleteInvoice($id)
    {
        try {
            $invoice = CommercialInvoice::findOrFail($id);
            $invoice->items()->delete();
            $invoice->delete();

            return redirect()->route('admin.commercial.invoice')
                ->with('success', 'Invoice deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }

    // ================== Other Commercial Sections (Stubs) ==================

    public function btbLc(Request $request)
    {
        $query = BankBtbLc::with(['supplier', 'bank', 'creator']);
        if ($request->filled('search')) {
            $query->where('lc_no', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        $records = $query->orderBy('id', 'desc')->paginate(20);
        $statusCounts = ['total' => BankBtbLc::count(), 'pending' => BankBtbLc::where('status', 1)->count(), 'active' => BankBtbLc::where('status', 2)->count(), 'closed' => BankBtbLc::where('status', 3)->count()];
        return view(adminTheme() . 'commercial.btb_lc', compact('records', 'statusCounts'));
    }

    public function btbLcAction(Request $request, $action, $id = null)
    {
        $suppliers = User::where('supplier', true)->orderBy('name')->get();
        $banks = User::orderBy('name')->get();
        if ($action === 'create') {
            $lcNo = BankBtbLc::generateLcNo();
            return view(adminTheme() . 'commercial.btb_lc_action', compact('action', 'lcNo', 'suppliers', 'banks'));
        }
        if ($action === 'edit' && $id) {
            $record = BankBtbLc::findOrFail($id);
            return view(adminTheme() . 'commercial.btb_lc_action', compact('action', 'record', 'suppliers', 'banks'));
        }
        if ($action === 'view' && $id) {
            $record = BankBtbLc::with(['supplier', 'bank', 'creator'])->findOrFail($id);
            return view(adminTheme() . 'commercial.btb_lc_view', compact('record'));
        }
        if ($action === 'delete' && $id) {
            BankBtbLc::findOrFail($id)->delete();
            return back()->with('success', 'Record deleted');
        }
        if (in_array($action, ['store', 'update']) && $request->isMethod('post')) {
            $record = $action === 'store' ? new BankBtbLc() : BankBtbLc::findOrFail($id);
            $record->lc_no = $request->lc_no ?? BankBtbLc::generateLcNo();
            $record->supplier_id = $request->supplier_id;
            $record->supplier_name = $request->supplier_name;
            $record->supplier_address = $request->supplier_address;
            $record->supplier_contact = $request->supplier_contact;
            $record->lc_open_date = $request->lc_open_date;
            $record->lc_expiry_date = $request->lc_expiry_date;
            $record->shipment_date = $request->shipment_date;
            $record->delivery_date = $request->delivery_date;
            $record->bank_id = $request->bank_id;
            $record->bank_name = $request->bank_name;
            $record->branch_name = $request->branch_name;
            $record->lc_value = $request->lc_value ?? 0;
            $record->currency = $request->currency ?? 'USD';
            $record->exchange_rate = $request->exchange_rate ?? 1;
            $record->lc_value_bdt = ($request->lc_value ?? 0) * ($request->exchange_rate ?? 1);
            $record->remaining_value = ($request->lc_value ?? 0) - $record->used_value;
            $record->status = $request->status ?? 1;
            $record->remarks = $request->remarks;
            $record->created_by = $action === 'store' ? Auth::id() : $record->created_by;
            $record->edited_by = $action === 'update' ? Auth::id() : null;
            $record->save();
            return redirect()->route('admin.commercial.btbLcAction', ['view', $record->id])->with('success', 'BTB LC ' . ($action === 'store' ? 'created' : 'updated') . ' successfully');
        }
        return redirect()->route('admin.commercial.btbLc');
    }

    // 2. Export LC/Sales Contact
    public function exportLc(Request $request)
    {
        $query = ExportLc::with(['buyer', 'creator']);
        if ($request->filled('search')) { $query->where('lc_no', 'like', '%' . $request->search . '%'); }
        if ($request->filled('status') && $request->status != 'all') { $query->where('status', $request->status); }
        $records = $query->orderBy('id', 'desc')->paginate(20);
        $statusCounts = ['total' => ExportLc::count(), 'pending' => ExportLc::where('status', 1)->count(), 'partial' => ExportLc::where('status', 2)->count(), 'realized' => ExportLc::where('status', 3)->count()];
        return view(adminTheme().'commercial.export_lc', compact('records', 'statusCounts'));
    }

    public function exportLcAction(Request $request, $action, $id = null)
    {
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        if ($action === 'create') { $lcNo = ExportLc::generateLcNo(); return view(adminTheme() . 'commercial.export_lc_action', compact('action', 'lcNo', 'buyers')); }
        if ($action === 'edit' && $id) { $record = ExportLc::findOrFail($id); return view(adminTheme() . 'commercial.export_lc_action', compact('action', 'record', 'buyers')); }
        if ($action === 'view' && $id) { $record = ExportLc::with(['buyer', 'creator'])->findOrFail($id); return view(adminTheme() . 'commercial.export_lc_view', compact('record')); }
        if ($action === 'delete' && $id) { ExportLc::findOrFail($id)->delete(); return back()->with('success', 'Record deleted'); }
        if (in_array($action, ['store', 'update']) && $request->isMethod('post')) {
            $record = $action === 'store' ? new ExportLc() : ExportLc::findOrFail($id);
            $record->lc_no = $request->lc_no ?? ExportLc::generateLcNo();
            $record->buyer_id = $request->buyer_id; $record->buyer_name = $request->buyer_name; $record->buyer_address = $request->buyer_address; $record->buyer_contact = $request->buyer_contact;
            $record->lc_open_date = $request->lc_open_date; $record->lc_expiry_date = $request->lc_expiry_date; $record->shipment_date = $request->shipment_date;
            $record->issuing_bank = $request->issuing_bank; $record->issuing_bank_branch = $request->issuing_bank_branch; $record->negotiating_bank = $request->negotiating_bank;
            $record->lc_value = $request->lc_value ?? 0; $record->currency = $request->currency ?? 'USD';
            $record->pending_value = ($request->lc_value ?? 0) - $record->realized_value;
            $record->status = $request->status ?? 1; $record->remarks = $request->remarks;
            $record->created_by = $action === 'store' ? Auth::id() : $record->created_by;
            $record->edited_by = $action === 'update' ? Auth::id() : null;
            $record->save();
            return redirect()->route('admin.commercial.exportLcAction', ['view', $record->id])->with('success', 'Export LC ' . ($action === 'store' ? 'created' : 'updated') . ' successfully');
        }
        return redirect()->route('admin.commercial.exportLc');
    }

    // 3. Purchase Order (PO)
    public function purchaseOrders(Request $request)
    {
        $query = CommercialPurchaseOrder::with(['supplier', 'buyer', 'creator']);
        if ($request->filled('search')) { $query->where('po_no', 'like', '%' . $request->search . '%'); }
        if ($request->filled('status') && $request->status != 'all') { $query->where('status', $request->status); }
        $records = $query->orderBy('id', 'desc')->paginate(20);
        $statusCounts = ['total' => CommercialPurchaseOrder::count(), 'pending' => CommercialPurchaseOrder::where('status', 1)->count(), 'confirmed' => CommercialPurchaseOrder::where('status', 2)->count(), 'shipped' => CommercialPurchaseOrder::where('status', 3)->count(), 'received' => CommercialPurchaseOrder::where('status', 4)->count()];
        return view(adminTheme().'commercial.purchase_orders', compact('records', 'statusCounts'));
    }

    public function purchaseOrdersAction(Request $request, $action, $id = null)
    {
        $suppliers = User::where('supplier', true)->orderBy('name')->get();
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        if ($action === 'create') { $poNo = CommercialPurchaseOrder::generatePoNo(); return view(adminTheme() . 'commercial.purchase_orders_action', compact('action', 'poNo', 'suppliers', 'buyers')); }
        if ($action === 'edit' && $id) { $record = CommercialPurchaseOrder::findOrFail($id); return view(adminTheme() . 'commercial.purchase_orders_action', compact('action', 'record', 'suppliers', 'buyers')); }
        if ($action === 'view' && $id) { $record = CommercialPurchaseOrder::with(['supplier', 'buyer', 'creator'])->findOrFail($id); return view(adminTheme() . 'commercial.purchase_orders_view', compact('record')); }
        if ($action === 'delete' && $id) { CommercialPurchaseOrder::findOrFail($id)->delete(); return back()->with('success', 'Record deleted'); }
        if (in_array($action, ['store', 'update']) && $request->isMethod('post')) {
            $record = $action === 'store' ? new CommercialPurchaseOrder() : CommercialPurchaseOrder::findOrFail($id);
            $record->po_no = $request->po_no ?? CommercialPurchaseOrder::generatePoNo();
            $record->supplier_id = $request->supplier_id; $record->supplier_name = $request->supplier_name; $record->supplier_address = $request->supplier_address; $record->supplier_contact = $request->supplier_contact;
            $record->po_date = $request->po_date; $record->delivery_date = $request->delivery_date; $record->pi_no = $request->pi_no; $record->lc_no = $request->lc_no;
            $record->buyer_id = $request->buyer_id; $record->buyer_name = $request->buyer_name;
            $record->style_no = $request->style_no; $record->order_no = $request->order_no;
            $record->total_qty = $request->total_qty ?? 0; $record->unit_price = $request->unit_price ?? 0;
            $record->total_amount = ($request->total_qty ?? 0) * ($request->unit_price ?? 0);
            $record->currency = $request->currency ?? 'USD';
            $record->status = $request->status ?? 1; $record->remarks = $request->remarks;
            $record->created_by = $action === 'store' ? Auth::id() : $record->created_by;
            $record->edited_by = $action === 'update' ? Auth::id() : null;
            $record->save();
            return redirect()->route('admin.commercial.purchaseOrdersAction', ['view', $record->id])->with('success', 'Purchase Order ' . ($action === 'store' ? 'created' : 'updated') . ' successfully');
        }
        return redirect()->route('admin.commercial.purchaseOrders');
    }

    // 4. Proforma Invoice (PI)
    public function pi(Request $request)
    {
        $query = CommercialProformaInvoice::with(['buyer', 'creator']);
        if ($request->filled('search')) { $query->where('pi_no', 'like', '%' . $request->search . '%'); }
        if ($request->filled('status') && $request->status != 'all') { $query->where('status', $request->status); }
        $records = $query->orderBy('id', 'desc')->paginate(20);
        $statusCounts = ['total' => CommercialProformaInvoice::count(), 'pending' => CommercialProformaInvoice::where('status', 1)->count(), 'confirmed' => CommercialProformaInvoice::where('status', 2)->count(), 'shipped' => CommercialProformaInvoice::where('status', 3)->count()];
        return view(adminTheme().'commercial.proforma_invoice', compact('records', 'statusCounts'));
    }

    public function piAction(Request $request, $action, $id = null)
    {
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        if ($action === 'create') { $piNo = CommercialProformaInvoice::generatePiNo(); return view(adminTheme() . 'commercial.proforma_invoice_action', compact('action', 'piNo', 'buyers')); }
        if ($action === 'edit' && $id) { $record = CommercialProformaInvoice::findOrFail($id); return view(adminTheme() . 'commercial.proforma_invoice_action', compact('action', 'record', 'buyers')); }
        if ($action === 'view' && $id) { $record = CommercialProformaInvoice::with(['buyer', 'creator'])->findOrFail($id); return view(adminTheme() . 'commercial.proforma_invoice_view', compact('record')); }
        if ($action === 'delete' && $id) { CommercialProformaInvoice::findOrFail($id)->delete(); return back()->with('success', 'Record deleted'); }
        if (in_array($action, ['store', 'update']) && $request->isMethod('post')) {
            $record = $action === 'store' ? new CommercialProformaInvoice() : CommercialProformaInvoice::findOrFail($id);
            $record->pi_no = $request->pi_no ?? CommercialProformaInvoice::generatePiNo();
            $record->buyer_id = $request->buyer_id; $record->buyer_name = $request->buyer_name; $record->buyer_address = $request->buyer_address; $record->buyer_contact = $request->buyer_contact;
            $record->pi_date = $request->pi_date; $record->valid_until = $request->valid_until; $record->payment_terms = $request->payment_terms; $record->delivery_terms = $request->delivery_terms;
            $record->shipment_from = $request->shipment_from; $record->shipment_to = $request->shipment_to;
            $record->total_qty = $request->total_qty ?? 0; $record->total_amount = $request->total_amount ?? 0; $record->commission = $request->commission ?? 0;
            $record->net_amount = ($request->total_amount ?? 0) - ($request->commission ?? 0);
            $record->currency = $request->currency ?? 'USD';
            $record->status = $request->status ?? 1; $record->remarks = $request->remarks;
            $record->created_by = $action === 'store' ? Auth::id() : $record->created_by;
            $record->edited_by = $action === 'update' ? Auth::id() : null;
            $record->save();
            return redirect()->route('admin.commercial.piAction', ['view', $record->id])->with('success', 'Proforma Invoice ' . ($action === 'store' ? 'created' : 'updated') . ' successfully');
        }
        return redirect()->route('admin.commercial.pi');
    }

    // 5. Pricing List
    public function pricingList(Request $request)
    {
        $query = PricingList::with(['buyer', 'creator']);
        if ($request->filled('search')) { $query->where('price_list_no', 'like', '%' . $request->search . '%'); }
        if ($request->filled('status') && $request->status != 'all') { $query->where('status', (int) $request->status); }
        $records = $query->orderBy('id', 'desc')->paginate(20);
        $statusCounts = [
            'total' => PricingList::count(),
            'active' => PricingList::where('status', 1)->count(),
            'expired' => PricingList::where('status', 2)->count(),
            'cancelled' => PricingList::where('status', 3)->count(),
        ];
        return view(adminTheme().'commercial.pricing_list', compact('records', 'statusCounts'));
    }

    public function pricingListAction(Request $request, $action, $id = null)
    {
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        if ($action === 'create') { $listNo = PricingList::generatePriceListNo(); return view(adminTheme() . 'commercial.pricing_list_action', compact('action', 'listNo', 'buyers')); }
        if ($action === 'edit' && $id) { $record = PricingList::with('items')->findOrFail($id); return view(adminTheme() . 'commercial.pricing_list_action', compact('action', 'record', 'buyers')); }
        if ($action === 'view' && $id) { $record = PricingList::with(['buyer', 'creator', 'items'])->findOrFail($id); return view(adminTheme() . 'commercial.pricing_list_view', compact('record')); }
        if ($action === 'delete' && $id) { PricingList::findOrFail($id)->delete(); return back()->with('success', 'Record deleted'); }
        if (in_array($action, ['store', 'update']) && $request->isMethod('post')) {
            $record = $action === 'store' ? new PricingList() : PricingList::findOrFail($id);
            $record->price_list_no = $request->price_list_no ?? PricingList::generatePriceListNo();
            $record->buyer_id = $request->buyer_id; $record->buyer_name = $request->buyer_name;
            $record->effective_date = $request->effective_date; $record->expiry_date = $request->expiry_date;
            $record->season = $request->season; $record->year = $request->year;
            $record->status = $request->status ?? 1; $record->remarks = $request->remarks;
            $record->created_by = $action === 'store' ? Auth::id() : $record->created_by;
            $record->edited_by = $action === 'update' ? Auth::id() : null;
            $record->save();
            return redirect()->route('admin.commercial.pricingListAction', ['view', $record->id])->with('success', 'Pricing List ' . ($action === 'store' ? 'created' : 'updated') . ' successfully');
        }
        return redirect()->route('admin.commercial.pricingList');
    }

    // 6. Packing List
    public function packingList(Request $request)
    {
        $query = PackingList::with(['invoice', 'buyer', 'creator']);
        if ($request->filled('search')) { $query->where('packing_list_no', 'like', '%' . $request->search . '%'); }
        if ($request->filled('status') && $request->status != 'all') { $query->where('status', $request->status); }
        $records = $query->orderBy('id', 'desc')->paginate(20);
        $statusCounts = ['total' => PackingList::count(), 'draft' => PackingList::where('status', 1)->count(), 'packed' => PackingList::where('status', 2)->count(), 'shipped' => PackingList::where('status', 3)->count()];
        return view(adminTheme().'commercial.packing_list', compact('records', 'statusCounts'));
    }

    public function packingListAction(Request $request, $action, $id = null)
    {
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        $invoices = CommercialInvoice::orderBy('invoice_no', 'desc')->get();
        if ($action === 'create') { $listNo = PackingList::generatePackingListNo(); return view(adminTheme() . 'commercial.packing_list_action', compact('action', 'listNo', 'buyers', 'invoices')); }
        if ($action === 'edit' && $id) { $record = PackingList::with('items')->findOrFail($id); return view(adminTheme() . 'commercial.packing_list_action', compact('action', 'record', 'buyers', 'invoices')); }
        if ($action === 'view' && $id) { $record = PackingList::with(['invoice', 'buyer', 'creator', 'items'])->findOrFail($id); return view(adminTheme() . 'commercial.packing_list_view', compact('record')); }
        if ($action === 'delete' && $id) { PackingList::findOrFail($id)->delete(); return back()->with('success', 'Record deleted'); }
        if (in_array($action, ['store', 'update']) && $request->isMethod('post')) {
            $record = $action === 'store' ? new PackingList() : PackingList::findOrFail($id);
            $record->packing_list_no = $request->packing_list_no ?? $request->pl_no ?? PackingList::generatePackingListNo();
            $record->invoice_id = $request->invoice_id; $record->invoice_no = $request->invoice_no;
            $record->buyer_id = $request->buyer_id; $record->buyer_name = $request->buyer_name;
            $record->packing_date = $request->packing_date ?? $request->pl_date; $record->shipment_date = $request->shipment_date;
            $record->shipment_from = $request->shipment_from; $record->shipment_to = $request->shipment_to;
            $record->vessel_flight_no = $request->vessel_flight_no; $record->container_no = $request->container_no; $record->seal_no = $request->seal_no;
            $record->total_cartons = $request->total_cartons ?? $request->total_ctn ?? 0;
            $record->net_weight = $request->net_weight ?? 0; $record->gross_weight = $request->gross_weight ?? 0;
            $record->total_volume = $request->total_volume ?? $request->total_cbm ?? 0;
            $record->status = $request->status ?? 1; $record->remarks = $request->remarks;
            $record->created_by = $action === 'store' ? Auth::id() : $record->created_by;
            $record->edited_by = $action === 'update' ? Auth::id() : null;
            $record->save();
            return redirect()->route('admin.commercial.packingListAction', ['view', $record->id])->with('success', 'Packing List ' . ($action === 'store' ? 'created' : 'updated') . ' successfully');
        }
        return redirect()->route('admin.commercial.packingList');
    }

    // 7. Shipping Bill/Docs
    public function shippingDocs(Request $request)
    {
        $query = ShippingDocument::with(['invoice', 'buyer', 'creator']);
        if ($request->filled('search')) { $query->where('doc_no', 'like', '%' . $request->search . '%'); }
        if ($request->filled('status') && $request->status != 'all') { $query->where('status', $request->status); }
        $records = $query->orderBy('id', 'desc')->paginate(20);
        $statusCounts = ['total' => ShippingDocument::count(), 'pending' => ShippingDocument::where('status', 1)->count(), 'submitted' => ShippingDocument::where('status', 2)->count(), 'approved' => ShippingDocument::where('status', 3)->count()];
        return view(adminTheme().'commercial.shipping_docs', compact('records', 'statusCounts'));
    }

    public function shippingDocsAction(Request $request, $action, $id = null)
    {
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        $invoices = CommercialInvoice::orderBy('invoice_no', 'desc')->get();
        if ($action === 'create') { $docNo = ShippingDocument::generateDocNo(); return view(adminTheme() . 'commercial.shipping_docs_action', compact('action', 'docNo', 'buyers', 'invoices')); }
        if ($action === 'edit' && $id) { $record = ShippingDocument::findOrFail($id); return view(adminTheme() . 'commercial.shipping_docs_action', compact('action', 'record', 'buyers', 'invoices')); }
        if ($action === 'view' && $id) { $record = ShippingDocument::with(['invoice', 'buyer', 'creator'])->findOrFail($id); return view(adminTheme() . 'commercial.shipping_docs_view', compact('record')); }
        if ($action === 'delete' && $id) { ShippingDocument::findOrFail($id)->delete(); return back()->with('success', 'Record deleted'); }
        if (in_array($action, ['store', 'update']) && $request->isMethod('post')) {
            $record = $action === 'store' ? new ShippingDocument() : ShippingDocument::findOrFail($id);
            $record->doc_no = $request->doc_no ?? $request->sb_no ?? ShippingDocument::generateDocNo();
            $record->invoice_id = $request->invoice_id; $record->invoice_no = $request->invoice_no;
            $record->buyer_id = $request->buyer_id; $record->buyer_name = $request->buyer_name;
            $record->issue_date = $request->issue_date ?? $request->sb_date; $record->shipment_type = $request->shipment_type;
            $record->vessel_name = $request->vessel_name; $record->flight_no = $request->flight_no;
            $record->departure_date = $request->departure_date; $record->arrival_date = $request->arrival_date;
            $record->port_of_loading = $request->port_of_loading; $record->port_of_discharge = $request->port_of_discharge;
            $record->country_of_origin = $request->country_of_origin; $record->destination_country = $request->destination_country;
            $record->bl_awb_no = $request->bl_awb_no; $record->bl_awb_date = $request->bl_awb_date;
            $record->commercial_invoice_no = $request->commercial_invoice_no; $record->packing_list_no = $request->packing_list_no;
            $record->certificate_of_origin = $request->certificate_of_origin; $record->gsp_form = $request->gsp_form;
            $record->inspection_certificate = $request->inspection_certificate; $record->insurance_policy = $request->insurance_policy;
            $record->status = $request->status ?? 1; $record->remarks = $request->remarks;
            $record->created_by = $action === 'store' ? Auth::id() : $record->created_by;
            $record->edited_by = $action === 'update' ? Auth::id() : null;
            $record->save();
            return redirect()->route('admin.commercial.shippingDocsAction', ['view', $record->id])->with('success', 'Shipping Document ' . ($action === 'store' ? 'created' : 'updated') . ' successfully');
        }
        return redirect()->route('admin.commercial.shippingDocs');
    }

    // 8. Export Realization
    public function realization(Request $request)
    {
        $query = ExportRealization::with(['exportLc', 'buyer', 'creator']);
        if ($request->filled('search')) { $query->where('realization_no', 'like', '%' . $request->search . '%'); }
        if ($request->filled('status') && $request->status != 'all') { $query->where('status', $request->status); }
        $records = $query->orderBy('id', 'desc')->paginate(20);
        $statusCounts = ['total' => ExportRealization::count(), 'pending' => ExportRealization::where('status', 1)->count(), 'partial' => ExportRealization::where('status', 2)->count(), 'realized' => ExportRealization::where('status', 3)->count()];
        return view(adminTheme().'commercial.export_realization', compact('records', 'statusCounts'));
    }

    public function realizationAction(Request $request, $action, $id = null)
    {
        $buyers = User::where('buyer', true)->orderBy('name')->get();
        $exportLcs = ExportLc::orderBy('lc_no', 'desc')->get();
        if ($action === 'create') { $realNo = ExportRealization::generateRealizationNo(); return view(adminTheme() . 'commercial.export_realization_action', compact('action', 'realNo', 'buyers', 'exportLcs')); }
        if ($action === 'edit' && $id) { $record = ExportRealization::findOrFail($id); return view(adminTheme() . 'commercial.export_realization_action', compact('action', 'record', 'buyers', 'exportLcs')); }
        if ($action === 'view' && $id) { $record = ExportRealization::with(['exportLc', 'buyer', 'creator'])->findOrFail($id); return view(adminTheme() . 'commercial.export_realization_view', compact('record')); }
        if ($action === 'delete' && $id) { ExportRealization::findOrFail($id)->delete(); return back()->with('success', 'Record deleted'); }
        if (in_array($action, ['store', 'update']) && $request->isMethod('post')) {
            $record = $action === 'store' ? new ExportRealization() : ExportRealization::findOrFail($id);
            $record->realization_no = $request->realization_no ?? ExportRealization::generateRealizationNo();
            $record->export_lc_id = $request->export_lc_id; $record->lc_no = $request->lc_no;
            $record->buyer_id = $request->buyer_id; $record->buyer_name = $request->buyer_name;
            $record->submission_date = $request->submission_date; $record->realization_date = $request->realization_date;
            $record->bank_name = $request->bank_name; $record->bank_branch = $request->bank_branch;
            $record->invoice_value = $request->invoice_value ?? 0;
            $record->realized_value = $request->realized_value ?? $request->realized_amount ?? 0;
            $record->discount = $request->discount ?? 0; $record->bank_charges = $request->bank_charges ?? 0;
            $record->net_realized = ($record->realized_value ?? 0) - ($request->discount ?? 0) - ($request->bank_charges ?? 0);
            $record->currency = $request->currency ?? 'USD'; $record->exchange_rate = $request->exchange_rate ?? 1;
            $record->realized_in_bdt = $record->net_realized * $record->exchange_rate;
            $record->status = $request->status ?? 1; $record->remarks = $request->remarks;
            $record->created_by = $action === 'store' ? Auth::id() : $record->created_by;
            $record->edited_by = $action === 'update' ? Auth::id() : null;
            $record->save();
            return redirect()->route('admin.commercial.realizationAction', ['view', $record->id])->with('success', 'Export Realization ' . ($action === 'store' ? 'created' : 'updated') . ' successfully');
        }
        return redirect()->route('admin.commercial.realization');
    }

    // 9. Commercial Reports
    public function reports()
    {
        $invoices = CommercialInvoice::with('buyer')->get();
        $totalInvoices = $invoices->count();
        $totalInvoiceValue = $invoices->sum('grand_total');
        $totalInBdt = $invoices->sum('total_in_bdt');
        $currency = 'USD';

        $invoiceCounts = [
            'all' => $totalInvoices,
            'pending' => $invoices->where('status', 1)->count(),
            'confirmed' => $invoices->where('status', 2)->count(),
            'shipped' => $invoices->where('status', 3)->count(),
        ];

        $exportLcs = ExportLc::with('buyer')->get();
        $totalLcValue = $exportLcs->sum('lc_value');
        $totalRealized = $exportLcs->sum('realized_value');
        $totalExportLCs = $exportLcs->count();

        $btbLcs = BankBtbLc::with('supplier')->get();
        $totalBtbValue = $btbLcs->sum('lc_value');
        $totalBtbUsed = $btbLcs->sum('used_value');
        $totalBTBLCs = $btbLcs->count();

        $totalPOs = CommercialPurchaseOrder::count();
        $totalPIs = CommercialProformaInvoice::count();
        $totalPLs = PackingList::count();
        $totalSBs = ShippingDocument::count();
        $totalRealizations = ExportRealization::count();

        return view(adminTheme().'commercial.reports', compact(
            'currency',
            'totalInvoices',
            'invoiceCounts',
            'totalInvoiceValue',
            'totalInBdt',
            'totalLcValue',
            'totalRealized',
            'totalBtbValue',
            'totalBtbUsed',
            'totalBTBLCs',
            'totalExportLCs',
            'totalPOs',
            'totalPIs',
            'totalPLs',
            'totalSBs',
            'totalRealizations'
        ));
    }
}
