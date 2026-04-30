<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// ----------------------
// Admin Controller
// ----------------------
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PostsController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Api\ZKTecoPushController;

// ----------------------
// Staff Controller
// ----------------------
use App\Http\Controllers\Admin\PurchasesController;
use App\Http\Controllers\Admin\CommercialController;
use App\Http\Controllers\Admin\ProductionController;
use App\Http\Controllers\Admin\RequisitionController;
use App\Http\Controllers\Admin\MerchandisingController;



// ----------------------
// AUTH ROUTES
// ----------------------
Route::get('/', function(){
    return redirect()->route('login');
})->name('index');

Route::any('/login', [AuthController::class, 'login'])->name('login');
Route::post('/log-out',[AuthController::class,'logout'])->name('logout');

Route::get('/`test-zkteco`', [ZKTecoPushController::class, 'test'])->name('test-zkteco');


// ----------------------
// ADMIN ROUTES
// ----------------------
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['logUserActivity', 'auth','redirectUser']], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/my-profile',[AdminController::class,'myProfile'])->name('myProfile');
    Route::any('/edit-profile',[AdminController::class,'editProfile'])->name('editProfile');

    //User Management
    Route::get('/users/admin/',[AdminController::class,'usersAdmin'])->name('usersAdmin');
    Route::any('/users/admin/{action}/{id?}',[AdminController::class,'usersAdminAction'])->name('usersAdminAction');

    Route::get('/users/staff/',[AdminController::class,'staffAdmin'])->name('staffAdmin');
    Route::any('/users/staff/{action}/{id?}',[AdminController::class,'staffAdminAction'])->name('staffAdminAction');

    Route::get('/users/employee/',[AdminController::class,'usersCustomer'])->name('usersCustomer');
    Route::any('/users/employee/{action}/{id?}',[AdminController::class,'usersCustomerAction'])->name('usersCustomerAction');

    Route::get('/users/roles',[AdminController::class,'userRoles'])->name('userRoles');
    Route::any('/users/roles/{action}/{id?}',[AdminController::class,'userRoleAction'])->name('userRoleAction');

    Route::get('/merchandisers',[AdminController::class,'merchandisers'])->name('merchandisers');
    Route::any('/merchandisers/{action}/{id?}',[AdminController::class,'merchandisersAction'])->name('merchandisersAction');

    // Apps Setting
    Route::get('/setting/{type}',[AdminController::class,'setting'])->name('setting');
    Route::post('/setting/{type}/update',[AdminController::class,'settingUpdate'])->name('settingUpdate');

    // Theme Route
    Route::get('/theme-setting',[AdminController::class,'themeSetting'])->name('themeSetting');


    // Expenses Management Route
    Route::get('/expenses/types',[AdminController::class,'expensesTypes'])->name('expensesTypes');
    Route::any('/expenses/types/{action}/{id?}',[AdminController::class,'expensesTypesAction'])->name('expensesTypesAction');

    Route::get('/expenses/iou-reports',[AdminController::class,'expenseIOUReports'])->name('expenseIOUReports');
    Route::get('/expenses/iou',[AdminController::class,'expensesIOU'])->name('expensesIOU');
    Route::get('/expenses/completed-iou',[AdminController::class,'completedIou'])->name('completedIou');
    Route::any('/expenses/iou/{action}/{id?}',[AdminController::class,'expensesIOUAction'])->name('expensesIOUAction');

    Route::get('/expenses/reports',[AdminController::class,'expenseReports'])->name('expenseReports');
    // Route::get('/expenses/summery',[AdminController::class,'expenseReports'])->name('expenseReports');

    Route::get('/expenses',[AdminController::class,'expenses'])->name('expenses');
    Route::any('/expenses/{action}/{id?}',[AdminController::class,'expensesAction'])->name('expensesAction');

    Route::get('/salary-sheet',[AdminController::class,'salarySheet'])->name('salarySheet');
    Route::any('/salary-sheet/{action}/{id?}',[AdminController::class,'salarySheetAction'])->name('salarySheetAction');
    //Expenses Management End

    // Purchases Module Route

    Route::get('/purchases-stocks',[PurchasesController::class,'purchasesStocks'])->name('purchasesStocks');
    Route::get('/purchases-items',[PurchasesController::class,'purchasesItems'])->name('purchasesItems');
    Route::any('/purchases-items/{action}/{id?}',[PurchasesController::class,'purchasesItemsAction'])->name('purchasesItemsAction');

    Route::get('/suppliers-ladgers',[PurchasesController::class,'suppliersLegers'])->name('suppliersLegers');
    Route::get('/suppliers',[PurchasesController::class,'suppliers'])->name('suppliers');
    Route::any('/suppliers/{action}/{id?}',[PurchasesController::class,'suppliersAction'])->name('suppliersAction');

    Route::get('/purchases-requisitions',[PurchasesController::class,'purchasesRequisitions'])->name('purchasesRequisitions');
    Route::any('/purchases-requisitions/{action}/{id?}',[PurchasesController::class,'purchasesRequisitionsAction'])->name('purchasesRequisitionsAction');

    Route::get('/purchases-reports',[PurchasesController::class,'purchasesReports'])->name('purchasesReports');
    Route::get('/purchases-orders',[PurchasesController::class,'purchasesOrders'])->name('purchasesOrders');
    Route::any('/purchases-orders/{action}/{id?}',[PurchasesController::class,'purchasesOrdersAction'])->name('purchasesOrdersAction');

    Route::get('/purchases-received',[PurchasesController::class,'purchasesReceived'])->name('purchasesReceived');
    Route::any('/purchases-received/{action}/{id?}',[PurchasesController::class,'purchasesReceivedAction'])->name('purchasesReceivedAction');

    Route::get('/purchases-damage-returns',[PurchasesController::class,'purchasesDamageReturn'])->name('purchasesDamageReturn');
    Route::any('/purchases-damage-returns/{action}/{id?}',[PurchasesController::class,'purchasesDamageReturnAction'])->name('purchasesDamageReturnAction');

    Route::get('/bill-payments',[PurchasesController::class,'billPayment'])->name('billPayment');
    Route::get('/bill-payments/print',[PurchasesController::class,'billPaymentPrint'])->name('billPaymentPrint');
    Route::any('/bill-payments/{action}/{id?}',[PurchasesController::class,'billPaymentAction'])->name('billPaymentAction');

    Route::get('/bill-collections',[PurchasesController::class,'billCollection'])->name('billCollection');
    Route::any('/bill-collections/{action}/{id?}',[PurchasesController::class,'billCollectionAction'])->name('billCollectionAction');
    // Purchases Module Route

    //Accounts Management
    Route::get('/accounts/transfers',[AdminController::class,'balanceTransfers'])->name('balanceTransfers');
    Route::any('/accounts/transfers/{action}/{id?}',[AdminController::class,'balanceTransfersAction'])->name('balanceTransfersAction');

    Route::get('/accounts/deposits',[AdminController::class,'deposits'])->name('deposits');
    Route::any('/accounts/deposits/{action}/{id?}',[AdminController::class,'depositsAction'])->name('depositsAction');

    Route::get('/accounts/withdrawal',[AdminController::class,'withdrawal'])->name('withdrawal');
    Route::any('/accounts/withdrawal/{action}/{id?}',[AdminController::class,'withdrawalAction'])->name('withdrawalAction');

    Route::get('/accounts/payment-methods',[AdminController::class,'paymentsMethods'])->name('paymentsMethods');
    Route::any('/accounts/payment-methods/{action}/{id?}',[AdminController::class,'paymentsMethodsAction'])->name('paymentsMethodsAction');

    Route::get('/accounts/statement',[AdminController::class,'accountsStatement'])->name('accountsStatement');
    Route::get('/accounts/list',[AdminController::class,'accounts'])->name('accounts');
    Route::any('/accounts/list/{action}/{id?}',[AdminController::class,'accountsAction'])->name('accountsAction');

    //Merchandising
    Route::get('/buyers',[MerchandisingController::class,'buyers'])->name('buyers');
    Route::any('/buyers/{action}/{id?}',[MerchandisingController::class,'buyersAction'])->name('buyersAction');
    Route::get('/samples',[MerchandisingController::class,'samples'])->name('samples');
    Route::any('/samples/{action}/{id?}',[MerchandisingController::class,'samplesAction'])->name('samplesAction');
    Route::get('/order-details',[MerchandisingController::class,'orderDetails'])->name('orderDetails');
    Route::any('/order-details/{action}/{id?}',[MerchandisingController::class,'orderDetailsAction'])->name('orderDetailsAction');
    Route::get('/proforma-invoice',[MerchandisingController::class,'proformaInvoice'])->name('proformaInvoice');
    Route::any('/proforma-invoice/{action}/{id?}',[MerchandisingController::class,'proformaInvoiceAction'])->name('proformaInvoiceAction');
    Route::get('/fabrications', [MerchandisingController::class, 'manageAttribute'])->defaults('type', 11)->defaults('view', 'fabrications')->name('fabrications');
    Route::any('/fabrications/{action}/{id?}', [MerchandisingController::class, 'manageAttribute'])->defaults('type', 11)->defaults('view', 'fabrications')->name('fabricationsAction');
    Route::get('/compositions', [MerchandisingController::class, 'manageAttribute'])->defaults('type', 12)->defaults('view', 'compositions')->name('compositions');
    Route::any('/compositions/{action}/{id?}', [MerchandisingController::class, 'manageAttribute'])->defaults('type', 12)->defaults('view', 'compositions')->name('compositionsAction');

    //Production
    Route::get('/production-planning',[ProductionController::class,'productionPlanning'])->name('productionPlanning');
    Route::any('/production-planning/{action}/{id?}',[ProductionController::class,'productionPlanningAction'])->name('productionPlanningAction');
    Route::get('/floor-planning',[ProductionController::class,'floorPlanning'])->name('floorPlanning');
    Route::any('/floor-planning/{action}/{id?}',[ProductionController::class,'floorPlanningAction'])->name('floorPlanningAction');

    Route::get('/booking',[MerchandisingController::class,'booking'])->name('booking');
    Route::any('/booking/{action}/{id?}',[MerchandisingController::class,'bookingAction'])->name('bookingAction');
    Route::get('/budget',[MerchandisingController::class,'budget'])->name('budget');
    Route::any('/budget/{action}/{id?}',[MerchandisingController::class,'budgetAction'])->name('budgetAction');
    Route::get('/fabric-status/{id}',[MerchandisingController::class,'fabricStatus'])->name('fabricStatus');


    Route::get('/procurement/yarn-booking',[ProductionController::class,'yarnBooking'])->name('yarnBooking');
    Route::any('/procurement/yarn-booking/{action}/{id?}',[ProductionController::class,'yarnBookingAction'])->name('yarnBookingAction');
    Route::get('/procurement/knitting-booking',[ProductionController::class,'knittingBooking'])->name('knittingBooking');
    Route::any('/procurement/knitting-booking/{action}/{id?}',[ProductionController::class,'knittingBookingAction'])->name('knittingBookingAction');
    Route::get('/procurement/dyeing-booking',[ProductionController::class,'dyeingBooking'])->name('dyeingBooking');
    Route::any('/procurement/dyeing-booking/{action}/{id?}',[ProductionController::class,'dyeingBookingAction'])->name('dyeingBookingAction');

    Route::get('/procurement/yarn-receive',[ProductionController::class,'yarnReceive'])->name('yarnReceive');
    Route::any('/procurement/yarn-receive/{action}/{id?}',[ProductionController::class,'yarnReceiveAction'])->name('yarnReceiveAction');
    Route::get('/procurement/knitting-receive',[ProductionController::class,'knittingReceive'])->name('knittingReceive');
    Route::any('/procurement/knitting-receive/{action}/{id?}',[ProductionController::class,'knittingReceiveAction'])->name('knittingReceiveAction');
    Route::get('/procurement/dyeing-receive',[ProductionController::class,'dyeingReceive'])->name('dyeingReceive');
    Route::any('/procurement/dyeing-receive/{action}/{id?}',[ProductionController::class,'dyeingReceiveAction'])->name('dyeingReceiveAction');

    Route::any('/procurement/pi-wise-fabric-status',[ProductionController::class,'piWiseFabricStatus'])->name('piWiseFabricStatus');

    Route::get('/daily-production',[ProductionController::class,'dailyProduction'])->name('dailyProduction');
    Route::get('/daily-production-print',[ProductionController::class,'dailyProductionPrint'])->name('dailyProductionPrint');
    Route::any('/daily-production/{action}/{id?}',[ProductionController::class,'dailyProductionAction'])->name('dailyProductionAction');
    Route::get('/production-list',[ProductionController::class,'production'])->name('production');
    Route::any('/production-list/{action}/{id?}',[ProductionController::class,'productionAction'])->name('productionAction');
    Route::get('/cutting',[ProductionController::class,'cutting'])->name('cutting');
    Route::any('/cutting/{action}/{id?}',[ProductionController::class,'cuttingAction'])->name('cuttingAction');

    Route::get('/finishing',[ProductionController::class,'finishing'])->name('finishing');
    Route::any('/finishing/{action}/{id?}',[ProductionController::class,'finishingAction'])->name('finishingAction');

    Route::get('/iron',[ProductionController::class,'iron'])->name('iron');
    Route::any('/iron/{action}/{id?}',[ProductionController::class,'ironAction'])->name('ironAction');

    Route::get('/poly',[ProductionController::class,'poly'])->name('poly');
    Route::any('/poly/{action}/{id?}',[ProductionController::class,'polyAction'])->name('polyAction');

    //Buyer Order Management
    Route::get('/products',[OrderController::class,'products'])->name('products');
    Route::any('/products/{action}/{id?}',[OrderController::class,'productsAction'])->name('productsAction');

    Route::get('/orders',[OrderController::class,'orders'])->name('orders');
    Route::any('/orders/{action}/{id?}',[OrderController::class,'ordersAction'])->name('ordersAction');


    Route::get('/hr/branchs',[AdminController::class,'branchs'])->name('branchs'); //0
    Route::any('/hr/branchs/{action}/{id?}',[AdminController::class,'branchsAction'])->name('branchsAction');
    Route::get('/hr/designations',[AdminController::class,'designations'])->name('designations'); //2
    Route::any('/hr/designations/{action}/{id?}',[AdminController::class,'designationsAction'])->name('designationsAction');
    Route::get('/hr/departments',[AdminController::class,'departments'])->name('departments'); //3
    Route::any('/hr/departments/{action}/{id?}',[AdminController::class,'departmentsAction'])->name('departmentsAction');
    Route::get('/hr/floor-lines',[AdminController::class,'floorLines'])->name('floorLines'); //4
    Route::any('/hr/floor-lines/{action}/{id?}',[AdminController::class,'floorLinesAction'])->name('floorLinesAction');
    Route::get('/hr/employee-types',[AdminController::class,'employeeType'])->name('employeeType'); //16
    Route::any('/hr/employee-types/{action}/{id?}',[AdminController::class,'employeeTypeAction'])->name('employeeTypeAction');
    Route::get('/hr/divisions',[AdminController::class,'divisions'])->name('divisions');//27
    Route::any('/hr/divisions/{action}/{id?}',[AdminController::class,'divisionsAction'])->name('divisionsAction');
    Route::get('/hr/grades',[AdminController::class,'grades'])->name('grades');//28
    Route::any('/hr/grades/{action}/{id?}',[AdminController::class,'gradesAction'])->name('gradesAction');
    Route::get('/hr/sections',[AdminController::class,'sections'])->name('sections'); //29
    Route::any('/hr/sections/{action}/{id?}',[AdminController::class,'sectionsAction'])->name('sectionsAction');
    Route::get('/hr/shifts',[AdminController::class,'shifts'])->name('shifts');
    Route::any('/hr/shifts/{action}/{id?}',[AdminController::class,'shiftsAction'])->name('shiftsAction');

    //Accounts Management End

    // Medies Library Route
    Route::get('/medies',[AdminController::class,'medies'])->name('medies');
    Route::post('/medies/create',[AdminController::class,'mediesCreate'])->name('mediesCreate');
    Route::match(['get','post'],'/medies/edit/{id}',[AdminController::class,'mediesEdit'])->name('mediesEdit');
    Route::get('/medies/delete/{id}',[AdminController::class,'mediesDelete'])->name('mediesDelete');
    // Medies Library Route End

    // Route::get('/sales',[RequisitionController::class,'sales'])->name('sales');
    // Route::any('/sales/{action}/{id?}',[RequisitionController::class,'salesAction'])->name('salesAction');


    // --- Commercial Management Routes ---
    // --- Commercial Management Routes ---
    Route::group(['prefix' => 'commercial', 'as' => 'commercial.'], function () {
        // 1. Bank BTB LC
        Route::get('/btb-lc', [CommercialController::class, 'btbLc'])->name('btbLc');
        Route::any('/btb-lc/{action}/{id?}', [CommercialController::class, 'btbLcAction'])->name('btbLcAction');

        // 2. Export LC/Sales Contact
        Route::get('/export-lc', [CommercialController::class, 'exportLc'])->name('exportLc');
        Route::any('/export-lc/{action}/{id?}', [CommercialController::class, 'exportLcAction'])->name('exportLcAction');

        // 3. Purchase Order (PO)
        Route::get('/purchase-orders', [CommercialController::class, 'purchaseOrders'])->name('purchaseOrders');
        Route::any('/purchase-orders/{action}/{id?}', [CommercialController::class, 'purchaseOrdersAction'])->name('purchaseOrdersAction');

        // 4. Proforma Invoice (PI)
        Route::get('/proforma-invoice', [CommercialController::class, 'pi'])->name('pi');
        Route::any('/proforma-invoice/{action}/{id?}', [CommercialController::class, 'piAction'])->name('piAction');

        // 5. Commercial Invoice
        Route::get('/invoices', [CommercialController::class, 'invoice'])->name('invoice');
        Route::get('/invoices/create', [CommercialController::class, 'createInvoice'])->name('invoiceCreate');
        Route::post('/invoices/store', [CommercialController::class, 'invoiceAction'])->name('invoiceStore');
        Route::any('/invoices/{action}/{id?}', [CommercialController::class, 'invoiceAction'])->name('invoiceAction');

        // 6. Packing List
        Route::get('/pricing-list', [CommercialController::class, 'pricingList'])->name('pricingList');
        Route::any('/pricing-list/{action}/{id?}', [CommercialController::class, 'pricingListAction'])->name('pricingListAction');

        // 6. Packing List
        Route::get('/packing-list', [CommercialController::class, 'packingList'])->name('packingList');
        Route::any('/packing-list/{action}/{id?}', [CommercialController::class, 'packingListAction'])->name('packingListAction');

        // 7. Shipping Bill/Docs
        Route::get('/shipping-docs', [CommercialController::class, 'shippingDocs'])->name('shippingDocs');
        Route::any('/shipping-docs/{action}/{id?}', [CommercialController::class, 'shippingDocsAction'])->name('shippingDocsAction');

        // 8. Export Realization
        Route::get('/export-realization', [CommercialController::class, 'realization'])->name('realization');
        Route::any('/export-realization/{action}/{id?}', [CommercialController::class, 'realizationAction'])->name('realizationAction');

        // 9. Commercial Reports
        Route::get('/reports', [CommercialController::class, 'reports'])->name('reports');
    });

    Route::get('/roadmap', [PostsController::class, 'roadmap']);


});


// ----------------------
// BUSINESS ROUTES
// ----------------------
Route::group(['prefix' => 'staff', 'as' => 'staff.', 'middleware' => ['auth','redirectUser']], function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
});
