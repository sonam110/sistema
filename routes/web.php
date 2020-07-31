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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('screen-lock/{currtime}/{id}/{randnum}', 'NoMiddlewareController@screenlock')->name('screenlock');

Auth::routes();

Route::get('admin', 'AdminController@loginPageAdmin');
Route::get('login', ['as' => 'login', 'uses' => 'AdminController@loginPage']);
Route::post('login', 'AdminController@authenticate')->name('user-register');
Route::get('logout', 'AdminController@logout')->name('logout');
Route::get('screenlock/{currtime}/{id}/{randnum}', 'AdminController@screenlock');

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

    //Profile management
	Route::get('profile', 'AdminController@profile')->name('edit-profile');
	Route::post('change-password', 'AdminController@changePassword')->name('change-password');
	Route::post('update-profile', 'AdminController@updateProfile')->name('update-profile');

	//Permissions
	Route::resource('permissions','PermissionController');

    //Roles
    Route::resource('roles','RoleController');
    Route::get('role-delete/{id}','RoleController@roleDelete')->name('role-delete');

    //Products
    Route::get('product-list', 'ProductController@products')->name('product-list');

    //Supplier
    Route::get('supplier-list', 'SupplierController@suppliers')->name('supplier-list');
    Route::get('supplier-create', 'SupplierController@supplierCreate')->name('supplier-create');
    Route::get('supplier-edit/{id}', 'SupplierController@supplierEdit')->name('supplier-edit');
    Route::get('supplier-view/{id}', 'SupplierController@supplierView')->name('supplier-view');
    Route::post('supplier-save', 'SupplierController@supplierSave')->name('supplier-save');
    Route::get('supplier-delete/{id}', 'SupplierController@supplierDelete')->name('supplier-delete');
    Route::post('supplier-action', 'SupplierController@supplierAction')->name('supplier-action');

    //Purchase order
    Route::get('purchase-order-list', 'PurchaseOrderController@purchaseOrderList')->name('purchase-order-list');
    Route::get('purchase-order-create', 'PurchaseOrderController@purchaseOrderCreate')->name('purchase-order-create');
    Route::post('purchase-order-save', 'PurchaseOrderController@purchaseOrderSave')->name('purchase-order-save');
    Route::get('purchase-order-view/{id}', 'PurchaseOrderController@purchaseOrderView')->name('purchase-order-view');
    Route::get('purchase-order-delete/{id}', 'PurchaseOrderController@purchaseOrderDelete')->name('purchase-order-delete');
    Route::get('purchase-order-download/{id}', 'PurchaseOrderController@purchaseOrderDownload')->name('purchase-order-download');
    Route::post('purchase-order-action', 'PurchaseOrderController@purchaseOrderAction')->name('purchase-order-action');

    //Purchase Receiving order
    Route::get('purchase-order-received-list', 'PurchaseOrderReceivingController@purchaseOrderReceivedList')->name('purchase-order-received-list');
    Route::get('purchase-order-receiving/{id}', 'PurchaseOrderReceivingController@purchaseOrderReceiving')->name('purchase-order-receiving');
    Route::post('purchase-order-receiving-save', 'PurchaseOrderReceivingController@purchaseOrderReceivingSave')->name('purchase-order-receiving-save');

    //Purchase Returns order
    Route::get('purchase-order-return-list', 'PurchaseOrderReturnController@purchaseOrderReturnList')->name('purchase-order-return-list');
    Route::get('purchase-order-return/{id}', 'PurchaseOrderReturnController@purchaseOrderReturn')->name('purchase-order-return');
    Route::post('purchase-order-return-save', 'PurchaseOrderReturnController@purchaseOrderReturnSave')->name('purchase-order-return-save');


    //Customers
    Route::get('customer-list', 'CustomerController@customers')->name('customer-list');
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



    Route::group(['prefix' => 'api'], function () {
        Route::post('products-datatable', 'ProductController@productsDatatable')->name('api.products-datatable');
        Route::post('purchase-order-datatable', 'PurchaseOrderController@purchaseOrderDatatable')->name('api.purchase-order-datatable');
        Route::post('get-product-list', 'NoMiddlewareController@getProductList')->name('api.get-product-list');
        Route::post('get-supplier-list', 'NoMiddlewareController@getSupplierList')->name('api.get-supplier-list');
        Route::post('po-received-product-datatable', 'PurchaseOrderReceivingController@poReceivedProductDatatable')->name('api.po-received-product-datatable');
        Route::post('po-return-product-datatable', 'PurchaseOrderReturnController@poReturnProductDatatable')->name('api.po-return-product-datatable');
        Route::post('sales-order-datatable', 'SalesOrderController@salesOrderDatatable')->name('api.sales-order-datatable');
        Route::post('get-customer-list', 'SalesOrderController@getCustomerList')->name('api.get-customer-list');
    });
});