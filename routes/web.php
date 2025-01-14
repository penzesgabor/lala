<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserActionLogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BaseMaterialTypeController;
use App\Http\Controllers\ProductGroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\ProductMappingController;
use App\Http\Controllers\OrderImportController;
use App\Http\Controllers\TrolleyController;
use App\Http\Controllers\CuttingListController;
use App\Http\Controllers\CustomerImportController;





Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RolePermissionController::class);
    Route::get('logs', [UserActionLogController::class, 'index'])->middleware('auth')->name('logs.index');
});

Route::get('/customers/import', [CustomerImportController::class, 'showImportForm'])->name('customers.import.form');
Route::post('/customers/import', [CustomerImportController::class, 'processImport'])->name('customers.import.process');


##Customers
Route::resource('customers', CustomerController::class);
Route::get('customers/{customer}/prices', [CustomerController::class, 'editPrices'])->name('customers.prices.edit');
Route::put('customers/{customer}/prices', [CustomerController::class, 'updatePrices'])->name('customers.prices.update');
Route::get('customers/{customer}/dashboard', [CustomerDashboardController::class, 'index'])->name('customers.dashboard');
## Customer import

##Base material
Route::resource('base_material_types', BaseMaterialTypeController::class);
##Product Group
Route::resource('product_groups', ProductGroupController::class);
##Product



Route::resource('product-mappings', ProductMappingController::class);
Route::get('product-mappings/{productMapping}/edit', [ProductMappingController::class, 'edit'])->name('product-mappings.edit');



##Update all custom prices for a customer
Route::post('customers/{customer}/prices/update-all', [CustomerController::class, 'updateAllPrices'])->name('customers.prices.updateAll');

##Update all base prices for products
Route::post('products/prices/update-all', [ProductController::class, 'updateAllBasePrices'])->name('products.prices.updateAll');

Route::resource('vats', VatController::class);

##Order

Route::resource('orders', OrderController::class);
Route::get('/customers/{customer}/delivery-addresses', [CustomerController::class, 'getDeliveryAddresses']);
Route::get('orders/{order}/products/create', [OrderProductController::class, 'create'])->name('order.products.create');
Route::post('orders/{order}/products', [OrderProductController::class, 'store'])->name('order.products.store');
Route::get('/orders/{order}/productsshow', [OrderProductController::class, 'productsshow'])->name('orders.productsshow');
Route::get('orders/{order}/products/{product}/edit', [OrderProductController::class, 'edit'])->name('order.products.edit');
Route::put('orders/{order}/products/{product}', [OrderProductController::class, 'update'])->name('order.products.update');
#Route::delete('orders/{order}/products/{product}', [OrderProductController::class, 'destroy'])->name('order.products.destroy');
Route::delete('/orders/{order}/products/{product}', [OrderProductController::class, 'destroy'])->name('order.products.destroy');
Route::post('/orders/{order}/products/delete-group', [OrderProductController::class, 'deleteGroup'])->name('order.products.deleteGroup');
Route::post('/orders/{order}/products/get-details', [OrderProductController::class, 'getDetails'])->name('order.products.getDetails');

#print etikett
Route::get('/orders/{id}/etiketts', [OrderController::class, 'printEtiketts'])->name('orders.etiketts');


Route::get('customers/{customer}/orders', [CustomerOrderController::class, 'index'])->name('customers.orders.index');
Route::get('customers/{customer}/orders/create', [OrderController::class, 'createFromCustomer'])->name('customers.orders.create');
Route::post('customers/{customer}/orders', [OrderController::class, 'storeFromCustomer'])->name('customers.orders.store');


// Order Import Routes
Route::get('/orders/import/{customer_id}', [OrderImportController::class, 'showImportForm'])->name('orders.import.form');
Route::post('/orders/import', [OrderImportController::class, 'processImport'])->name('orders.import.process');
Route::post('/orders/import/match', [OrderImportController::class, 'saveMatching'])->name('orders.import.match');
Route::post('/orders/import/save', [OrderImportController::class, 'saveOrder'])->name('orders.import.save');
Route::get('/orders/import/third-step/{order_id}', [OrderImportController::class, 'thirdStepForm'])->name('orders.import.third-step');
Route::post('/orders/import/third-step/{order_id}', [OrderImportController::class, 'saveThirdStep'])->name('orders.import.save-third-step');

## Order import

## Trolley

Route::resource('trolleys', TrolleyController::class);

## Cut list
Route::resource('cutting-lists', CuttingListController::class);
Route::post('/cutting-lists/selection', [CuttingListController::class, 'secondStep'])->name('cutting-lists.second-step');
Route::get('/cutting-lists/{cuttingList}', [CuttingListController::class, 'show'])->name('cutting-lists.show');


// Resourceful routes for Product CRUD
Route::resource('products', ProductController::class);


// print
Route::get('/orders/{order}/print', [OrderController::class, 'printOrder'])->name('orders.print');
Route::get('/orders/{order}/print-etikett', [OrderController::class, 'printEtikett'])->name('orders.print.etikett');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/home', function () {
    return view('dashboard');
})->name('home');



require __DIR__.'/auth.php';
