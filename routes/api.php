<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetOwnerController;
use App\Http\Controllers\CategoryItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\MasterItemController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/* Login Route */
Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware([ApiAuthMiddleware::class])->group(function(){
    /* User Route */
    Route::get('/users/current', [UserController::class, 'get']);
    Route::patch('/users/current', [UserController::class, 'update']);
    Route::delete('/users/logout', [UserController::class, 'logout']);

    /* Customer Route */
    Route::post('/customers', [CustomerController::class, 'create']);
    Route::get('/customers', [CustomerController::class, 'search']);
    Route::get('/customers/all', [CustomerController::class, 'getAll']);
    Route::get('/customers/{id}', [CustomerController::class, 'get'])->where('id', '[0-9]+');
    Route::put('/customers/{id}', [CustomerController::class,'update'])->where('id', '[0-9]+');
    Route::delete('/customers/{id}', [CustomerController::class, 'delete'])-> where('id','[0-9]+');
    Route::patch('/customers/inactive/{id}', [CustomerController::class,'inactiveCustomer'])->where('id', '[0-9]+');
    Route::post('/customers/import-csv', [CustomerController::class, 'importCsv']);

    /* Master Item Route */
    Route::post('/masteritems', [MasterItemController::class, 'create']);
    Route::get('/masteritems', [MasterItemController::class, 'search']);
    Route::get('/masteritems/all', [MasterItemController::class, 'getAll']);
    Route::get('/masteritems/itemtype/{itemType}', [MasterItemController::class, 'getItemByItemType']);
    Route::get('/masteritems/{id}', [MasterItemController::class, 'get'])->where('id', '[0-9]+');
    Route::put('/masteritems/{id}', [MasterItemController::class, 'update'])->where('id','[0-9]+');
    Route::delete('/masteritems/{id}', [MasterItemController::class,  'delete'])->where('id','[0-9]+');
    Route::patch('/masteritems/inactive/{id}', [MasterItemController::class,'inactiveItem'])->where('id', '[0-9]+');

    /* Input Stock Route */
    Route::post('/stockitems/{id}', [StockItemController::class, 'create'])->where('id', '[0-9]+');
    Route::put('/stockitems/{id}', [StockItemController::class, 'update'])->where('id', '[0-9]+');
    Route::get('/stockitems/currentstock/{itemId?}', [StockItemController::class, 'getCurrentStock']);
    Route::get('/stockitems/detailstock/{itemId}', [StockItemController::class, 'getDetailStock'])->where('itemId', '[0-9]+');
    Route::get('/stockitems/displaystock', [StockItemController::class, 'getDisplayStock']);
    
    /* Transaction Route */
    Route::post('/transactions', [TransactionController::class, 'create']);
    Route::get('/transactions/today/{date?}', [TransactionController::class, 'getTransaction'])->defaults('date', Carbon::today());
    Route::get('/transactions/outstanding', [TransactionController::class, 'getOutstandingTransaction']);
    /* Chart */
    Route::get('/transactions/salesperweek', [TransactionController::class, 'getSalesPerWeek']);
    Route::get('/transactions/topcustomer', [TransactionController::class, 'getTopCustomer']);
    Route::patch('/transactions/{id}', [TransactionController::class, 'update'])->where('id', '[0-9]+');

    /* Debt Route */
    Route::post('/debts/{customerId}', [DebtController::class, 'create'])->where('customerId', '[0-9]+');
    Route::get('/debts/customer/{customerId}', [DebtController::class, 'getByCust'])->where('customerId', '[0-9]+');
    Route::get('/debts/summary', [DebtController::class, 'getDebtSummary']);
    Route::get('/debts/outstanding', [DebtController::class, 'getDebtOutstanding']);
    Route::patch('/debts/{id}', [DebtController::class, 'update'])->where('id', '[0-9]+');

    /* Asset Owner */
    Route::post('/assetowners', [AssetOwnerController::class, 'create']);
    Route::get('/assetowners/all', [AssetOwnerController::class, 'getAll']);
    Route::get('/assetowners/{id}', [AssetOwnerController::class, 'get'])->where('id', '[0-9]+');
    Route::patch('/assetowners/{id}', [AssetOwnerController::class,'update'])->where('id', '[0-9]+');
    Route::delete('/assetowners/{id}', [AssetOwnerController::class, 'delete'])-> where('id','[0-9]+');
    Route::patch('/assetowners/inactive/{id}', [AssetOwnerController::class,'inactiveOwner'])->where('id', '[0-9]+');

    /* Asset */
    Route::post('/assets', [AssetController::class, 'create']);
    Route::get('/assets/summary', [AssetController::class, 'getSumAssetOwner']);
    Route::get('/assets/details/{ownerId}/assets/{assetName}', [AssetController::class, 'getDetailAsset'])
            ->where('ownerId', '[0-9]+');
    Route::patch('/assets/{id}', [AssetController::class,'update'])->where('id', '[0-9]+');

    /* Category Item */
    Route::post('/categoryitems', [CategoryItemController::class, 'create']);
    Route::get('/categoryitems/{id}', [CategoryItemController::class, 'get'])->where('id', '[0-9]+');
    Route::get('/categoryitems/all', [CategoryItemController::class, 'getAll']);
    Route::get('/categoryitems/active', [CategoryItemController::class, 'getActiveCategoryItems']);
    Route::patch('/categoryitems/{id}', [CategoryItemController::class,'update'])->where('id', '[0-9]+');
    Route::delete('/categoryitems/{id}', [CategoryItemController::class, 'delete'])-> where('id','[0-9]+');
    Route::patch('/categoryitems/inactive/{id}', [CategoryItemController::class,'inactiveOwner'])->where('id', '[0-9]+');
});
