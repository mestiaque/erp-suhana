<?php

namespace App\Traits;
use App\Models\Permission;
use Auth;
trait UserPermission{
	public function checkRequestPermission(){

		if($activeRole =Permission::find(Auth::user()->permission_id)){
			if(
			    
				
				
				empty(json_decode($activeRole->permission, true)['salarySheet']['view']) && \Request::is('admin/salary-sheet/export*')||
				
				empty(json_decode($activeRole->permission, true)['expenses']['delete']) && \Request::is('admin/expenses/delete*')||
				empty(json_decode($activeRole->permission, true)['expenses']['report']) && \Request::is('admin/expenses/reports*')||
				empty(json_decode($activeRole->permission, true)['expenses']['type']) && \Request::is('admin//expenses/types*')||
				
				
				empty(json_decode($activeRole->permission, true)['departments']['list']) && \Request::is('admin/hr/departments*')||
				empty(json_decode($activeRole->permission, true)['designations']['list']) && \Request::is('admin/hr/designations*')||
				empty(json_decode($activeRole->permission, true)['companies']['list']) && \Request::is('admin/hr/companies*')||
				empty(json_decode($activeRole->permission, true)['merchandisers']['list']) && \Request::is('admin/hr/merchandisers*')||
				
				empty(json_decode($activeRole->permission, true)['paymentMethod']['list']) && \Request::is('admin/accounts/payment-methods*')||
				empty(json_decode($activeRole->permission, true)['accounts']['list']) && \Request::is('admin/accounts/accounts-methods*')||
				empty(json_decode($activeRole->permission, true)['deposit']['list']) && \Request::is('admin/accounts/deposits*')||
				empty(json_decode($activeRole->permission, true)['loanManagement']['list']) && \Request::is('admin/accounts/loans*')||
				
				empty(json_decode($activeRole->permission, true)['employees']['list']) && \Request::is('admin/users/customer*')||

				empty(json_decode($activeRole->permission, true)['adminUsers']['list']) && \Request::is('admin/users/admin*')||

				// empty(json_decode($activeRole->permission, true)['adminRoles']['list']) && \Request::is('admin/users/roles*') ||

				empty(json_decode($activeRole->permission, true)['appsSetting']['general']) && \Request::is('admin/setting/general*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['general']) && \Request::is('admin/setting/logo*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['general']) && \Request::is('admin/setting/favicon*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['mail']) && \Request::is('admin/setting/mail*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['sms']) && \Request::is('admin/setting/sms*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['social']) && \Request::is('admin/setting/social*')


			){
				return abort('401');
			}
		}
	}
}