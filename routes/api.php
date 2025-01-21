<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MasterItemController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
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

    /* Master Item Route */
    Route::post('/masteritems', [MasterItemController::class, 'create']);
    Route::get('/masteritems', [MasterItemController::class, 'search']);
    Route::get('/masteritems/all', [MasterItemController::class, 'getAll']);
    Route::get('/masteritems/{id}', [MasterItemController::class, 'get'])->where('id', '[0-9]+');
    Route::put('/masteritems/{id}', [MasterItemController::class, 'update'])->where('id','[0-9]+');
    Route::delete('/masteritems/{id}', [MasterItemController::class,  'delete'])->where('id','[0-9]+');

    /* Input Stock Route */
    Route::post('/stockitems/{id}', [StockItemController::class, 'create'])->where('id', '[0-9]+');
    Route::put('/stockitems/{id}', [StockItemController::class, 'update'])->where('id', '[0-9]+');
    Route::get('/stockitems/currentstock', [StockItemController::class, 'getCurrentStock']);
    
    /* Transaction Route */
    Route::post('/transactions/{itemId}/customer/{customerId}', [TransactionController::class, 'create']);
    Route::get('/transactions/today', [TransactionController::class, 'getTodayTransaction']);
    Route::patch('/transactions/{id}', [TransactionController::class, 'update'])->where('id', '[0-9]+');
});
