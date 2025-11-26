<?php

namespace App\Traits;
use App\Models\Permission;
use Auth;
trait UserPermission{
	public function checkRequestPermission(){
		if($activeRole =Permission::find(Auth::user()->permission_id)){
            $p = json_decode($activeRole->permission, true);
			if(

                empty($p['salarySheet']['view']) && \Request::is('admin/salary-sheet/export*') ||

                empty($p['expenses']['delete']) && \Request::is('admin/expenses/delete*') ||
                empty($p['expenses']['report']) && \Request::is('admin/expenses/reports*') ||
                empty($p['expenses']['type']) && \Request::is('admin/expenses/types*') ||
                empty($p['expenses']['list']) && \Request::is('admin/expenses*') ||

                empty($p['departments']['list']) && \Request::is('admin/hr/departments*') ||
                empty($p['designations']['list']) && \Request::is('admin/hr/designations*') ||
                empty($p['companies']['list']) && \Request::is('admin/hr/companies*') ||
                empty($p['merchandisers']['list']) && \Request::is('admin/hr/merchandisers*') ||

                empty($p['paymentMethod']['list']) && \Request::is('admin/accounts/payment-methods*') ||
                empty($p['accounts']['list']) && \Request::is('admin/accounts/list*') ||
                empty($p['deposit']['list']) && \Request::is('admin/accounts/deposits*') ||
                empty($p['loanManagement']['list']) && \Request::is('admin/accounts/loans*') ||
                empty($p['accounts']['transfer']) && \Request::is('admin/accounts/transfers*') ||
                empty($p['accounts']['withdraw']) && \Request::is('admin/accounts/withdrawal*') ||

                empty($p['employees']['list']) && \Request::is('admin/users/employee*') ||

                empty($p['adminUsers']['list']) && \Request::is('admin/users/admin*') ||

                empty($p['staff']['list']) && \Request::is('admin/users/staff*') ||

                empty($p['adminRoles']['list']) && \Request::is('admin/users/roles*') ||

                empty($p['media']['list']) && \Request::is('admin/medies*') ||

                empty($p['branch']['list']) && \Request::is('admin/hr/branchs*') ||

                empty($p['appsSetting']['general']) && \Request::is('admin/setting/general*') ||
                empty($p['appsSetting']['general']) && \Request::is('admin/setting/logo*') ||
                empty($p['appsSetting']['general']) && \Request::is('admin/setting/favicon*') ||

                empty($p['appsSetting']['mail']) && \Request::is('admin/setting/mail*') ||
                empty($p['appsSetting']['sms']) && \Request::is('admin/setting/sms*') ||

                empty($p['appsSetting']['social']) && \Request::is('admin/setting/social*')

			){
				return abort('401');
			}
		}
	}
}
