<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// ----------------------
// AUTH ROUTES
// ----------------------
Route::get('/', function () {
    return redirect()->route('login');
})->name('index');

Route::any('/login', [AuthController::class, 'login'])->name('login');
Route::post('/log-out', [AuthController::class, 'logout'])->name('logout');


// ----------------------
// ADMIN ROUTES
// ----------------------
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['logUserActivity', 'auth', 'redirectUser']], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/my-profile', [AdminController::class, 'myProfile'])->name('myProfile');
    Route::any('/edit-profile', [AdminController::class, 'editProfile'])->name('editProfile');
    Route::get('/users/file-reset/{field}/{id}', [AdminController::class, 'userFileReset'])->name('userFileReset');
    Route::get('/login-history', [AdminController::class, 'loginHistory'])->name('loginHistory');
    Route::get('/login-history/user/{user}', [AdminController::class, 'userLoginHistory'])->name('userLoginHistory');
    Route::get('/data-change-log', [AdminController::class, 'dataChangeLog'])->name('dataChangeLog');
    Route::get('/data-change-log/{activityLog}', [AdminController::class, 'dataChangeLogShow'])->name('dataChangeLogShow');

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    // User Management
    Route::get('/users/admin/', [AdminController::class, 'usersAdmin'])->name('usersAdmin');
    Route::any('/users/admin/{action}/{id?}', [AdminController::class, 'usersAdminAction'])->name('usersAdminAction');

    Route::get('/users/staff/', [AdminController::class, 'staffAdmin'])->name('staffAdmin');
    Route::any('/users/staff/{action}/{id?}', [AdminController::class, 'staffAdminAction'])->name('staffAdminAction');

    Route::get('/users/employee/', [AdminController::class, 'usersCustomer'])->name('usersCustomer');
    Route::any('/users/employee/{action}/{id?}', [AdminController::class, 'usersCustomerAction'])->name('usersCustomerAction');

    Route::get('/users/roles', [AdminController::class, 'userRoles'])->name('userRoles');
    Route::get('/users/developer-permissions', [AdminController::class, 'developerPermissions'])->name('developerPermissions');
    Route::post('/users/developer-permissions', [AdminController::class, 'developerPermissionsUpdate'])->name('developerPermissionsUpdate');
    Route::any('/users/roles/{action}/{id?}', [AdminController::class, 'userRoleAction'])->name('userRoleAction');

    Route::get('/merchandisers', [AdminController::class, 'merchandisers'])->name('merchandisers');
    Route::any('/merchandisers/{action}/{id?}', [AdminController::class, 'merchandisersAction'])->name('merchandisersAction');

    // Apps Setting
    Route::get('/setting/{type}', [AdminController::class, 'setting'])->name('setting');
    Route::post('/setting/{type}/update', [AdminController::class, 'settingUpdate'])->name('settingUpdate');

    // Theme Route
    Route::get('/theme-setting', [AdminController::class, 'themeSetting'])->name('themeSetting');

    // Medies Library Route
    Route::get('/medies', [AdminController::class, 'medies'])->name('medies');
    Route::post('/medies/create', [AdminController::class, 'mediesCreate'])->name('mediesCreate');
    Route::match(['get', 'post'], '/medies/edit/{id}', [AdminController::class, 'mediesEdit'])->name('mediesEdit');
    Route::get('/medies/delete/{id}', [AdminController::class, 'mediesDelete'])->name('mediesDelete');
    // Medies Library Route End
});
