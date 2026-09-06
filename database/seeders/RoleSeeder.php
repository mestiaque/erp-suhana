<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

/**
 * Seeds the 3 garments-ERP roles requested for this rollout — Merchandiser,
 * IE Plan, Production — against the checkbox permission system already used
 * by the "User Roles" screen (App\Models\Permission::permission, a JSON map
 * of module_key => [action_key => 'on']).
 *
 * Scope per role follows src/work/GARMENTS_ERP_MASTER_PROMPT.md (§4 end-to-end
 * flow, §17 master data groups) and merchandising-trace's work/merchent.md §3:
 *   - Merchandiser : owns everything upstream of production (buyer → order →
 *     BOM → costing → T&A → handover) with read-only visibility into how
 *     their orders are progressing on the floor.
 *   - IE Plan      : owns capacity/production planning and the IE masters
 *     (lines, operators, machines, workflow stages) — approves/confirms the
 *     production plan that Production then executes against.
 *   - Production   : owns shop-floor execution, fabric store through
 *     shipment/dispatch — views (does not edit) the plan IE Plan set.
 *
 * Re-running this seeder is safe: roles are matched by name and their
 * permission JSON is fully replaced, so it stays in sync with this file.
 */
class RoleSeeder extends Seeder
{
    /**
     * module_key => 'all' (every action currently defined for that module)
     * or an explicit array of action keys to grant.
     */
    private function definitions(): array
    {
        return [
            'Merchandiser' => [
                // Masters
                'merch_buyer' => 'all', 'merch_season' => 'all', 'merch_product_type' => 'all',
                'merch_color' => 'all', 'merch_size' => 'all', 'merch_wash_type' => 'all',
                'merch_ship_mode' => 'all', 'merch_factory' => 'all', 'merch_supplier' => 'all',
                'merch_currency' => 'all', 'merch_item_category' => 'all', 'merch_item' => 'all',
                'merch_uom' => 'all', 'merch_department' => 'all', 'merch_style_image_type' => 'all',
                // Inquiry -> Order -> Handover
                'merch_inquiry' => 'all', 'merch_style' => 'all', 'merch_risk_assessment' => 'all',
                'merch_sample' => 'all', 'merch_sample_type' => 'all',
                'merch_bom' => 'all', 'merch_costing' => 'all',
                'merch_sales_contract' => 'all', 'merch_tna' => 'all',
                'merch_material_booking' => 'all', 'merch_production_handover' => 'all',
                'merch_shipment_plan' => 'all', 'merch_documentation' => 'all', 'merch_communication' => 'all',
                'merch_dashboard' => 'all', 'merch_reports' => 'all',
                // Read-only visibility into what production is doing with their orders
                'trc_reports' => ['view'],
                'trc_production_plan' => ['list', 'view'],
                'trc_buyer_inspection' => ['list', 'view'],
                'trc_shipment' => ['list', 'view'],
                'trc_fg_stock' => ['view'],
            ],

            'IE Plan' => [
                // Capacity/production-planning masters
                'trc_line' => 'all', 'trc_operator' => 'all', 'trc_machine' => 'all',
                'trc_workflow_stage' => 'all', 'trc_part' => 'all', 'trc_style_part' => 'all',
                'trc_size_group' => 'all', 'trc_product' => 'all', 'trc_season' => 'all',
                // Dynamic per-style routing (StageGateService) is IE Plan's territory
                'trc_style_workflow' => 'all',
                // Capacity/manpower-shortage planning (IECalculationService)
                'trc_capacity_plan' => 'all',
                // The plan itself, incl. Approve/Confirm
                'trc_production_plan' => 'all', 'trc_production_plan_import' => 'all',
                'trc_sewing_board' => 'all',
                'trc_reports' => 'all',
                // Needs T&A/handover context to plan against confirmed, PCD-passed orders
                'merch_tna' => ['list', 'view'],
                'merch_production_handover' => ['list', 'view'],
                'merch_dashboard' => ['view'],
                'merch_reports' => ['list', 'view'],
            ],

            'Production' => [
                // Scan/traceability
                'trc_barcode' => 'all',
                // Fabric store
                'trc_fabric_receipt' => 'all', 'trc_fabric_inspection' => 'all', 'trc_fabric_issue' => 'all',
                // Cutting & bundling
                'trc_cutting' => 'all', 'trc_bundle_qc' => 'all', 'trc_replacement_cutting' => 'all',
                // Embroidery/Print
                'trc_part_process' => 'all',
                // Sewing
                'trc_sewing_input' => 'all', 'trc_sewing_output' => 'all', 'trc_sewing_qc' => 'all',
                // Washing/Garment print
                'trc_garment_process' => 'all',
                // Finishing -> Buyer QC/Approval
                'trc_finishing' => 'all', 'trc_internal_final_qc' => 'all',
                'trc_buyer_inspection' => 'all', 'trc_buyer_approval' => 'all',
                // Packing -> FG -> Dispatch
                'trc_packing_list' => 'all', 'trc_carton' => 'all',
                'trc_fg_receipt' => 'all', 'trc_fg_stock' => 'all', 'trc_shipment' => 'all',
                // Day-to-day operational masters (not planning-owned)
                'trc_defect_type' => 'all', 'trc_vendor' => 'all', 'trc_warehouse' => 'all', 'trc_location' => 'all',
                'trc_reports' => ['view'],
                // Planning stays with IE Plan — floor only views the plan/board IE Plan set
                'trc_production_plan' => ['list', 'view'],
                'trc_sewing_board' => ['view'],
                'trc_line' => ['list', 'view'], 'trc_machine' => ['list', 'view'], 'trc_operator' => ['list', 'view'],
            ],
        ];
    }

    public function run(): void
    {
        // module_key => every action key currently registered for it, across
        // every merged group (PRODUCTION_TRACE, MERCHANDISING_TRACE, ...) —
        // so 'all' never grants an action a module doesn't actually have.
        $available = [];
        foreach (config('permission.modules', []) as $group) {
            foreach ($group as $moduleKey => $module) {
                if (is_array($module) && isset($module['permissions'])) {
                    $available[$moduleKey] = array_keys($module['permissions']);
                }
            }
        }

        foreach ($this->definitions() as $roleName => $moduleActions) {
            $permission = [];

            foreach ($moduleActions as $moduleKey => $actions) {
                if (! isset($available[$moduleKey])) {
                    // Module not installed/merged in this app — skip quietly
                    // rather than fail the whole seed.
                    continue;
                }

                $actions = $actions === 'all' ? $available[$moduleKey] : $actions;
                foreach ($actions as $action) {
                    if (in_array($action, $available[$moduleKey], true)) {
                        $permission[$moduleKey][$action] = 'on';
                    }
                }
            }

            Permission::updateOrCreate(
                ['name' => $roleName],
                ['permission' => json_encode($permission), 'status' => 'active']
            );

            $this->command?->info("Role '{$roleName}' seeded with ".count($permission).' module(s).');
        }
    }
}
