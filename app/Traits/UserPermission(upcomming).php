w<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Models\Permission;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->permission_id) {
            abort(401);
        }

        $permissionRecord = Permission::find($user->permission_id);
        if (!$permissionRecord) {
            abort(401);
        }

        $permissions = json_decode($permissionRecord->permission, true);

          // Route pattern => [module, action]
        $routePermissions = [
             // ----------------------
             // Accounts / Expenses
             // ----------------------
            'admin/expenses*'         => ['expenses', 'list'],
            'admin/expenses/types*'   => ['expenses_type', 'list'],
            'admin/expenses/reports*' => ['expenses_report', 'view'],
            'admin/iou*'              => ['iou', 'list'],
            'admin/iou-report*'       => ['iou_report', 'view'],

              // Payment Methods
            'admin/accounts/payment-methods*' => ['payment_methods', 'list'],

              // Accounts
            'admin/accounts/list*'             => ['accounts', 'list'],
            'admin/accounts/bill-payments*'    => ['bill_payments', 'list'],
            'admin/accounts/bill-collections*' => ['bill_collections', 'list'],
            'admin/accounts/deposits*'         => ['deposits', 'list'],
            'admin/accounts/withdrawal*'       => ['withdrawal', 'list'],
            'admin/accounts/statement*'        => ['statement', 'view'],

              // ----------------------
              // HR / Users
              // ----------------------
            'admin/users/employee*'  => ['employee', 'list'],
            'admin/users/staff*'     => ['staff', 'list'],
            'admin/users/admin*'     => ['admin', 'list'],
            'admin/users/roles*'     => ['roles', 'list'],
            'admin/hr/branchs*'      => ['branchs', 'list'],
            'admin/hr/departments*'  => ['departments', 'list'],
            'admin/hr/designations*' => ['designations', 'list'],

              // ----------------------
              // Settings
              // ----------------------
            'admin/setting/general*' => ['general', 'view'],
            'admin/setting/mail*'    => ['mail', 'view'],
            'admin/setting/sms*'     => ['sms', 'view'],

              // ----------------------
              // Purchases Management
              // ----------------------
            'admin/purchases/orders*'            => ['purchases_orders', 'list'],
            'admin/purchases/creditor*'          => ['creditor', 'list'],
            'admin/purchases/items*'             => ['purchases_items', 'list'],
            'admin/purchases/items-units*'       => ['purchases_items_units', 'list'],
            'admin/purchases/items-categories*'  => ['purchases_items_categories', 'list'],
            'admin/purchases/requisitions*'      => ['purchases_requisitions', 'list'],
            'admin/purchases/received*'          => ['purchases_received', 'list'],
            'admin/purchases/damage-returns*'    => ['purchases_damage_returns', 'list'],
            'admin/purchases/suppliers-ladgers*' => ['suppliers_ladgers', 'view'],
            'admin/purchases/reports*'           => ['purchases_reports', 'view'],
            'admin/purchases/stocks*'            => ['purchases_stocks', 'view'],
        ];

        foreach ($routePermissions as $pattern => [$module, $action]) {
            if ($request->is($pattern) && empty($permissions[$module][$action])) {
                abort(401);
            }
        }

        return $next($request);
    }
}
