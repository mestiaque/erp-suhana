<?php

namespace Tests\Feature;

use App\Models\Permission as RolePermission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use ME\MerchandisingTrace\Models\Bom;
use ME\MerchandisingTrace\Models\BomItem;
use ME\MerchandisingTrace\Models\Buyer;
use ME\MerchandisingTrace\Models\FabricConsumption;
use ME\MerchandisingTrace\Models\Item;
use ME\MerchandisingTrace\Models\MaterialBooking;
use ME\MerchandisingTrace\Models\MaterialReceipt;
use ME\MerchandisingTrace\Models\SalesContract;
use ME\MerchandisingTrace\Models\SalesContractPo;
use ME\MerchandisingTrace\Models\Style;
use ME\MerchandisingTrace\Models\Supplier;
use ME\ProductionTrace\Exceptions\BundleIncompleteException;
use ME\ProductionTrace\Exceptions\PackingNotAuthorizedException;
use ME\ProductionTrace\Exceptions\StageNotAllowedException;
use ME\ProductionTrace\Models\TrcBundle;
use ME\ProductionTrace\Models\TrcBuyerApproval;
use ME\ProductionTrace\Models\TrcBuyerInspection;
use ME\ProductionTrace\Models\TrcCapacityPlan;
use ME\ProductionTrace\Models\TrcCutting;
use ME\ProductionTrace\Models\TrcCuttingItem;
use ME\ProductionTrace\Models\TrcDailyTarget;
use ME\ProductionTrace\Models\TrcFabricIssue;
use ME\ProductionTrace\Models\TrcFabricIssueItem;
use ME\ProductionTrace\Models\TrcFabricRoll;
use ME\ProductionTrace\Models\TrcGarmentUnit;
use ME\ProductionTrace\Models\TrcLine;
use ME\ProductionTrace\Models\TrcOperator;
use ME\ProductionTrace\Models\TrcPart;
use ME\ProductionTrace\Models\TrcPartProcessJob;
use ME\ProductionTrace\Models\TrcPlanLine;
use ME\ProductionTrace\Models\TrcPlanLineSize;
use ME\ProductionTrace\Models\TrcPlanStyle;
use ME\ProductionTrace\Models\TrcProduct;
use ME\ProductionTrace\Models\TrcProductionPlan;
use ME\ProductionTrace\Models\TrcSewingOutput;
use ME\ProductionTrace\Models\TrcSizeGroup;
use ME\ProductionTrace\Models\TrcStyleWorkflow;
use ME\ProductionTrace\Models\TrcWorkflowStage;
use ME\ProductionTrace\Services\BarcodeService;
use ME\ProductionTrace\Services\BundleStageGateService;
use ME\ProductionTrace\Services\BuyerApprovalGateService;
use ME\ProductionTrace\Services\CapacityPlanningService;
use ME\ProductionTrace\Services\GarmentStageGateService;
use ME\ProductionTrace\Services\IECalculationService;
use ME\ProductionTrace\Services\KpiService;
use ME\ProductionTrace\Services\StageGateService;
use ME\ProductionTrace\Services\TrcExtendedReportService;
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
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '3306',
            'DB_DATABASE' => 'suhana_erp_actual',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => 'admin',
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

    public function test_rft_percent(): void
    {
        $kpi = new KpiService;

        $this->assertSame(96.0, $kpi->rftPercent(480, 500));
        $this->assertSame(0.0, $kpi->rftPercent(0, 0));
    }

    // -----------------------------------------------------------------
    // BundleStageGateService — §5.4 hard gate: an incomplete bundle
    // (ok_qty + replaced_qty < bundle_qty) may never advance, and the
    // exception must report the exact shortage.
    // -----------------------------------------------------------------

    public function test_incomplete_bundle_is_blocked_with_exact_shortage(): void
    {
        $bundle = new TrcBundle([
            'bundle_no' => 'BDL-TEST-001',
            'bundle_qty' => 10,
            'ok_qty' => 7,
            'replaced_qty' => 0,
            'status' => 'incomplete',
        ]);

        $this->expectException(BundleIncompleteException::class);
        $this->expectExceptionMessage('shortage of 3 pcs');

        (new BundleStageGateService)->assertIssuable($bundle);
    }

    public function test_complete_bundle_passes_the_gate(): void
    {
        $bundle = new TrcBundle([
            'bundle_no' => 'BDL-TEST-002',
            'bundle_qty' => 10,
            'ok_qty' => 10,
            'replaced_qty' => 0,
            'status' => 'complete',
        ]);

        (new BundleStageGateService)->assertIssuable($bundle);
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

    // -----------------------------------------------------------------
    // StageGateService — §8 WORKFLOW ENGINE, the dynamic per-style
    // routing "single source of truth" that replaces hard-coded stage
    // sequences. Uses the real trc_workflow_stages seed (TrcMasterSeeder)
    // as the zero-config default route.
    // -----------------------------------------------------------------

    public function test_default_route_lets_sewing_qc_skip_optional_wash_and_print_to_finishing(): void
    {
        // No trc_style_workflows override for this style at all — falls
        // back to trc_workflow_stages directly. washing/washing_qc/
        // printing/printing_qc are all seeded is_optional=true, so from
        // sewing_qc every one of them, plus finishing, is a legal next hop.
        $gate = new StageGateService;

        $this->assertTrue($gate->canMove(null, 'garment', 'sewing_qc', 'washing'));
        $this->assertTrue($gate->canMove(null, 'garment', 'sewing_qc', 'printing'));
        $this->assertTrue($gate->canMove(null, 'garment', 'sewing_qc', 'finishing'));
        // But it cannot jump straight to buyer_qc, skipping finishing/internal_final_qc.
        $this->assertFalse($gate->canMove(null, 'garment', 'sewing_qc', 'buyer_qc'));
    }

    public function test_style_override_makes_washing_mandatory_and_blocks_skipping_to_finishing(): void
    {
        $buyer = Buyer::create(['code' => 'BYR-'.uniqid(), 'name' => 'Test Buyer']);
        $style = Style::create(['style_no' => 'STY-'.uniqid(), 'name' => 'Test Style', 'buyer_id' => $buyer->id]);

        $washing = TrcWorkflowStage::where('code', 'washing')->firstOrFail();
        $washingQc = TrcWorkflowStage::where('code', 'washing_qc')->firstOrFail();
        $finishing = TrcWorkflowStage::where('code', 'finishing')->firstOrFail();

        // This style's route: sewing_qc -> washing (required) -> washing_qc (required) -> finishing (required).
        // Print is deliberately left out of the override entirely.
        TrcStyleWorkflow::create(['style_id' => $style->id, 'workflow_stage_id' => $washing->id, 'sequence' => 1, 'is_required' => true]);
        TrcStyleWorkflow::create(['style_id' => $style->id, 'workflow_stage_id' => $washingQc->id, 'sequence' => 2, 'is_required' => true]);
        TrcStyleWorkflow::create(['style_id' => $style->id, 'workflow_stage_id' => $finishing->id, 'sequence' => 3, 'is_required' => true]);

        $gate = new StageGateService;

        // sewing_qc has no matching row in this style's route (its first
        // configured stage is "washing"), so it's treated as "before the
        // route starts" and washing is the only legal next hop.
        $this->assertTrue($gate->canMove($style->id, 'garment', 'sewing_qc', 'washing'));
        $this->assertFalse($gate->canMove($style->id, 'garment', 'sewing_qc', 'finishing'));

        $this->assertTrue($gate->canMove($style->id, 'garment', 'washing_qc', 'finishing'));

        $this->expectException(StageNotAllowedException::class);
        $this->expectExceptionMessage('only: finishing');
        $gate->assertCanMove($style->id, 'garment', 'washing_qc', 'printing');
    }

    public function test_garment_stage_gate_service_uses_stage_gate_for_finishing_readiness(): void
    {
        $garment = new TrcGarmentUnit([
            'code' => 'G-TEST-001',
            'status' => 'active',
            'current_stage' => 'sewing_qc',
        ]);

        // No plan_line_size_id set -> resolveStyleId() returns null -> default route -> allowed.
        (new GarmentStageGateService)->assertReadyForFinishing($garment);
        $this->addToAssertionCount(1);

        $garment->current_stage = 'sewing'; // hasn't even reached sewing_qc yet
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('is not ready for Finishing');
        (new GarmentStageGateService)->assertReadyForFinishing($garment);
    }

    // -----------------------------------------------------------------
    // IECalculationService / CapacityPlanningService —
    // GARMENTS_ERP_MASTER_PROMPT.md §8/§9: "Capacity = Available Minutes
    // × Efficiency ÷ SMV" and the manpower-shortage table.
    // -----------------------------------------------------------------

    public function test_capacity_pieces_formula(): void
    {
        $ie = new IECalculationService;

        // 10 operators x 480 min = 4800 available minutes; 60% efficiency
        // = 2880 effective minutes; / 20 SMV = 144 pcs.
        $this->assertSame(144, $ie->capacityPieces(operatorsAvailable: 10, workingMinutesPerOperator: 480, efficiencyPercent: 60, smv: 20));
        $this->assertSame(0, $ie->capacityPieces(10, 480, 60, 0)); // SMV 0 guarded, no division by zero
    }

    public function test_operators_required_formula_is_inverse_of_capacity(): void
    {
        $ie = new IECalculationService;

        // Same inputs as above but asking "how many operators for 144 pcs?" must round-trip to 10.
        $this->assertSame(10, $ie->operatorsRequired(targetPieces: 144, workingMinutesPerOperator: 480, efficiencyPercent: 60, smv: 20));

        // A higher target of 200 needs 14 (rounds up from 13.89).
        $this->assertSame(14, $ie->operatorsRequired(200, 480, 60, 20));
    }

    public function test_shortage_and_line_efficiency_formulas(): void
    {
        $ie = new IECalculationService;

        $this->assertSame(4, $ie->shortage(required: 14, available: 10));
        $this->assertSame(0, $ie->shortage(required: 8, available: 10)); // never negative

        $this->assertSame(60.0, $ie->lineEfficiencyPercent(outputPieces: 144, smv: 20, operators: 10, workingMinutes: 480));
    }

    public function test_capacity_planning_service_persists_computed_shortage(): void
    {
        $line = TrcLine::create(['code' => 'LN-'.uniqid(), 'name' => 'Test Line']);

        $plan = (new CapacityPlanningService)->save([
            'line_id' => $line->id,
            'plan_date' => now()->toDateString(),
            'working_minutes_per_operator' => 480,
            'operators_available' => 10,
            'efficiency_percent' => 60,
            'smv' => 20,
            'target_pieces' => 200,
        ]);

        $this->assertInstanceOf(TrcCapacityPlan::class, $plan);
        $this->assertSame(144, $plan->capacity_pieces);
        $this->assertSame(14, $plan->operators_required);
        $this->assertSame(4, $plan->shortage_operators);
        $this->assertTrue($plan->hasShortage());
    }

    public function test_capacity_planning_service_without_target_has_no_shortage(): void
    {
        $line = TrcLine::create(['code' => 'LN-'.uniqid(), 'name' => 'Test Line']);

        $plan = (new CapacityPlanningService)->save([
            'line_id' => $line->id,
            'plan_date' => now()->toDateString(),
            'working_minutes_per_operator' => 480,
            'operators_available' => 10,
            'efficiency_percent' => 60,
            'smv' => 20,
        ]);

        $this->assertSame(144, $plan->capacity_pieces);
        $this->assertFalse($plan->hasShortage());
    }

    /**
     * GARMENTS_ERP_MASTER_PROMPT.md §8: "Line efficiency = (output × SMV)
     * ÷ (operators × working minutes) × 100" — the ACTUAL efficiency a
     * line ran at, computed live from real sewing-output scans for that
     * line/date, distinct from efficiency_percent (the planning
     * assumption). Zero output must report 0%, not divide by zero.
     */
    public function test_capacity_plan_actual_efficiency_is_computed_from_real_sewing_output(): void
    {
        $line = TrcLine::create(['code' => 'LN-'.uniqid(), 'name' => 'Test Line']);

        $product = TrcProduct::create(['code' => 'PRD-'.uniqid(), 'name' => 'Test Product']);
        $sizeGroup = TrcSizeGroup::create(['name' => 'Test Size Group '.uniqid()]);
        $prodPlan = TrcProductionPlan::create([
            'plan_no' => 'PP-'.uniqid(), 'buyer_id' => 1, 'product_id' => $product->id,
            'size_group_id' => $sizeGroup->id, 'up_date' => now(), 'status' => 'confirmed',
        ]);
        $planStyle = TrcPlanStyle::create(['production_plan_id' => $prodPlan->id, 'style_id' => 1, 'style_name' => 'Test Style']);
        $planLine = TrcPlanLine::create(['plan_style_id' => $planStyle->id, 'po_number' => 'PO-'.uniqid(), 'color_id' => 1]);

        $capacityPlan = (new CapacityPlanningService)->save([
            'line_id' => $line->id,
            'plan_date' => now()->toDateString(),
            'working_minutes_per_operator' => 480,
            'operators_available' => 10,
            'efficiency_percent' => 60,
            'smv' => 20,
        ]);

        $this->assertSame(0, $capacityPlan->actualOutputQty());
        $this->assertSame(0.0, $capacityPlan->actualEfficiencyPercent());

        // 144 pcs actually sewn today on this line — exactly the planned capacity.
        TrcSewingOutput::create([
            'line_id' => $line->id, 'plan_line_id' => $planLine->id, 'size_id' => 1,
            'output_date' => now()->toDateString(), 'hour_slot' => 1, 'output_qty' => 144,
        ]);

        $this->assertSame(144, $capacityPlan->actualOutputQty());
        // (144 * 20) / (10 * 480) * 100 = 60.0 — matches the 60% target exactly.
        $this->assertSame(60.0, $capacityPlan->actualEfficiencyPercent());
    }

    // -----------------------------------------------------------------
    // TrcScanApiController — the handheld/PWA scan API's newly-added
    // bundle-input and bundle-QC stage/action pairs (P6/P8), on top of
    // the pre-existing sewing output/QC pairs. Exercises the real HTTP
    // route with Sanctum auth + idempotency-key replay protection.
    // -----------------------------------------------------------------

    private function makeOpenBundle(int $bundleQty = 10): TrcBundle
    {
        $product = TrcProduct::create(['code' => 'PRD-'.uniqid(), 'name' => 'Test Product']);
        $sizeGroup = TrcSizeGroup::create(['name' => 'Test Size Group '.uniqid()]);
        $plan = TrcProductionPlan::create([
            'plan_no' => 'PP-'.uniqid(), 'buyer_id' => 1, 'product_id' => $product->id,
            'size_group_id' => $sizeGroup->id, 'up_date' => now(), 'status' => 'confirmed',
        ]);
        $planStyle = TrcPlanStyle::create(['production_plan_id' => $plan->id, 'style_id' => 1, 'style_name' => 'Test Style']);
        $planLine = TrcPlanLine::create(['plan_style_id' => $planStyle->id, 'po_number' => 'PO-'.uniqid(), 'color_id' => 1]);
        $cutting = TrcCutting::create(['cut_no' => 'CUT-'.uniqid(), 'plan_line_id' => $planLine->id, 'cut_date' => now()]);
        $part = TrcPart::create(['code' => 'PT-'.uniqid(), 'name' => 'Test Part']);
        $cuttingItem = TrcCuttingItem::create([
            'cutting_id' => $cutting->id, 'part_id' => $part->id, 'size_id' => 1,
            'required_qty' => $bundleQty, 'cut_qty' => $bundleQty,
        ]);

        $bundle = TrcBundle::create([
            'bundle_no' => 'BDL-'.uniqid(), 'code' => 'BDL-'.uniqid(),
            'cutting_id' => $cutting->id, 'cutting_item_id' => $cuttingItem->id, 'part_id' => $part->id,
            'size_id' => 1, 'plan_line_id' => $planLine->id, 'bundle_qty' => $bundleQty,
        ]);

        app(BarcodeService::class)->issue($bundle, $bundle->code, 'bundle');

        return $bundle;
    }

    /**
     * A bundle already complete/QC-passed and flagged for the given
     * process, plus an open job for it — the state PartProcessJobService
     * expects (BundleStageGateService::assertIssuableToProcess() requires
     * status=complete and the matching requires_* flag).
     */
    private function makeCompleteBundleForProcess(string $processType): array
    {
        $bundle = $this->makeOpenBundle(bundleQty: 10);
        $bundle->update([
            'status' => 'complete',
            'qc_status' => 'passed',
            'ok_qty' => 10,
            'requires_embroidery' => $processType === 'embroidery',
            'requires_print' => $processType === 'print',
        ]);

        $job = TrcPartProcessJob::create([
            'job_no' => 'JOB-'.uniqid(),
            'process_type' => $processType,
            'is_inhouse' => true,
            'plan_line_id' => $bundle->plan_line_id,
            'issue_date' => now(),
            'status' => 'issued',
        ]);

        return [$bundle, $job];
    }

    public function test_scan_api_part_process_issue_receive_and_qc_flow(): void
    {
        [$bundle, $job] = $this->makeCompleteBundleForProcess('embroidery');
        $productionRole = RolePermission::where('name', 'Production')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $productionRole->id]);
        Sanctum::actingAs($user, ['*']);

        // 1. Issue the bundle to the embroidery job.
        $issueResponse = $this->postJson(route('tracing.api.scan'), [
            'code' => $bundle->code, 'stage' => 'part_process', 'action' => 'issue',
            'job_id' => $job->id, 'idempotency_key' => 'test-'.uniqid(),
        ]);
        $issueResponse->assertOk()->assertJson(['ok' => true]);

        // 2. Receive the whole balance back clean.
        $receiveResponse = $this->postJson(route('tracing.api.scan'), [
            'code' => $bundle->code, 'stage' => 'part_process', 'action' => 'receive',
            'job_id' => $job->id, 'idempotency_key' => 'test-'.uniqid(),
        ]);
        $receiveResponse->assertOk()->assertJson(['ok' => true, 'data' => ['status' => 'received']]);

        // 3. QC it pass.
        $qcResponse = $this->postJson(route('tracing.api.scan'), [
            'code' => $bundle->code, 'stage' => 'part_process_qc', 'action' => 'pass',
            'job_id' => $job->id, 'idempotency_key' => 'test-'.uniqid(),
        ]);
        $qcResponse->assertOk()->assertJson(['ok' => true, 'data' => ['result' => 'pass']]);

        // A second issue attempt (already open/closed for this bundle+job) is refused.
        $secondIssue = $this->postJson(route('tracing.api.scan'), [
            'code' => $bundle->code, 'stage' => 'part_process', 'action' => 'issue',
            'job_id' => $job->id, 'idempotency_key' => 'test-'.uniqid(),
        ]);
        $secondIssue->assertStatus(422)->assertJson(['ok' => false]);
    }

    public function test_scan_api_part_process_issue_rejects_unflagged_bundle(): void
    {
        // Bundle flagged for print, but we try to issue it to an embroidery job.
        [$bundle] = $this->makeCompleteBundleForProcess('print');
        $embJob = TrcPartProcessJob::create([
            'job_no' => 'JOB-'.uniqid(), 'process_type' => 'embroidery', 'is_inhouse' => true,
            'plan_line_id' => $bundle->plan_line_id, 'issue_date' => now(), 'status' => 'issued',
        ]);
        $productionRole = RolePermission::where('name', 'Production')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $productionRole->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('tracing.api.scan'), [
            'code' => $bundle->code, 'stage' => 'part_process', 'action' => 'issue',
            'job_id' => $embJob->id, 'idempotency_key' => 'test-'.uniqid(),
        ]);

        $response->assertStatus(422)->assertJson(['ok' => false]);
    }

    public function test_scan_api_bundle_qc_pass_scores_the_whole_balance_and_completes_the_bundle(): void
    {
        $bundle = $this->makeOpenBundle(bundleQty: 10);
        $productionRole = RolePermission::where('name', 'Production')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $productionRole->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('tracing.api.scan'), [
            'code' => $bundle->code,
            'stage' => 'bundle_qc',
            'action' => 'pass',
            'idempotency_key' => 'test-'.uniqid(),
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $this->assertSame('complete', $bundle->fresh()->status);
        $this->assertSame(10, $bundle->fresh()->ok_qty);
    }

    public function test_scan_api_replays_idempotency_key_without_double_counting(): void
    {
        $bundle = $this->makeOpenBundle(bundleQty: 10);
        $productionRole = RolePermission::where('name', 'Production')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $productionRole->id]);
        Sanctum::actingAs($user, ['*']);

        $key = 'test-'.uniqid();
        $payload = ['code' => $bundle->code, 'stage' => 'bundle_qc', 'action' => 'pass', 'idempotency_key' => $key];

        $this->postJson(route('tracing.api.scan'), $payload)->assertOk();
        $this->postJson(route('tracing.api.scan'), $payload)->assertOk();

        // Re-sent scan must not double-apply: still exactly bundle_qty, not 20.
        $this->assertSame(10, $bundle->fresh()->ok_qty);
    }

    public function test_scan_api_bundle_input_to_sewing_requires_a_complete_bundle(): void
    {
        $bundle = $this->makeOpenBundle(bundleQty: 10); // status=open, not yet QC'd/complete
        $line = TrcLine::create(['code' => 'LN-'.uniqid(), 'name' => 'Test Line']);
        $operator = TrcOperator::create(['card_no' => 'OP-'.uniqid(), 'name' => 'Test Operator']);
        $productionRole = RolePermission::where('name', 'Production')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $productionRole->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('tracing.api.scan'), [
            'code' => $bundle->code,
            'stage' => 'sewing',
            'action' => 'in',
            'line_id' => $line->id,
            'operator_id' => $operator->id,
            'idempotency_key' => 'test-'.uniqid(),
        ]);

        // Incomplete bundle is physically refused, not just via the web form.
        $response->assertStatus(422)->assertJson(['ok' => false]);
    }

    public function test_scan_api_finishing_input_scan_requires_sewing_qc_passed_piece(): void
    {
        $product = TrcProduct::create(['code' => 'PRD-'.uniqid(), 'name' => 'Test Product']);
        $sizeGroup = TrcSizeGroup::create(['name' => 'Test Size Group '.uniqid()]);
        $plan = TrcProductionPlan::create([
            'plan_no' => 'PP-'.uniqid(), 'buyer_id' => 1, 'product_id' => $product->id,
            'size_group_id' => $sizeGroup->id, 'up_date' => now(), 'status' => 'confirmed',
        ]);
        $planStyle = TrcPlanStyle::create(['production_plan_id' => $plan->id, 'style_id' => 1, 'style_name' => 'Test Style']);
        $planLine = TrcPlanLine::create(['plan_style_id' => $planStyle->id, 'po_number' => 'PO-'.uniqid(), 'color_id' => 1]);
        $planLineSize = TrcPlanLineSize::create(['plan_line_id' => $planLine->id, 'size_id' => 1, 'order_qty' => 10]);

        $garment = TrcGarmentUnit::create([
            'plan_line_size_id' => $planLineSize->id,
            'code' => 'G-'.uniqid(),
            'serial' => 1,
            'current_stage' => 'sewing_qc',
            'status' => 'active',
        ]);
        app(BarcodeService::class)->issue($garment, $garment->code, 'garment');

        $productionRole = RolePermission::where('name', 'Production')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $productionRole->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('tracing.api.scan'), [
            'code' => $garment->code,
            'stage' => 'finishing',
            'action' => 'in',
            'idempotency_key' => 'test-'.uniqid(),
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $this->assertSame('finishing', $garment->fresh()->current_stage);

        // A second scan attempt (still at 'finishing' now, not 'sewing_qc') is refused.
        $second = $this->postJson(route('tracing.api.scan'), [
            'code' => $garment->code,
            'stage' => 'finishing',
            'action' => 'in',
            'idempotency_key' => 'test-'.uniqid(),
        ]);
        $second->assertStatus(422)->assertJson(['ok' => false]);
    }

    // -----------------------------------------------------------------
    // TrcExtendedReportService — src/work/report.md's 6 extended daily
    // reports. Buyer/Style/PO come through the SalesContractPo <->
    // TrcPlanLine bridge (production_plan_line_id), the same join
    // ShipmentPlanService/ProductionHandoverService already rely on, so
    // this is the highest-risk part of the new code to leave unverified.
    // -----------------------------------------------------------------

    /**
     * @return array{po: SalesContractPo, planLine: TrcPlanLine}
     */
    private function makeReportFixture(string $tag, int $orderQty = 500): array
    {
        $buyer = Buyer::create(['code' => "{$tag}BUY", 'name' => "{$tag} Buyer"]);
        $style = Style::create(['style_no' => "{$tag}-STY", 'name' => "{$tag} Style", 'buyer_id' => $buyer->id]);
        $merchandiser = User::factory()->create(['name' => "{$tag} Merchandiser"]);
        $sc = SalesContract::create([
            'contract_no' => "{$tag}-SC", 'buyer_id' => $buyer->id, 'merchandiser_id' => $merchandiser->id,
            'contract_date' => now()->toDateString(), 'status' => 'confirmed',
        ]);

        $product = TrcProduct::create(['code' => "{$tag}PRD", 'name' => "{$tag} Product"]);
        $sizeGroup = TrcSizeGroup::create(['name' => "{$tag} Size Group"]);
        $plan = TrcProductionPlan::create([
            'plan_no' => "{$tag}-PP", 'buyer_id' => $buyer->id, 'product_id' => $product->id,
            'size_group_id' => $sizeGroup->id, 'up_date' => now(), 'status' => 'confirmed',
        ]);
        $planStyle = TrcPlanStyle::create(['production_plan_id' => $plan->id, 'style_id' => $style->id, 'style_name' => $style->name]);
        $planLine = TrcPlanLine::create(['plan_style_id' => $planStyle->id, 'po_number' => "{$tag}-PO", 'color_id' => 1]);
        TrcPlanLineSize::create(['plan_line_id' => $planLine->id, 'size_id' => 1, 'order_qty' => $orderQty]);

        $po = SalesContractPo::create([
            'sales_contract_id' => $sc->id, 'style_id' => $style->id, 'color_id' => 1,
            'po_no' => "{$tag}-PO", 'po_qty' => $orderQty, 'unit_price' => 4, 'cost_smv' => 12,
            'production_plan_line_id' => $planLine->id,
            'shipment_date' => now()->addDays(30)->toDateString(),
            'fty_committed_delivery' => now()->addDays(25)->toDateString(),
        ]);

        return ['po' => $po, 'planLine' => $planLine, 'buyer' => $buyer, 'style' => $style, 'merchandiser' => $merchandiser];
    }

    public function test_daily_cutting_report_computes_achievement_and_consumption_variance(): void
    {
        $fixture = $this->makeReportFixture('T30', orderQty: 1000);
        $planLine = $fixture['planLine'];
        $today = now()->toDateString();

        TrcDailyTarget::create(['date' => $today, 'plan_line_id' => $planLine->id, 'stage' => 'cutting', 'target_qty' => 200]);

        $issue = TrcFabricIssue::create(['issue_no' => 'T30-ISS', 'plan_line_id' => $planLine->id, 'issue_date' => $today]);
        $roll = TrcFabricRoll::firstOrFail();
        TrcFabricIssueItem::create(['fabric_issue_id' => $issue->id, 'fabric_roll_id' => $roll->id, 'issued_qty' => 190]); // 190m issued

        $cutting = TrcCutting::create([
            'cut_no' => 'T30-CUT', 'plan_line_id' => $planLine->id, 'cut_date' => $today, 'fabric_issue_id' => $issue->id,
        ]);
        $part = TrcPart::create(['code' => 'T30PT', 'name' => 'T30 Part']);
        TrcCuttingItem::create([
            'cutting_id' => $cutting->id, 'part_id' => $part->id, 'size_id' => 1,
            'required_qty' => 200, 'cut_qty' => 190, 'rejected_qty' => 10, 'replaced_qty' => 8,
        ]);

        FabricConsumption::create(['style_id' => $fixture['style']->id, 'color_id' => 1, 'yy' => 1.0000, 'marker_efficiency' => 85.5]);

        $data = app(TrcExtendedReportService::class)->run('daily_cutting', ['date_from' => $today, 'date_to' => $today, 'plan_line_id' => $planLine->id]);
        $this->assertCount(1, $data['rows']);
        $row = $data['rows'][0];

        $this->assertSame($fixture['buyer']->name, $row['Buyer']);
        $this->assertSame($fixture['style']->style_no, $row['Style No.']);
        $this->assertSame($fixture['po']->po_no, $row['PO No.']);
        $this->assertSame(200, $row['Planned Cut']);
        $this->assertSame(190, $row['Actual Cut']);
        $this->assertSame(10, $row['Cut Reject']);
        $this->assertSame(8, $row['Recut Qty']);
        $this->assertSame(180, $row['Net Good Cut']); // 190 actual - 10 reject
        $this->assertSame(95.0, $row['Achievement %']); // 190/200
        $this->assertSame(190.0, $row['Fabric Issued (m)']);
        $this->assertSame(85.5, $row['Marker Efficiency %']);
        $this->assertSame(1.0, $row['Standard Cons./Pc']);
        // Actual Cons./Pc = 190m / 190 actual cut = 1.0 exactly, so 0% variance.
        $this->assertSame(1.0, $row['Actual Cons./Pc']);
        $this->assertSame(0.0, $row['Consumption Var. %']);
    }

    public function test_order_po_status_report_flags_delay_and_risk_before_ex_factory(): void
    {
        $fixture = $this->makeReportFixture('T31', orderQty: 1000);
        $planLine = $fixture['planLine'];

        // Ex-factory date already 2 days in the past and nothing shipped —
        // must be flagged Delayed with positive Delay Days.
        $fixture['po']->update(['fty_committed_delivery' => now()->subDays(2)->toDateString()]);

        $size = TrcPlanLineSize::where('plan_line_id', $planLine->id)->first();
        $size->update(['cut_qty' => 1000, 'sewn_qty' => 800, 'finished_qty' => 500, 'packed_qty' => 200, 'shipped_qty' => 0]);

        $data = app(TrcExtendedReportService::class)->run('order_po_status', ['plan_line_id' => $planLine->id]);
        $this->assertCount(1, $data['rows']);
        $row = $data['rows'][0];

        $this->assertSame($fixture['merchandiser']->name, $row['Responsible']);
        // cut_qty (1000) already meets order_qty; sewn_qty (800) is the first shortfall.
        $this->assertSame('Sewing', $row['Current Stage']);
        $this->assertSame(1000, $row['Order Qty']);
        $this->assertSame(0.0, $row['Completion %']); // shipped 0 / order 1000
        $this->assertSame(1000, $row['Balance Qty']); // order 1000 - shipped 0
        $this->assertSame(2, $row['Delay Days']);
        $this->assertSame('Delayed', $row['Risk Status']);
    }

    /**
     * Regression test for a real bug caught while building this: the
     * bookings query originally matched by style only (mirroring
     * ReportService::bomShortage()), so a style-wide with(['items' => ...])
     * eager-load left OTHER items' bookings in the collection too —
     * Supplier/Expected Arrival for Thread ended up reporting the Fabric
     * supplier. Fixed with an added whereHas('items', ...) so each
     * material's row only ever draws from bookings that actually book it.
     */
    public function test_material_shortage_report_keeps_each_items_supplier_separate(): void
    {
        $fixture = $this->makeReportFixture('T32', orderQty: 1000);
        $po = $fixture['po'];

        $bom = Bom::create(['bom_no' => 'T32-BOM', 'style_id' => $fixture['style']->id, 'version' => 1, 'status' => 'approved']);
        $fabricItem = Item::create(['code' => 'T32FAB', 'name' => 'T32 Fabric', 'type' => 'fabric']);
        $threadItem = Item::create(['code' => 'T32THR', 'name' => 'T32 Thread', 'type' => 'trim']);
        BomItem::create(['bom_id' => $bom->id, 'item_id' => $fabricItem->id, 'item_type' => 'fabric', 'consumption' => 1.0, 'wastage_percent' => 0]);
        BomItem::create(['bom_id' => $bom->id, 'item_id' => $threadItem->id, 'item_type' => 'trim', 'consumption' => 0.01, 'wastage_percent' => 0]);

        $fabricSupplier = Supplier::create(['code' => 'T32SUPF', 'name' => 'T32 Fabric Mill', 'type' => 'fabric']);
        $threadSupplier = Supplier::create(['code' => 'T32SUPT', 'name' => 'T32 Thread Co', 'type' => 'trims']);

        $fabricBooking = MaterialBooking::create([
            'booking_no' => 'T32-MB-FAB', 'type' => 'fabric', 'sales_contract_id' => $po->sales_contract_id,
            'style_id' => $fixture['style']->id, 'supplier_id' => $fabricSupplier->id, 'status' => 'booked',
            'expected_inhouse_date' => now()->addDays(10)->toDateString(),
        ]);
        $fabricBooking->items()->create(['item_id' => $fabricItem->id, 'booked_qty' => 900]);

        $threadBooking = MaterialBooking::create([
            'booking_no' => 'T32-MB-THR', 'type' => 'trims', 'sales_contract_id' => $po->sales_contract_id,
            'style_id' => $fixture['style']->id, 'supplier_id' => $threadSupplier->id, 'status' => 'booked',
            'expected_inhouse_date' => now()->addDays(3)->toDateString(),
        ]);
        $threadBooking->items()->create(['item_id' => $threadItem->id, 'booked_qty' => 20]);

        MaterialReceipt::create(['booking_id' => $fabricBooking->id, 'item_id' => $fabricItem->id, 'receive_date' => now()->toDateString(), 'qty' => 900]);
        MaterialReceipt::create(['booking_id' => $threadBooking->id, 'item_id' => $threadItem->id, 'receive_date' => now()->toDateString(), 'qty' => 20]);

        $data = app(TrcExtendedReportService::class)->run('material_shortage', ['plan_line_id' => null, 'po_no' => $po->po_no]);
        $rows = collect($data['rows'])->keyBy('Material Description');

        $this->assertSame('T32 Fabric Mill', $rows['T32 Fabric']['Supplier']);
        $this->assertSame('T32 Thread Co', $rows['T32 Thread']['Supplier']);
        $this->assertSame(1000.0, $rows['T32 Fabric']['Required Qty']); // 1.0 consumption x 1000 order qty
        $this->assertSame(900.0, $rows['T32 Fabric']['Received Qty']);
        $this->assertSame(100.0, $rows['T32 Fabric']['Shortage Qty']);
        $this->assertSame(10.0, $rows['T32 Thread']['Required Qty']); // 0.01 x 1000
        $this->assertSame(0.0, $rows['T32 Thread']['Shortage Qty']); // 20 received > 10 required
    }
}
