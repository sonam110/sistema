<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     if (\Auth::check()) {
//         return redirect('/dashboard');
//     }
//     return view('auth.login');
// });
Route::get('/', 'AdminController@start');
Route::get('screen-lock/{currtime}/{id}/{randnum}', 'NoMiddlewareController@screenlock')->name('screenlock');

Auth::routes();

Route::get('admin', 'AdminController@loginPageAdmin');
Route::get('login', ['as' => 'login', 'uses' => 'AdminController@loginPage']);
Route::post('login', 'AdminController@authenticate')->name('user-register');
Route::get('logout', 'AdminController@logout')->name('logout');
Route::get('screenlock/{currtime}/{id}/{randnum}', 'AdminController@screenlock');
Route::post('export-import-product', 'ProductController@exportImportProduct')->name('export-import-product');
Route::group(['middleware' => ['auth']], function () {
	Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');

	//Employee  Management
	Route::get('employee-list', 'UserController@employees')->name('employee-list');
    Route::get('employee-create', 'UserController@employeeCreate')->name('employee-create');
    Route::get('employee-edit/{id}', 'UserController@employeeEdit')->name('employee-edit');
    Route::get('employee-view/{id}', 'UserController@employeeView')->name('employee-view');
    Route::post('employee-save', 'UserController@employeeSave')->name('employee-save');
    Route::get('employee-delete/{id}', 'UserController@employeeDelete')->name('employee-delete');
    Route::post('employee-action', 'UserController@employeeAction')->name('employee-action');

  
     //Order management
    Route::get('allOrders', 'OrderController@allOrders')->name('all-orders');
    Route::post('order-list-datatable', 'OrderController@orderListDatatable')->name('api.order-list-datatable');
    Route::post('actionOrders', 'OrderController@actionOrders')->name('actionOrders');
    Route::post('getOrderDetail','OrderController@getOrderDetail')->name('getOrderDetail');
    Route::get('generate-invoice/{transaction_id?}', 'OrderController@generateInvoiceUser')->name('generate-invoice');

    //Profile management
	Route::get('profile', 'AdminController@profile')->name('edit-profile');
	Route::post('change-password', 'AdminController@changePassword')->name('change-password');
	Route::post('update-profile', 'AdminController@updateProfile')->name('update-profile');

	//Permissions permissions/create
	Route::resource('permissions','PermissionController');

    //Roles
    Route::resource('roles','RoleController');
    Route::get('role-delete/{id}','RoleController@roleDelete')->name('role-delete');

    //Products
    Route::get('product-list', 'ProductController@products')->name('product-list');
    Route::get('price-change-ml', 'ProductController@priceChangeMl')->name('price-change-ml');

     /*---------Update price using excel*/
    Route::get('update-price-excel', 'ProductController@updatePriceEcxel')->name('update-price-excel');
    Route::post('product-list-filter-excel', 'ProductController@productListFilterExcel')->name('product-list-filter-excel');

    Route::get('dimension-change-ml', 'ProductController@dimensionChangeMl')->name('dimension-change-ml');

    Route::get('ml-list-shipping-mode-me1', 'ProductController@mlListShippingModeMe1')->name('ml-list-shipping-mode-me1');

    Route::get('sync-cat-for-ml', 'ProductController@updateMLCat')->name('sync-cat-for-ml');

    Route::get('add-products-on-ml', 'ProductController@addProductsOnML')->name('add-products-on-ml');
    Route::post('save-products-on-ml', 'ProductController@saveProductsOnML')->name('save-products-on-ml');

    //Supplier
    Route::get('supplier-list', 'SupplierController@suppliers')->name('supplier-list');
    Route::get('supplier-create', 'SupplierController@supplierCreate')->name('supplier-create');
    Route::get('supplier-edit/{id}', 'SupplierController@supplierEdit')->name('supplier-edit');
    Route::get('supplier-view/{id}', 'SupplierController@supplierView')->name('supplier-view');
    Route::post('supplier-save', 'SupplierController@supplierSave')->name('supplier-save');
    Route::get('supplier-delete/{id}', 'SupplierController@supplierDelete')->name('supplier-delete');
    Route::post('supplier-action', 'SupplierController@supplierAction')->name('supplier-action');

    // Concepts
    Route::get('concept-list', 'ConceptController@concepts')->name('concept-list');
    Route::get('concept-create', 'ConceptController@conceptCreate')->name('concept-create');
    Route::get('concept-edit/{id}', 'ConceptController@conceptEdit')->name('concept-edit');
    Route::post('concept-save', 'ConceptController@conceptSave')->name('concept-save');
    Route::get('concept-delete/{id}', 'ConceptController@conceptDelete')->name('concept-delete');

    //Purchase order
    Route::get('purchase-order-list', 'PurchaseOrderController@purchaseOrderList')->name('purchase-order-list');
    Route::get('purchase-order-create', 'PurchaseOrderController@purchaseOrderCreate')->name('purchase-order-create');
    Route::post('purchase-order-save', 'PurchaseOrderController@purchaseOrderSave')->name('purchase-order-save');
    Route::get('purchase-order-view/{id}', 'PurchaseOrderController@purchaseOrderView')->name('purchase-order-view');
    Route::get('purchase-order-delete/{id}', 'PurchaseOrderController@purchaseOrderDelete')->name('purchase-order-delete');
    Route::get('purchase-order-download/{id}', 'PurchaseOrderController@purchaseOrderDownload')->name('purchase-order-download');
    Route::post('purchase-order-action', 'PurchaseOrderController@purchaseOrderAction')->name('purchase-order-action');

    //Purchase invoice
    Route::get('purchase-invoice-list', 'PurchaseInvoiceController@purchaseInvoiceList')->name('purchase-invoice-list');
    Route::get('purchase-invoice-create', 'PurchaseInvoiceController@purchaseInvoiceCreate')->name('purchase-invoice-create');
    Route::post('purchase-invoice-save', 'PurchaseInvoiceController@purchaseInvoiceSave')->name('purchase-invoice-save');
    Route::get('purchase-invoice-view/{id}', 'PurchaseInvoiceController@purchaseInvoiceView')->name('purchase-invoice-view');
    Route::get('purchase-invoice-delete/{id}', 'PurchaseInvoiceController@purchaseInvoiceDelete')->name('purchase-invoice-delete');
    Route::get('purchase-invoice-pay/{id}', 'PurchaseInvoiceController@purchaseInvoicePay')->name('purchase-invoice-pay');

    //Purchase Receiving order
    Route::get('purchase-order-received-list', 'PurchaseOrderReceivingController@purchaseOrderReceivedList')->name('purchase-order-received-list');
    Route::get('purchase-order-receiving/{id}', 'PurchaseOrderReceivingController@purchaseOrderReceiving')->name('purchase-order-receiving');
    Route::post('purchase-order-receiving-save', 'PurchaseOrderReceivingController@purchaseOrderReceivingSave')->name('purchase-order-receiving-save');

    //Products ordered but not received
    Route::get('products-ordered-but-not-received', 'PurchaseOrderController@productsOrderedButNotReceived')->name('products-ordered-but-not-received');
    Route::post('products-ordered-but-not-received-list', 'PurchaseOrderController@productsOrderedButNotReceivedList')->name('api.products-ordered-but-not-received-list');

    //Purchase Returns order
    Route::get('purchase-order-return-list', 'PurchaseOrderReturnController@purchaseOrderReturnList')->name('purchase-order-return-list');
    Route::get('purchase-order-return/{id}', 'PurchaseOrderReturnController@purchaseOrderReturn')->name('purchase-order-return');
    Route::post('purchase-order-return-save', 'PurchaseOrderReturnController@purchaseOrderReturnSave')->name('purchase-order-return-save');


    //Suuplier invoice
    Route::get('supplier-invoice-list', 'SupplierInvoiceController@supplierInvoiceList')->name('supplier-invoice-list');
    Route::get('supplier-invoice-create', 'SupplierInvoiceController@supplierInvoiceCreate')->name('supplier-invoice-create');
    Route::post('supplier-invoice-save', 'SupplierInvoiceController@supplierInvoiceSave')->name('supplier-invoice-save');
    Route::get('supplier-invoice-view/{id}', 'SupplierInvoiceController@supplierInvoiceView')->name('supplier-invoice-view');
    Route::get('supplier-invoice-delete/{id}', 'SupplierInvoiceController@supplierInvoiceDelete')->name('supplier-invoice-delete');
    Route::get('supplier-invoice-pay/{id}', 'SupplierInvoiceController@purchaseInvoicePay')->name('supplier-invoice-pay');


    //Customers
    Route::get('customer-list', 'CustomerController@customers')->name('customer-list');
    Route::post('customer-list-datatable', 'CustomerController@customerListDatable')->name('api.customer-list-datatable');
    Route::get('customer-create', 'CustomerController@customerCreate')->name('customer-create');
    Route::get('customer-edit/{id}', 'CustomerController@customerEdit')->name('customer-edit');
    Route::get('customer-view/{id}', 'CustomerController@customerView')->name('customer-view');
    Route::post('customer-save', 'CustomerController@customerSave')->name('customer-save');
    Route::get('customer-delete/{id}', 'CustomerController@customerDelete')->name('customer-delete');
    Route::post('customer-action', 'CustomerController@customerAction')->name('customer-action');

    //Sales Order
    Route::get('sales-order-list', 'SalesOrderController@salesOrders')->name('sales-order-list');
    Route::get('sales-order-create', 'SalesOrderController@salesOrderCreate')->name('sales-order-create');
    Route::get('sales-order-view/{id}', 'SalesOrderController@salesOrderView')->name('sales-order-view');
    Route::post('sales-order-save', 'SalesOrderController@salesOrderSave')->name('sales-order-save');
    Route::get('sales-order-download/{id}', 'SalesOrderController@salesOrderDownload')->name('sales-order-download');
    Route::post('sales-order-action', 'SalesOrderController@salesOrderAction')->name('sales-order-action');
    Route::get('sales-order-facturar/{id}', 'SalesOrderController@salesOrderFacturar')->name('sales-order-facturar');
    Route::post('getInstallmentsAmount', 'SalesOrderController@getInstallmentsAmount')->name('getInstallmentsAmount');


     /*Coupon Code*/
    Route::post('coupons-list', 'SalesOrderController@couponList')->name('coupons-list');
    Route::post('check-coupon-code',  'SalesOrderController@checkCouponCode')->name('check-coupon-code');
    Route::post('apply-for-coupon',  'SalesOrderController@applyForCoupon')->name('apply-for-coupon');

    /*   Budget    */
    Route::get('all-budget', 'BudgetController@allBudget')->name('all-budget');
    Route::get('budget-create', 'BudgetController@budgetCreate')->name('budget-create');
    Route::get('budget-view/{id}', 'BudgetController@budgetView')->name('budget-view');
    Route::post('budget-save', 'BudgetController@budgetSave')->name('budget-save');
    Route::get('budget-download/{id}', 'BudgetController@budgetDownload')->name('budget-download');
    Route::post('budget-action', 'BudgetController@budgetAction')->name('budget-action');


    //Sales Returns order
    Route::get('sales-order-return-list', 'SalesOrderReturnController@salesOrderReturnList')->name('sales-order-return-list');
    Route::get('sales-order-return/{id}', 'SalesOrderReturnController@salesOrderReturn')->name('sales-order-return');
    Route::post('sales-order-return-save', 'SalesOrderReturnController@salesOrderReturnSave')->name('sales-order-return-save');
    Route::get('sales-order-return-nc/{id}', 'SalesOrderReturnController@salesOrderReturnNC')->name('sales-order-return-nc');

    Route::get('sales-return-by-token/{bookingID}/{token}', 'NoMiddlewareController@salesReturnByToken')->name('sales-return-by-token');




    //Sales Returns order
    Route::get('direct-sales-return', 'ReturnController@directSalesReturn')->name('direct-sales-return');
    Route::get('direct-purchase-return', 'ReturnController@directPurchaseReturn')->name('direct-purchase-return');

    //Installments
    Route::get('installment-order-list', 'InstallmentController@installmentOrderList')->name('installment-order-list');
    Route::get('installment-paid-history/{id}/{paymentThroughId}', 'InstallmentController@installmentPaidHistory')->name('installment-paid-history');
    Route::get('installment-receive', 'InstallmentController@installmentReceive')->name('installment-receive');
    //Route::get('installment-receive-save/{bookingId}/{paymentThroughId}', 'InstallmentController@installmentReceiveSave')->name('installment-receive-save');
    Route::get('installment-receive-save', 'InstallmentController@installmentReceiveSave')->name('installment-receive-save');
    Route::post('installment-action', 'InstallmentController@installmentAction')->name('installment-action');

    //Report Managemenet//
    Route::get('sales-report', 'ReportController@salesReport')->name('sales-report');
    Route::post('sales-report-list', 'ReportController@salesReportList')->name('sales-report-list');
    Route::post('download-sales-report', 'ReportController@downloadsalesReport')->name('download-sales-report');

    Route::any('sales-report-new', 'ReportNewController@salesReportNew')->name('sales-report-new');
    Route::post('sales-report-new-list', 'ReportNewController@salesReportNewList')->name('sales-report-new-list');
    Route::post('download-sales-report-new', 'ReportNewController@downloadsalesReportNew')->name('download-sales-report-new');

    Route::any('product-sales-report', 'ReportNewController@productSalesReport')->name('product-sales-report');

    Route::any('product-stock-report', 'ReportNewController@productStockReport')->name('product-stock-report');

    Route::post('product-stock-report-filter', 'ReportNewController@productStockReportFilter')->name('product-stock-report-filter');

    Route::get('purchase-report', 'ReportController@purchaseReport')->name('purchase-report');
    Route::get('purchase-concept-report', 'ReportController@purchaseConceptReport')->name('purchase-concept-report');
    Route::post('purchase-concept-report', 'ReportController@purchaseConceptReport')->name('purchase-concept-report');
    Route::post('purchase-report-list', 'ReportController@purchaseReportList')->name('purchase-report-list');
    Route::post('download-purchase-report', 'ReportController@downloadpurchaseReport')->name('download-purchase-report');
		Route::get('facturas-concept-report', 'ReportController@facturasConceptReport')->name('facturas-concept-report');
    Route::get('short-stock-item-report', 'ReportController@shortStockItemReport')->name('short-stock-item-report');


    // Notification
    Route::get('read-notification/{id}', 'NotificationController@readNotification')->name('read-notification');
    Route::get('read-all-notification', 'NotificationController@readAllNotification')->name('read-all-notification');




    Route::group(['prefix' => 'api'], function () {
        Route::post('products-datatable', 'ProductController@productsDatatable')->name('api.products-datatable');
        Route::post('purchase-order-datatable', 'PurchaseOrderController@purchaseOrderDatatable')->name('api.purchase-order-datatable');
         Route::post('supplier-invoice-datatable', 'SupplierInvoiceController@supplierInvoiceDatatable')->name('api.supplier-invoice-datatable');

        Route::post('purchase-invoice-datatable', 'PurchaseInvoiceController@purchaseInvoiceDatatable')->name('api.purchase-invoice-datatable');
        Route::post('get-product-list', 'NoMiddlewareController@getProductList')->name('api.get-product-list');
        Route::post('get-supplier-list', 'NoMiddlewareController@getSupplierList')->name('api.get-supplier-list');
        Route::post('get-concept-list', 'NoMiddlewareController@getConceptList')->name('api.get-concept-list');
        Route::post('po-received-product-datatable', 'PurchaseOrderReceivingController@poReceivedProductDatatable')->name('api.po-received-product-datatable');
        Route::post('po-return-product-datatable', 'PurchaseOrderReturnController@poReturnProductDatatable')->name('api.po-return-product-datatable');
        Route::post('sales-order-datatable', 'SalesOrderController@salesOrderDatatable')->name('api.sales-order-datatable');
        Route::post('get-customer-list', 'SalesOrderController@getCustomerList')->name('api.get-customer-list');
        Route::post('get-product-price', 'SalesOrderController@getProductPrice')->name('api.get-product-price');
        Route::post('get-customer-info', 'SalesOrderController@getCustomerInfo')->name('api.get-customer-info');

        Route::post('sales-return-product-datatable', 'SalesOrderReturnController@salesReturnProductDatatable')->name('api.sales-return-product-datatable');
        Route::post('get-order-list', 'ReturnController@getOrderList')->name('api.get-order-list');
        Route::post('get-sales-order-information', 'ReturnController@getSalesOrderInformation')->name('api.get-sales-order-information');
        Route::post('get-sales-order-history', 'ReturnController@getSalesOrderHistory')->name('api.get-sales-order-history');
        Route::post('get-purchase-order-list', 'ReturnController@getPurchaseOrderList')->name('api.get-purchase-order-list');
        Route::post('get-purchase-order-information', 'ReturnController@getPurchaseOrderInformation')->name('api.get-purchase-order-information');
        Route::post('get-purchase-order-history', 'ReturnController@getPurchaseOrderHistory')->name('api.get-purchase-order-history');
        Route::post('add-customer-modal', 'CustomerController@addCustomerModal')->name('api.add-customer-modal');
        Route::post('add-supplier-modal', 'SupplierController@addSupplierModal')->name('api.add-supplier-modal');
        Route::post('installment-order-datatable', 'InstallmentController@installmentOrderDatatable')->name('api.installment-order-datatable');
        Route::post('get-instalment-order-list', 'InstallmentController@getInstalmentOrderList')->name('api.get-instalment-order-list');
        Route::post('get-installment-order-information', 'InstallmentController@getInstallmentOrderInformation')->name('api.get-installment-order-information');
        Route::post('get-installment-history', 'InstallmentController@getInstallmentHistory')->name('api.get-installment-history');
        Route::post('short-stock-items-datatable', 'ReportController@shortStockItemsDatatable')->name('api.short-stock-items-datatable');
        Route::post('edit-sales-order-modal', 'SalesOrderController@editSalesOrderModal')->name('api.edit-sales-order-modal');
        Route::post('save-sales-order-modal', 'SalesOrderController@saveSalesOrderModal')->name('api.save-sales-order-modal');

        Route::post('budget-datatable', 'BudgetController@budgetDatatable')->name('api.budget-datatable');
       
        Route::post('get-selected-type-list', 'ProductController@getSelectedTypeList')->name('api.get-selected-type-list');
        Route::post('product-list-filter', 'ProductController@productListFilter')->name('api.product-list-filter');
        Route::post('price-change-ml-update', 'ProductController@priceChangeMLUpdate')->name('api.price-change-ml-update');

        Route::post('get-selected-type-list-dimension', 'ProductController@getSelectedTypeListDimension')->name('api.get-selected-type-list-dimension');
        Route::post('product-list-filter-dimension', 'ProductController@productListFilterDimension')->name('api.product-list-filter-dimension');
        Route::post('dimension-ml-update', 'ProductController@DimensionChangeMLUpdate')->name('api.dimension-ml-update');

        Route::get('sync-shipping-info-from-ml', 'ProductController@syncShippingInfoFromMl')->name('api.sync-shipping-info-from-ml');
        Route::post('get-selected-type-list-shipping-info', 'ProductController@getSelectedTypeListShippingInfo')->name('api.get-selected-type-list-shipping-info');
        Route::post('product-list-filter-having-me1-status', 'ProductController@productListFilterHavingME1Status')->name('api.product-list-filter-having-me1-status');
        Route::post('ml-list-shipping-mode-me1-update', 'ProductController@mlListShippingModeMe1Update')->name('api.ml-list-shipping-mode-me1-update');

        Route::post('type-list-all', 'ReportNewController@typeListAll')->name('api.type-list-all');
    });

});
