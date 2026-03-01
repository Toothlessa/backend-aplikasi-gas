<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\TransactionCreateRequest;
use App\Http\Requests\Transaction\TransactionUpdateRequest;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Http\Resources\Transaction\TransactionDailyCollection;
use App\Http\Resources\Transaction\TransactionOutstandingCollection;
use App\Http\Resources\Transaction\TransactionTop10CustomerCollection;
use App\Http\Resources\Transaction\TransactionResource;
use Illuminate\Support\Facades\Auth;
use App\Services\TransactionService;

class TransactionController extends Controller
{
     protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function createTransaction(TransactionCreateRequest $request)
    {
        # Validate and Call Service
        $transaction = $this->transactionService->createTransaction($request->validated());
        #return response
        return (new TransactionResource($transaction))->response()->setStatusCode(201);
    }

    public function updateTransaction(int $id, TransactionUpdateRequest $request) {
        $transaction = $this->transactionService->updateTransaction(
            $id,
            $request->validated()
        );

        return (new TransactionResource($transaction))->response()->setStatusCode(200);
    }


    public function getTransactionByDate($date)
    {
        $transaction = $this->transactionService->getTransactionByDate($date);

        return (new TransactionCollection($transaction))->response()->setStatusCode(200);
    }

    public function getOutstandingTransaction(){
        Auth::user();

        $transaction = $this->transactionService->getOutsTransaction();

        return (new TransactionOutstandingCollection($transaction))->response()->setStatusCode(200);
    }

   public function getDailySale() {
        Auth::user();

        $transaction = $this->transactionService->getDailySale();

        return (new TransactionDailyCollection($transaction))->response()->setStatusCode(200);
   }

   public function getTopCustomer() {
        Auth::user();

        $transaction = $this->transactionService->getTopCustomer();

        return (new TransactionTop10CustomerCollection($transaction))->response()->setStatusCode(200);
   }

}