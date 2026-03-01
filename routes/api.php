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

/* Login Route */
Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware(ApiAuthMiddleware::class)->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('current', 'get');
        Route::patch('current', 'update');
        Route::delete('logout', 'logout');
    });

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */
    Route::prefix('customers')->controller(CustomerController::class)->group(function () {
        Route::post('/', 'create');
        Route::get('/', 'search');
        Route::get('{id}', 'get')->whereNumber('id');
        Route::put('{id}', 'update')->whereNumber('id');
        Route::delete('{id}', 'delete')->whereNumber('id');

        Route::patch('{id}/inactive', 'inactiveCustomer')->whereNumber('id');
        Route::post('import-csv', 'importCsv');
    });

    /*
    |--------------------------------------------------------------------------
    | Master Items
    |--------------------------------------------------------------------------
    */
    Route::prefix('masteritems')->controller(MasterItemController::class)->group(function () {
        Route::get('/', 'getAll');
        Route::post('/', 'create');
        Route::get('{id}', 'findById')->whereNumber('id');
        Route::put('{id}', 'update')->whereNumber('id');

        Route::get('itemtype/{itemType}', 'getItemByItemType');
        Route::get('status/{flagStatus}', 'getItemByFlagStatus');
        Route::patch('{id}/inactive', 'inactiveItem')->whereNumber('id');
    });

    /*
    |--------------------------------------------------------------------------
    | Stock Items
    |--------------------------------------------------------------------------
    */
    Route::prefix('stockitems')->controller(StockItemController::class)->group(function () {
        Route::post('{itemId}', 'createNewStock')->whereNumber('id');
        Route::put('{id}', 'updateStock')->whereNumber('id');

        Route::get('current/{itemId?}', 'getCurrentStock');
        Route::get('detail/{itemId}', 'getDetailStock')->whereNumber('itemId');
        Route::get('display/{filledGasId}/{emptyGasId}', 'getDisplayStock')
            ->whereNumber('filledGasId')
            ->whereNumber('emptyGasId');
    });

    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */
    Route::prefix('transactions')->controller(TransactionController::class)->group(function () {
        Route::post('/', 'createTransaction');
        Route::patch('{id}', 'updateTransaction')->whereNumber('id');

        Route::get('date/{date?}', 'getTransactionByDate')->defaults('date', Carbon::today());
        Route::get('outstanding', 'getOutstandingTransaction');

        Route::get('chart/daily-sale', 'getDailySale');
        Route::get('chart/top-customer', 'getTopCustomer');
    });

    /*
    |--------------------------------------------------------------------------
    | Debts
    |--------------------------------------------------------------------------
    */
    Route::prefix('debts')->controller(DebtController::class)->group(function () {
        Route::post('/', 'create');
        Route::patch('{id}', 'update')->whereNumber('id');

        Route::get('customer/{customerId}', 'findDebtByCustId')->whereNumber('customerId');
        Route::get('summary', 'findDebtSummary');
        Route::get('outstanding', 'findDebtOutstanding');
    });

    /*
    |--------------------------------------------------------------------------
    | Asset Owners
    |--------------------------------------------------------------------------
    */
    Route::prefix('assetowners')->controller(AssetOwnerController::class)->group(function () {
        Route::post('/', 'create');
        Route::get('/', 'getAll');
        Route::get('{id}', 'find')->whereNumber('id');
        Route::patch('{id}', 'update')->whereNumber('id');
        Route::patch('{id}/inactive', 'inactiveOwner')->whereNumber('id');
    });

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    */
    Route::prefix('assets')->controller(AssetController::class)->group(function () {
        Route::post('/', 'create');
        Route::patch('{id}', 'update')->whereNumber('id');

        Route::get('summary', 'getSumAssetByOwner');
        Route::get('details/{ownerId}/{itemId}', 'getDetailAsset')
            ->whereNumber('ownerId')
            ->whereNumber('itemId');
    });

    /*
    |--------------------------------------------------------------------------
    | Category Items
    |--------------------------------------------------------------------------
    */
    Route::prefix('categoryitems')->controller(CategoryItemController::class)->group(function () {
        Route::post('/', 'create');
        Route::get('/', 'getAll');
        Route::get('active', 'getActiveCategoryItems');
        Route::get('{id}', 'get')->whereNumber('id');
        Route::patch('{id}', 'update')->whereNumber('id');
        Route::delete('{id}', 'delete')->whereNumber('id');
        Route::patch('{id}/inactive', 'inactiveOwner')->whereNumber('id');
    });

});
