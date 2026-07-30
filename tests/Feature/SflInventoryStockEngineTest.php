<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ME\SflInventory\Models\InvDepartment;
use ME\SflInventory\Models\InvGrn;
use ME\SflInventory\Models\InvItem;
use ME\SflInventory\Models\InvItemCategory;
use ME\SflInventory\Models\InvIssue;
use ME\SflInventory\Models\InvPurchaseOrder;
use ME\SflInventory\Models\InvRequisition;
use ME\SflInventory\Models\InvStockTransaction;
use ME\SflInventory\Models\InvStore;
use ME\SflInventory\Models\InvSupplier;
use ME\SflInventory\Models\InvUnit;
use ME\SflInventory\Services\StockService;
use Tests\TestCase;

/**
 * Runs against the real MySQL schema (matching production), wrapped in a
 * transaction that's rolled back after every test — nothing is persisted.
 * Uses DatabaseTransactions (not RefreshDatabase) specifically so this never
 * touches migrate:fresh against the shared dev database.
 */
class SflInventoryStockEngineTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    protected function setUp(): void
    {
        // phpunit.xml forces DB_CONNECTION=sqlite/DB_DATABASE=:memory: for the
        // default test suite (config/database.php's 'mysql' array reads the
        // same DB_DATABASE env var, so it inherits that override too). This
        // package's schema and DocumentNumberService (SUBSTRING_INDEX) are
        // MySQL-specific, matching the real deployment target, so these must
        // be set BEFORE parent::setUp() boots the application — DatabaseTransactions
        // begins its transaction during parent::setUp(), so a later config()
        // call would be too late. Safe: DatabaseTransactions rolls everything
        // back, nothing is persisted to the real dev database.
        // phpunit.xml's <env> tags populate $_ENV/$_SERVER directly, which
        // Laravel's env() helper checks before getenv(), so putenv() alone
        // isn't enough — override all three.
        $overrides = [
            'DB_CONNECTION' => 'mysql',
            'DB_HOST'       => '127.0.0.1',
            'DB_PORT'       => '3306',
            'DB_DATABASE'   => 'suhana_erp',
            'DB_USERNAME'   => 'root',
            'DB_PASSWORD'   => 'admin',
        ];
        foreach ($overrides as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        parent::setUp();
    }

    private function makeItem(InvStore $store, float $openingStock = 0, float $openingValue = 0): InvItem
    {
        $category = InvItemCategory::create(['name' => 'Test Category ' . uniqid()]);
        $unit = InvUnit::create(['name' => 'Yard', 'short_name' => 'YD']);

        return InvItem::create([
            'item_name'        => 'Test Fabric ' . uniqid(),
            'category_id'      => $category->id,
            'unit_id'          => $unit->id,
            'item_type'        => 'raw_material',
            'opening_stock'    => $openingStock,
            'opening_value'    => $openingValue,
            'opening_store_id' => $openingStock > 0 ? $store->id : null,
        ]);
    }

    public function test_item_code_is_auto_generated(): void
    {
        $store = InvStore::create(['name' => 'Store ' . uniqid(), 'code' => 'S' . uniqid(), 'type' => 'raw_material']);
        $item = $this->makeItem($store);

        $this->assertStringStartsWith('ITM-', $item->item_code);
    }

    public function test_opening_stock_posts_to_ledger_and_stock_service_reads_it_back(): void
    {
        $store = InvStore::create(['name' => 'Store ' . uniqid(), 'code' => 'S' . uniqid(), 'type' => 'raw_material']);
        $item = $this->makeItem($store, 100, 5000);

        $stock = app(StockService::class);

        $this->assertSame(100.0, $stock->currentStock($item->id, $store->id));
        $this->assertSame(5000.0, $stock->stockValue($item->id, $store->id));
        $this->assertSame(50.0, $stock->averageRate($item->id, $store->id));
    }

    public function test_stock_transaction_rows_are_immutable(): void
    {
        $store = InvStore::create(['name' => 'Store ' . uniqid(), 'code' => 'S' . uniqid(), 'type' => 'raw_material']);
        $item = $this->makeItem($store, 10, 100);

        $txn = InvStockTransaction::where('item_id', $item->id)->first();

        $this->expectException(\RuntimeException::class);
        $txn->update(['qty_in' => 999]);
    }

    public function test_full_po_to_grn_workflow_updates_stock_and_po_status(): void
    {
        $user = User::query()->firstOrFail();
        $store = InvStore::create(['name' => 'Store ' . uniqid(), 'code' => 'S' . uniqid(), 'type' => 'raw_material']);
        $supplier = InvSupplier::create(['name' => 'Supplier ' . uniqid(), 'code' => 'SUP' . uniqid()]);
        $item = $this->makeItem($store);
        $stock = app(StockService::class);

        $po = InvPurchaseOrder::create([
            'supplier_id' => $supplier->id, 'order_date' => now(), 'status' => 'draft',
            'total_amount' => 500, 'created_by' => $user->id,
        ]);
        $poItem = $po->items()->create(['item_id' => $item->id, 'quantity' => 100, 'rate' => 5, 'amount' => 500]);
        $po->update(['status' => 'approved', 'approved_by' => $user->id, 'approved_at' => now()]);

        $grn = InvGrn::create([
            'purchase_order_id' => $po->id, 'store_id' => $store->id, 'supplier_id' => $supplier->id,
            'receive_date' => now(), 'status' => 'posted', 'total_amount' => 500, 'created_by' => $user->id,
        ]);
        $grn->items()->create([
            'purchase_order_item_id' => $poItem->id, 'item_id' => $item->id,
            'ordered_qty' => 100, 'received_qty' => 100, 'rate' => 5, 'amount' => 500,
        ]);
        $stock->post([
            'item_id' => $item->id, 'store_id' => $store->id, 'transaction_date' => now()->toDateString(),
            'transaction_type' => 'grn', 'qty_in' => 100, 'rate' => 5,
            'reference_type' => 'inv_grn', 'reference_id' => $grn->id, 'created_by' => $user->id,
        ]);
        $poItem->increment('received_qty', 100);
        $po->refreshReceiptStatus();

        $this->assertSame('closed', $po->fresh()->status);
        $this->assertSame(100.0, $stock->currentStock($item->id, $store->id));
    }

    public function test_issue_to_floor_store_posts_paired_out_and_in(): void
    {
        $user = User::query()->firstOrFail();
        $mainStore = InvStore::create(['name' => 'Main ' . uniqid(), 'code' => 'M' . uniqid(), 'type' => 'raw_material']);
        $cuttingStore = InvStore::create(['name' => 'Cutting ' . uniqid(), 'code' => 'C' . uniqid(), 'type' => 'cutting']);
        $department = InvDepartment::create(['name' => 'Cutting ' . uniqid(), 'code' => 'D' . uniqid(), 'default_store_id' => $cuttingStore->id]);
        $item = $this->makeItem($mainStore, 200, 10000);
        $stock = app(StockService::class);

        $requisition = InvRequisition::create([
            'requisition_date' => now(), 'department_id' => $department->id, 'store_id' => $mainStore->id,
            'requested_by' => $user->id, 'status' => 'pending', 'created_by' => $user->id,
        ]);
        $reqItem = $requisition->items()->create(['item_id' => $item->id, 'requested_qty' => 50, 'approved_qty' => 50]);
        $requisition->update(['status' => 'approved', 'approved_by' => $user->id, 'approved_at' => now()]);

        $issue = InvIssue::create([
            'requisition_id' => $requisition->id, 'store_id' => $mainStore->id, 'to_store_id' => $cuttingStore->id,
            'department_id' => $department->id, 'issue_date' => now(), 'issued_by' => $user->id, 'created_by' => $user->id,
        ]);
        $issue->items()->create(['requisition_item_id' => $reqItem->id, 'item_id' => $item->id, 'issued_qty' => 50]);

        $outTxn = $stock->post([
            'item_id' => $item->id, 'store_id' => $mainStore->id, 'transaction_date' => now()->toDateString(),
            'transaction_type' => 'issue', 'qty_out' => 50, 'department_id' => $department->id,
            'reference_type' => 'inv_issue', 'reference_id' => $issue->id, 'created_by' => $user->id,
        ]);
        $stock->post([
            'item_id' => $item->id, 'store_id' => $cuttingStore->id, 'transaction_date' => now()->toDateString(),
            'transaction_type' => 'issue', 'qty_in' => 50, 'rate' => $outTxn->rate, 'department_id' => $department->id,
            'reference_type' => 'inv_issue', 'reference_id' => $issue->id, 'created_by' => $user->id,
        ]);
        $reqItem->increment('issued_qty', 50);
        $requisition->refreshIssueStatus();

        $this->assertSame('issued', $requisition->fresh()->status);
        $this->assertSame(150.0, $stock->currentStock($item->id, $mainStore->id));
        $this->assertSame(50.0, $stock->currentStock($item->id, $cuttingStore->id));
    }

    public function test_low_stock_detection(): void
    {
        $store = InvStore::create(['name' => 'Store ' . uniqid(), 'code' => 'S' . uniqid(), 'type' => 'raw_material']);
        $category = InvItemCategory::create(['name' => 'Cat ' . uniqid()]);
        $unit = InvUnit::create(['name' => 'Yard', 'short_name' => 'YD']);
        $item = InvItem::create([
            'item_name' => 'Low Stock Item', 'category_id' => $category->id, 'unit_id' => $unit->id,
            'item_type' => 'raw_material', 'minimum_stock' => 500,
            'opening_stock' => 10, 'opening_value' => 100, 'opening_store_id' => $store->id,
        ]);

        $this->assertTrue(app(StockService::class)->isLowStock($item));
    }
}
