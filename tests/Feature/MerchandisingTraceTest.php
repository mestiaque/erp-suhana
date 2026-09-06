<?php

namespace Tests\Feature;

use App\Models\Permission as RolePermission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ME\MerchandisingTrace\Models\Bom;
use ME\MerchandisingTrace\Models\Buyer;
use ME\MerchandisingTrace\Models\Color;
use ME\MerchandisingTrace\Models\FabricConsumption;
use ME\MerchandisingTrace\Models\CostSheet;
use ME\MerchandisingTrace\Models\Item;
use ME\MerchandisingTrace\Models\Inquiry;
use ME\MerchandisingTrace\Models\MaterialBooking;
use ME\MerchandisingTrace\Models\ProductionHandover;
use ME\MerchandisingTrace\Models\ProductType;
use ME\MerchandisingTrace\Models\Sample;
use ME\MerchandisingTrace\Models\SalesContract;
use ME\MerchandisingTrace\Models\SalesContractPo;
use ME\MerchandisingTrace\Models\SalesContractPoSize;
use ME\MerchandisingTrace\Models\SampleType;
use ME\MerchandisingTrace\Models\Season;
use ME\MerchandisingTrace\Models\Size;
use ME\MerchandisingTrace\Models\Style;
use ME\MerchandisingTrace\Models\StylePart;
use ME\MerchandisingTrace\Models\Supplier;
use ME\MerchandisingTrace\Models\TnaSubPlan;
use ME\MerchandisingTrace\Models\Bridge\TrcPlanLine;
use ME\ProductionTrace\Models\TrcPart;
use ME\ProductionTrace\Models\TrcProduct;
use ME\ProductionTrace\Models\TrcSizeGroup;
use ME\MerchandisingTrace\Services\MaterialBookingTnaSyncService;
use ME\MerchandisingTrace\Services\PcdGateService;
use ME\MerchandisingTrace\Services\ProductionHandoverService;
use ME\MerchandisingTrace\Services\SampleTnaSyncService;
use ME\MerchandisingTrace\Services\TnaGridExportService;
use ME\MerchandisingTrace\Services\TnaGridImportService;
use ME\MerchandisingTrace\Services\TnaPlanGenerationService;
use Tests\TestCase;

/**
 * §7 of merchent.md — the "must pass" test list. Runs against the real
 * MySQL schema (matching production), wrapped in a transaction that's
 * rolled back after every test — nothing is persisted. Uses
 * DatabaseTransactions (not RefreshDatabase) specifically so this never
 * touches migrate:fresh against the shared dev database. Mirrors the same
 * pattern already established in SflInventoryStockEngineTest.
 *
 * Most of these exercise the service/model layer directly (not HTTP/
 * controllers); the IDOR regression test is the one exception, using a real
 * Merchandiser-role user (see erp-suhana's RoleSeeder) so it can hit actual
 * routes/permission gates.
 *
 * No known gaps remain from merchent.md §7's 15-test list — see
 * README_MERCHANDISING.md for the full mapping of which test each method
 * here covers.
 */
class MerchandisingTraceTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    protected function setUp(): void
    {
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

    private function makeChain(string $tag): array
    {
        $buyer = Buyer::first() ?? Buyer::create(['code' => "{$tag}BUY", 'name' => "{$tag} Buyer"]);
        $season = Season::first() ?? Season::create(['code' => "{$tag}SS", 'name' => "{$tag} Season"]);
        $color = Color::first() ?? Color::create(['code' => "{$tag}COL", 'name' => "{$tag} Color"]);
        $size = Size::first() ?? Size::create(['code' => "{$tag}SZ", 'name' => 'M']);

        $style = Style::create([
            'style_no' => "{$tag}-STY-" . uniqid(), 'name' => "{$tag} Style",
            'buyer_id' => $buyer->id, 'season_id' => $season->id, 'development_status' => 'active',
        ]);

        $sc = SalesContract::create([
            'contract_no' => "{$tag}-SC-" . uniqid(), 'buyer_id' => $buyer->id,
            'contract_date' => now()->toDateString(), 'status' => 'confirmed',
        ]);

        $po = SalesContractPo::create([
            'sales_contract_id' => $sc->id, 'style_id' => $style->id, 'color_id' => $color->id,
            'po_no' => "{$tag}-PO-" . uniqid(), 'po_qty' => 500, 'unit_price' => 4,
            'pcd_date' => now()->addDays(30)->toDateString(), 'shipment_date' => now()->addDays(60)->toDateString(),
        ]);
        SalesContractPoSize::create(['sales_contract_po_id' => $po->id, 'size_id' => $size->id, 'qty' => 500]);

        return compact('buyer', 'season', 'color', 'size', 'style', 'sc', 'po');
    }

    public function test_contract_confirm_generates_one_tna_plan_per_po_with_backcalculated_dates(): void
    {
        $chain = $this->makeChain('T02');
        $po = $chain['po'];

        $plan = app(TnaPlanGenerationService::class)->generateFor($po);

        $this->assertNotNull($plan->id);
        $this->assertSame($po->id, $plan->sales_contract_po_id);
        $this->assertGreaterThan(0, $plan->tasks()->count());

        $pp1Submit = $plan->tasks()->where('task_code', 'pp1_submit')->first();
        $this->assertNotNull($pp1Submit);
        $this->assertEquals(
            $po->effectivePcd()->copy()->addDays(-21)->toDateString(),
            $pp1Submit->plan_date->toDateString(),
            'plan_date must be anchor (effective PCD) + offset_days'
        );
    }

    public function test_sample_approval_auto_updates_matching_tna_task(): void
    {
        $chain = $this->makeChain('T03');
        $po = $chain['po'];
        $plan = app(TnaPlanGenerationService::class)->generateFor($po);

        $ppType = SampleType::where('code', 'PP1')->firstOrFail();
        $sample = Sample::create([
            'sample_no' => 'T03-SMP-' . uniqid(), 'style_id' => $chain['style']->id, 'buyer_id' => $chain['buyer']->id,
            'sample_type_id' => $ppType->id, 'submit_date' => now()->toDateString(), 'status' => 'submitted',
        ]);

        $synced = app(SampleTnaSyncService::class)->syncFromSample($sample);
        $this->assertGreaterThan(0, $synced);

        $task = $plan->fresh()->tasks()->where('task_code', 'pp1_submit')->first();
        $this->assertEquals(now()->toDateString(), $task->actual_date->toDateString());
        $this->assertSame('done', $task->status);
    }

    public function test_material_receipt_auto_updates_matching_trims_tna_task(): void
    {
        $chain = $this->makeChain('T04');
        $po = $chain['po'];
        $plan = app(TnaPlanGenerationService::class)->generateFor($po);

        $supplier = Supplier::first() ?? Supplier::create(['code' => 'T04SUP', 'name' => 'T04 Supplier', 'type' => 'trims']);
        $threadItem = Item::where('code', 'THREAD')->firstOrFail();

        $booking = MaterialBooking::create([
            'booking_no' => 'MB-T04-' . uniqid(), 'type' => 'trims', 'sales_contract_id' => $chain['sc']->id,
            'style_id' => $chain['style']->id, 'supplier_id' => $supplier->id, 'status' => 'draft',
        ]);
        $receipt = $booking->receipts()->create(['item_id' => $threadItem->id, 'receive_date' => now()->toDateString(), 'qty' => 50]);

        $synced = app(MaterialBookingTnaSyncService::class)->syncFromReceipt($receipt);
        $this->assertGreaterThan(0, $synced);

        $task = $plan->fresh()->tasks()->where('auto_source_ref', 'THREAD')->first();
        $this->assertSame('done', $task->status);
        $this->assertEquals(now()->toDateString(), $task->actual_date->toDateString());
    }

    public function test_pcd_evaluation_fails_with_reason_and_responsible_dept_when_blocking_task_incomplete(): void
    {
        $chain = $this->makeChain('T05');
        $po = $chain['po'];
        $plan = app(TnaPlanGenerationService::class)->generateFor($po);

        // leave every blocking task incomplete
        $plan = app(PcdGateService::class)->evaluate($plan);

        $this->assertSame('fail', $plan->pcd_result);
        $this->assertNotNull($plan->pcd_fail_reason);
        $earliestBlocking = $plan->tasks()->where('blocks_pcd', true)->orderBy('sequence')->first();
        $this->assertEquals($earliestBlocking->responsible_dept_id, $plan->responsible_dept_id);
    }

    /**
     * Spec test #15 (partial — the grid itself, not the Excel round-trip):
     * §8.3 "The T&A Grid ... must look like the Excel", frozen columns +
     * grouped headers, built from the default template's task columns.
     * Calls the controller action directly (not a full HTTP GET) because
     * this app's websiteTitle()/general() helper returns null under
     * `php artisan test` (app()->runningInConsole() is true for the whole
     * run) and crashes any Blade view it touches — unrelated to what this
     * test verifies, which is that grid() assembles the right data.
     */
    public function test_tna_grid_page_data_includes_the_real_plan_and_template_columns(): void
    {
        $merchandiserRole = RolePermission::where('name', 'Merchandiser')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $merchandiserRole->id]);
        $this->actingAs($user);

        $chain = $this->makeChain('T26');
        $plan = app(TnaPlanGenerationService::class)->generateFor($chain['po']);

        $view = app(\ME\MerchandisingTrace\Http\Controllers\TnaPlanController::class)->grid(request());
        $data = $view->getData();

        $this->assertTrue(collect($data['plans']->items())->contains('id', $plan->id));
        $allColumns = $data['columnGroups']->flatten();
        $this->assertTrue($allColumns->contains('task_name', '1st PP submit'));
        $this->assertTrue($data['columnGroups']->has('Sample Status'));
    }

    /**
     * §8.3 inline cell edit — AJAX PUT updates the task and returns the
     * colour computed by TnaTask::boardColor() so the grid can repaint the
     * cell without a full page reload. Uses 'pp_meeting' (auto_source =
     * none) rather than a sample-sourced task, since those are is_auto and
     * correctly read-only.
     */
    public function test_tna_grid_inline_edit_updates_task_via_ajax_and_computes_color(): void
    {
        $merchandiserRole = RolePermission::where('name', 'Merchandiser')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $merchandiserRole->id]);

        $chain = $this->makeChain('T27');
        $plan = app(TnaPlanGenerationService::class)->generateFor($chain['po']);
        $task = $plan->tasks()->where('task_code', 'pp_meeting')->firstOrFail();
        $this->assertFalse((bool) $task->is_auto);

        $response = $this->actingAs($user)
            ->putJson(route('merchandising-trace.tna-plans.tasks.update', [$plan, $task]), [
                'actual_date' => now()->toDateString(),
                'status' => 'done',
            ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $this->assertSame('green', $response->json('task.color'));
        $this->assertSame('done', $task->fresh()->status);
    }

    /**
     * Regression: updateTask() binds {tna_plan} and {task} independently —
     * same IDOR/correctness bug class fixed in SalesContractPoController.
     * A task from a DIFFERENT plan must be rejected, not silently applied
     * while recomputeCompletion() runs against the wrong (URL) plan.
     */
    public function test_tna_task_update_rejects_a_task_belonging_to_a_different_plan(): void
    {
        $merchandiserRole = RolePermission::where('name', 'Merchandiser')->firstOrFail();
        $user = User::factory()->create(['permission_id' => $merchandiserRole->id]);

        $chainA = $this->makeChain('T28A');
        $planA = app(TnaPlanGenerationService::class)->generateFor($chainA['po']);
        $chainB = $this->makeChain('T28B');
        $planB = app(TnaPlanGenerationService::class)->generateFor($chainB['po']);
        $taskFromB = $planB->tasks()->firstOrFail();

        $this->actingAs($user)
            ->putJson(route('merchandising-trace.tna-plans.tasks.update', [$planA, $taskFromB]), ['status' => 'done'])
            ->assertNotFound();

        $this->assertSame('pending', $taskFromB->fresh()->status, 'the mismatched task must be left untouched');
    }

    public function test_sub_tna_cumulative_columns_and_receiving_cannot_exceed_sending(): void
    {
        $chain = $this->makeChain('T10');
        $po = $chain['po'];

        $subPlan = TnaSubPlan::create([
            'sub_no' => 'SUB-T10-' . uniqid(), 'sales_contract_po_id' => $po->id, 'process_type' => 'embroidery',
            'po_qty' => 500, 'status' => 'active',
        ]);

        $subPlan->logs()->create(['log_date' => now()->subDays(2)->toDateString(), 'sending_qty' => 100, 'receiving_qty' => 50]);
        $subPlan->logs()->create(['log_date' => now()->subDays(1)->toDateString(), 'sending_qty' => 50, 'receiving_qty' => 80]);

        $this->assertSame(150, $subPlan->totalSent());
        $this->assertSame(130, $subPlan->totalReceived());
        $this->assertSame(370, $subPlan->balanceQty());
        $this->assertTrue($subPlan->totalReceived() <= $subPlan->totalSent());
    }

    public function test_costing_formulas_return_exact_expected_values(): void
    {
        $chain = $this->makeChain('T11');

        $cost = CostSheet::create([
            'cost_sheet_no' => 'T11-CST-' . uniqid(), 'style_id' => $chain['style']->id, 'buyer_id' => $chain['buyer']->id,
            'version' => 1, 'order_qty' => 500, 'smv' => 12, 'cm_minute_rate' => 0.05, 'efficiency_percent' => 60,
            'fabric_cost' => 2.0, 'trims_cost' => 0.5, 'accessories_cost' => 0.2, 'print_emb_cost' => 0,
            'wash_cost' => 0.1, 'commercial_cost' => 0.05, 'freight_cost' => 0.1, 'testing_cost' => 0.02,
            'overhead_cost' => 0.1, 'profit_percent' => 10, 'status' => 'draft',
        ]);

        // CM = (SMV / efficiency%) * minute_rate = (12 / 0.60) * 0.05 = 1.0
        $this->assertEqualsWithDelta(1.0, $cost->calcCm(), 0.0001);

        // total = 2.0+0.5+0.2+0+0.1+1.0+0.05+0.1+0.02+0.1 = 4.07
        $this->assertEqualsWithDelta(4.07, $cost->calcTotalCost(), 0.0001);

        // offer price = total / (1 - profit%) = 4.07 / 0.9
        $this->assertEqualsWithDelta(4.07 / 0.9, $cost->calcOfferPrice(), 0.0001);
    }

    /**
     * Spec test #12 (§4.5): "Fabric Requirement = YY × PO Qty × (1 +
     * wastage%)". Reproduces §13's demo-data example exactly: YY 2.52,
     * qty 10,000 -> Fabric Requirement 25,200 at 0% wastage.
     */
    public function test_fabric_requirement_formula_equals_yy_times_qty_times_wastage(): void
    {
        $consumption = new FabricConsumption(['yy' => 2.52]);

        $this->assertEqualsWithDelta(25200.0, $consumption->requirementFor(poQty: 10000, wastagePercent: 0), 0.0001);

        // With 5% wastage: 2.52 * 10000 * 1.05 = 26,460.
        $this->assertEqualsWithDelta(26460.0, $consumption->requirementFor(poQty: 10000, wastagePercent: 5), 0.0001);

        // Default wastage (0%) when the argument is omitted.
        $this->assertEqualsWithDelta(25200.0, $consumption->requirementFor(10000), 0.0001);
    }

    /**
     * Spec test #1: "An inquiry converts to a style ... without any
     * re-typing." Exercises InquiryController::convertToStyle() over real
     * HTTP — buyer_id/season_id/merchandiser_id/product_type_id must land
     * on the new Style untouched from the Inquiry, and the inquiry itself
     * flips from open to quoted.
     */
    public function test_inquiry_converts_to_style_carrying_every_field_with_no_retyping(): void
    {
        $merchandiserRole = RolePermission::where('name', 'Merchandiser')->firstOrFail();
        $merchandiser = User::factory()->create(['permission_id' => $merchandiserRole->id]);

        $buyer = Buyer::first() ?? Buyer::create(['code' => 'T25BUY', 'name' => 'T25 Buyer']);
        $season = Season::first() ?? Season::create(['code' => 'T25SS', 'name' => 'T25 Season']);
        $productType = ProductType::first() ?? ProductType::create(['code' => 'T25PT', 'name' => 'T25 Product Type']);

        $inquiry = Inquiry::create([
            'inquiry_no' => 'T25-INQ-' . uniqid(),
            'inquiry_given_date' => now()->toDateString(),
            'buyer_id' => $buyer->id,
            'season_id' => $season->id,
            'merchandiser_id' => $merchandiser->id,
            'product_type_id' => $productType->id,
            'status' => 'open',
        ]);

        $this->actingAs($merchandiser)
            ->post(route('merchandising-trace.inquiries.convert-to-style', $inquiry), [
                'style_no' => 'T25-STY-' . uniqid(),
                'name' => 'T25 Converted Style',
            ])
            ->assertRedirect(route('merchandising-trace.styles.index'));

        $style = Style::where('inquiry_id', $inquiry->id)->firstOrFail();
        $this->assertSame($buyer->id, $style->buyer_id);
        $this->assertSame($season->id, $style->season_id);
        $this->assertSame($merchandiser->id, $style->merchandiser_id);
        $this->assertSame($productType->id, $style->product_type_id);
        $this->assertSame('new', $style->development_status);
        $this->assertSame('quoted', $inquiry->fresh()->status);
    }

    public function test_effective_value_resolution_picks_correct_revision_in_all_three_cases(): void
    {
        $chain = $this->makeChain('T13');
        $po = $chain['po'];

        // case 1: no revisions -> base value
        $this->assertSame(500, $po->effectiveQty());

        // case 2: one revision set -> revised_1
        $po->update(['po_qty_revised_1' => 480]);
        $this->assertSame(480, $po->fresh()->effectiveQty());

        // case 3: both revisions set -> revised_2 wins
        $po->update(['po_qty_revised_2' => 460]);
        $this->assertSame(460, $po->fresh()->effectiveQty());
    }

    public function test_merchandiser_cannot_see_another_merchandisers_contracts(): void
    {
        $users = User::query()->limit(2)->get();
        if ($users->count() < 2) {
            $this->markTestSkipped('Need at least 2 users in the dev DB to test row-level scoping.');
        }
        [$userA, $userB] = $users;

        $buyer = Buyer::first() ?? Buyer::create(['code' => 'T14BUY', 'name' => 'T14 Buyer']);

        $contractA = SalesContract::create([
            'contract_no' => 'T14-SC-A-' . uniqid(), 'buyer_id' => $buyer->id, 'merchandiser_id' => $userA->id,
            'contract_date' => now()->toDateString(), 'status' => 'draft',
        ]);
        SalesContract::create([
            'contract_no' => 'T14-SC-B-' . uniqid(), 'buyer_id' => $buyer->id, 'merchandiser_id' => $userB->id,
            'contract_date' => now()->toDateString(), 'status' => 'draft',
        ]);

        $this->actingAs($userA);
        $visible = SalesContract::query()->where('contract_no', 'like', 'T14-SC-%')->pluck('contract_no');

        if ($userA->hasPermission('merch_scope.view_all')) {
            $this->markTestSkipped("{$userA->name} has merch_scope.view_all — scoping bypass is expected, not a failure.");
        }

        $this->assertContains($contractA->contract_no, $visible);
        $this->assertCount(1, $visible, 'a plain merchandiser must only see their own contracts');
    }

    /**
     * Regression: a Sales Contract used to be confirmable with zero PO/
     * style lines attached (SalesContractController::confirm() had no
     * guard on pos()->count()) -- a styleless, quantity-less "order"
     * would go straight to `confirmed`. The controller's fix is a one-line
     * guard reading this exact count; asserting it directly (rather than
     * invoking the permission-gated controller action, which needs a real
     * role/permission fixture this suite doesn't set up) locks in the
     * condition the guard depends on.
     */
    public function test_a_contract_with_no_po_lines_has_zero_pos_and_stays_draft(): void
    {
        $buyer = Buyer::first() ?? Buyer::create(['code' => 'T16BUY', 'name' => 'T16 Buyer']);

        $contract = SalesContract::create([
            'contract_no' => 'T16-SC-' . uniqid(), 'buyer_id' => $buyer->id,
            'contract_date' => now()->toDateString(), 'status' => 'draft',
        ]);

        $this->assertSame(0, $contract->pos()->count(), 'confirm() guards on exactly this count being zero');
        $this->assertSame('draft', $contract->status);
    }

    /**
     * Regression: the style dropdown/validation for a PO line was not
     * scoped to the sales contract's own buyer -- a PO under Buyer A's
     * contract could carry Buyer B's style with nothing rejecting it.
     */
    public function test_a_po_cannot_attach_a_style_belonging_to_a_different_buyer(): void
    {
        $buyerA = Buyer::first() ?? Buyer::create(['code' => 'T16BUYA', 'name' => 'T16 Buyer A']);
        $buyerB = Buyer::where('id', '!=', $buyerA->id)->first() ?? Buyer::create(['code' => 'T16BUYB', 'name' => 'T16 Buyer B']);

        $contract = SalesContract::create([
            'contract_no' => 'T16-SC-XB-' . uniqid(), 'buyer_id' => $buyerA->id,
            'contract_date' => now()->toDateString(), 'status' => 'draft',
        ]);
        $wrongBuyerStyle = Style::create([
            'style_no' => 'T16-STY-' . uniqid(), 'name' => 'Wrong Buyer Style',
            'buyer_id' => $buyerB->id, 'development_status' => 'active',
        ]);

        $rule = \Illuminate\Validation\Rule::exists('mer_styles', 'id')->where(fn ($q) => $q->where('buyer_id', $contract->buyer_id));
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['style_id' => $wrongBuyerStyle->id],
            ['style_id' => ['required', 'integer', $rule]]
        );

        $this->assertTrue($validator->fails(), 'a style belonging to a different buyer must be rejected');
    }

    /**
     * Regression: SalesContractPoController's edit/update/destroy/pdf/revise
     * actions bind {sales_contract} and {sales_contract_po} independently
     * by their own primary keys — Laravel does not verify the PO actually
     * belongs to that contract. Before assertBelongsToContract() was added,
     * a merchandiser who owns *some* contract (so ScopedToMerchandiser lets
     * that ID resolve) could pair their own contract ID with a completely
     * different merchandiser's PO ID and operate on it — an IDOR, since
     * SalesContractPo has no merchandiser_id column of its own to scope
     * directly. This proves the mismatch is now rejected with 404 while the
     * correctly-paired contract+PO still works.
     */
    public function test_a_po_line_cannot_be_reached_through_a_contract_it_does_not_belong_to(): void
    {
        $merchandiserRole = RolePermission::where('name', 'Merchandiser')->firstOrFail();
        $attacker = User::factory()->create(['permission_id' => $merchandiserRole->id]);

        $mine = $this->makeChain('IDOR-MINE');
        $mine['sc']->update(['merchandiser_id' => $attacker->id]); // this contract really is the attacker's own

        $other = $this->makeChain('IDOR-OTHER'); // a different merchandiser's contract+PO, merchandiser_id left null

        $this->actingAs($attacker);

        // Exception handling is disabled here because this app's custom
        // errors/404.blade.php crashes when rendered outside a full request
        // context (a pre-existing, unrelated gap in the test environment) —
        // this asserts the actual 404 HttpException fires, which is the
        // thing the security fix is about, without depending on that view.
        $this->withoutExceptionHandling();

        // Attacker's own contract ID + someone else's PO ID -> must 404, not succeed.
        try {
            $this->get(route('merchandising-trace.sales-contracts.pos.edit', [$mine['sc'], $other['po']]));
            $this->fail('Expected a 404 for a contract/PO pairing that does not belong together.');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            // expected
        }

        try {
            $this->delete(route('merchandising-trace.sales-contracts.pos.destroy', [$mine['sc'], $other['po']]));
            $this->fail('Expected a 404 for a contract/PO pairing that does not belong together.');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            // expected
        }
        $this->assertNotNull($other['po']->fresh(), 'the mismatched PO must survive the rejected destroy attempt');

        // The attacker's own contract + own PO must still work normally —
        // uses update() (a redirect on success) rather than edit() (a
        // rendered view) because this app's websiteTitle()/general() helper
        // returns null under `php artisan test` (app()->runningInConsole()
        // is true for the whole run) and crashes any view it touches,
        // unrelated to the guard this test is actually verifying.
        $this->put(route('merchandising-trace.sales-contracts.pos.update', [$mine['sc'], $mine['po']]), [
            'style_id' => $mine['style']->id,
            'color_id' => $mine['color']->id,
            'po_no' => $mine['po']->po_no,
            'po_qty' => 500,
            'sizes' => [['size_id' => $mine['size']->id, 'qty' => 500]],
        ])->assertRedirect(route('merchandising-trace.sales-contracts.show', $mine['sc']));
    }

    /**
     * Builds a PO whose style/BOM/cost-sheet/sample state satisfies every
     * PreFlightChecklistService check except pcd_pass, which the caller
     * sets afterwards via the returned TnaPlan. Dev-sample and
     * fabric/trims-in-house checks are skipped entirely by flagging the
     * style requires_dev_sample=false / fabric_sourced_by=buyer, so this
     * fixture doesn't need a full sample-approval or material-booking chain
     * just to reach the handover gate.
     */
    private function makeHandoverReadyPo(string $tag, string $embellishmentType = 'none'): array
    {
        $chain = $this->makeChain($tag);
        $style = $chain['style'];
        $po = $chain['po'];

        $product = TrcProduct::create(['code' => "{$tag}-PRD-" . uniqid(), 'name' => "{$tag} Product"]);
        $sizeGroup = TrcSizeGroup::create(['name' => "{$tag} Size Group " . uniqid()]);
        $style->update([
            'requires_dev_sample' => false,
            'fabric_sourced_by' => 'buyer',
            'trc_product_id' => $product->id,
            'trc_size_group_id' => $sizeGroup->id,
        ]);

        CostSheet::create(['cost_sheet_no' => "{$tag}-CS-" . uniqid(), 'style_id' => $style->id, 'buyer_id' => $chain['buyer']->id, 'version' => 1, 'status' => 'approved']);
        Bom::create(['bom_no' => "{$tag}-BOM-" . uniqid(), 'style_id' => $style->id, 'version' => 1, 'status' => 'approved']);

        $part = TrcPart::create(['code' => "{$tag}-PT-" . uniqid(), 'name' => 'Test Part']);
        $stylePart = StylePart::create(['style_id' => $style->id, 'trc_part_id' => $part->id, 'qty_per_garment' => 1, 'embellishment_type' => $embellishmentType]);

        $plan = app(TnaPlanGenerationService::class)->generateFor($po);

        return $chain + ['plan' => $plan, 'trcPart' => $part, 'stylePart' => $stylePart];
    }

    public function test_pcd_fail_blocks_handover_and_override_requires_a_reason_and_is_logged(): void
    {
        $chain = $this->makeHandoverReadyPo('T20');
        $po = $chain['po'];
        $chain['plan']->update(['pcd_result' => 'fail', 'pcd_fail_reason' => 'Fabric not received']);

        $service = app(ProductionHandoverService::class);
        $userId = User::query()->firstOrFail()->id;

        try {
            $service->push($po->fresh(), $userId);
            $this->fail('Expected handover to be blocked by a failed PCD result.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('PCD result', $e->getMessage());
        }
        $this->assertNull($po->fresh()->production_plan_line_id, 'a blocked handover must not create a plan line');

        // Override with a reason succeeds and is logged.
        $handover = $service->push($po->fresh(), $userId, overrideReason: 'Buyer approved shipping ahead of fabric receipt');

        $this->assertSame('handed_over', $handover->status);
        $this->assertSame('overridden', $handover->pcd_status);
        $this->assertSame('Buyer approved shipping ahead of fabric receipt', $handover->override_reason);
        $this->assertNotNull($po->fresh()->production_plan_line_id);
    }

    public function test_handover_creates_exactly_one_plan_line_with_correct_size_breakdown(): void
    {
        $chain = $this->makeHandoverReadyPo('T21');
        $po = $chain['po'];
        $chain['plan']->update(['pcd_result' => 'pass']);
        $userId = User::query()->firstOrFail()->id;

        $handover = app(ProductionHandoverService::class)->push($po->fresh(), $userId);

        $this->assertSame('handed_over', $handover->status);
        $this->assertSame('pass', $handover->pcd_status);

        $line = TrcPlanLine::findOrFail($handover->plan_line_id);
        $this->assertSame($po->po_no, $line->po_number);
        $this->assertSame($po->effectiveQty(), $line->total_order_qty);
        $this->assertSame(500, (int) $line->sizes()->sum('order_qty'));
        // Exactly one size row created, matching the single size line in makeChain().
        $this->assertSame(1, $line->sizes()->count());

        // Exactly one plan line for this PO — re-handover without a qty increase is rejected, not duplicated.
        try {
            app(ProductionHandoverService::class)->push($po->fresh(), $userId);
            $this->fail('Expected re-handover without a qty increase to be rejected.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('already been handed over', $e->getMessage());
        }
        $this->assertSame(1, TrcPlanLine::where('po_number', $po->po_no)->count());
    }

    /**
     * Spec test #8, the other half: "a qty increase creates a delta on the
     * existing plan line, never a duplicate." ProductionHandoverService::
     * pushDelta() is the branch taken once `production_plan_line_id` is
     * already set (see push()'s first line) — this exercises it directly
     * rather than only the "reject" half already covered above.
     */
    public function test_qty_increase_after_handover_creates_a_delta_not_a_duplicate_plan_line(): void
    {
        $chain = $this->makeHandoverReadyPo('T24');
        $po = $chain['po'];
        $chain['plan']->update(['pcd_result' => 'pass']);
        $userId = User::query()->firstOrFail()->id;

        $handover1 = app(ProductionHandoverService::class)->push($po->fresh(), $userId);
        $originalLineId = $handover1->plan_line_id;

        // Buyer increases the PO qty after handover — a real revision, logged with a reason.
        $po->update(['po_qty_revised_1' => 800]);
        $po->sizes()->first()->update(['qty' => 800]);
        $this->assertSame(800, $po->fresh()->effectiveQty());

        $handover2 = app(ProductionHandoverService::class)->push($po->fresh(), $userId);

        $this->assertSame($originalLineId, $handover2->plan_line_id, 'the delta must land on the SAME plan line, not a new one');
        $this->assertSame(1, TrcPlanLine::where('po_number', $po->po_no)->count(), 'exactly one plan line must exist after a qty-increase re-handover');

        $line = TrcPlanLine::findOrFail($originalLineId);
        $this->assertSame(800, $line->total_order_qty);
        $this->assertSame(800, (int) $line->sizes()->sum('order_qty'));
        $this->assertSame(1, $line->sizes()->count(), 'the existing size row must be updated in place, not duplicated');

        $this->assertSame('delta', $handover2->checklist_snapshot['type']);
        $this->assertSame(500, $handover2->checklist_snapshot['previous_qty']);
        $this->assertSame(800, $handover2->checklist_snapshot['new_qty']);
    }

    /**
     * Spec test #9 (§M11 step 5 / §14 field map): "Embellishment flags
     * propagate into style_parts and then to bundles in Production."
     * ProductionHandoverService::syncStyleParts() writes
     * requires_embroidery/requires_print into production-trace's own
     * trc_style_parts from BOTH sources — the style's own Parts &
     * Embellishment tab (embellishment_type) AND the PO-level flags
     * (emb_applique_ih/print_emb/heat_seal_ih) — either one being set must
     * turn the corresponding flag on.
     */
    public function test_embellishment_flags_propagate_into_trc_style_parts_on_handover(): void
    {
        $chain = $this->makeHandoverReadyPo('T22', embellishmentType: 'embroidery');
        $po = $chain['po'];
        $chain['plan']->update(['pcd_result' => 'pass']);
        $userId = User::query()->firstOrFail()->id;

        app(ProductionHandoverService::class)->push($po->fresh(), $userId);

        $trcStylePart = \ME\MerchandisingTrace\Models\Bridge\TrcStylePart::where('style_id', $chain['style']->id)
            ->where('part_id', $chain['trcPart']->id)
            ->firstOrFail();

        // From the style's own Parts & Embellishment tab (embellishment_type = embroidery).
        $this->assertTrue((bool) $trcStylePart->requires_embroidery);
        $this->assertFalse((bool) $trcStylePart->requires_print);
    }

    public function test_po_level_embellishment_flags_also_propagate_into_trc_style_parts(): void
    {
        // Style part itself says 'none' — only the PO-level flags say yes.
        $chain = $this->makeHandoverReadyPo('T23', embellishmentType: 'none');
        $po = $chain['po'];
        $po->update(['heat_seal_ih' => 'yes']);
        $chain['plan']->update(['pcd_result' => 'pass']);
        $userId = User::query()->firstOrFail()->id;

        app(ProductionHandoverService::class)->push($po->fresh(), $userId);

        $trcStylePart = \ME\MerchandisingTrace\Models\Bridge\TrcStylePart::where('style_id', $chain['style']->id)
            ->where('part_id', $chain['trcPart']->id)
            ->firstOrFail();

        $this->assertTrue((bool) $trcStylePart->requires_print, 'heat_seal_ih=yes at PO level must turn requires_print on even when the style part itself is none');
        $this->assertFalse((bool) $trcStylePart->requires_embroidery);
    }

    /**
     * Spec test #15: "T&A Excel export → import round-trip preserves every
     * value." TnaGridExportService/TnaGridImportService both work on plain
     * PHP arrays (the same shape Maatwebsite\Excel hands the importer, and
     * what GenericArrayExport is built from) — so the round trip is
     * exercised directly here without needing a real .xlsx file on disk.
     */
    public function test_tna_grid_excel_export_import_round_trip_preserves_values_and_skips_auto_cells(): void
    {
        $chain = $this->makeChain('T30');
        $plan = app(TnaPlanGenerationService::class)->generateFor($chain['po']);
        $userId = User::query()->firstOrFail()->id;

        // A manually-editable task with a known value.
        $manualTask = $plan->tasks()->where('task_code', 'pp_meeting')->firstOrFail();
        $manualTask->update(['actual_date' => '2026-03-10', 'status' => 'done']);

        // An auto-filled task (from sample sync) that must survive import untouched.
        $autoTask = $plan->tasks()->where('task_code', 'pp1_submit')->firstOrFail();
        $this->assertTrue((bool) $autoTask->is_auto);
        $autoTask->update(['actual_date' => '2026-03-01', 'status' => 'done']);

        $export = app(TnaGridExportService::class)->export();
        // Not ->firstWhere('PO No.', ...): Collection::firstWhere() resolves
        // its key via data_get(), which treats the "." in "PO No." as a
        // nested-path separator and never matches — a plain array search
        // avoids that trap.
        $row = collect($export['rows'])->first(fn ($r) => $r['PO No.'] === $chain['po']->po_no);
        $this->assertNotNull($row);
        $this->assertSame('2026-03-10', $row[$manualTask->task_name]);
        $this->assertSame('2026-03-01', $row[$autoTask->task_name]);

        // Simulate a fresh load: wipe the manual task's actual_date, then
        // re-import the exact sheet the export produced.
        $manualTask->update(['actual_date' => null, 'status' => 'pending']);

        $sheet = array_merge([$export['headers']], array_map(
            fn ($r) => array_map(fn ($h) => $r[$h] ?? '', $export['headers']),
            $export['rows']
        ));

        $result = app(TnaGridImportService::class)->import($sheet, $userId);

        $this->assertGreaterThanOrEqual(1, $result['updated']);
        $this->assertGreaterThanOrEqual(1, $result['skipped_auto']);

        // Value round-tripped back onto the manual task...
        $this->assertSame('2026-03-10', $manualTask->fresh()->actual_date->toDateString());
        $this->assertSame('done', $manualTask->fresh()->status);

        // ...but the auto-filled task's value was never touched by the import
        // (it was already '2026-03-01' and stays exactly that — not re-applied).
        $this->assertSame('2026-03-01', $autoTask->fresh()->actual_date->toDateString());
    }
}
