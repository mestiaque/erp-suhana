<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ME\ProductionTrace\Exceptions\BundleIncompleteException;
use ME\ProductionTrace\Exceptions\PackingNotAuthorizedException;
use ME\ProductionTrace\Models\TrcBuyerApproval;
use ME\ProductionTrace\Models\TrcBuyerInspection;
use ME\ProductionTrace\Models\TrcPlanLine;
use ME\ProductionTrace\Models\TrcPlanStyle;
use ME\ProductionTrace\Models\TrcProduct;
use ME\ProductionTrace\Models\TrcProductionPlan;
use ME\ProductionTrace\Models\TrcSizeGroup;
use ME\ProductionTrace\Services\BuyerApprovalGateService;
use ME\ProductionTrace\Services\KpiService;
use Tests\TestCase;

/**
 * Covers the hard gates and KPI formulas mandated by
 * production-trace's src/work/prompt.md §14 (required tests) and §17
 * (definition of done) that had no automated coverage at all before this
 * file — see the gap audit that produced it. Runs against the real MySQL
 * schema (matching production, trc_* tables already migrated there),
 * wrapped in a transaction that's rolled back after every test, same
 * pattern as SflInventoryStockEngineTest / MerchandisingTraceTest.
 */
class ProductionTraceTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    protected function setUp(): void
    {
        // Must run before parent::setUp() — see SflInventoryStockEngineTest
        // for why (DatabaseTransactions starts its transaction during boot).
        $overrides = [
            'DB_CONNECTION' => 'mysql',
            'DB_HOST'       => '127.0.0.1',
            'DB_PORT'       => '3306',
            'DB_DATABASE'   => 'suhana_erp_actual',
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

    // -----------------------------------------------------------------
    // KpiService — §16 formulas, pure functions, no DB needed.
    // -----------------------------------------------------------------

    public function test_achievement_percent(): void
    {
        $kpi = new KpiService;

        $this->assertSame(85.0, $kpi->achievementPercent(425, 500));
        $this->assertSame(0.0, $kpi->achievementPercent(100, 0));
    }

    public function test_dhu_percent(): void
    {
        $kpi = new KpiService;

        $this->assertSame(4.0, $kpi->dhuPercent(20, 500));
        $this->assertSame(0.0, $kpi->dhuPercent(20, 0));
    }

    public function test_rework_and_reject_percent(): void
    {
        $kpi = new KpiService;

        $this->assertSame(10.0, $kpi->reworkPercent(50, 500));
        $this->assertSame(2.0, $kpi->rejectPercent(10, 500));
    }

    public function test_buyer_approval_and_overall_production_percent(): void
    {
        $kpi = new KpiService;

        $this->assertSame(90.0, $kpi->buyerApprovalPercent(900, 1000));
        $this->assertSame(95.5, $kpi->overallProductionAchievementPercent(955, 1000));
    }

    // -----------------------------------------------------------------
    // BundleStageGateService — §5.4 hard gate: an incomplete bundle
    // (ok_qty + replaced_qty < bundle_qty) may never advance, and the
    // exception must report the exact shortage.
    // -----------------------------------------------------------------

    public function test_incomplete_bundle_is_blocked_with_exact_shortage(): void
    {
        $bundle = new \ME\ProductionTrace\Models\TrcBundle([
            'bundle_no' => 'BDL-TEST-001',
            'bundle_qty' => 10,
            'ok_qty' => 7,
            'replaced_qty' => 0,
            'status' => 'incomplete',
        ]);

        $this->expectException(BundleIncompleteException::class);
        $this->expectExceptionMessage('shortage of 3 pcs');

        (new \ME\ProductionTrace\Services\BundleStageGateService)->assertIssuable($bundle);
    }

    public function test_complete_bundle_passes_the_gate(): void
    {
        $bundle = new \ME\ProductionTrace\Models\TrcBundle([
            'bundle_no' => 'BDL-TEST-002',
            'bundle_qty' => 10,
            'ok_qty' => 10,
            'replaced_qty' => 0,
            'status' => 'complete',
        ]);

        (new \ME\ProductionTrace\Services\BundleStageGateService)->assertIssuable($bundle);
        $this->addToAssertionCount(1); // reaching here without an exception is the assertion
    }

    // -----------------------------------------------------------------
    // BuyerApprovalGateService — §5.9 HARD GATE: released_qty is the only
    // quantity Packing may ever consume.
    // -----------------------------------------------------------------

    private function makePlanLineWithApproval(int $releasedQty): TrcPlanLine
    {
        $product = TrcProduct::create(['code' => 'PRD-'.uniqid(), 'name' => 'Test Product']);
        $sizeGroup = TrcSizeGroup::create(['name' => 'Test Size Group '.uniqid()]);

        $plan = TrcProductionPlan::create([
            'plan_no' => 'PP-'.uniqid(),
            'buyer_id' => 1, // soft reference — merchandising-trace not migrated in this test run
            'product_id' => $product->id,
            'size_group_id' => $sizeGroup->id,
            'up_date' => now(),
            'status' => 'confirmed',
        ]);

        $planStyle = TrcPlanStyle::create([
            'production_plan_id' => $plan->id,
            'style_id' => 1,
            'style_name' => 'Test Style',
        ]);

        $planLine = TrcPlanLine::create([
            'plan_style_id' => $planStyle->id,
            'po_number' => 'PO-'.uniqid(),
            'color_id' => 1,
        ]);

        $inspection = TrcBuyerInspection::create([
            'inspection_no' => 'BI-'.uniqid(),
            'plan_line_id' => $planLine->id,
            'inspection_type' => 'final',
            'inspection_date' => now(),
            'offered_qty' => 100,
            'inspected_qty' => 100,
            'result' => 'approved',
        ]);

        TrcBuyerApproval::create([
            'buyer_inspection_id' => $inspection->id,
            'approved_qty' => $releasedQty,
            'released_qty' => $releasedQty,
            'status' => $releasedQty > 0 ? 'released' : 'pending',
        ]);

        return $planLine;
    }

    public function test_packing_without_any_release_is_blocked(): void
    {
        $planLine = $this->makePlanLineWithApproval(releasedQty: 0);

        $this->expectException(PackingNotAuthorizedException::class);

        (new BuyerApprovalGateService)->assertPackable($planLine, newQty: 10);
    }

    public function test_packing_beyond_released_balance_is_blocked(): void
    {
        $planLine = $this->makePlanLineWithApproval(releasedQty: 100);

        $this->expectException(PackingNotAuthorizedException::class);

        // 60 already packed + 50 new = 110 > 100 released
        (new BuyerApprovalGateService)->assertPackable($planLine, newQty: 50, alreadyPackedQty: 60);
    }

    public function test_packing_within_released_balance_is_allowed(): void
    {
        $planLine = $this->makePlanLineWithApproval(releasedQty: 100);

        (new BuyerApprovalGateService)->assertPackable($planLine, newQty: 40, alreadyPackedQty: 60);
        $this->addToAssertionCount(1);
    }
}
