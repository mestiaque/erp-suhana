<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PostsController;
use App\Http\Controllers\Welcome\WelcomeController;
use App\Http\Controllers\Customer\CustomerController;


// ----------------------
// AUTH ROUTES
// ----------------------
Route::get('/', function(){

    return redirect()->route('login');
});
Route::any('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ----------------------
// ADMIN ROUTES
// ----------------------
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth','redirectUser']], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    //User Management
    Route::get('/users/admin/',[AdminController::class,'usersAdmin'])->name('usersAdmin');
    Route::any('/users/admin/{action}/{id?}',[AdminController::class,'usersAdminAction'])->name('usersAdminAction');

    Route::get('/users/customer/',[AdminController::class,'usersCustomer'])->name('usersCustomer');
    Route::any('/users/customer/{action}/{id?}',[AdminController::class,'usersCustomerAction'])->name('usersCustomerAction');

    Route::get('/users/roles',[AdminController::class,'userRoles'])->name('userRoles');
    Route::any('/users/roles/{action}/{id?}',[AdminController::class,'userRoleAction'])->name('userRoleAction');

    // Apps Setting
    Route::get('/setting/{type}',[AdminController::class,'setting'])->name('setting');
    Route::post('/setting/{type}/update',[AdminController::class,'settingUpdate'])->name('settingUpdate');

    // Theme Route
    Route::get('/theme-setting',[AdminController::class,'themeSetting'])->name('themeSetting');


    // Expenses Management Route
    Route::get('/expenses/types',[AdminController::class,'expensesTypes'])->name('expensesTypes');
    Route::any('/expenses/types/{action}/{id?}',[AdminController::class,'expensesTypesAction'])->name('expensesTypesAction');

    Route::get('/expenses/reports',[AdminController::class,'expenseReports'])->name('expenseReports');

    Route::get('/expenses',[AdminController::class,'expenses'])->name('expenses');
    Route::any('/expenses/{action}/{id?}',[AdminController::class,'expensesAction'])->name('expensesAction');

    Route::get('/salary-sheet',[AdminController::class,'salarySheet'])->name('salarySheet');
    Route::any('/salary-sheet/{action}/{id?}',[AdminController::class,'salarySheetAction'])->name('salarySheetAction');

    Route::get('/suppliers-trading',[AdminController::class,'supplierTrading'])->name('supplierTrading');
    Route::any('/suppliers-trading/{action}/{id?}',[AdminController::class,'supplierTradingAction'])->name('supplierTradingAction');
    //Expenses Management End


    //Accounts Management
    Route::get('/accounts/transfers',[AdminController::class,'balanceTransfers'])->name('balanceTransfers');
    Route::any('/accounts/transfers/{action}/{id?}',[AdminController::class,'balanceTransfersAction'])->name('balanceTransfersAction');

    Route::get('/accounts/deposits',[AdminController::class,'deposits'])->name('deposits');
    Route::any('/accounts/deposits/{action}/{id?}',[AdminController::class,'depositsAction'])->name('depositsAction');

    Route::get('/accounts/withdrawal',[AdminController::class,'withdrawal'])->name('withdrawal');
    Route::any('/accounts/withdrawal/{action}/{id?}',[AdminController::class,'withdrawalAction'])->name('withdrawalAction');

    Route::get('/accounts/payment-methods',[AdminController::class,'paymentsMethods'])->name('paymentsMethods');
    Route::any('/accounts/payment-methods/{action}/{id?}',[AdminController::class,'paymentsMethodsAction'])->name('paymentsMethodsAction');

    Route::get('/accounts/accounts-methods',[AdminController::class,'accountsMethods'])->name('accountsMethods');
    Route::any('/accounts/accounts-methods/{action}/{id?}',[AdminController::class,'accountsMethodsAction'])->name('accountsMethodsAction');


    Route::get('/accounts/loans',[AdminController::class,'loansManagement'])->name('loansManagement');
    Route::any('/accounts/loans/{action}/{id?}',[AdminController::class,'loansManagementAction'])->name('loansManagementAction');
    //Accounts Management End

    Route::get('/my-location-update',[AdminController::class,'myLocationUpdate'])->name('myLocationUpdate');
    Route::get('/my-profile',[AdminController::class,'myProfile'])->name('myProfile');
    Route::any('/edit-profile',[AdminController::class,'editProfile'])->name('editProfile');

    // Medies Library Route
    Route::get('/medies',[AdminController::class,'medies'])->name('medies');
    Route::post('/medies/create',[AdminController::class,'mediesCreate'])->name('mediesCreate');
    Route::match(['get','post'],'/medies/edit/{id}',[AdminController::class,'mediesEdit'])->name('mediesEdit');
    Route::get('/medies/delete/{id}',[AdminController::class,'mediesDelete'])->name('mediesDelete');
    // Medies Library Route End

    
});


// ----------------------
// BUSINESS ROUTES
// ----------------------
Route::group(['prefix' => 'staff', 'as' => 'staff.', 'middleware' => ['auth','redirectUser']], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard2'])->name('dashboard');
});

